<?php

/********************

TippDB-Klasse
==========================

Liest die Tipps aus der DB



Philipp Crocoll, Jan. 06

*********************/

  require_once( dirname(__FILE__).'/../tippconfig.php5' );
  require_once(BASECLASSES_PATH."/BaseClasses/base.db.class.php5");
  require_once(BASECLASSES_PATH."/BaseClasses/log.class.php5");
  require_once("Tipp.class.php5");


  class CTippDB extends CDBBase
  {
    public $OrderByClause = "";


    //Key des Rückgabearrays ist die TipID
    public function LiesAlleTipps()
    {
      return $this->_liesTipps("1");
    }

    //Key des Rückgabearrays ist die SpielID (macht schnellere Zuordnung möglich)
    public function LiesBenutzerTipps($strBenutzername)
    {
      $liste = $this->_liesTipps("Tips.UserID = \"".mysql_real_escape_string($strBenutzername)."\"");
      if (count($liste)==0) return;
      $res = array();
      foreach ($liste as $Tipp)
        $res[$Tipp->iSpielNr] = $Tipp;
      return $res;
    }

    //Keys des Rückgabearrays ist die TipID
    public function LiesAbgeschlTipps()  //liefert alle Tipps, bei denen ein Ergebnis vorliegt
    {
      return  $this->_liesTipps("(NOT (Matches.ResTeam1Goals IS NULL OR Matches.ResTeam2Goals IS NULL))");
    }

    public function SpeichereTipp($Tipp)
    {

      //Hat der Benutzer für dieses Spiel schon einen Tipp abgegeben?
      $arTip = $this->_liesTipps("Matches.MatchNr=".addslashes($Tipp->iSpielNr).
                                 " AND Tips.UserID = \"".mysql_real_escape_string($Tipp->BenutzerName)."\"");
      if (count($arTip) == 0)
        $this->_InsertTipp($Tipp);
      else
      {
        if (count($arTip)>1)
        {
          CLogClass::Log("Benutzer $Tipp->BenutzerName hat mehr als einen Tip für ein Spiel!!",LOG_TYPE_CRITICAL_ERROR);
          throw new Exception("Fehler in den Daten aufgetreten!");
        }
        foreach ($arTip as $key=>$value) ;

        $this->_UpdateTipp($Tipp,$key);
      }

    }




    protected function _liesTippsSQL($sql)
    {
      //Initialisiere Eintrags-Liste als leeres Array
      $returnlist = NULL;

      //Lies Einträge aus der Datenbank
      try
      {
	    $time_start = microtime_float();
        if( $result = $this->getConnection()->Execute($sql) )
        {
           $time_start_copy = microtime_float();
           $returnliste = array();
           //Schleife ueber die Ergebnisliste
           while (!$result->EOF)
           {
             $ID = $result->fields['TipID'];
             $returnlist[$ID]= new CTipp($result->fields['UserID'],$result->fields['MatchNr']);
             $returnlist[$ID]->SetzeTipp($result->fields['Team1Goals'],$result->fields['Team2Goals'],$result->fields['TipTime']);

             if ($result->fields['ResTeam1Goals'] != NULL)
             $returnlist[$ID]->SetzeErgebnis($result->fields['ResTeam1Goals'],$result->fields['ResTeam2Goals']);

             $result->MoveNext();
           }
		   
          $time_end = microtime_float();
          $time_copy = $time_end - $time_start_copy;
          $time = $time_end - $time_start;
                      $strMsg = "SQL Erfolgreich ausgeführt (in $time / $time_copy)
                           SQL: $sql";
           CLogClass::Log($strMsg,LOG_TYPE_DEBUG_MESSAGE);

        }
        else //ein Fehler ist aufgetreten
        {
          $strError = "Fehler beim Ausführen eines SQL:
                           SQL: $sql
                           ADODB-Meldung: ".$this->getConnection()->ErrorMsg();
           throw new Exception ($strError);

        };
      }
      catch( Exception $e )
      {
        throw new Exception ($e->getMessage());
      }
      return $returnlist;
    }

    protected function _liesTipps($WhereClause)
    {
      $strSQL = "SELECT * FROM ".TIPP_TABELLE." Tips
                   LEFT OUTER JOIN ".MATCH_TABELLE." Matches ON Tips.MatchNr=Matches.MatchNr
                   WHERE ".$WhereClause;
      if ($this->OrderByClause != "")
       $strSQL = $strSQL." ORDER BY ".$this->OrderByClause;
      return $this->_liesTippsSQL($strSQL);
    }

    protected function _InsertTipp($Tipp)
    {
      $sql = "INSERT INTO ".TIPP_TABELLE."
       (MatchNr, UserID, Team1Goals, Team2Goals, TipTime)
                 VALUES (".addslashes($Tipp->iSpielNr).",\"".mysql_real_escape_string($Tipp->BenutzerName)."\", ".
                 addslashes($Tipp->Team1Tore).", ".addslashes($Tipp->Team2Tore).",
                  \"".date("Y-m-d H:i:s",$Tipp->TippDateTime)."\")";
      return self::_execInsertSQL($sql);
    }

    protected function _UpdateTipp($Tipp, $TippID)
    {
      $sql = "UPDATE ".TIPP_TABELLE."
       SET
        Team1Goals=".addslashes($Tipp->Team1Tore).",
        Team2Goals=".addslashes($Tipp->Team2Tore).",
        TipTime = \"".date("Y-m-d H:i:s",$Tipp->TippDateTime)."\"
       WHERE TipID = ".addslashes($TippID);
      return self::_execSQLNoReturn($sql);

    }



  }
?>