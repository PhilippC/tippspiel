<?php
/********************

Klasse für die Benutzer-Verwaltung
==================================

Implementiert als Singleton

Philipp Crocoll, Feb. 06

*********************/

define('FILTER_USERMAIL_IS_CONFIRMED', '___NOT_IN_PENDING_GROUP');

require_once("BenutzerVerwTexts.inc.php5");
require_once("Benutzer.class.php5");
require_once("Benutzer.db.class.php5");
require_once("baseconfig.php5");
require_once(dirname(__FILE__)."/../authconf.php");

require(dirname(__FILE__)."/../phpmailer/class.phpmailer.php");

class CBenutzerVerw
{
      protected $auth = NULL;
      protected $BenutzerDB = NULL;
      protected $UserList = NULL;

      //Liefert den Namen, den das Feld für den Benutzernamen im Login-Form haben muss
      public function GetUsernameFieldname()
      {
        $this->CreateAuth();
        return $this->auth->_options['usernameField'];
      }
      public function GetPasswordFieldname()
      {
        $this->CreateAuth();
        return $this->auth->_options['passwordField'];
      }

      public function GetUserList($group="")
      {
        //$this->CreateAuth();
        $BenutzerDB = new CBenutzerDB();
        $BenutzerDB->OrderByClause = "Benutzer.Name";
        if ($group == "")
          return $BenutzerDB->LiesAlleBenutzer();
        else if ($group == FILTER_USERMAIL_IS_CONFIRMED)
          return $BenutzerDB->LiesBestBenutzer();
        else
          return $BenutzerDB->LiesGruppenBenutzer($group);
      }

      public function DoesUserExist($Name)
      {
        $this->CreateBenutzerDB();
        if ($this->UserList == NULL)
          $this->UserList = $this->BenutzerDB->LiesAlleBenutzer();
        foreach ($this->UserList as $User)
          if ($User->Name == $Name) return true;

        return false;
      }

      public function DoesMailExist($Mail)
      {
        $this->CreateBenutzerDB();
        if ($this->UserList == NULL)
          $this->UserList = $this->BenutzerDB->LiesAlleBenutzer();

        foreach ($this->UserList as $User)
          if ($User->Mail == $Mail) return true;

        return false;
      }

      public function IsUserInGroup($UserName, $GroupName)
      {
        $this->CreateBenutzerDB();
        $groups = $this->BenutzerDB->LiesBenutzerGruppen($UserName);
        return isset($groups[$GroupName]);
      }
      
      private function EnsureUsernameIsValid($UserName)
      {
      	$forbid = array('--', '&quot;','!','@','#','$','%','^','&','*','+','{','}','|',':','"','<','>','?','[',']','\\',';',"'",'/','*','+','~','`','=');
      	for ($i=0;$i<count($forbid);$i++)
      	{
      		if (strpos($UserName, $forbid[$i]) !== false) 
      		{
      			throw new BenutzerMsgException("Ungültiger Benutzername. Das Zeichen \"".htmlspecialchars($forbid[$i])."\" ist nicht erlaubt.");
      		}
      	}

      	
      	
      }

      //**********
      // RegistertNewUser
      //  -Prüft, ob Name oder Mail-Adresse schon bestehen
      //  -Falls $CheckMailAddress true ist, geht die Funktion folgendermaßen vor:
      //    -Trägt Benutzer in Benutzer-Tabelle ein und vermerkt im ConfirmString, in welche Gruppe der Nutzer später eingetragen werden soll
      //    -Trägt Benutzer in users_pending ein
      //    -Verschickt Überprüfungs-Mail
      //  -Falls $CheckMailAddress false ist, läuft es so ab:
      //    -Trägt Benutzer in BenutzerTabelle ein, ohne ConfirmString
      //    -Trägt Benutzer in "Benutzer" und in evtl. gewünschte Gruppe ein

      public function RegisterNewUser($Name, $Mail, $Pwd, $Group="", $CheckMailAddress=true, $AddToUserGroup=true)
      {
        $this->CreateBenutzerDB();
        //does user already exist?
        if ($this->DoesUserExist($Name))
          throw new BenutzerMsgException("Ein Benutzer mit Namen $Name existiert bereits.");
          
        $this->EnsureUsernameIsValid($Name);
   

        //does Mail address already exist?
        if (($Mail != "") && ($Mail != null) && $this->DoesMailExist($Mail))
          throw new BenutzerMsgException("Ein Benutzer mit der E-Mail-Adresse $Mail existiert bereits.");

        if ($CheckMailAddress)
        {
          //create confirmation string
          $ConfirmationString = md5 (uniqid(rand()));

          //Get group id:
          $GroupID = -1;
          if ($Group != "")
            $GroupID = $this->BenutzerDB->LiesGruppenID($Group);
        }
        else
        {
          $ConfirmationString = "";
          $GroupID = 0;
        }

        //GroupID found?
        if ($GroupID == -1)
        {
          throw new Exception("Die Gruppe $Group wurde in der Datenbank nicht gefunden!");
        }

        //create user
        try
        {
			$this->CreateAuth();
			if ($this->auth->encodePWD)
				$Pwd = md5($Pwd);
		
			$this->BenutzerDB->ErzeugeNeuenBenutzer($Name,$Mail,$Pwd,$ConfirmationString, $GroupID);
        }
        catch (Exception $e)
        {
          throw new BenutzerMsgException("Benutzer konnte nicht in die Datenbank eingetragen werden! Bitte Administrator kontaktieren.");
        }

        if ($CheckMailAddress)
        {
          CLogClass::Log("ConfirmationString $ConfirmationString für User $Name erzeugt und eingetragen. Benutzer soll in Gruppe '$Group' eingetragen werden");

          //add user to group users_pending
          try
          {
            $this->AddUserToGroup($Name, "users_pending");
          }
          catch (Exception $e)
          {
            throw new BenutzerMsgException("Benutzer konnte nicht zur Gruppe der noch nicht aktivierten Benutzer hinzugefügt werden werden! Bitte Administrator kontaktieren.");
          }
          //Send check mail:
          $this->SendCheckMail($Name, $Mail, $ConfirmationString);
        }
        else
        {
          //Benutzer zu "Benutzer"-Gruppe hinzufügen:
          $this->AddUserToGroup($Name, "Benutzer");
          //Benutzer in evtl. gewünschte zusätzliche Gruppe eintragen:
          if ($Group != "")
            $this->AddUserToGroup($Name, $Group);
        }

      }
	  
	  public function RegisterFacebookUser($Name, $Mail, $FacebookUserId, $Group="")
	  {
		$this->RegisterNewUser($Name, $Mail, "", $Group, false);
		$this->BenutzerDB->SetFacebookUserId($Name, $FacebookUserId);
	  }

      //**********
      // ConfirmUser
      //  -Prüft, ob der ConfirmString existiert (falls nein: BenutzerMsgException)
      //  -Löscht ConfirmString/GroupAufterConfirm aus Benutzertabelle
      //  -Fügt Benutzer zur gewünschten Gruppe hinzu
      //  -Fügt Benutzer zu "Benutzer" hinzu
      // - Liefert Benutzer-Instanz zurück

	  public function ChangePwd($PwdResetKey, $NewPwd)
	  {
		$this->BenutzerDB = new CBenutzerDB();	  
		if (!$this->BenutzerDB->FindePwdResetKey($PwdResetKey))
           throw new BenutzerMsgException("Passwort konnte nicht geändert werden.
                                           Bitte prüfen, ob der Link aus der E-Mail
                                           komplett in den Browser kopiert wurde. Ansonsten bitte den Administrator
                                           kontaktieren.");

         try
         {
			$this->CreateAuth();
			if ($this->auth->encodePWD)
				$NewPwd = md5($NewPwd);
          $this->BenutzerDB->AenderePwd($PwdResetKey, $NewPwd);
         }
         catch (Exception $e)
         {
		 CLogClass::log($e->getMessage(),LOG_TYPE_ERROR);
          throw new BenutzerMsgException("Beim Ändern des Passwort ist ein Fehler aufgetreten (ErrorNr 210678).
         			                             Bitte Administrator kontaktieren.");
         }
	  }


      public function ConfirmUser($ConfirmString)
      {
         $this->CreateBenutzerDB();
         $GroupID = -1; $UserID = -1;
         if (!$this->BenutzerDB->FindeConfirmString($ConfirmString,  $UserID, $GroupID))
           throw new BenutzerMsgException("Der Benutzeraccount konnte nicht aktiviert werden.
                                           Bitte prüfen, ob dies schon geschehen ist und ob der Link aus der E-Mail
                                           komplett in den Browser kopiert wurde. Ansonsten bitte den Administrator
                                           kontaktieren.");

         try
         {
          $this->BenutzerDB->LoescheConfirmString($ConfirmString);
         }
         catch (Exception $e)
         {
          throw new BenutzerMsgException("Beim Aktivieren des Kontos ist ein Fehler aufgetreten (ErrorNr 100584).
         			                             Bitte Administrator kontaktieren.");
         }


         //User aus users_pending nehmen:
         $UsersPendingID = $this->BenutzerDB->LiesGruppenID("users_pending");
         $this->BenutzerDB->LoescheBenutzerAusGruppe($UserID,$UsersPendingID);

         //User in bei Anmeldung gewünschte Gruppe eintragen:
         if ($GroupID != -1)
           $this->BenutzerDB->FuegeBenutzerZuGruppeHinzu($UserID,$GroupID);

         //User in "Benutzer"-Gruppe eintragen:
         $BenutzerGruppeID = $this->BenutzerDB->LiesGruppenID("Benutzer");
         if ($GroupID != $BenutzerGruppeID)
           $this->BenutzerDB->FuegeBenutzerZuGruppeHinzu($UserID,$BenutzerGruppeID);

         return true;

      }


      public function SendCheckMail($Name, $Mail, $ConfirmString)
      {
      	 $serverUrl = "http://".$_SERVER['SERVER_NAME'];
      	 $serverPort = $_SERVER['SERVER_PORT'];
      	 if ($serverPort != 80)
      	 	$serverUrl = $serverUrl.":$serverPort";

         $ActivationLink = $serverUrl.ACCOUNT_AKTIVIERUNGS_DATEI."?ActivationCode=".$ConfirmString;
         

	$mail = new PHPMailer();

	$mail->From     = USER_MANAGER_MAIL;
	$mail->FromName = USER_MANAGER_NAME;

	$mail->Mailer   = "mail";
	$mail->CharSet="utf-8";

	$mail->Subject = getCheckMailSubject();

    	$mail->Body    = getCheckMailBody($Name, $ActivationLink);

    	$mail->AddAddress($Mail, $Name);

    	if(!$mail->Send())
         {
      	
           CLogClass::log("Fehler beim Versenden der Mail an $Name ($Mail): ".$mail->ErrorInfo,LOG_TYPE_ERROR);
           throw new BenutzerMsgException("Fehler beim Versenden der Mail! Bitte <a href=\"mailto:".USER_MANAGER_MAIL."\">Administrator</a> kontaktieren! ");
         }
         return true;

      }

      public function AddUserToGroup($UserName, $GroupName)
      {
        if ($this->IsUserInGroup($UserName, $GroupName)) throw new BenutzerMsgException("Der Benutzer $UserName ist bereits Mitglied der Gruppe $GroupName!");

        $this->CreateBenutzerDB();
        $UserID = $this->BenutzerDB->LiesBenutzerID($UserName);
        $GroupID = $this->BenutzerDB->LiesGruppenID($GroupName);

        if ($GroupID == -1)
        {
          $msg = "GruppenID von Gruppe $GroupName wurde in der Datenbank nicht gefunden!";
          CLogClass::log($msg,LOG_TYPE_CRITICAL_ERROR);
          throw new Exception($msg);
        }

        if ($UserID == -1)
        {
          $msg = "UserID von Benutzer $UserName wurde in der Datenbank nicht gefunden!";
          CLogClass::log($msg,LOG_TYPE_CRITICAL_ERROR);
          throw new Exception($msg);
        }

        $this->BenutzerDB->FuegeBenutzerZuGruppeHinzu($UserID,$GroupID);

        return true;

      }

      public function SendPasswordMail($Name)
      {
		

		$mail = new PHPMailer();

		$mail->From     = USER_MANAGER_MAIL;
		$mail->FromName = USER_MANAGER_NAME;

		$mail->Mailer   = "mail";
		$mail->CharSet="utf-8";

		$mail->Subject = getPasswordMailSubject();
	  
		$BenutzerDB = new CBenutzerDB();
		$Benutzer = $BenutzerDB->LiesBenutzer($Name);
		if ($Benutzer->IsFacebookUser)
		{
			throw new BenutzerMsgException("Passwort-Mail konnte nicht versendet werden. Bitte logge dich über die Facebook-App im Tippspiel ein!");
		} 
		
		$this->CreateAuth();
        if ($this->auth->encodePWD)
		{
			$key = $BenutzerDB->ErzeugePwdResetKey($Name);
			$serverUrl = "http://".$_SERVER['SERVER_NAME'];
			$serverPort = $_SERVER['SERVER_PORT'];
			if ($serverPort != 80)
				$serverUrl = $serverUrl.":$serverPort";

			$resetLink = $serverUrl.PWDRESET_DATEI."?PwdResetKey=".$key;

			$mail->Body    = getPasswordMailResetBody($Name, $resetLink);
		}
		else
		{         
		
			$mail->Body    = getPasswordMailPlainBody($Name, $Benutzer->Pwd);
			
		}

    	$mail->AddAddress($Benutzer->Mail, $Name);

    	if(!$mail->Send())
         {
           CLogClass::log("Fehler beim Versenden der Mail an $Name ($Mail): ".$mail->ErrorInfo,LOG_TYPE_ERROR);
           throw new BenutzerMsgException("Fehler beim Versenden der Mail! Bitte <a href=\"mailto:".USER_MANAGER_MAIL."\">Administrator</a> kontaktieren! ");
         }

         return true;
      }

      protected function CreateAuth()
      {
        if ($this->auth == NULL)
          $this->auth = &newGroupAuth();
      }

      protected function CreateBenutzerDB()
      {
        if ($this->BenutzerDB == NULL)
          $this->BenutzerDB = new CBenutzerDB;
      }

      //Fügt die alle Benutzer der Gruppe $GroupName als Benutzer in die phpbb-Tabellen ein
      //Im Moment werden nur die Standard-Namen der phpbb-Tabellen unterstützt!
      //Wird GroupName = "" übergeben, werden alle Benutzer (auch nicht confirmte)
      //im phpBB angemeldet
      public function AddGroupToPhpBB($GroupName)
      {
        $this->CreateBenutzerDB();
        $BenutzerListe = $this->GetUserList($GroupName);
        foreach ($BenutzerListe as $Benutzer)
        {
          $this->BenutzerDB->TrageBenutzerInPhpBBEin($Benutzer->ID,$Benutzer->Name,$Benutzer->Pwd);
        }
      }


}
?>
