<?php

/********************

TippspielDB-Klasse
==========================

Liest Infos zum Tippspiel aus der DB



Philipp Crocoll, Jan. 06

*********************/

  require_once(BASECLASSES_PATH."/BaseClasses/base.db.class.php5");
  require_once(BASECLASSES_PATH."/BaseClasses/log.class.php5");
  require_once(BASECLASSES_PATH."/BaseClasses/BenutzerVerw.class.php5");
  require_once( dirname(__FILE__).'/../tippconfig.php5' );


  class CTippspielDB extends CDBBase
  {
     protected function _execCountSQL($SQL, $CountField)
     {
        try
        {
          if( $result = $this->getConnection()->Execute($SQL) )
          {
                     $strMsg = "SQL Erfolgreich ausgeführt.
                           SQL: $SQL";
           CLogClass::Log($strMsg,LOG_TYPE_DEBUG_MESSAGE);

               return $result->fields[$CountField];
          }
          else //ein Fehler ist aufgetreten
          {
             CLogClass::log  ("Fehler beim Lesen der Tipps", LOG_TYPE_ERROR);
             throw new Exception ("Fehler beim Lesen der Tipps");
          };
        }
        catch( Exception $e )
        {
          throw new Exception ($e->getMessage());
        }
        return -1;

     }


     public function LiesAnzahlSpieleGesamt()
     {
       $strSQL = "SELECT Count(MatchNr) as AnzSpiele FROM ".MATCH_TABELLE;
       return $this->_execCountSQL($strSQL,"AnzSpiele");

     }

     public function LiesAnzahlSpieleAbgeschlossen()
     {

       $strSQL = "SELECT Count(MatchNr) as AnzSpiele
                 FROM ".MATCH_TABELLE."
                 WHERE NOT (ResTeam1Goals is NULL OR ResTeam2Goals is NULL)";
       return $this->_execCountSQL($strSQL,"AnzSpiele");

     }
     
     public function LiesNaechsteMatchSortId($matchSortId)
     {
     	return $this->_execSingleValueSQL("SELECT min(MatchSortId) AS MinMatchSortId FROM ".MATCH_TABELLE." WHERE MatchSortId > $matchSortId","MinMatchSortId");
     }

     public function LiesAnzahlSpieler()
     {
       $BenutzerVerw = new CBenutzerVerw();
       return count($BenutzerVerw->GetUserList(TIPPSPIEL_USERGROUP));

     }



  }
?>