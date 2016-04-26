<?php

  class CTippVerw
  {
    protected static $m_TippDB = NULL;
    //Mehrere Tipps speichern:
    //Liefert ein Array mit den Fehlermeldungen zum jeweiligen Tipp zurück.
    public static function SpeichereTipps($Tipps)
    {
      if (self::$m_TippDB == NULL)
        self::$m_TippDB = new CTippDB();
 	  $MatchDB = new CMatchDB();
	  
      $Error = array();
      foreach ($Tipps as $key=>$Tipp)
      {

		if (!$MatchDB->LadeMatch($Tipp->iSpielNr)->isTippbar)
		{
			$Error[$key] = "Spiel kann nicht mehr getippt werden.";
		}
		else
		{
			
			//Zuerst prüfen, ob die Eingaben gültig sind :
			//Kommazahlen sind auch "numeric", aber dann gibts hinten eine Exception!
			$Valid = is_numeric($Tipp->Team1Tore) && is_numeric($Tipp->Team2Tore)
					 && ($Tipp->Team1Tore>=0) && ($Tipp->Team2Tore>=0);
			//Falls nicht: Fehler
			if (!$Valid)
			{
			  $Error[$key] = "Ungültige Eingabe";
			}
			else
			{
			  //ansonsten speichern:
			  try
			  {

				self::$m_TippDB->SpeichereTipp($Tipp);
			  }
			  catch (BenutzerMsgException $e)
			  {
				$Error[$key] = $e->getMessage();
			  }
			  catch (Exception $e)
			  {
				$Error[$key] = "Fehler beim Speichern in der Datenbank";
			  }
			}
        }
      }
      return $Error;
    }
  }

?>