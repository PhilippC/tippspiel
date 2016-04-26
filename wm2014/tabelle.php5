<?php

  require_once("./script/UIWeb1Classes.inc.php5");

  require_once("./script/Tippspiel.db.class.php5");


    require_once('./script/UIFactory.inc.php5');
 $TippSpielUI = createWebUi1();

  $TippSpielUI->OutputHeader("Tabelle des Tippspiels");

  $Tippspiel = new CTippSpielDB();



?>


<?php
  try
  {
    $TippErgebnisse = new CTippErgebnisse();
    if (!isset($_GET["msi"]))
    	$maxSortId = 0;
    else
    	$maxSortId = (int)$_GET["msi"];
    if (isset($_GET["msi_up"]))
    {
    	$maxSortId = $Tippspiel->LiesNaechsteMatchSortId(((int)$_GET["msi_up"])-1);

    }
       	
    $Tabelle = $TippErgebnisse->ErzeugeTippTabelle($maxSortId);

    if (count($Tabelle)>0)
    {
      $AnzSpieleAbgeschlossen = $Tippspiel->LiesAnzahlSpieleAbgeschlossen();
      echo("<p align=\"center\">Nach ".$Tabelle[0]->iAnzSpieleTabelle." von ".$Tippspiel->LiesAnzahlSpieleGesamt()." Ergebnissen</p>");
      if (($Tabelle[0]->iAnzSpieleTabelle > 1) || ($Tabelle[0]->iAnzSpieleTabelle < $AnzSpieleAbgeschlossen))
      {
      	echo("<p align=\"center\">");
      	if ($Tabelle[0]->iAnzSpieleTabelle > 1)
      	{
      		$previousMaxSortId = $Tabelle[0]->iMaxMatchSortId-1;
      		echo ("<a href=\"tabelle.php5?msi=$previousMaxSortId\">&lt;&lt; vorige Tabelle</a>");
      	}
      	if (($Tabelle[0]->iAnzSpieleTabelle > 1) && ($Tabelle[0]->iAnzSpieleTabelle < $AnzSpieleAbgeschlossen))
      	{
      		echo(" -- ");
      	}
      	if ($Tabelle[0]->iAnzSpieleTabelle < $AnzSpieleAbgeschlossen)
      	{
      		$nextMaxSortId = $Tabelle[0]->iMaxMatchSortId+1;
      		echo ("<a href=\"tabelle.php5?msi_up=$nextMaxSortId\">&gt;&gt; folgende Tabelle</a> -- <a href=\"tabelle.php5\">&gt;&gt;&gt; aktuelle Tabelle</a>");
      	}
      	
      	echo("</p>");      	
      }
     
      if ($Tabelle[0]->iMaxMatchSortId > 1)
      {
      	$TabelleVergleich = $TippErgebnisse->ErzeugeTippTabelle($Tabelle[0]->iMaxMatchSortId-1);
      }
      else 
      {
      	$TabelleVergleich = NULL;
      }
      
      $TippspielTabelle = $TippSpielUI->CreateUIComponent("TippTabelle",$Tabelle, $TabelleVergleich);
      if ($TippspielTabelle->Output(2) == false)
      {
        CLogClass::log("Fehler bei TippSpielTabelle->Output()");
        throw new Exception("Fehler bei Output");
      }
  
      if (ENABLE_STATIMAGES)
      	echo("<p align=\"center\">Der Verlauf der Tabelle ist bei den <a href=\"statistiken.php5\">Statistiken </a> abgebildet.</p>\n");
    }
    else
    {
      echo('Es wurden noch keine Tipps abgegeben - eine Tabelle kann daher nicht erstellt werden!');
    }

  }
  catch (Exception $e)
  {
     echo("<table border=\"0\"><tr><td>-Fehler beim Laden der Tabelle-<!--".$e->getMessage()."--></td></tr></table>");
  }


  $TippSpielUI->OutputFooter();

 ?>