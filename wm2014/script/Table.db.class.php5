<?php

require_once( dirname(__FILE__).'/../tippconfig.php5' );
require_once(BASECLASSES_PATH."/BaseClasses/base.db.class.php5");
require_once(BASECLASSES_PATH."/BaseClasses/log.class.php5");


class CTableDB extends CDBBase
{
    public $OrderByClause = "";

    protected function _liesSQL($sql)
    {
      //Initialisiere Eintrags-Liste als leeres Array
      $returnlist = NULL;

      //Lies Einträge aus der Datenbank
      try
      {
        if( $result = $this->getConnection()->Execute($sql) )
        {
           if ($result->RecordCount()>0)
             $returnlist = array();
           //Schleife ueber die Ergebnisliste
           $counter=0;
           while (!$result->EOF)
           {
             $counter++;
             $returnlist[$counter] = array(
               "GroupNr" => $result->fields['GroupNr'],
               "NameLong" => $result->fields['NameLong'],
               "NameShort" => $result->fields['NameShort'],
               "Matches" => $result->fields['Matches'],
               "Points" => $result->fields['Points'],
               "Goals" => $result->fields['Goals'],
               "GoalsAgainst" => $result->fields['GoalsAgainst']);
             $result->MoveNext();
           }
           $strMsg = "SQL Erfolgreich ausgeführt.
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

    public function liesAlle()
    {
      $strSQL = "SELECT GroupNr, NameLong, NameShort, COUNT(Points) AS Matches, SUM(Points) AS Points, SUM(Goals) AS Goals, SUM(GoalsAgainst) AS GoalsAgainst FROM 
((SELECT Team1Short AS Team, 3 AS Points, ResTeam1Goals AS Goals, ResTeam2Goals AS GoalsAgainst FROM ".MATCH_TABELLE." WHERE MatchType=32 AND ResTeam1Goals>ResTeam2Goals)
UNION ALL
(SELECT Team1Short, 1, ResTeam1Goals, ResTeam2Goals FROM ".MATCH_TABELLE." WHERE MatchType=32 AND ResTeam1Goals=ResTeam2Goals)
UNION ALL
(SELECT Team1Short, 0, ResTeam1Goals, ResTeam2Goals FROM ".MATCH_TABELLE." WHERE MatchType=32 AND ResTeam1Goals<ResTeam2Goals)
UNION ALL
(SELECT Team2Short, 3, ResTeam2Goals, ResTeam1Goals FROM ".MATCH_TABELLE." WHERE MatchType=32 AND ResTeam1Goals<ResTeam2Goals)
UNION ALL
(SELECT Team2Short, 1, ResTeam2Goals, ResTeam1Goals FROM ".MATCH_TABELLE." WHERE MatchType=32 AND ResTeam1Goals=ResTeam2Goals)
UNION ALL
(SELECT Team2Short, 0, ResTeam2Goals, ResTeam1Goals FROM ".MATCH_TABELLE." WHERE MatchType=32 AND ResTeam1Goals>ResTeam2Goals)) AS Query1
RIGHT JOIN ".TEAM_TABELLE." ON Team=NameShort
GROUP BY NameLong ORDER BY GroupNr, Points DESC, Goals-GoalsAgainst DESC;";
      return $this->_liesSQL($strSQL);
    }

}

?>