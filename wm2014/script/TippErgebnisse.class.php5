<?php

/********************

TippErgebnisse-Klasse
======================

Philipp Crocoll, Jan. 06

*********************/

require_once("Tipp.db.class.php5");
require_once("Tippspiel.db.class.php5");
require_once("TippTabellenEintrag.class.php5");
require_once("TippTabellenCache.db.class.php5");
require_once("MatchVerw.class.php5");
require_once("TippErgVerglEintrag.class.php5");


require_once( dirname(__FILE__).'/../tippconfig.php5' );
require_once(BASECLASSES_PATH."/BaseClasses/BenutzerVerw.class.php5");

define('FILTER_NONE',0);
define('FILTER_TOP', 1);
define('FILTER_USER',2);

define('ERGEBNIS_ZAHL_SIEG_T1',-1);
define('ERGEBNIS_ZAHL_UNENTSCH',0);
define('ERGEBNIS_ZAHL_SIEG_T2',1);

class CTippErgebnisse
{
	protected $m_BenutzerVerw=NULL;
	protected $m_Tipps = NULL;  //abgegebene Tipps
	//AnzSpiele und AnzSpieleGes werden immer im Set geladen:
	protected $m_AnzSpiele = -1;  //Anzahl der beendeten Spiele
	protected $m_AnzSpieleGes = -1;  //Anzahl der Spiele insgesamt
	protected $m_Benutzerliste = NULL;

	protected function LadeAnzSpiele()
	{
		//Objekt für Tippspiel-DB-Zugriff erzeugen:
		$TippspielDB = new CTippspielDB();
		//AnzSpiele und AnzSpieleGes auslesen:
		$this->m_AnzSpiele = $TippspielDB->LiesAnzahlSpieleAbgeschlossen();
		$this->m_AnzSpieleGes = $TippspielDB->LiesAnzahlSpieleGesamt();
		return true;
	}

	protected function bAnzSpieleGeladen()
	{
		return (($this->m_AnzSpiele >= 0) && ($this->m_AnzSpieleGes >= 0));
	}

	protected static function ErzeugeErgZahl($Team1Tore,$Team2Tore)
	{
		$result = 0;
		if ($Team1Tore>$Team2Tore) $result = ERGEBNIS_ZAHL_SIEG_T1;
		if ($Team1Tore<$Team2Tore) $result = ERGEBNIS_ZAHL_SIEG_T2;
		return $result;
	}


	protected static function BerechnePunkte($iAnzErgebnis, $iAnzDifferenz, $iAnzTendenz)
	{
		return $iAnzErgebnis*PUNKTE_FUER_ERGEBNIS
		+ $iAnzDifferenz*PUNKTE_FUER_DIFFERENZ
		+ $iAnzTendenz*PUNKTE_FUER_TENDENZ;
	}

	protected static function Vergleiche($Eintrag1, $Eintrag2)
	{
		//Wer hat mehr Punkte?
		if ($Eintrag1->iPunkte > $Eintrag2->iPunkte) return 1;
		if ($Eintrag1->iPunkte < $Eintrag2->iPunkte) return -1;

		//Wer hat bei mehr Spielen Punkte bekommen?
		$AnzSpiele1 = $Eintrag1->iAnzErgebnis + $Eintrag1->iAnzDifferenz + $Eintrag1->iAnzTendenz;
		$AnzSpiele2 = $Eintrag2->iAnzErgebnis + $Eintrag2->iAnzDifferenz + $Eintrag2->iAnzTendenz;
		if ($AnzSpiele1 > $AnzSpiele2) return 1;
		if ($AnzSpiele1 < $AnzSpiele2) return -1;

		//Wer hat weniger Spiele getippt?
		if ($Eintrag1->iAnzSpieleGetippt < $Eintrag2->iAnzSpieleGetippt) return 1;
		if ($Eintrag1->iAnzSpieleGetippt > $Eintrag2->iAnzSpieleGetippt) return -1;


		//Gleichwertig:
		return 0;

	}

	protected static function VergleicheFuerSortieren($Eintrag1, $Eintrag2)
	{
		 
		$res = self::Vergleiche($Eintrag1, $Eintrag2);
		if ($res == 0)
		{
			$str1 =  strtoupper($Eintrag1->BenutzerName);
			$str2 =  strtoupper($Eintrag2->BenutzerName);
			return strcmp($str1,$str2);
		}
		return -$res;


	}

	protected static function WendeFilterAn(&$Tabelle, $FilterType, $FilterData)
	{
		if ($FilterType == FILTER_NONE)
		{
			//nichts filtern:
			return;
		}
		if ($FilterType == FILTER_TOP)
		{
			//ab dem FilterData+1ten Element alles löschen:
			$count = count($Tabelle);
			for ($i = $FilterData;$i<$count;$i++)
			{
				unset($Tabelle[$i]);
			}
		}
		if ($FilterType == FILTER_USER)
		{
			//AnzPlaetze "um den Platz" des Benutzers BenutzerName filtern.

			//Falls AnzPlaetze mehr als Daten, braucht nicht gefiltert werden:
			if ($FilterData["AnzPlaetze"] >= count($Tabelle)) return;

			//Platz des Benutzers finden:
			$counter = 0;
			foreach ($Tabelle as $Eintrag)
			{
				if ($FilterData["BenutzerName"] == $Eintrag->BenutzerName) break;
				$counter++;
			}
			if ($counter == count($Tabelle))
			{
				//Wenn der Benutzer noch keinen Tipp abgegeben hat wird keine Exception
				//erzeugt sondern einfach der Anfang der Tabelle angezeigt
				/*        $msg = "Benutzer für FILTER_USER nicht gefunden!";
				 CLogClass::Log($msg, LOG_TYPE_ERROR);
				 throw new Exception($msg);*/
				$BenutzerPlatz = 1;
			}
			else
			$BenutzerPlatz = $counter;

			//Min- und Max-Werte des Filters:
			$MinFilter = $BenutzerPlatz-($FilterData["AnzPlaetze"]-1)/2;
			$MaxFilter = $BenutzerPlatz+($FilterData["AnzPlaetze"]-1)/2;

			//Prüfen, ob Min/Max in [0..count($Tabelle)-1] liegen.
			if ($MinFilter<0)
			{
				//Min/Max nach hinten verschieben:
				$MaxFilter = $MaxFilter-$MinFilter;
				$MinFilter = 0;
			}
			if ($MaxFilter>=count($Tabelle))
			{
				//Min/Max nach vorne verschieben:
				$MinFilter = $MinFilter-$MaxFilter+count($Tabelle)-1;
				$MaxFilter = count($Tabelle)-1;
			}

			$count = count($Tabelle);
			//Elemente rauswerfen:
			for ($i=0;$i<$MinFilter;$i++)
			unset($Tabelle[$i]);
			for ($i=$MaxFilter+1;$i<$count;$i++)
			unset($Tabelle[$i]);

			//Array neu indizieren:
			$Tabelle = array_values($Tabelle);
		}

	}

	protected function FuegeLeereEintraegeHinzu(&$Tabelle, &$BenutzerInTabelle)
	{
		if ($this->m_BenutzerVerw == NULL)
		$this->m_BenutzerVerw = new CBenutzerVerw();

		$time_start_2 = microtime_float();

		//Benutzerliste laden:
		if ($this->m_Benutzerliste == NULL)
		$this->m_Benutzerliste = $this->m_BenutzerVerw->GetUserList(TIPPSPIEL_USERGROUP);

		$time_end_2 = microtime_float();
		$time_2 = $time_end_2 - $time_start_2;
		CLogClass::log("benutzerliste geholt in $time_2 sek.");
		$time_start_2 = microtime_float();


		//Falls kein Benutzer angemeldet, muss auch nichts hinzugefügt werden:
		if (count($this->m_Benutzerliste)==0) return;

		//Für jeden Benutzer prüfen, ob er in der Tabelle ist:
		foreach ($this->m_Benutzerliste as $Benutzer)
		{
			//if ($this->FindeEintrag($Benutzer->Name, $Tabelle) == NULL)
			if ($BenutzerInTabelle[$Benutzer->Name] != true)
			{
				//Benutzer ist nicht in der Tabelle! -> Eintragen:
				$NeuerEintrag = new CTippTabellenEintrag($Benutzer->Name);
				$BenutzerInTabelle[$Benutzer->Name] = true;
				$NeuerIndex = count($Tabelle);
				while (isset($Tabelle[$NeuerIndex])) $NeuerIndex++;
				$Tabelle[$NeuerIndex] = $NeuerEintrag;
			}
		}

		$time_end_2 = microtime_float();
		$time_2 = $time_end_2 - $time_start_2;
		CLogClass::log("Tabelle ergaenzet in $time_2 sek.");


	}

	public static function FindeEintrag($BenutzerName, $Tabelle)
	{
		foreach ($Tabelle as $Eintrag)
		if ($BenutzerName == $Eintrag->BenutzerName) return $Eintrag;

		return NULL;
	}

	public static function BerechnePunkteFuerTipp($Tipp)
	{
		if (!isset($Tipp)) return 0;
		if (!$Tipp->ErgebnisBekannt)
		return 0;


		$TipTeam1 = $Tipp->Team1Tore;
		$TipTeam2 = $Tipp->Team2Tore;
		$ResTeam1 = $Tipp->Team1ToreErg;
		$ResTeam2 = $Tipp->Team2ToreErg;


		//Ergebnis richtig?
		if (($TipTeam1==$ResTeam1) && ($TipTeam2==$ResTeam2)) return PUNKTE_FUER_ERGEBNIS;
		//Differenz richtig?
		if (($TipTeam1-$TipTeam2 == $ResTeam1-$ResTeam2)) return PUNKTE_FUER_DIFFERENZ;
		//Tendenz richtig?
		if (self::ErzeugeErgZahl($TipTeam1, $TipTeam2) == self::ErzeugeErgZahl($ResTeam1,$ResTeam2)) return PUNKTE_FUER_TENDENZ;
		//Gar nix richtig:
		return 0;
	}


	public function LadeErgebnisse()
	{
		//Falls Ergebnisse schon geladen: raus!
		if ($this->m_Tipps != NULL)
		return true;

		//Objekt für Tipp-DB-Zugriff erzeugen:
		$TippDB = new CTippDB();
		//Die "abgeschlossenen Tipps", also die Tipps, deren Spiele schon fertig sind, holen:
		$TippDB->OrderByClause = "Matches.MatchSortId, Matches.MatchNr, Matches.MatchDate, Matches.StartTime";
		$this->m_Tipps = $TippDB->LiesAbgeschlTipps();


		//Ok!
		return true;
	}

	public function __get($Property)
	{
		switch($Property)
		{
			case "AnzSpiele": return $this->m_AnzSpiele;
			case "AnzSpieleGes": return $this->m_AnzSpieleGes;
		}
	}

	private function quickSort(&$Tabelle,$links,$rechts)
	{
		if ($links < $rechts)
		{
			$teiler = self::teile($Tabelle,$links,$rechts);
			self::quickSort($Tabelle,$links, $teiler-1);
			self::quickSort($Tabelle,$teiler+1, $rechts);
		}
	}

	private function teile(&$Tabelle, $links, $rechts)
	{
		$i = $links;
		$j = $rechts - 1;
		$pivot = $Tabelle[$rechts];
		do
		{
			while ((self::VergleicheFuerSortieren($Tabelle[$i],$pivot) <= 0) && ($i < $rechts))
			{
				$i++;
			}
			while ((self::VergleicheFuerSortieren($Tabelle[$j],$pivot) >= 0) && ($j > $links))
			{
				$j--;
			}
			if ($i < $j)
			{
				$temp = $Tabelle[$j];
				$Tabelle[$j] = $Tabelle[$i];
				$Tabelle[$i] = $temp;
			}
			 
		} while($i<$j);
		 
		if (self::VergleicheFuerSortieren($Tabelle[$i],$pivot) > 0)
		{
			$temp = $Tabelle[$rechts];
			$Tabelle[$rechts] = $Tabelle[$i];
			$Tabelle[$i] = $temp;
		}
		return $i;
		 
	}
	

	public function ErzeugeTippTabelle($maxDesiredMatchSortId=0,$FilterType = FILTER_NONE, $FilterData=0)
	{

		$time_start_withload = microtime_float();
		 
		$MatchVerw = new CMatchVerw();
		$MatchVerw->SetOrderByClause("MatchSortId, MatchNr, MatchDate, StartTime");
		$Matches = $MatchVerw->HoleMatches(MATCH_FILTER_ABGESCHL);

		$maxMatchSortId = 0;
		$this->m_AnzSpiele = 0;
		foreach ($Matches as $Match)
		{
			if (($maxDesiredMatchSortId > 0) && ($Match->MatchSortId > $maxDesiredMatchSortId))
				continue;
				
			$this->m_AnzSpiele++;
			
			$maxMatchSortId = max($maxMatchSortId, $Match->MatchSortId);
			
		}
		$this->m_AnzSpieleGesamt = count($Matches);

		$tabellenCache = new CTippTabellenCacheDB();
		$Tabelle = $tabellenCache->LadeTabelleAusCache($maxMatchSortId);

		if ($Tabelle == false)
		{
			//Tabelle muss berechnet werden. 
			
			if ($this->m_Tipps == NULL)
			{
				if (!$this->LadeErgebnisse()) throw new Exception ("Fehler beim Laden der Tipp-Ergebnisse");
			}

			$Tabelle = array();  //array of TippTabellenEintrag
			$BenutzerInTabelle = array();

			//Über alle Tipps iterieren. Dabei die TippTabellenEinträge erzeugen und
			//die Felder Benutzername, AnzSpiele, AnzTendenz und AnzErgebnis setzen.
			//Mitzählen, wieviele Spiele gewertet wurden. Nun kann es aber sein, dass zu einem Spiel
			//von keinem Spieler ein Tipp abgegeben wurde (zumindest möglich!).
			//Daher noch eine MatchListe laden und diesen Fall abchecken!


			 
			if (count($Matches) > 0)
			{
				//Keys durchnummerieren:
				$Matches = array_values($Matches);
			}
			 
			 
			$time_start = microtime_float();
			$AnzSpieleGewertet = 0;
			$LetztesSpiel = 0;
			$TippCounter=0;
			if (count($this->m_Tipps) > 0)
			foreach($this->m_Tipps as $Tipp)
			{
				if ($Tipp->iSpielNr != $LetztesSpiel)
				{
					//AnzSpieleGewertet inkrementieren, bis der Index des aktuell behandelten Spiels erreicht wird.
					while (isset($Matches[$AnzSpieleGewertet]) && !($Matches[$AnzSpieleGewertet]->MatchNr==$Tipp->iSpielNr))
					{
						//  echo("AnzSpieleGewertet++<br />");
						$AnzSpieleGewertet++;
					}
					$LetztesSpiel = $Tipp->iSpielNr;

					//Wurde die Anzahl der zu wertenden Spiele erreicht?
					if (($AnzSpieleGewertet>=$maxMatchSortId) && ($maxMatchSortId > 0))
					break;          //dann raus.
				}


				//Falls erster Tipp des Benutzers: Anlegen
				if (!isset($Tabelle[$Tipp->BenutzerName]))
				{
					//   echo("Erzeuge Eintrag <br />");
					$AktEintrag = new CTippTabellenEintrag($Tipp->BenutzerName);
					$BenutzerInTabelle[$Tipp->BenutzerName] = true;
				}
				else //Ansonsten aus Tabelle den Eintrag zum Benutzer holen:
				{
					$AktEintrag = $Tabelle[$Tipp->BenutzerName];

				}
				$AktEintrag->iAnzSpieleGetippt++;

				//War das Ergebnis richtig?
				if (($Tipp->Team1Tore==$Tipp->Team1ToreErg) && ($Tipp->Team2Tore==$Tipp->Team2ToreErg))
				$AktEintrag ->iAnzErgebnis++;
				else //Nein, aber vielleicht die Differenz?
				if (($Tipp->Team1Tore - $Tipp->Team2Tore) == ($Tipp->Team1ToreErg-$Tipp->Team2ToreErg))
				$AktEintrag ->iAnzDifferenz++;
				else  //Nein, aber vielleicht die Tendenz?
				if (self::ErzeugeErgZahl($Tipp->Team1Tore,$Tipp->Team2Tore) == self::ErzeugeErgZahl($Tipp->Team1ToreErg,$Tipp->Team2ToreErg))
				$AktEintrag ->iAnzTendenz++;

				$Tabelle[$Tipp->BenutzerName] = $AktEintrag;

			}
			$time_start_2 = microtime_float();
			//Falls für einen Benutzer kein abgeschlossener Tipp besteht, taucht er nicht in der Tabelle
			//auf. Um das zu verhindern, wird folgende Funktion aufgerufen:
			$this->FuegeLeereEintraegeHinzu($Tabelle, $BenutzerInTabelle);
			$time_end_2 = microtime_float();
			$time_2 = $time_end_2 - $time_start_2;

			CLogClass::log(" . Leere hinzugefügt in $time_2 Sekunden.");

			//Jetzt sollte die Tabelle praktisch immer gefüllt sein - es sei denn es ist noch niemand angemeldet:
			if (count($Tabelle)==0) return array();

			$time_start_2 = microtime_float();

			//Jetzt können die Punkte berechnet werden. Ausserdem jetzt die Felder MaxSortId, AnzSpieleTabelle setzen
			foreach ($Tabelle as $TabEintrag)
			{
				$TabEintrag->iPunkte = self::BerechnePunkte($TabEintrag->iAnzErgebnis,
				$TabEintrag->iAnzDifferenz,
				$TabEintrag->iAnzTendenz);
				$TabEintrag->iAnzSpieleTabelle = $this->m_AnzSpiele;
				$TabEintrag->iMaxMatchSortId = $maxMatchSortId;
			}

			$time_end_2 = microtime_float();
			$time_2 = $time_end_2 - $time_start_2;

			CLogClass::log(" . Punkte berechnet in $time_2 Sekunden.");


			$Tabelle = array_values($Tabelle);

			$time_start_2 = microtime_float();
			//Jetzt Rangliste erstellen (sortieren):
			$N = count($Tabelle);
			self::quickSort($Tabelle,0,$N-1);

			//Bubble Sort
			/*for ($i=0; $i<$N-1; $i++)
			 {
			 for ($j=0; $j < $N-1-$i; $j++)
			 {
			 $vergleichsErgebnis =  self::VergleicheFuerSortieren($Tabelle[$j], $Tabelle[$j+1]);
			 if ($vergleichsErgebnis<0)
			 {
			 $temp = $Tabelle[$j];
			 $Tabelle[$j] = $Tabelle[$j+1];
			 $Tabelle[$j+1] = $temp;
			 }
			 }
	   }
	   */
			 
			$time_end_2 = microtime_float();
			$time_2 = $time_end_2 - $time_start_2;

			CLogClass::log(" . Tabelle sortiert in $time_2 Sekunden.");

			$time_start_2 = microtime_float();


			//Jetzt Plätze vergeben:
			$iPlatz = 1;
			$Tabelle[0]->iPlatz = $iPlatz;
			$iGemPlatz = 1;
			for ($i=1;$i<$N;$i++)
			{
				if (self::Vergleiche($Tabelle[$i-1], $Tabelle[$i])>0)
				{
					$iPlatz+=$iGemPlatz;
					$iGemPlatz = 0;
				}
				$iGemPlatz++;
				$Tabelle[$i]->iPlatz = $iPlatz;
			}

			$time_end_2 = microtime_float();
			$time_2 = $time_end_2 - $time_start_2;

			CLogClass::log(" . Plätze vergeben in $time_2 Sekunden.");
			
			$tabellenCache->SpeichereTabelleInCache($Tabelle);
		}
	
		$time_start_2 = microtime_float();

		//Filter anwenden:
		self::WendeFilterAn($Tabelle, $FilterType,$FilterData);
		 
		$time_end_2 = microtime_float();
		$time_2 = $time_end_2 - $time_start_2;

		CLogClass::log(" . Tabelle gefiltert in $time_2 Sekunden.");


		//Fertig.
		 
		$time_end = microtime_float();
		$time = $time_end - $time_start;
		$time_withload = $time_end - $time_start_withload;

		CLogClass::log("TippErgebnisse in $time Sekunden (mit Laden $time_withload Sekunden) erstellt.");

		return $Tabelle;
	}

	public function ErzeugeTippErgVergleich()
	{
		//Benötigte Daten laden:
		$TippDB = new CTippDB();
		$tipps = $TippDB->LiesAlleTipps();	//alle Tipps laden und später filtern, welche zu Spielen gehören, die wir anzeigen
		if ($this->bAnzSpieleGeladen())
			if (!$this->LadeAnzSpiele()) throw new Exception ("Fehler beim Laden der Anzahl Spiele");

		$MatchVerw = new CMatchVerw();
		$Matches = $MatchVerw->HoleMatches(MATCH_FILTER_HATBEGONNEN);
		$Matches = array_reverse($Matches);
		//Dieses Array wird später zurückgegeben. Es enthält Instanzen von CTippErgVerglEintrag
		$Vergleich = array();
		//Zu jedem abgeschlossenen Spiel einen Eintrag erstellen. Sollte zu einem Spiel kein Tipp abgegeben worden
		//sein, taucht es auf diese Weise dennoch auf:
		if (count($Matches)>0)
		foreach ($Matches as $Match)
		{
			$Vergleich[$Match->MatchNr] = new CTippErgVerglEintrag($Match);
		}
		

		
		//Alle Tipps durchgehen und Tipp/Ergebnis-Vergleich erstellen:
		if (count($tipps) > 0)
		foreach($tipps as $Tipp)
		{
			//Wie ging das Spiel aus?
			$ErgZahl = $this->ErzeugeErgZahl($Tipp->Team1ToreErg,$Tipp->Team2ToreErg);
			//Wie wurde das Spiel getippt?
			$TippZahl = $this->ErzeugeErgZahl($Tipp->Team1Tore,$Tipp->Team2Tore);
			//Welcher Vergleich ist für diesen Tipp "zuständig"
			if (!array_key_exists($Tipp->iSpielNr, $Vergleich))
				continue;//kein Vergleich für diesen tipp -> ignorieren
			$Vergl = $Vergleich[$Tipp->iSpielNr]->GetVerglByErgZahl($TippZahl);
			//echo $Tipp->Team1Tore;
			if ($Tipp->Team1ToreErg == NULL)
			{
				$Vergl->iAnzTippsUnbekannt++;
			}
			else 
			{

			//Ergebnis richtig?
			if (($Tipp->Team1Tore==$Tipp->Team1ToreErg) && ($Tipp->Team2Tore==$Tipp->Team2ToreErg)) $Vergl->iAnzErgebnis++;
			//Differenz richtig?
			else if (($Tipp->Team1Tore-$Tipp->Team2Tore == $Tipp->Team1ToreErg-$Tipp->Team2ToreErg)) $Vergl->iAnzDifferenz++;
			//Tendenz richtig?
			else if (self::ErzeugeErgZahl($Tipp->Team1Tore, $Tipp->Team2Tore) ==
			self::ErzeugeErgZahl($Tipp->Team1ToreErg,$Tipp->Team2ToreErg)) $Vergl->iAnzTendenz++;
			//Gar nix richtig:
			else $Vergl->iAnzFalsch++;
			}



		}

		return $Vergleich;
	}
}

?>