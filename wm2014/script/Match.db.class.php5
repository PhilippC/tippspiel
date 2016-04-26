<?php

require_once( dirname(__FILE__).'/../tippconfig.php5' );
require_once(BASECLASSES_PATH."/BaseClasses/base.db.class.php5");
require_once(BASECLASSES_PATH."/BaseClasses/log.class.php5");
require_once("Match.class.php5");


class CMatchDB extends CDBBase
{
    public $OrderByClause = "";

    protected function _liesSQL($sql)
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
           if ($result->RecordCount()>0)
             $returnliste = array();
           //Schleife ueber die Ergebnisliste
           $counter=0;
           while (!$result->EOF)
           {
             $counter++;
             $MatchNr = $result->fields['MatchNr'];
             $Team1Type = $result->fields['Team1Type'];
             $Team2Type = $result->fields['Team2Type'];
             $Team1Short = $result->fields['Team1Short'];
             $Team2Short = $result->fields['Team2Short'];
             $MatchDate = $result->fields['MatchDate'];
             $StartTime = $result->fields['StartTime'];
             $MatchType = $result->fields['MatchType'];
             $ResTeam1Goals = $result->fields['ResTeam1Goals'];
             $ResTeam2Goals = $result->fields['ResTeam2Goals'];
             $MatchSortId = $result->fields['MatchSortId'];
             $Group = $result->fields['GroupNr'];

             $returnlist[$counter]= new CMatch($MatchNr, $Team1Type, $Team2Type, $Team1Short, $Team2Short, $MatchDate, $StartTime, $MatchType, $ResTeam1Goals, $ResTeam2Goals, $MatchSortId, $Group);
             $result->MoveNext();
           }
		   $time_end = microtime_float();
           $time = $time_end - $time_start;
           $time_copy = $time_end - $time_start_copy;
           $strMsg = "SQL Erfolgreich ausgeführt (in $time / $time_copy).
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

    protected function _liesWhere($WhereClause)
    {
      $strSQL = "SELECT M.*, T1.GroupNr As GroupNr FROM ".MATCH_TABELLE." M
                   LEFT OUTER JOIN ".TEAM_TABELLE." T1 ON M.Team1Short = T1.NameShort
                   WHERE ".$WhereClause;
      if ($this->OrderByClause != "")
       $strSQL = $strSQL." ORDER BY ".$this->OrderByClause;
      return $this->_liesSQL($strSQL);
    }
	
	public function LiesWhere($WhereClause)
    {
		return $this->_liesWhere($WhereClause);
    }

    public function LiesAlle()
    {
      return $this->_liesWhere("1");
    }
	
	public function LadeMatch($matchNr)
    {
      $sqlMatchNr = addslashes((string)$matchNr);
		$matches = self::_liesWhere("MatchNr = $matchNr");
		return $matches[1];
    }

    public function ErgaenzeMatchUmErgebnis($Match)
    {
       $T1=addslashes((string)$Match->ResTeam1Goals);
       $T2=addslashes((string)$Match->ResTeam2Goals);
       if (!isset($T1)) $T1 = "NULL";
       if (!isset($T2)) $T2 = "NULL";

       $sql = "UPDATE ".MATCH_TABELLE."
       SET
        ResTeam1Goals = $T1,
        ResTeam2Goals = $T2
       WHERE MatchNr = $Match->MatchNr";
      return self::_execSQLNoReturn($sql);
    }


    public function ErgaenzeMatchUmTeams($Match)
    {
       $sql = "UPDATE ".MATCH_TABELLE."
       SET
        Team1Short=\"".addslashes($Match->Team1Short)."\",
        Team2Short = \"".addslashes($Match->Team2Short)."\"
       WHERE MatchNr = $Match->MatchNr";
      return self::_execSQLNoReturn($sql);
    }

	public function ZaehleMatchesVorLetztemSpieltag()
	{
		return $this->_execSingleValueSQL("SELECT count(MatchNr) as NumMatches FROM ".MATCH_TABELLE." WHERE matchdate < (Select max(matchdate) from ".MATCH_TABELLE." where matchdate <= now())", "NumMatches");
	}
}

?>