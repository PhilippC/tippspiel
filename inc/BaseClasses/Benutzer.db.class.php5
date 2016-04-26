<?php

/********************

BenutzerDB-Klasse
==========================

Liest die Benutzer aus der DB


Philipp Crocoll, Jan. 06

*********************/

  require_once("baseconfig.php5" );
  require_once("base.db.class.php5");
  require_once("log.class.php5");
  require_once("Benutzer.class.php5");


  class CBenutzerDB extends CDBBase
  {
    public $OrderByClause = "";

    protected function _liesBenutzerSQL($sql)
    {
      //Initialisiere Eintrags-Liste als leeres Array
      $returnlist = array();
      //Lies Einträge aus der Datenbank
      try
      {
	    $time_start = microtime_float();
        if( $result = $this->getConnection()->Execute($sql) )
        {
           if ($result->RecordCount()>0)
           {
             //Schleife ueber die Ergebnisliste
             while (!$result->EOF)
             {
               if (!isset($returnlist[$result->fields['BenutzerID']]))
               {
                 $returnlist[$result->fields['BenutzerID']] = new CBenutzer($result->fields['BenutzerID'],
                 							   $result->fields['Name'],
                                                                            $result->fields['Mail'],
                                                                            $result->fields['Pwd']);

               }
               $result->MoveNext();
             }
           }
		   $time_end = microtime_float();
		   $time = $time_end - $time_start;
                      $strMsg = "SQL erfolgreich ausgeführt (in $time):
                           SQL: $sql
                           ADODB-Meldung: ".$this->getConnection()->ErrorMsg();
           CLogClass::log($strMsg);

        }
        else //ein Fehler ist aufgetreten
        {
           $strError = "Fehler beim Lesen der Benutzer (Execute).
                           SQL: $sql
                           ADODB-Meldung: ".$this->getConnection()->ErrorMsg();
                           
           //wird ein paar Zeilen später auf jeden Fall geloggt!
           throw new Exception ($strError);
        };
      }
      catch( Exception $e )
      {
        CLogClass::log  ($e->getMessage(), LOG_TYPE_ERROR);
        throw new Exception ($e->getMessage());
      }
      return $returnlist;
    }
   protected function _liesGruppenSQL($sql)
    {
      //Initialisiere Eintrags-Liste als leeres Array
      $returnlist = array();


      //Lies Einträge aus der Datenbank
      try
      {
        if( $result = $this->getConnection()->Execute($sql) )
        {
           if ($result->RecordCount()>0)
            $returnlist = array();
           //Schleife ueber die Ergebnisliste
           while (!$result->EOF)
           {
               $returnlist[$result->fields['Name']] = $result->fields['Titel'];

               $result->MoveNext();
           }
        }
        else //ein Fehler ist aufgetreten
        {
           $strError = "Fehler beim Lesen der Gruppen (Execute).
                           SQL: $sql
                           ADODB-Meldung: ".$this->getConnection()->ErrorMsg();
           throw new Exception ($strError);
        };
      }
      catch( Exception $e )
      {
        CLogClass::log  ($e->getMessage(), LOG_TYPE_ERROR);
        throw new Exception ($e->getMessage());
      }
      return $returnlist;
    }


    protected function _liesBenutzer($WhereClause, $FilterNichtBest=false)
    {
      $strSQL = "SELECT Benutzer.*, Gruppe.Name as GruppenName FROM ".USER_TABELLE." Benutzer
                   LEFT OUTER JOIN ".USERGROUP_TABELLE." BenGruppe ON Benutzer.BenutzerID=BenGruppe.BenutzerID
                   LEFT OUTER JOIN ".GROUP_TABELLE." Gruppe ON BenGruppe.GruppenID = Gruppe.GruppenID
                   WHERE ".$WhereClause;
      if ($this->OrderByClause != "")
        $strSQL = $strSQL." ORDER BY ".$this->OrderByClause;
      return $this->_liesBenutzerSQL($strSQL);
    }


    protected function _liesGruppen($WhereClause)
    {
      $strSQL = "SELECT Gruppe.* FROM ".GROUP_TABELLE." Gruppe
                   LEFT OUTER JOIN ".USERGROUP_TABELLE." BenGruppe ON Gruppe.GruppenID= BenGruppe.GruppenID
                   LEFT OUTER JOIN ".USER_TABELLE." Benutzer ON BenGruppe.BenutzerID = Benutzer.BenutzerID
                   WHERE ".$WhereClause;
      if ($this->OrderByClause != "")
        $strSQL = $strSQL." ORDER BY ".$this->OrderByClause;
      return $this->_liesGruppenSQL($strSQL);
    }

    public function LiesBenutzer($Name)
    {
      $Benutzer = $this->_liesBenutzer("Benutzer.Name = \"".mysql_real_escape_string($Name)."\"");
      if (count($Benutzer)!=1)
        throw new Exception("LiesBenutzer($Name) lieferte ".count($Benutzer)." Ergebnisse!!");
      $keys=array_keys($Benutzer);
      return $Benutzer[$keys[0]];
    }

    public function LiesAlleBenutzer()
    {
      return $this->_liesBenutzer("1");
    }

    public function LiesBestBenutzer()
    {
      return $this->_liesBenutzer("not (Gruppe.Name = \"users_pending\")");
    }

    public function LiesGruppenBenutzer($strGruppe)
    {
      return $this->_liesBenutzer("Gruppe.Name = \"$strGruppe\"");
    }

    public function LiesGruppen()
    {
      return $this->_liesGruppen("1");
    }

    public function LiesBenutzerGruppen($UserName)
    {
      return $this->_liesGruppen("Benutzer.Name=\"".mysql_real_escape_string($UserName)."\"");

    }

    public function LiesBenutzerID($UserName)
    {
      $strSQL="SELECT BenutzerID FROM ".USER_TABELLE." WHERE Name = \"".mysql_real_escape_string($UserName)."\"";
      return $this->_execSingleValueSQL($strSQL,"BenutzerID");
    }

    public function LiesGruppenID($GroupName)
    {
      $strSQL="SELECT GruppenID FROM ".GROUP_TABELLE." WHERE Name = \"".$GroupName."\"";
      return $this->_execSingleValueSQL($strSQL,"GruppenID");
    }

    public function FuegeBenutzerZuGruppeHinzu($UserID,$GroupID)
    {
      if (($UserID < 0) || ($GroupID<0))
      {
        $strMsg = "Fehlerhafte Parameter an FuegeBenutzerZuGruppeHinzu: UserID=$UserID, GroupID=$GroupID";
        CLogClass::log($strMsg);
        throw new Exception($strMsg);
      }
      $strSQL="INSERT INTO ".USERGROUP_TABELLE."  ( BenutzerID , GruppenID ) VALUES ( $UserID, $GroupID)";
      return $this->_execInsertSQL($strSQL);
    }
    public function ErzeugeNeuenBenutzer($Name, $Mail, $Pwd, $ConfirmString, $GroupAfterConfirm=-1)
    {
	  $strMail = "\"".mysql_real_escape_string($Mail)."\"";
	  if ($Mail == null)
	    $strMail = "null";
      $strSQL="INSERT INTO ".USER_TABELLE."
                 (Name, Mail, Pwd, ConfirmString, GroupAfterConfirm)
                 VALUES (\"".mysql_real_escape_string($Name)."\",$strMail, \"$Pwd\", \"$ConfirmString\", $GroupAfterConfirm)";
      return $this->_execInsertSQL($strSQL);
    }
	
	public function SetFacebookUserId($Name, $FacebookUserId)
    {
		$strSQL="UPDATE ".USER_TABELLE." SET FacebookUserID=\"".$FacebookUserId."\" WHERE Name like \"".mysql_real_escape_string($Name)."\""; 
		return $this->_execSQLNoReturn($strSQL);
	}

      //Sucht nach dem übergebenen Confirmstring und liefert, falls gefunden, true
     //zurück. Falls nicht gefunden, wird eine Exception geworfen.
    public function FindeConfirmString($ConfirmString, &$UserID, &$GroupID)
    {
      $SQL="SELECT GroupAfterConfirm, BenutzerID FROM ".USER_TABELLE." WHERE ConfirmString = \"".mysql_real_escape_string($ConfirmString)."\"";
      if( $result = $this->getConnection()->Execute($SQL) )
      {
               $strMsg = "SQL Erfolgreich ausgeführt.
                           SQL: $SQL";
               CLogClass::Log($strMsg,LOG_TYPE_DEBUG_MESSAGE);
               if ($result->RecordCount()!=1) throw new NoSingleValueException("SingleRecordSQL lieferte ".$result->RecordCount()." Ergebnisse!");
               $GroupID = $result->fields["GroupAfterConfirm"];
               $UserID = $result->fields["BenutzerID"];
               return true;
      }
      else //ein Fehler ist aufgetreten
      {
               $strMsg = "Fehler beim Ausführen eines SQL:
                           SQL: $SQL";
             CLogClass::log  ($strMsg, LOG_TYPE_ERROR);
             throw new Exception ($strMsg);
          };

      return false;
    }
	
	public function FindePwdResetKey($PwdResetKey)
    {
      $SQL="SELECT BenutzerID FROM ".USER_TABELLE." WHERE PwdResetKey = \"".$PwdResetKey."\"";
      if( $result = $this->getConnection()->Execute($SQL) )
      {
               $strMsg = "SQL Erfolgreich ausgeführt.
                           SQL: $SQL";
               CLogClass::Log($strMsg,LOG_TYPE_DEBUG_MESSAGE);
               if ($result->RecordCount()!=1) throw new NoSingleValueException("SingleRecordSQL lieferte ".$result->RecordCount()." Ergebnisse!");
               return true;
      }
      else //ein Fehler ist aufgetreten
      {
               $strMsg = "Fehler beim Ausführen eines SQL:
                           SQL: $SQL";
             CLogClass::log  ($strMsg, LOG_TYPE_ERROR);
             throw new Exception ($strMsg);
          };

      return false;
    }
	
	public function AenderePwd($PwdResetKey,$NewPwd)
    {
      $SQL="UPDATE ".USER_TABELLE." Set Pwd = \"$NewPwd\" WHERE PwdResetKey = \"".$PwdResetKey."\"";
	  return $this->_execSQLNoReturn($SQL);
	}


    public function LoescheConfirmString($ConfirmString)
    {
      $strSQL="UPDATE ".USER_TABELLE." SET ConfirmString=\"\" WHERE ConfirmString = \"".mysql_real_escape_string($ConfirmString)."\"";
      return $this->_execSQLNoReturn($strSQL);
    }

    public function LoescheBenutzerAusGruppe($UserID,$GruppeID)
    {
      $strSQL="DELETE FROM ".USERGROUP_TABELLE." WHERE BenutzerID = ".$UserID." AND GruppenID = ".$GruppeID;
      return $this->_execSQLNoReturn($strSQL);
    }
	
	//Wird nur verwendet wenn MD5-Passwörter gespeichert sind: Um ein Passwort zurückzusetzen bekommt der User eine Mail mit 
	//einem Passwort-Reset-Key. Wenn er diesen hat, kann er ein neues Passwort eingeben.
	public function ErzeugePwdResetKey($UserName)
    {
		$key = md5 (uniqid(rand()));
		$strSQL="UPDATE ".USER_TABELLE." SET PwdResetKey=\"$key\" WHERE Name = \"".mysql_real_escape_string($UserName)."\"";
		$this->_execSQLNoReturn($strSQL);
		return $key;
	}


    public function TrageBenutzerInPhpBBEin($UserID, $UserName, $Password, $bbUserTable="phpbb_users", $bbGroupTable="phpbb_groups",$bbUserGroupTable="phpbb_user_group")
    {
      //Trägt einen Benutzer in der Tabelle phpbb_users ein,
      //erstellt in phpbb_groups eine Gruppe für ihn und
      //trägt ihn in diese Gruppe ein

      //s. http://www.phpbb.de/doku/kb/artikel.php?artikel=integration

      //BenutzerID ermitteln: diese ist die Anzahl der Datensätze in der phpbb

      //Benutzer eintragen:
      $bbPassword=md5($Password);
      $user_active=1;
      $userregdate = time();
      $user_level = 0;

      $strSQL = "INSERT INTO $bbUserTable (user_id, user_active, username, user_password, user_regdate, user_level)
                  VALUES($UserID,$user_active,\"".mysql_real_escape_string($UserName)."\",\"".$bbPassword."\",  $userregdate,$user_level)";
      $this->_execInsertSQL($strSQL);

      //Benutzergruppe erstellen:
      $group_type=1;
      $group_name="";
      $group_description = 'Personal User';
      $group_moderator = 0;
      $group_single_user = 1;
      $strSQL = "INSERT INTO $bbGroupTable (group_type, group_name, group_description, group_moderator, group_single_user)
      		VALUES ($group_type, \"".addslashes($group_name)."\", \"".addslashes($group_description)."\", $group_moderator,
                 $group_single_user)";

      $group_id = $this->_execInsertSQL($strSQL);

      //Benutzer der Gruppe hinzufügen:
      $user_pending = 0;

      $strSQL = "INSERT INTO $bbUserGroupTable (group_id, user_id, user_pending)
      		VALUES ($group_id, $UserID, $user_pending)";


      $this->_execInsertSQL($strSQL);

      return true;


    }




  }
?>