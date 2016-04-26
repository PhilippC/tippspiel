<?php

  require_once("./script/UIWeb1Classes.inc.php5");

  require_once("./script/Tipp.db.class.php5");
  require_once("./script/Team.db.class.php5");
  require_once("./script/Match.db.class.php5");
  require_once("./script/StatistikVerw.class.php5");

  
  
  $strAktion="ETV";
  if (isset($_GET["Aktion"]))
    $strAktion = $_GET["Aktion"];

  if (isset($_GET["Filter"]))
    $strFilter = $_GET["Filter"];
  else
    $strFilter = "";
    
    require_once('./script/UIFactory.inc.php5');
 $TippSpielUI = createWebUi1();

  if ((ENABLE_CACHE_TIPPERGVERGLEICH) && ($strAktion == "ETV"))
  {
	$bufferFile = "ergtippvergl".$TippSpielUI->GetUIName().".txt";
	$handle = @fopen($bufferFile, "r");
	if (!$handle)
	{
		CLogClass::log("Konnte kein Handle auf buffer file $bufferFile bekommen!");
	}
	$cachetime = CACHETIME_TIPPERGVERGLEICH; //sekunden
	if ((@flock($handle, LOCK_SH | LOCK_NB)) && (@filesize($bufferFile) > 100)|| (!$handle)) 
	{
		$cachefile_created = ((@file_exists($bufferFile))) ? @filemtime($bufferFile) : 0; 
		@clearstatcache(); 
		// Show file from cache if still valid 
		if (time() - $cachetime < $cachefile_created) 
		{
			CLogClass::log("Verwende output buffer"); 
			@readfile($bufferFile);
			@fclose($handle); 
			exit(); 
		} 
		// If we're still here, we need to generate a cache file
		CLogClass::log("Starte output buffering");
		ob_start(); 
		$isCreatingCache = true;
	} 
	@fclose($handle);
	
  }

  $StatVerw = new CStatistikVerw();
  
  $TippSpielUI->OutputHeader("");

?>
 <table><tr>
 <td class="SubMenuPreTD">Statistiken &gt;&gt;</td>
<?php
  $TippSpielUI->OutputSubMenu("statistiken.php5?Aktion=ETV","Ergebnis-Tipp-Vergleich", $strAktion =="ETV");
  if (ENABLE_STATIMAGES)
  {
	$TippSpielUI->OutputSubMenu("statistiken.php5?Aktion=TE","Tabellen-Entwicklung", $strAktion =="TE");
	$TippSpielUI->OutputSubMenu("statistiken.php5?Aktion=PE","Punkte-Entwicklung", $strAktion =="PE");
  }

?>
</tr></table>
<br/>
<p align="center">
<?php
if ($strAktion == "ETV")
{
		
 $TippErgebnisse = new CTippErgebnisse();
 $Vergleich =  $TippErgebnisse->ErzeugeTippErgVergleich();
 $UIVergleich = $TippSpielUI->CreateUIComponent("TippErgVergleich");
 $UIVergleich->Output($Vergleich);
 
}
else
{
  $TippSpielDB = new CTippspielDB();
  if ($TippSpielDB->LiesAnzahlSpieleAbgeschlossen() < 2)
  {
    $TippSpielUI->OutputMessageBox("Bitte noch etwas Geduld...","Diese Statistiken stehen erst nach dem Ablauf von mindestens 2 Spielen zur Verfügung!");
  }
  else
  {
    $AnzSpieler = $TippSpielDB->LiesAnzahlSpieler();
    $FilterSteps = 10;
    if ($AnzSpieler>$FilterSteps)
    {
      echo('Für bessere Übersichtlichkeit kann die Grafik auf eine geringere Zahl von Spielern
        beschränkt werden:
        <form Name="FilterForm" action="statistiken.php5" method="get">
          <select name="Filter">
          <option value="">Alle anzeigen</option>');
          $FilterStrings = $StatVerw->GetFilterStrings();
          foreach ($FilterStrings as $FilterString)
          {
            if ($FilterString == $strFilter)
              $Selected = 'selected="selected"';
            else
              $Selected = '';
            $ReadableString = $StatVerw->MakeFilterStringReadable($FilterString);
            echo("<option $Selected value=\"$FilterString\">$ReadableString</option>");
          }

       echo('
          </select>
          <input type="hidden" name="Aktion" value="'.$strAktion.'"></input>
          <input type="submit" value="Grafik neu laden"></input>

        </form>
        <br />');
    }


    echo('<img src="'.$StatVerw->GetImageFilename($strAktion,$strFilter).'">');

  }
}
?>
</p>
<?php


  $TippSpielUI->OutputFooter();
  
  if ($isCreatingCache)
  {  
 	$handle = @fopen($bufferFile, 'w');
	if (!$handle)
	{
		CLogClass::log("Konnte kein Handle auf buffer file bekommen! Kann Ausgabebuffer nicht cachen!");
	}
	
	if (@flock($handle, LOCK_EX | LOCK_NB)) 
	{
		@fwrite($handle, ob_get_contents()); 
	  	@fclose($handle); 
	  	
	  	ob_end_flush(); 
		CLogClass::log("Ausgabebuffer in cachefile geschrieben!");
	} else @fclose($handle);
  } 
?>