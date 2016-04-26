<?php

//Klasse für Statistik-Bild Punktentwicklung
require_once("StatistikBild.class.php5");

class CStatistikBildPE extends CStatistikBild
{
  public function GetFilename()
  {
    if ($this->m_FilterString!="")
      return STATIC_STATIMAGES_DIR."pe_".$this->m_FilterString.".png";
    else
      return STATIC_STATIMAGES_DIR."pe_gesamt.png";
  }

  public function SetGraphAxis(&$graph)
  {
       	$graph->SetScale("int",0,$this->m_MaxPunkte,0,$this->m_MaxSpiele-1);
	$graph->title->Set("Punkte-Entwicklung");
	$graph->yaxis->title->Set("Punkte");


  }


  protected function LoadData()
  {
         if ($this->m_LineData != NULL) return;

	$TippErgebnisse = new CTippErgebnisse();
         $Tippspiel = new CTippspielDB();
         $this->m_AnzSpiele = $Tippspiel->LiesAnzahlSpieleAbgeschlossen();
	$this->m_MaxSpiele = $this->m_AnzSpiele;


         $this->m_LineData = array();
	$this->m_SpielerDataIndex = array();

         $Tabelle = array();

	$this->m_AnzSpieler = 0;
	$this->m_MaxPunkte = 0;

	for ($SpielCounter=$this->m_AnzSpiele;$SpielCounter>=1;$SpielCounter--)
	{
	   //Tabelle nach $SpielCounter Spielen erzeugen
	  $Tabelle = $TippErgebnisse->ErzeugeTippTabelle($SpielCounter);

    	  //Falls es der erste Schleifendurchgang ist: LineData-Array erzeugen
	  //und Spielernamen merken. Außerdem merken, welcher Spieler welchem LineData entspricht:
	  if ($SpielCounter==$this->m_AnzSpiele)
	  {

	    $this->m_AnzSpieler = count($Tabelle);
	    $LineDataCounter=0;
	    foreach ($Tabelle as $TippEintrag)
	    {
	      $this->m_LineData[$LineDataCounter] = array();
	      $this->m_SpielerDataIndex[$TippEintrag->BenutzerName] = $LineDataCounter;
	      $LineDataCounter++;
	      if ($this->m_MaxPunkte < $TippEintrag->iPunkte) $this->m_MaxPunkte = $TippEintrag->iPunkte;
	    }
	  }
	  //Für jeden Spieler ein neues Wertepaar in den entsprechenden LinePlot einfügen (x=AnzSpiele, y=TabStand)
           $MaxShift = 0.05/$this->m_MaxPunkte;
    	  foreach ($Tabelle as $TippEintrag)
	  {
	    $PlotIndex = $this->m_SpielerDataIndex[$TippEintrag->BenutzerName];
	    $this->m_LineData[$PlotIndex][$SpielCounter-1] = ($TippEintrag->iPunkte)-$PlotIndex/$this->m_MaxPunkte*$MaxShift;
	  }

	}
  }

}
?>
