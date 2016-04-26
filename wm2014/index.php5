<?php
  require_once $_SERVER["DOCUMENT_ROOT"].'/inc/BaseClasses/log.class.php5';

  if (isset($_GET["logout"]))
    require('./script/TippspielLogout.php5');
	
  require_once($_SERVER["DOCUMENT_ROOT"].'/inc/facebooktools.php');

  
  require_once('./script/authCallbackTippspiel.php5');
  
  require_once($_SERVER["DOCUMENT_ROOT"].'/inc/authconf.php');

  	
  $auth = &newGroupAuth();
  	
  $auth->startSession(false);
  
  
  //print_r($_POST);
  //print_r($_GET);
  if (isFacebookApp())
  {
	if ($_GET["error"])
	{
		 require_once('./script/UIFactory.inc.php5');
		 $TippSpielUI->OutputHeader(TIPPSPIEL_NAME, false);
		CLogClass::log("Fehler bei Facebook-Authorisierung: ".$_GET["error"]."Description: ".$_GET["error_description"], LOG_TYPE_ERROR);
		
		echo("Bei der Facebook-Authorisierung ist ein Fehler aufgetreten! Du musst das Tippspiel zum Zugriff auf deine Facebook-User-Id authorisieren, 
		sonst kannst du nicht über Facebook teilnehmen. Bitte wähle folgende Optionen:<br>
		<a href=\"$auth_url\">Nochmal probieren</a><br>
		<a href=\"$canvas_page\" target=\"_blank\">Tippspiel außerhalb von Facebook nutzen</a><br>
		");
	}	
	else 
	{
		if (isset($_GET["code"]))
		{
			include("neuerspieler.php5");
			exit();
		}
	} 
  }
  
  
  require_once('./script/UIFactory.inc.php5');
  require_once('./script/TippspielClasses.inc.php5');

  require_once('./script/MatchVerw.class.php5');

  $MatchVerw = new CMatchVerw();
  $Matches = $MatchVerw->HoleMatches(MATCH_FILTER_AKTUELL);
  $TippSpielUI = createWebUi1();

  $showCountDown = time() < mktime (22,0,0,6,12,2014);

  $TippSpielUI->AdditionalHTMLHeader =('');
  $TippSpielUI->OutputHeader(TIPPSPIEL_NAME, $showCountDown);
  
  
?>





 <?php

   $TippSpielUI->StartColumn1();

   if ($showCountDown)
   {
   $CountDownBox = $TippSpielUI->CreateUIComponent("Countdown");

   echo'<div class="Box">
   <div class="BoxTitle">Bald geht\'s los!</div>

   <div class="BoxContent" align="center">';

   $CountDownBox->Output();   

   echo' </div>
     </div><br />
     ';

   }



   if (KOLUMNE_ANZEIGEN)
   {



     $Kolumne = $TippSpielUI->CreateUIComponent("Kolumne");
     echo('<div class="Box">
     <div class="BoxTitle">'.KOLUMNE_TITEL.'</div><br />
     ');
     if (!$Kolumne->Output()) echo('<p align="center"> - noch keine Kolumnen-Beiträge verfasst - </p>');

	 echo('</div><br />'); 
   }

   if (WERBUNG_ZEIGEN)
   	require("mychurch/events.htmlsnippet");
   	
    $TippSpielUI->StartColumn2();

 ?>


<div class="Box">
<div class="BoxTitle">Das Tippspiel</div>
<div class="BoxContent">
<br />

<?php
  try
  {
    $TippErgebnisse = new CTippErgebnisse();
    $Tabelle = $TippErgebnisse->ErzeugeTippTabelle(0, FILTER_TOP,3);
    if (count($Tabelle)>0)
    {
	$TippspielTabelle = $TippSpielUI->CreateUIComponent("TippTabelle",$Tabelle);
         if ($TippspielTabelle->Output(1,FILTER_TOP,3) == false)
	{
	   CLogClass::log("Fehler bei TippSpielTabelle->Output()", LOG_TYPE_CRITICAL_ERROR);
	   throw new Exception("Fehler bei Output");
	}
	echo('<a href="tabelle.php5">ganze Tabelle anzeigen</a>
<br /><br />
');
    }
  }
  catch (Exception $e)
  {
     echo("<table border=\"1\"><tr><td>-Fehler beim Laden der Tabelle-<!--".$e->getMessage()."--></td></tr></table>");
  }

?>



<?php
  if ($auth->isIdentified)
  {
    echo("Hallo!<br/> Du bist eingeloggt als ".$auth->user["Name"]);
	if (!isFacebookApp())
		echo('<br /><a href="index.php5?logout=1">Ausloggen</a>');
  }
  else
  {
  if (!isFacebookApp())
  {
	  try
	  {
		echo("<br /> ");
		$LoginForm = $TippSpielUI->CreateUIComponent("LoginForm","LoginForm","tippspiel.php5","post", TIPPSPIEL_USERGROUP, true);
		$LoginForm->Output();
		echo 'oder<br />
	<form action="neuerspieler.php5">
	<input type="submit" value="Als neuer Spieler anmelden" class="smallSubmitButton" />
	</form>';
	  }
	  catch (Exception $e)
	  {
		CLogClass::log("Fehler beim Anzeigen des LoginForms: ".$e->getMessage(), LOG_TYPE_CRITICAL_ERROR);
		echo("- Login-Formular konnte nicht geladen werden -");
	  }
	  
	}
	else
	{
		echo ('<b>Spiele mit!</b><br /><form action="neuerspieler.php5">
	<input type="submit" value="Am Tippspiel teilnehmen" class="smallSubmitButton" />
	</form>');
	}
  }
?>


 </div>
 </div>
        <br />
   <div class="Box">
   <div class="BoxTitle">Aktuell</div>
<?php
  if (count($Matches) > 0)
  {
    $AktSpielListe = $TippSpielUI->CreateUIComponent("SpielListe");
    $AktSpielListe->Output($Matches);
  }
  else
  {
    echo('<p align="center">--- zur Zeit keine Spiele ---</p>');
  }
?>
   <a href="spielplan.php5">Zum Spielplan</a>
   </div>


         
<?php
$TippSpielUI->EndColumn2();
$TippSpielUI->OutputFooter();
 ?>
