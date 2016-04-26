<?php

require_once( dirname(__FILE__).'/../tippconfig.php5' );
require_once(BASECLASSES_PATH."/BaseClasses/base.db.class.php5");
require_once(BASECLASSES_PATH."/BaseClasses/log.class.php5");

require_once("Team.class.php5");

class CTeamDB extends CDBBase
{
    protected $OrderByClause = "";

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
             $returnliste = array();
           //Schleife ueber die Ergebnisliste
           $counter=0;
           while (!$result->EOF)
           {
             $counter++;
             $ID = $result->fields['ID'];
             $NameLong = $result->fields['NameLong'];
             $NameShort = $result->fields['NameShort'];
             $FlagURL = $result->fields['FlagURL'];
             $GroupNr = $result->fields['GroupNr'];
             $returnlist[$NameShort]= new CTeam($ID, $NameLong, $NameShort, $FlagURL, $GroupNr);
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

    protected function _liesWhere($WhereClause)
    {
      $strSQL = "SELECT * FROM ".TEAM_TABELLE."  WHERE ".$WhereClause;
      
      if ($this->OrderByClause != "")
       $strSQL = $strSQL." ORDER BY ".$this->OrderByClause;
      return $this->_liesSQL($strSQL);
    }

    public function LiesAlle()
    {
      return $this->_liesWhere("1");
    }

    public function LiesTeam($TeamShort)
    {
      $TeamShort = addslashes($TeamShort);
      $Teams = $this->_liesWhere("NameShort = \"$TeamShort\"");
      if (count($Teams) != 1)
        throw new Exception ("LiesTeam($TeamShort) lieferte ".count($Teams)." Ergebnisse!");
      return $Teams[$TeamShort];
    }

}
?>