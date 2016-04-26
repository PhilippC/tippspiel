<?php

require_once( dirname(__FILE__).'/../tippconfig.php5' );
require_once(BASECLASSES_PATH."/BaseClasses/base.db.class.php5");
require_once(BASECLASSES_PATH."/BaseClasses/log.class.php5");
require_once("KolumneEintrag.class.php5");


class CKolumneEintragDB extends CDBBase
{
    protected $OrderByClause = "ID Desc";

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
             $Datum = $result->fields['Datum'];
             $Titel = stripslashes($result->fields['Titel']);
             $Text = stripslashes($result->fields['Text']);
             $returnlist[$counter]= new CKolumneEintrag($ID, $Datum, $Titel, $Text);
             $result->MoveNext();
           }
           $strMsg = "SQL Erfolgreich ausgeführt.
                           SQL: $sql";
           CLogClass::Log($strMsg,LOG_TYPE_DEBUG_MESSAGE);

        }
        else //ein Fehler ist aufgetreten
        {
           $stError = "Fehler beim Ausführen eines SQL:
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
      $strSQL = "SELECT * FROM ".KOLUMNE_TABELLE." 
                   WHERE ".$WhereClause;
      if ($this->OrderByClause != "")
       $strSQL = $strSQL." ORDER BY ".$this->OrderByClause;
      return $this->_liesSQL($strSQL);
    }

    protected function _InsertEintrag($Eintrag)
    {
      $sql = "INSERT INTO ".KOLUMNE_TABELLE."
       (Datum, Titel, Text)
                 VALUES (\"".addslashes($Eintrag->Datum)."\",\"".addslashes($Eintrag->Titel)."\", \"".
                 addslashes($Eintrag->Text)."\")";
      return self::_execInsertSQL($sql);
    }

    public function LiesAlle()
    {
      return $this->_liesWhere("1");
    }


    public function SpeichereEintrag($Eintrag)
    {

      //Eine Updatefunktion ist nicht vorgesehen:
      $this->_InsertEintrag($Eintrag);

    }

}

?>