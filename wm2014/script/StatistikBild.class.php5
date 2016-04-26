<?php

require("TippspielClasses.inc.php5");
require_once("stringutil.php5");
require_once(BASECLASSES_PATH."/jpgraph/src/jpgraph.php");
require_once(BASECLASSES_PATH."/jpgraph/src/jpgraph_line.php");


abstract class CStatistikBild
{

  protected $m_FilterNumPlayers=0;
  protected $m_FilterString="";
  protected $m_FilterVon=0;
  protected $m_FilterBis=0;
  protected $m_FilterGesetzt = false;

  protected $m_LineData = NULL;
  protected $m_SpielerDataIndex = NULL;
  protected $m_AnzSpiele;
  protected $m_AnzSpieler;
  protected $m_MaxPunkte;

  public function UnsetFilter()
  {
    $this->m_FilterVon = 0;
    $this->m_FilterBis = 0;
    $this->m_FilterGesetzt = false;
    $this->m_FilterString = "";
  }

  public function SetFilter($FilterNumPlayers,$FilterString)
  {
    $this->UnsetFilter();

    $this->m_FilterNumPlayers = $FilterNumPlayers;
    $this->m_FilterString = $FilterString;
    $FilterParts = explode("_",$FilterString);
    if (count($FilterParts)==2)
    {
      $this->m_FilterVon = (int)$FilterParts[0];
      $this->m_FilterBis = (int)$FilterParts[1];
      $this->m_FilterGesetzt = true;
    }

  }

  //Liefert den Dateinamen zurück, unter dem ein Bild der abgeleiteten Klasse
  //gespeichert ist.
  //Filter muss zuvor gesetzt werden, falls gewünscht.
  abstract public function GetFilename();

  //Erzeugt das Bild. Lädt dazu alle Daten und speichert es unter
  //dem von GetFilename erzeugten Dateinamen ab.
  //Filter muss, falls gewünscht vorher gesetzt werden.
  public function CreateImage()
  {
  	$graph = $this->CreateGraph();

         $filename = $this->GetFilename();

         $graph->Stroke($filename);

         return $filename;
  }

  abstract protected function LoadData();
  abstract protected function SetGraphAxis(&$graph);

  protected function CreateGraph()
  {

     	$this->LoadData();


       	$ColorArray = array("red","blue","green" ,"darkred","orange", "yellow","black");//,"darkblue","orange","darkred","silver","black", "darkgreen"



	$XAxisLabels = array();
	for ($i=0;$i<$this->m_MaxSpiele;$i++)
	{
	  $XAxisLabels[$i]= $i+1;
	}
	// Grafik generieren und Grafiktyp festlegen
	$GraphHeight = $this->m_AnzSpieler*16+100;
	if ($GraphHeight<300) $GraphHeight = 300;
	$graph = new Graph(712,$GraphHeight);

	$graph->img->SetImgFormat("png");

         $this->SetGraphAxis($graph);

	//LinePlots erstellen und zu Graph hinzufügen:
	$counter=0;
	foreach ($this->m_LineData as $key=>$ld)
	{
	  $counter++;
	  if (($this->m_FilterGesetzt) && (($key+1<$this->m_FilterVon) || ($key+1>$this->m_FilterBis))) continue;
	  $LinePlot[$key] = new LinePlot($ld);
	  $LinePlot[$key]->SetColor($ColorArray[$counter%count($ColorArray)]);
	  $LinePlot[$key]->SetLegend(substr(ersetzeNonAscii(array_search($key,$this->m_SpielerDataIndex)),0,20));
	  $graph->Add($LinePlot[$key]);

	}

        	// Grafik Formatieren
	$graph->img->SetMargin(40,125,20,40);

         //Auf TTF wird verzichtet, da die Installation auf UNIX-Servern etwas Mühe erfordert.
         $font = FF_FONT0;
	$fontmedium = FF_FONT1;
	$fontbig = FF_FONT2;

	$graph->SetMarginColor("#E8E8E8");
	$graph->SetFrame(true,"#FFCC33",1);
	$graph->legend->SetFrameWeight(0);

         $graph->xaxis->title->Set("Spiele");
	$graph->xaxis->SetFont($font,FS_NORMAL,7);
	$graph->xaxis->SetPos(-0.5);
	$graph->xgrid->Show(true);

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


	$graph->xaxis->SetTickLabels($XAxisLabels);

	$graph->legend->SetLeftMargin(0);
	// 10x10 pixels from the upper right corner
	$graph->legend->SetAbsPos(10,10,'right','top');
	$graph->legend->SetFillColor(0);
	$graph->legend->SetShadow("#E8E8E8",0);

         return $graph;

  }
}

?>