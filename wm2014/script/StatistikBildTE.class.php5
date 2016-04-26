<?php

//Klasse für Statistik-Bild Tabellenentwicklung
require_once("StatistikBild.class.php5");

class CStatistikBildTE extends CStatistikBild
{
  public function GetFilename()
  {
    if ($this->m_FilterString!="")
      return STATIC_STATIMAGES_DIR."te_".$this->m_FilterString.".png";
    else
      return STATIC_STATIMAGES_DIR."te_gesamt.png";
  }

  public function SetGraphAxis(&$graph)
  {

	$graph->SetScale("int",0,$this->m_AnzSpieler-1,0,$this->m_MaxSpiele-1);
	$graph->title->Set("Tabellen-Entwicklung");
	$graph->yaxis->title->Set("Platz");
         $YAxisLabels = array();
	for ($i=$this->m_AnzSpieler;$i>0;$i--)
	{
	  $YAxisLabels[$i-1]= ($this->m_AnzSpieler-$i+1);
	}
	$graph->yaxis->SetTickLabels($YAxisLabels);

  }


  protected function LoadData()
  {

         if ($this->m_LineData != NULL)
         {
           return;
         }

	$TippErgebnisse = new CTippErgebnisse();
         $Tippspiel = new CTippspielDB();
         $this->m_AnzSpiele = $Tippspiel->LiesAnzahlSpieleAbgeschlossen();
	$this->m_MaxSpiele = $this->m_AnzSpiele;

         $this->m_LineData = array();
	$this->m_SpielerDataIndex = array();

	$Tabelle = array();

	$this->m_AnzSpieler = 0;

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
	    }
	  }
	  //Für jeden Spieler ein neues Wertepaar in den entsprechenden LinePlot einfügen (x=AnzSpiele, y=TabStand)
	  foreach ($Tabelle as $TippEintrag)
	  {
	    $PlotIndex = $this->m_SpielerDataIndex[$TippEintrag->BenutzerName];
	    $MaxShift = 0.15;
	    $this->m_LineData[$PlotIndex][$SpielCounter-1] = $this->m_AnzSpieler-($TippEintrag->iPlatz)-$PlotIndex/$this->m_AnzSpieler*$MaxShift;
	  }

	}


  }

}
?>