<?php

require("TippspielClasses.inc.php5");
require_once("stringutil.php5");
require_once(BASECLASSES_PATH."/jpgraph/src/jpgraph.php");
require_once(BASECLASSES_PATH."/jpgraph/src/jpgraph_line.php");

$TippErgebnisse = new CTippErgebnisse();
$Tippspiel = new CTippspielDB();

$AnzSpiele = $Tippspiel->LiesAnzahlSpieleAbgeschlossen();
$MaxSpiele = $AnzSpiele;

  if (isset($_GET["Filter"]))
    $strFilter = $_GET["Filter"];
  else
    $strFilter = "";


  $FilterVon = 0;
  $FilterBis = 0;
  $FilterGesetzt = false;
  $FilterParts = explode("_",$strFilter);
  if (count($FilterParts)==2)
  {
    $FilterVon = (int)$FilterParts[0];
    $FilterBis = (int)$FilterParts[1];
    $FilterGesetzt = true;
  }

//***
// Gemäß der bis zu 64 Tipptabellen die "LineData", also die Charts für die einzelnen
// Spieler erstellen.
// Dabei auch "SpielerDataIndex" füllen, dieses ordnet jedem SpielerNamen den Index
// des LinePlots zu. Kann auch "rückwärts" (Index->SpielerName) durchsucht werden.
// Die Legende soll so sortiert sein wie die aktuelle Tabelle. Dabei wird von
// hinten nach vorne durch die Spiele gegangen (letzten Stand zuerst behandeln)

$LineData = array();
$SpielerDataIndex = array();

$Tabelle = array();

$AnzSpieler = 0;
$MaxPunkte = 0;

for ($SpielCounter=$AnzSpiele;$SpielCounter>=1;$SpielCounter--)
{
  //Tabelle nach $SpielCounter Spielen erzeugen
  $Tabelle = $TippErgebnisse->ErzeugeTippTabelle($SpielCounter);

  //Falls es der erste Schleifendurchgang ist: LineData-Array erzeugen
  //und Spielernamen merken. Außerdem merken, welcher Spieler welchem LineData entspricht:
  if ($SpielCounter==$AnzSpiele)
  {
    $AnzSpieler = count($Tabelle);
    $LineDataCounter=0;
    foreach ($Tabelle as $TippEintrag)
    {
      $LineData[$LineDataCounter] = array();
      $SpielerDataIndex[$TippEintrag->BenutzerName] = $LineDataCounter;
      $LineDataCounter++;
      if ($MaxPunkte < $TippEintrag->iPunkte) $MaxPunkte = $TippEintrag->iPunkte;
    }
  }
  //Für jeden Spieler ein neues Wertepaar in den entsprechenden LinePlot einfügen (x=AnzSpiele, y=TabStand)
  foreach ($Tabelle as $TippEintrag)
  {
    $PlotIndex = $SpielerDataIndex[$TippEintrag->BenutzerName];
    if (($FilterGesetzt) && (($PlotIndex+1<$FilterVon) || ($PlotIndex+1>$FilterBis))) continue;
    $MaxShift = 0.05/$MaxPunkte;
    $LineData[$PlotIndex][$SpielCounter-1] = ($TippEintrag->iPunkte)-$PlotIndex/$MaxPunkte*$MaxShift;
  }

}

$ColorArray = array("red","blue","green" ,"darkred","orange", "yellow","black");//,"darkblue","orange","darkred","silver","black", "darkgreen"


$YAxisLabels = array();
for ($i=$AnzSpieler;$i>0;$i--)
{
  $YAxisLabels[$i-1]= ($AnzSpieler-$i+1);
}

$XAxisLabels = array();
for ($i=0;$i<$MaxSpiele;$i++)
{
  $XAxisLabels[$i]= $i+1;
}
// Grafik generieren und Grafiktyp festlegen
$GraphHeight = $AnzSpieler*16+50;
if ($GraphHeight<300) $GraphHeight = 300;
$graph = new Graph(712,$GraphHeight);
$graph->SetScale("int",0,$MaxPunkte,0,$MaxSpiele-1);
$graph->img->SetImgFormat( "png");

//LinePlots erstellen und zu Graph hinzufügen:
$counter=0;
foreach ($LineData as $key=>$ld)
{
  $counter++;
  if (($FilterGesetzt) && (($key+1<$FilterVon) || ($key+1>$FilterBis))) continue;
  $LinePlot[$key] = new LinePlot($ld);
  $LinePlot[$key]->SetColor($ColorArray[$counter%count($ColorArray)]);
  $LinePlot[$key]->SetLegend(substr(ersetzeNonAscii(array_search($key,$SpielerDataIndex)),0,20));
  $graph->Add($LinePlot[$key]);
}

// Grafik Formatieren
$graph->img->SetMargin(40,125,20,40);

$graph->SetMarginColor("#E8E8E8");
$graph->SetFrame(true,"#FFCC33",1);
$graph->legend->SetFrameWeight(0);

$font = FF_FONT0;
$fontmedium = FF_FONT1;
$fontbig = FF_FONT2;

$graph->title->Set("Punkte-Entwicklung");
$graph->xaxis->title->Set("Spiele");
$graph->xaxis->SetFont($font,FS_NORMAL,7);
$graph->xaxis->SetPos(-0.5);
$graph ->xgrid->Show(true);

$graph->yaxis->title->Set("Punkte");
$graph->yaxis->SetFont($font,FS_NORMAL,7);

$graph->SetTickDensity(TICKD_DENSE,TICKD_DENSE);


$graph->title->SetFont($fontbig,FS_BOLD, 14);
$graph->title->SetMargin(15,0,0,0);
$graph->yaxis->title->SetFont($fontmedium,FS_NORMAL,12);
$graph->xaxis->title->SetFont($fontmedium,FS_NORMAL,12);

$graph->legend->SetFont($font,FS_NORMAL,7);


$graph->yaxis->SetColor("#FFCC33","black");
$graph->yaxis->SetWeight(1);
$graph->xaxis->SetColor("#FFCC33","black");
$graph->xaxis->SetWeight(1);


$graph->img->SetAntiAliasing();

$graph->xaxis->SetTickSide(SIDE_DOWN);

//$graph->yaxis->SetTickLabels($YAxisLabels);
$graph->xaxis->SetTickLabels($XAxisLabels);

$graph->legend->SetLeftMargin(0);
// 10x10 pixels from the upper right corner
$graph->legend->SetAbsPos(10,10,'right','top');
$graph->legend->SetFillColor(0);
$graph->legend->SetShadow("#E8E8E8",0);
// Style can also be specified as SetStyle([1|2|3|4]) or
// SetStyle("solid"|"dotted"|"dashed"|"lobgdashed")


// Grafik anzeigen
$graph->Stroke();
?>