<?php

/********************

TippTabellenEintrag-Klasse
==========================

Repräsentiert eine Zeile in der Tabelle der Tipper.

Schnittstelle:
 - Alle Werte werden dem Konstruktor übergeben und können
 - Alle Properties können gelesen werden
 - Alle Properties können geschrieben werden


Philipp Crocoll, Jan. 06

*********************/


  class CTippTabellenEintrag
  {
  	     public $BenutzerName;         		//Benutzername
         public $iAnzSpieleTabelle = 0;			//Wie viele Spiele in der Tabelle schon abgeschlossen sind
         public $iMaxMatchSortId = 0;			//Sortier-Nummer der Tabelle
         public $iPlatz = 0;			//z.B. 3, wenn Benutzer 3. ist
         public $iAnzSpieleGetippt = 0;			//Wie viele Spiele der Benutzer getippt hat
         public $iAnzTendenz = 0;			//Wie viele Spiele der Benutzer mit richtiger Tendenz, aber falscher Tordifferenz und Ergebnis getippt hat
         public $iAnzDifferenz = 0;			//Wie viele Spiele der Benutzer mit richtiger Tordifferenz, aber falschem Ergebnis getippt hat
         public $iAnzErgebnis = 0;			//Wie viele Spiele der Benutzer mit richtigem Ergebnis getippt hat
         public $iPunkte = 0;			//Wie viele Punkte der Benutzer hat

         public function __construct($Benutzer)
         {
           $this->BenutzerName = $Benutzer;


         }

  }

?>