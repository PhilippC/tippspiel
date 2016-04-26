<?php

  /**
  Anzeigeklasse für die Tipp-Eingabe-Liste



  Feb. 06, Philipp Crocoll

  **/

  require_once("TippErgebnisse.class.php5");



  class CUIWeb1TippListe extends CUIWeb1Component
  {




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
      echo('<th>Datum/Zeit</th><th colspan="5">Mannschaften</th><th colspan="3">Dein Tipp</th><th>Status</th>');
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
          $StatusHTML = '<img src="bilder/point_yellow.jpg" alt="'.$ErrorMsgs[$Match->MatchNr].'" />';
        else
        if (isset($Tipps[$Match->MatchNr]))
          $StatusHTML = '<img src="bilder/point_green.jpg" alt="Tipp gespeichert" />';
        else
        if ($Match->isAktuell)
          $StatusHTML = '<img src="bilder/point_red.jpg" alt="Spiel fängt bald an, noch kein Tipp abgegeben!" />';
        else
          $StatusHTML = '<img src="bilder/point_gray.jpg" alt="Noch kein Tipp abgegeben" />';
        if ($counter%2 == 0)
          echo('<tr class="OddRow">');
        else
          echo('<tr class="EvenRow">');
        echo('<td align="center">'.date("d.m.y",strtotime($Match->MatchDate)).' '.date("H:i",strtotime($Match->StartTime)).'</td>
              <td align="center" title="'.$Teams[$Match->Team1Short]->NameLong.'"><img src="bilder/flaggen/'.$Teams[$Match->Team1Short]->FlagURL.'" alt="'.$Teams[$Match->Team1Short]->NameLong.'" /></td>
              <td align="center">'.$Match->Team1Short.'</td>
              <td align="center">-</td>
              <td align="center">'.$Match->Team2Short.'</td>
              <td align="center" title="'.$Teams[$Match->Team2Short]->NameLong.'"><img src="bilder/flaggen/'.$Teams[$Match->Team2Short]->FlagURL.'" alt="'.$Teams[$Match->Team2Short]->NameLong.'" /></td>
              <td align="right"><input type="text" size="1" name="Match_'.$Match->MatchNr.'_1" value="'.$TippTeam1.'"></input></td>
              <td align="center">:</td>
              <td align="left"><input type="text" size="1" name="Match_'.$Match->MatchNr.'_2" value="'.$TippTeam2.'"></input></td>
              <td align="center">'.$StatusHTML);
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

      foreach ($Matches as $Match)
      {
        if (($Match->isAbgeschlossen == false) && ($Match->hatBegonnen == false)) continue;
        if ($Match->Team1Short==NULL) continue;

        if ($counter++>0) echo("</tr>");
        else
        {
          echo('

<table border="0" width="100%" cellspacing="0" cellpadding="1">');
          echo('<tr>');
          echo('<th>Datum</th><th colspan="5">Mannschaften</th><th colspan="3">Dein Tipp</th><th colspan="3">Ergebnis</th><th>Punkte</th>');
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
              <td align="center"><img src="bilder/flaggen/'.$Teams[$Match->Team1Short]->FlagURL.'" alt="'.$Teams[$Match->Team1Short]->NameLong.'" /></td>
              <td align="center">'.$Match->Team1Short.'</td>
              <td align="center">-</td>
              <td align="center">'.$Match->Team2Short.'</td>
              <td align="center"><img src="bilder/flaggen/'.$Teams[$Match->Team2Short]->FlagURL.'" alt="'.$Teams[$Match->Team2Short]->NameLong.'" /></td>
              <td align="right">'.$TippTeam1.'</td>
              <td align="center">:</td>
              <td align="left">'.$TippTeam2.'</td>
              <td align="right">'.$Match->ResTeam1Goals.'</td>
              <td align="center">:</td>
              <td align="left">'.$Match->ResTeam2Goals.'</td>
              <td align="center">'.$TippPunkte.' </td>
              ');
      }
      if ($counter>0)
      {
        echo('
</table>
');
        return true;
      }
    }
    
    public function OutputTeamInfoArea()
    {
    	
    }

      public function GetJavaScript()
    {
    	return '';
    }
  

  }
?>