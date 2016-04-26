<?php
 require_once( dirname(__FILE__).'/../tippconfig.php5' );
  require_once(BASECLASSES_PATH."/BaseClasses/base.db.class.php5");
  require_once(BASECLASSES_PATH."/BaseClasses/log.class.php5");
  require_once("TippTabellenEintrag.class.php5");


  class CTippTabellenCacheDB extends CDBBase
  {
  
	public function LadeTabelleAusCache($maxSortId)
	{
	  try
      {
      	$TabelleAusCache = array();
      	$sql = "SELECT * from ".TIPPTABELLE_CACHE." WHERE MaxMatchSortId = $maxSortId ORDER BY Platz, BenutzerName";
	    $time_start = microtime_float();
        if( $result = $this->getConnection()->Execute($sql) )
        {
           $time_start_copy = microtime_float();
           $returnliste = array();
           //Schleife ueber die Ergebnisliste
           while (!$result->EOF)
           {
           	 $eintrag = new CTippTabellenEintrag($result->fields['BenutzerName']);
           	 $eintrag->iAnzSpieleTabelle = $result->fields['AnzSpiele'];
           	 $eintrag->iMaxMatchSortId = $result->fields['MaxMatchSortId'];
           	 $eintrag->iPlatz = $result->fields['Platz'];
           	 $eintrag->iAnzSpieleGetippt = $result->fields['AnzSpieleGetippt']; 
           	 $eintrag->iAnzTendenz = $result->fields['AnzTendenz']; 
           	 $eintrag->iAnzDifferenz = $result->fields['AnzDifferenz']; 
           	 $eintrag->iAnzErgebnis = $result->fields['AnzErgebnis'];
           	 $eintrag->iPunkte = $result->fields['Punkte'];
	
           	 $TabelleAusCache[count($TabelleAusCache)] = $eintrag;           	 
           	 
             $result->MoveNext();
           }
		   
          $time_end = microtime_float();
          $time_copy = $time_end - $time_start_copy;
          $time = $time_end - $time_start;
                      $strMsg = "SQL Erfolgreich ausgeführt (in $time / $time_copy)
                           SQL: $sql";
           CLogClass::Log($strMsg,LOG_TYPE_DEBUG_MESSAGE);
           
          if (count($TabelleAusCache) > 0)
          	return $TabelleAusCache;
          else 
            return false;

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
        CLogClass::Log("Fehler beim Laden der Tabelle aus dem Cache: ".$e->getMessage(),LOG_TYPE_ERROR);
        return false;
      }		
	}
	
	public function LeereCache($minMaxMatchSortId)
	{	
		$sql = "DELETE FROM ".TIPPTABELLE_CACHE." WHERE MaxMatchSortId >= $minMaxMatchSortId";
		
	
        self::_execSQLNoReturn($sql);
        return true;
	
	}
	
	public function SpeichereTabelleInCache($Tabelle)
	{	
		try
      {	
		$sql = "INSERT INTO ".TIPPTABELLE_CACHE."
       (BenutzerName,AnzSpiele,MaxMatchSortId,Platz,AnzSpieleGetippt,AnzTendenz,AnzDifferenz,AnzErgebnis,Punkte)
                 VALUES ";
		
		$isFirst = true;
		foreach ($Tabelle as $eintrag)
		{
			if ($isFirst == false)
				$sql = $sql.",";
			$sql = $sql."( \"" .mysql_real_escape_string($eintrag->BenutzerName)."\"," 
					." $eintrag->iAnzSpieleTabelle, $eintrag->iMaxMatchSortId, $eintrag->iPlatz, $eintrag->iAnzSpieleGetippt, " 
					." $eintrag->iAnzTendenz, $eintrag->iAnzDifferenz, $eintrag->iAnzErgebnis, $eintrag->iPunkte)";
			
					
			$isFirst = false; 
		}
		$sql = $sql.";";
		
        self::_execInsertSQL($sql);
        return true;
      }
      catch( Exception $e )
      {
        CLogClass::Log("Fehler beim Laden der Tabelle aus dem Cache: ".$e->getMessage(),LOG_TYPE_ERROR);
        return false;
      }		
	}
  }
  
 ?>