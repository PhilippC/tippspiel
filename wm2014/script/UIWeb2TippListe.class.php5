<?php

  /**
  Anzeigeklasse für die Tipp-Eingabe-Liste



  Feb. 06, Philipp Crocoll

  **/

  require_once("TippErgebnisse.class.php5");
  require_once("UIWeb2Component.class.php5");


    function ErsetzeUml($zeichenkette) {

   $von = array('ä', 'ö', 'ü', 'ß', 'Ä','Ö','Ü');
   $nach = array('&auml;','&ouml;','&uuml;','&szlig;','&Auml;','&Ouml;','&Uuml;');
   for($x = 0; $x < count($von); $x++){
     $zeichenkette = str_replace($von[$x], $nach[$x], $zeichenkette);
   }
   return $zeichenkette;
 }

  function AJAXGetTeamInfo($TeamShort)
    {
  	  $TeamDB = new CTeamDB();
	    $Team = $TeamDB->liesTeam($TeamShort);
	    $res = '<img src="bilder/flaggen/'.$Team->FlagURL.'" alt="'.ErsetzeUml($Team->NameLong).'" /> <b>'.ErsetzeUml($Team->NameLong).'</b>';
	    $res .= '<br /><br />
	    Bisherige Spiele:<br />
	    ';
	    $MatchVerw = new CMatchVerw();
	    $MatchesAlle = $MatchVerw->HoleTeamMatches($TeamShort);
	    $MatchesAbgeschl = array();
	    foreach ($MatchesAlle as $Match)
	    {
	      if ($Match->isAbgeschlossen)
	        $MatchesAbgeschl[$Match->MatchNr] = $Match;
	    }
	    if (count($MatchesAbgeschl) == 0)
	      $res .= "--- hat noch nicht gespielt ---";
	    else
	    {
	      $SpiellisteUI = new CUIWeb1Spielliste();
	      $res.= $SpiellisteUI->GetOutputString($MatchesAbgeschl);
	    }
	    if (TEAM_INFO_LINK != "")
	    {
	   	$href = str_replace("###",strtolower($TeamShort), TEAM_INFO_LINK);
	    $res .= "<br />";
	    $res .= '<a target="blank" href="'.$href.'">Weitere Informationen</a>';
	    $res .= "<br /> <br />";
	    }
	    return $res;
    }

  class CUIWeb2TippListe extends CUIWeb2Component
  {



    public function __construct()
    {
      sajax_export("AJAXGetTeamInfo");
      sajax_handle_client_request();
    }


    public function GetJavaScript()
    {
      return '

var currentTeamInfo1 ="";
var currentTeamInfo2 ="";
var pendingTeam1Short ="";
var pendingTeam2Short ="";

  function GetTeamInfo1_cb(res)
  {

HideTeamInfo(1);

var newDiv = document.createElement("div");
newDiv.id = "TeamInfo1";
newDiv.innerHTML = res;
newDiv.className="TeamInfoBox";

var Ausgabebereich1 = document.getElementById("rechteSpalteOben1");
Ausgabebereich1.appendChild(newDiv);

var newBr = document.createElement("br");
newBr.id = "TeamInfo1Br";
Ausgabebereich1.appendChild(newBr);

currentTeamInfo1 = pendingTeam1Short;
pendingTeam1Short = "";
  }

  function GetTeamInfo2_cb(res)
  {
HideTeamInfo(2);

var newDiv = document.createElement("div");
newDiv.id = "TeamInfo2";
newDiv.innerHTML = res;
newDiv.className="TeamInfoBox";


var Ausgabebereich2 = document.getElementById("rechteSpalteOben2");
Ausgabebereich2.appendChild(newDiv);

var newBr = document.createElement("br");
newBr.id = "TeamInfo2Br";
Ausgabebereich2.appendChild(newBr);


currentTeamInfo2 = pendingTeam2Short;
pendingTeam2Short = "";

  }

function ShowTeamInfo(TeamInfo1, TeamInfo2)
{

if ((currentTeamInfo1 == TeamInfo1) && (currentTeamInfo2 == TeamInfo2))
  return;

HideTeamInfo(0);

pendingTeam1Short = TeamInfo1;
pendingTeam2Short = TeamInfo2;

x_AJAXGetTeamInfo(TeamInfo1,GetTeamInfo1_cb);
x_AJAXGetTeamInfo(TeamInfo2,GetTeamInfo2_cb);

}

function HideTeamInfo(TINr)
{


if ((TINr == 0) || (TINr == 1))
{
  var Ausgabebereich1 = document.getElementById("rechteSpalteOben1");
  var TI1 = document.getElementById("TeamInfo1");
  if (TI1)
    Ausgabebereich1.removeChild(TI1);
  var TI1Br = document.getElementById("TeamInfo1Br");
  if (TI1Br)
    Ausgabebereich1.removeChild(TI1Br);

  currentTeamInfo1 ="";
}

if ((TINr == 0) || (TINr == 2))
{
  var Ausgabebereich2 = document.getElementById("rechteSpalteOben2");
  var TI2 = document.getElementById("TeamInfo2");
  if (TI2)
    Ausgabebereich2.removeChild(TI2);
  var TI2Br = document.getElementById("TeamInfo2Br");
  if (TI2Br)
    Ausgabebereich2.removeChild(TI2Br);
  currentTeamInfo2 ="";
}

}

function InputChanged(Name)
{
statusTD = document.getElementById("Status_"+Name);
statusImg = "bilder/point_yellow.jpg";
statusMessage="Tipp geändert aber noch nicht gespeichert";
statusTD.setAttribute("title",statusMessage);
innerHTML = "<img src=\""+statusImg+"\" alt=\""+statusMessage+"\">";
statusTD.innerHTML = innerHTML;
}

';
    }

    public function OutputTeamInfoArea()
    {
     echo ('<div id="rechteSpalte">
<div id="rechteSpalteOben1"></div>
<div id="rechteSpalteOben2"></div>
<div id="rechteSpalteContentNormal">
</div>');
    }

    public function OutputTippbar($Matches, $Tipps, $Teams, $MaxMatches, $ErrorMsgs=NULL)
    {

      //hier wird nicht gleich der Tabellenkopf etc. ausgegen, da evtl. gar kein Spiel getippt werden kann!
      //Sobald ein TIppbares Spiel ausgegeben wird, wird über counter>0 geprüft, ob es das erste ausgegebene Spiel ist
      $counter=0;

      foreach ($Matches as $Match)
      {

        if ($Match->isTippbar == false) continue;
        if ($Match->Team1Short==NULL) continue;

        if ($counter++>0) echo("</tr>");
        else
        {
echo('Für folgende Spiele kannst du im Moment Tipps abgegeben oder bearbeiten:<br />
<br />
<form action="tippspiel.php5" method="post">
<input type="hidden" name="Aktion" value="SaveTipps"></input>
<table border="0" width="100%" cellspacing="0" cellpadding="1">');
      echo('<tr>');
      echo('<th>Datum</th><th>Zeit</th><th colspan="5">Mannschaften</th><th colspan="3">Dein Tipp</th><th>Status</th>');
      echo('</tr>');
        }
        if (($counter>$MaxMatches) && ($MaxMatches>0)) break;
        $TippTeam1 = "";
        $TippTeam2 = "";
        if (isset($Tipps[$Match->MatchNr]))
        {
          $TippTeam1 = $Tipps[$Match->MatchNr]->Team1Tore;
          $TippTeam2 = $Tipps[$Match->MatchNr]->Team2Tore;
        }

        //HTML-Text für Status-Bild (farbiger Punkt) mit Fehlermeldung als alt= erstellen:
        if (isset($ErrorMsgs[$Match->MatchNr]))
        {
          $StatusMessage = $ErrorMsgs[$Match->MatchNr];
          $StatusImage = "bilder/point_yellow.jpg";
        }
        else
        if (isset($Tipps[$Match->MatchNr]))
        {
          $StatusMessage = "Tipp gespeichert";
          $StatusImage = "bilder/point_green.jpg";
        }
        else
        if ($Match->isAktuell)
        {
          $StatusMessage = "Spiel fängt bald an, noch kein Tipp abgegeben!";
          $StatusImage = "bilder/point_red.jpg";
        }
        else
        {
          $StatusMessage = "Noch kein Tipp abgegeben";
          $StatusImage = "bilder/point_gray.jpg";
        }
        if ($counter%2 == 0)
          echo('<tr class="OddRow">');
        else
          echo('<tr class="EvenRow">');
        echo('<td align="center">'.date("d.m.y",strtotime($Match->MatchDate)).'</td>
              <td align="center">'.date("H:i",strtotime($Match->StartTime)).'</td>
              <td align="center" title="'.$Teams[$Match->Team1Short]->NameLong.'"><img src="bilder/flaggen/'.$Teams[$Match->Team1Short]->FlagURL.'" alt="'.$Teams[$Match->Team1Short]->NameLong.'" /></td>
              <td align="center" title="'.$Teams[$Match->Team1Short]->NameLong.'">'.$Match->Team1Short.'</td>
              <td align="center">-</td>
              <td align="center" title="'.$Teams[$Match->Team2Short]->NameLong.'">'.$Match->Team2Short.'</td>
              <td align="center" title="'.$Teams[$Match->Team2Short]->NameLong.'"><img src="bilder/flaggen/'.$Teams[$Match->Team2Short]->FlagURL.'" alt="'.$Teams[$Match->Team2Short]->NameLong.'" /></td>
              <td align="right"><input type="text" size="1" name="Match_'.$Match->MatchNr.'_1" value="'.$TippTeam1.'"
              				onFocus="javascript:ShowTeamInfo(\''.$Match->Team1Short.'\',\''.$Match->Team2Short.'\')"
                                         onChange="javascript:InputChanged(\'Match_'.$Match->MatchNr.'\')">
                  		</input></td>
              <td align="center">:</td>
              <td align="left"><input type="text" size="1" name="Match_'.$Match->MatchNr.'_2" value="'.$TippTeam2.'"
              				onFocus="javascript:ShowTeamInfo(\''.$Match->Team1Short.'\',\''.$Match->Team2Short.'\')"
                                         onChange="javascript:InputChanged(\'Match_'.$Match->MatchNr.'\')">
                  		</input></td>
              <td align="center" title="'.$StatusMessage.'" id="Status_Match_'.$Match->MatchNr.'"><img src="'.$StatusImage.'" alt="'.$StatusMessage.'">');
              //Merken, dass dieser Tipp gesetzt war:
              if (isset($Tipps[$Match->MatchNr]))
                echo('<input type=hidden name="Match_'.$Match->MatchNr.'_3" value="WasSet" />');

              echo('</td>  ');
      }
      if ($counter>0)
      {
        echo('
</table>

<input type="submit" value="Tipps speichern"></input><br />
</form>
       ');
       return true;
      }
    }

    public function OutputAbgeschlossen($Matches, $Tipps, $Teams)
    {


      $counter=0;
	  $showFbLinks = ENABLE_FACEBOOK_SUPPORT;

      foreach ($Matches as $Match)
      {

        if ($Match->isTippbar == true) continue;
        if ($Match->Team1Short==NULL) continue;

        if ($counter++>0) echo("</tr>");
        else
        {
          echo('

<table border="0" width="100%" cellspacing="0" cellpadding="1">');
          echo('<tr>');
          echo('<th>Datum</th><th colspan="5">Mannschaften</th><th colspan="3">Dein Tipp</th><th colspan="3">Ergebnis</th><th>Punkte</th>');
		  if ($showFbLinks)
			echo('<th></th>');
          echo('</tr>');
        }
        $TippTeam1 = "";
        $TippTeam2 = "";
        if (isset($Tipps[$Match->MatchNr]))
        {
          $TippTeam1 = $Tipps[$Match->MatchNr]->Team1Tore;
          $TippTeam2 = $Tipps[$Match->MatchNr]->Team2Tore;
        }
        if (isset($Tipps[$Match->MatchNr]))
          $TippPunkte = CTippErgebnisse::BerechnePunkteFuerTipp($Tipps[$Match->MatchNr]);
        else
          $TippPunkte = 0;


        if ($counter%2 == 0)
          echo('<tr class="OddRow">');
        else
          echo('<tr class="EvenRow">');
        echo('<td align="center">'.date("d.m.y",strtotime($Match->MatchDate)).'</td>
              <td align="center" title="'.$Teams[$Match->Team1Short]->NameLong.'"><img src="bilder/flaggen/'.$Teams[$Match->Team1Short]->FlagURL.'" alt="'.$Teams[$Match->Team1Short]->NameLong.'" /></td>
              <td align="center" title="'.$Teams[$Match->Team1Short]->NameLong.'">'.$Match->Team1Short.'</td>
              <td align="center">-</td>
              <td align="center" title="'.$Teams[$Match->Team2Short]->NameLong.'">'.$Match->Team2Short.'</td>
              <td align="center" title="'.$Teams[$Match->Team2Short]->NameLong.'"><img src="bilder/flaggen/'.$Teams[$Match->Team2Short]->FlagURL.'" alt="'.$Teams[$Match->Team2Short]->NameLong.'" /></td>
              <td align="right">'.$TippTeam1.'</td>
              <td align="center">:</td>
              <td align="left">'.$TippTeam2.'</td>
              <td align="right">'.$Match->ResTeam1Goals.'</td>
              <td align="center">:</td>
              <td align="left">'.$Match->ResTeam2Goals.'</td>
              <td align="center">'.$TippPunkte.' </td>
              ');
			  if ($showFbLinks)
			  {
			    echo('<td>');
				if ($Match->ResTeam1Goals !== null)
				{
					$res = $Match->ResTeam1Goals.":".$Match->ResTeam2Goals;
					if ($TippTeam1 != "")
						$tipp = $TippTeam1.":".$TippTeam2;
					else
						$tipp = "-:-";
					echoFacebookPostResultLink($Teams[$Match->Team1Short]->NameLong, $Teams[$Match->Team2Short]->NameLong, $res, $tipp, $TippPunkte);
				}
				echo('</td>');
			  }
      }
      if ($counter>0)
      {
        echo('
</table>
');
        return true;
      }
    }

  }
?>