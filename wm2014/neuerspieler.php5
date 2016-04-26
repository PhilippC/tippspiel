<?php
require_once("tippconfig.php5"); 
 require_once("./script/TippSpielUIWeb1.class.php5");
  require_once("neuerspieler.inc.php5");
    require_once("./script/TippTabellenCache.db.class.php5");

  require_once('./script/UIFactory.inc.php5');
 $TippSpielUI = createWebUi1();
 
  require_once($_SERVER["DOCUMENT_ROOT"].'/inc/authconf.php');
  $auth = &newGroupAuth();

  $strAktion = '';
   if (isset($_GET['Aktion']))
    $strAktion = $_GET['Aktion'];
  if (isset($_POST['Aktion']))
    $strAktion = $_POST['Aktion'];
 
  //DEFAULT / "Startseite" für das Anlegen eines neuen Accounts fürs Tippsspiel:
  if ($strAktion == '')
  {
    $TippSpielUI->OutputHeader(TIPPSPIEL_NAME);
	if (isFacebookApp())
	{
		if ($auth->isIdentified)
		{
			echo("Du bist bereits angemeldet! Unter <a href=\"tippspiel.php5\">Mein Tippspiel</a> kannst du deine Tipps abgeben.");
		}
		else PrintNewFacebookUserForm();
	}
	else
	{
		PrintTransferUserForm();
		PrintNewUserForm();
	}
  }

//ein bestehender Benutzer möchte sich fürs Tippspiel anmelden:
  if ($strAktion == 'TransferUser')
  {
    //Prüfen, ob der Benutzer sich erfolgreich angemeldet hat:
    require_once('./script/authCallbackTippspiel.php5');
    require_once($_SERVER["DOCUMENT_ROOT"].'/inc/authconf.php');
    $auth = &newGroupAuth();
    $auth->requireAtLeast("Benutzer");

    $TippSpielUI->OutputHeader("Benutzer für's Tippspiel anmelden");

    //Benutzer in die TippspielGruppe eintragen:
    $BenutzerVerw = new CBenutzerVerw();
    try
    {
      $BenutzerVerw->AddUserToGroup($auth->user["Name"],TIPPSPIEL_USERGROUP);
       $Username = $auth->user["Name"];
       $auth->user['::groups::'] = $auth->_getGroups($auth->user["BenutzerID"]);
       $TippSpielUI->OutputMessageBox('Anmeldung erfolgreich!', 'Die Anmeldung für Benutzer '.$Username.' wurde erfolgreich durchgeführt!
         Du kannst nun <a href="tippspiel.php5">am Tippspiel teilnehmen!</a>');
    }
    catch(BenutzerMsgException $e)
    {
      $TippSpielUI->OutputMessageBox('Anmeldung zum Tippspiel fehlgeschlagen!',$e->getMessage()."<br /><br /><a href=\"index.php5\">Zur Startseite</a>");
      CLogClass::log("BenutzerMsgException: ".$e->getMessage());
    }
    catch(Exception $e)
    {
      $TippSpielUI->OutputMessageBox('Anmeldung zum Tippspiel fehlgeschlagen!',"Die Anmeldung konnte nicht durchgeführt werden, bitte Administrator kontaktieren!<br /><br /><a href=\"index.php5\">Zur Startseite</a>");

      CLogClass::log("Exception: ".$e->getMessage(), LOG_TYPE_ERROR);
    }
  }
  
  
//ein Facebook-Benutzer möchte sich fürs Tippspiel anmelden:
  if ($strAktion == 'RegisterFacebookUser')
  {
    $BenutzerVerw = new CBenutzerVerw();
	
	
    try
    {
      $Name = $_POST["Name"];
	  $Mail = $_POST["Mail"];
	  $facebookUser = tryGetLoggedInFacebookUser();
	  if (empty($facebookUser))
	  {
	    $TippSpielUI->OutputHeader("Fehler bei Anmeldung über Facebook");
		throw new Exception("Couldn't get Facebook user data");
	  }
		
	  $facebookUserId = $facebookUser["id"];
      $BenutzerVerw->RegisterFacebookUser($Name, $Mail, $facebookUserId, TIPPSPIEL_USERGROUP);
	  

	  //Tipptabellen-Cache leeren, sonst wird der neue User in der Tabelle nicht angezeigt (und könnte sich darüber wundern)
	  $TippTabellenCache = new CTippTabellenCacheDB();
	  $TippTabellenCache->LeereCache(0);
	  
	  $TippSpielUI->OutputHeader("Facebook-Benutzer für's Tippspiel anmelden");
       $TippSpielUI->OutputMessageBox('Anmeldung erfolgreich!', 'Die Anmeldung für Benutzer '.$Name.' als Facebook-User wurde erfolgreich durchgeführt!
         Du kannst nun <a href="tippspiel.php5">am Tippspiel teilnehmen!</a>');
    }
    catch(BenutzerMsgException $e)
    {
      $TippSpielUI->OutputMessageBox('Anmeldung zum Tippspiel fehlgeschlagen!',$e->getMessage()."<br /><br /><a href=\"index.php5\">Zur Startseite</a>");
      CLogClass::log("BenutzerMsgException: ".$e->getMessage());
    }
    catch(Exception $e)
    {
      $TippSpielUI->OutputMessageBox('Anmeldung zum Tippspiel fehlgeschlagen!',"Die Anmeldung konnte nicht durchgeführt werden, bitte Administrator kontaktieren!<br /><br /><a href=\"index.php5\">Zur Startseite</a>");

      CLogClass::log("Exception: ".$e->getMessage(), LOG_TYPE_ERROR);
    }
  }

  //ein neuer Benutzer möchte sich anmelden:
  if ($strAktion == 'RegisterNewUser')
  {
    $TippSpielUI->OutputHeader("Neuen Benutzer für's Tippspiel anmelden");

    $BenutzerVerw = new CBenutzerVerw();

    try
    {
      $Name = $_POST["Name"];
      $Mail = $_POST["Mail"];
      $Pwd1 = $_POST["Pwd1"];
      $Pwd2 = $_POST["Pwd2"];
      if ($Pwd1 != $Pwd2) throw new BenutzerMsgException("Die Passwortwiederholung stimmt nicht mit dem Passwort �berein!");
      if ($Mail == "") throw new BenutzerMsgException("Es wurde keine E-Mail-Adresse eingegeben!");
      $BenutzerVerw->RegisterNewUser($Name, $Mail, $Pwd1, TIPPSPIEL_USERGROUP);
      $TippSpielUI->OutputMessageBox("Anmeldevorgang erfolgreich gestartet!",
             "Hallo $Name,<br />
             vielen Dank für die Eingabe deiner Daten!<br />
             Bevor du mit dem Tippspiel loslegen kannst, muss noch deine E-Mail-Adresse überprüft werden.
             Dazu wirst du in Kürze eine E-Mail erhalten, in der du einen Link findest, mit dem du dein Benutzerkonto
             freischalten kannst!<br />
             <br />
             <a href=\"index.php5\">Zur Startseite</a>");
    }
    catch(BenutzerMsgException $e)
    {
      $TippSpielUI->OutputMessageBox('Anmeldung zum Tippspiel fehlgeschlagen!',$e->getMessage()."<br /><br /><a href=\"index.php5\">Zur Startseite</a>");
      CLogClass::log("BenutzerMsgException: ".$e->getMessage());
    }
    catch(Exception $e)
    {
      $TippSpielUI->OutputMessageBox('Anmeldung zum Tippspiel fehlgeschlagen!',"Die Anmeldung konnte nicht durchgeführt werden, bitte Administrator kontaktieren!<br /><br /><a href=\"index.php5\">Zur Startseite</a>");

      CLogClass::log("Exception: ".$e->getMessage(), LOG_TYPE_ERROR);
    }
  }




  $TippSpielUI->OutputFooter();

?>