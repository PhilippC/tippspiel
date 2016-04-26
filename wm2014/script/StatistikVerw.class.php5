<?php

require_once("./script/StatistikBildTE.class.php5");
require_once("./script/StatistikBildPE.class.php5");
require_once("./script/Tippspiel.db.class.php5");
require_once( dirname(__FILE__).'/../tippconfig.php5' );



class CStatistikVerw
{
  public function GetFilterSteps()
  {
    return STAT_FILTER_STEPS;
  }

  public function GetFilterStrings()
  {
    $FilterStrings = array();

    $TippSpielDB = new CTippspielDB();
    $AnzSpieler = $TippSpielDB->LiesAnzahlSpieler();

    $FilterSteps= $this->GetFilterSteps();
    $counter = 1;
    while ($counter<=$AnzSpieler)
    {
      $von = $counter;
      $bis = $counter+$FilterSteps-1;
      if ($bis>$AnzSpieler) $bis = $AnzSpieler;
      $FilterString = $von."_".$bis;
      $FilterStrings[count($FilterStrings)] = $FilterString;
      $counter+=$FilterSteps;
    }

    return $FilterStrings;
  }

  public function MakeFilterStringReadable($FilterString)
  {
    if ($FilterString == "") return "Alles";
    $FilterParts = explode("_",$FilterString);
    if (count($FilterParts)==2)
    {
      return "Platz $FilterParts[0] bis $FilterParts[1]";
    }
    else throw new Exception("Fehlerhaften FilterString an MakeFilterStringReadable übergeben ($FilterString)");
  }

  public function UpdateAllImages()
  {
    if (USE_DYNAMIC_STATIMAGES || (!ENABLE_STATIMAGES)) return true; 	//nichts zu tun

    $FilterStrings = $this->GetFilterStrings();
    //Bilder mit Filter erzeugen:
    $StatImages = array();
    $StatImages[0] = new CStatistikBildTE();
    $StatImages[1] = new CStatistikBildPE();
    CLogClass::log("AnzFilterStrings=".count($FilterStrings));


    foreach ($FilterStrings as $FilterString)
    {
      CLogClass::log("CurrentFilterString=".$FilterString);
      foreach ($StatImages as $image)
      {
        $image->SetFilter($this->GetFilterSteps(), $FilterString);
        $image->CreateImage();
      }

    }
    //PE und TE Bild ohne Filter erzeugen:
    foreach ($StatImages as $image)
    {
      $image->UnsetFilter();
      $image->CreateImage();
    }

    return true;

  }

  public function GetImageFilename($StatType, $FilterString)
  {

    if (USE_DYNAMIC_STATIMAGES)
    {
      $filename = "script/Statistik$StatType.img.php5";
      if ($FilterString != "")
        $filename.="?Filter=$FilterString";
      return $filename;
    }
    else
    {
      if ($StatType == "TE")
        $StatImage = new CStatistikBildTE();
      else if ($StatType == "PE")
        $StatImage = new CStatistikBildPE();
      else return "";

      $StatImage->SetFilter($this->GetFilterSteps(), $FilterString);

      return $StatImage->GetFilename();
    }
  }

}

?>