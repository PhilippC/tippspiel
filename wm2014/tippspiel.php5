<?php
  require("./script/TippspielLogin.php5");

  require_once("./script/UIWeb2Classes.inc.php5");
  
  require_once("./script/Tipp.db.class.php5");
  require_once("./script/Team.db.class.php5");
  require_once("./script/Match.db.class.php5");
  require_once("./script/TippVerw.class.php5");

  require_once("tippconfig.php5");
  require_once("./script/facebooklink.php5");
  
  require_once('./script/UIFactory.inc.php5');
  $TippSpielUI = createWebUi2();

  require(BASECLASSES_PATH."/sajax/Sajax.php");


  sajax_init();
  $sajax_debug_mode = 0;


  //Daten laden, die auf jeden Fall angezeigt werden sollen (unabh. von der Aktion)
  $TippErgebnisse = new CTippErgebnisse();
  $Tabelle = $TippErgebnisse->ErzeugeTippTabelle(0,FILTER_USER,array("BenutzerName" => $auth->user["Name"], "AnzPlaetze" => 3));
  $TippTabEintrag =  $TippErgebnisse->FindeEintrag($auth->user["Name"],$Tabelle);
  if ($TippTabEintrag == NULL)
    $BenutzerPlatz = NULL;
  else
    $BenutzerPlatz = $TippTabEintrag->iPlatz;

  $TippspielDB = new CTippSpielDB();
  $AnzSpieleAbgeschl = $TippspielDB->LiesAnzahlSpieleAbgeschlossen();
  $AnzSpieleGesamt = $TippspielDB->LiesAnzahlSpieleGesamt();

  $TippListe = $TippSpielUI->CreateUIComponent("TippListe");

    $strAktion = "";
  if (isset($_POST["Aktion"]))
    $strAktion = $_POST["Aktion"];
  if (isset($_GET["Aktion"]))
    $strAktion = $_GET["Aktion"];

  $strJavascript ='
<script language="JavaScript">'.
sajax_get_javascript().
$TippListe->GetJavaScript().
'
</script>';
  $TippSpielUI->AdditionalHTMLHeader = $strJavascript;
  $TippSpielUI->OutputHeader("");

function PrintTippListe( $ShowResults=false, $ErrorMsgs=NULL)
{

    global $auth;
    global $TippSpielUI;
    global $BenutzerPlatz;
    global $AnzSpieleAbgeschl;
    global $AnzSpieleGesamt;
    global $TippListe;
    $TippDB = new CTippDB();
    $UserTipps = $TippDB->LiesBenutzerTipps($auth->user["Name"]);
    $TeamDB = new CTeamDB();
    $Teams = $TeamDB->LiesAlle();
    $MatchDB = new CMatchDB();
    $MatchDB->OrderByClause = "MatchDate, StartTime, MatchNr";
    $Matches = $MatchDB->LiesAlle();

    if ($ShowResults)
    {
      if (!$TippListe->OutputAbgeschlossen($Matches, $UserTipps, $Teams))
      {
        $TippSpielUI->OutputMessageBox("Turnier hat noch nicht begonnen!","Du musst dich noch etwas gedulden - sobald das erste Spiel
        begonnen hat, kannst du hier deine abgegebenen Tipps und später das Ergebnis sehen!");
      }
    }
    else
      if (!$TippListe->OutputTippbar($Matches, $UserTipps, $Teams,10, $ErrorMsgs))
      {
        if ($AnzSpieleAbgeschl == $AnzSpieleGesamt)
        {
          if ($BenutzerPlatz == 1)
          {
            $TippSpielUI->OutputMessageBox("Herzlichen Glückwunsch!","Du hast den $BenutzerPlatz. Platz belegt und das Tippspiel damit gewonnen!
            <br />
             Wenn du den Verlauf des Tippspiels ansehen möchtest, kannst du noch einen Blick in die
          <a href=\"statistiken.php5\">Statistiken</a> werfen! <br />.BIS_ZUM_NAECHSTEN_MAL");

          }
          else
            $TippSpielUI->OutputMessageBox("Das Tippspiel ist beendet!","Du hast den $BenutzerPlatz. Platz belegt! Nun können keine
          Tipps mehr abgegeben werden. Wenn du den Verlauf des Tippspiels ansehen möchtest, kannst du noch einen Blick in die
          <a href=\"statistiken.php5\">Statistiken</a> werfen! <br />".BIS_ZUM_NAECHSTEN_MAL);
        }
        else
        {
          $TippSpielUI->OutputMessageBox("Moment keine Tipps abzugeben!","Bitte schau in den nächsten Tagen nochmal rein, ob die
          nächsten Begegnungen feststehen. Dann können diese auch getippt werden!");
        }

      }
}


?>


<table><tr>
 <td class="SubMenuPreTD"> Tippspiel &gt;&gt;</td>
<?php

  $TippSpielUI->OutputSubMenu("tippspiel.php5","Tipps abgeben", $strAktion=="" || $strAktion=="SaveTipps");
  $TippSpielUI->OutputSubMenu("tippspiel.php5?Aktion=PointHistory","Abgeschlossene und laufende Spiele", $strAktion=="PointHistory");
?>
</tr></table>
<br/>


<?php $TippSpielUI->StartColumn1();?>



<h1>Tippspielseite von <?php echo($auth->user["Name"]);?></h1>



<?php


  if ($strAktion == "")
  //Standard-Seite anzeigen:
  {
   PrintTippListe();
  }
  else
  if ($strAktion == "SaveTipps")
  //Benutzer hat Tipps eingegeben ->speichern
  {
    $TippDB = new CTippDB();
    //Parameter auslesen
    $Tipps = array();
    foreach ($_POST as $key=>$ToreTipp)
    {
      $KeyParts = explode("_", $key);
      if ($KeyParts[0] == "Match")
      {
        if ($ToreTipp == "") continue;
        settype($ToreTipp, 'integer');
        //Der Parameter gibt einen Tipp an. Der Key hat das Format Match_X_1 oder Match_X_2:
        //TeamNr kann auch 3 sein. Das dient dazu, Tipps hier auch zu erzeugen, die schon gespeichert
        //waren und bei denen der User beide Tipps gelöscht hat. Dann bekommt er einer Fehlermeldung.
        //(Das Löschen von Tipps ist im Moment nicht implementiert).

        $MatchNr = $KeyParts[1];
        $TeamNr = $KeyParts[2];
        if (!isset($Tipps[$MatchNr]))
          $Tipps[$MatchNr] = new CTipp($auth->user["Name"], $MatchNr);

        if ($TeamNr == 1)
          $Tipps[$MatchNr]->Team1Tore = $ToreTipp;
        if ($TeamNr == 2)
          $Tipps[$MatchNr]->Team2Tore = $ToreTipp;
        $Tipps[$MatchNr]->TippDateTime= time();
      }
    }
    $ErrorMsgs = CTippVerw::SpeichereTipps($Tipps);

    if (count($ErrorMsgs)>0) echo('<div class="FehlerMeldung">Achtung! Es konnten nicht alle Tipps gespeichert werden!</div>
    <div class="FehlerMeldungText">Die gelben Punkte zeigen, wo ein Fehler aufgetreten ist. Bei diesen Tipps wurden die alten Eintr&auml;ge beibehalten (falls welche existierten).<br>
    Wenn du mit der Maus auf den gelben Punkt f&auml;hrst, erscheint die jeweilige Fehlermeldung!</div>');
    else echo('<div class="Meldung">Es wurden alle Tipps gespeichert!</div>');

    PrintTippListe(false, $ErrorMsgs);
  }
  else
  if ($strAktion == "PointHistory")
  {
    echo('<h2>Liste der abgeschlossenen und laufenden Spiele</h2>');
    PrintTippListe(true);
    echo('<br />Du möchtest deine Ergebnisse genauer auswerten? Daf&uuml;r stehen dir die <a href="statistiken.php5">Statistiken</a> zur Verfügung!');
  }

?>



<?php
 $TippSpielUI->StartColumn2();

  $TippListe->OutputTeamInfoArea();

  if ($BenutzerPlatz != NULL)
    echo("<div class=\"Box\"> Aktuelle Platzierung: <br /><span id=\"AktPlatz\">$BenutzerPlatz. Platz</span></div><br />");
    
  if (ENABLE_FACEBOOK_SUPPORT)
  {
  	
  	echo('<div class="Box">');
  	echoFacebookLink();
	echo('</div><br />');
  }
  

?>


<?php
  if (WERBUNG_ZEIGEN)
  {
  	echo("<div class=\"Box\">");
    $Adv = $TippSpielUI->CreateUIComponent("Advertisement");
    $Adv->Output();
    echo("</div><br />");
  }
?>



<?php
  try
  {
    if (count($Tabelle)>0)
    {
      echo('<div class="Box">  ');

      $TippspielTabelle = $TippSpielUI->CreateUIComponent("TippTabelle",$Tabelle);
     if ($TippspielTabelle->Output(1) == false)
      {
        CLogClass::log("Fehler bei TippSpielTabelle->Output()");
        throw new Exception("Fehler bei Output");
      }
      echo('<a href="tabelle.php5">ganze Tabelle anzeigen</a>');
      echo('

</div>
<br />');

    }
  }
  catch (Exception $e)
  {
     echo("<table border=\"1\"><tr><td>-Fehler beim Laden der Tabelle-<!--".$e->getMessage()."--></td></tr></table>");
  }

?>


 


<?php
$TippSpielUI->EndColumn2();
$TippSpielUI->OutputFooter();

?>