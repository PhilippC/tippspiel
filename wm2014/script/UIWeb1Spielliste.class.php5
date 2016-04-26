<?php

class CUIWeb1Spielliste extends CUIWeb1Component
{
  public function GetOutputString($MatchListe)
  {
   $OutputString = "";

   if (count($MatchListe)==0) $OutputString.="<p  align=\"center\">--- zur Zeit keine Spiele ---</p>";
   else
   {
     $OutputString.=('<table border="0" class="SpielListe" align="center"> ');
     foreach ($MatchListe as $Match)
     {
       $OutputString.=("<tr>");
       $OutputString.="<td>".$Match->Team1Short."</td><td>-</td><td>".
            $Match->Team2Short."</td>".
            "<td>".date("d.m.y", strtotime($Match->MatchDate))." ".
            date("H:i",strtotime($Match->StartTime))."</td>";
       if ($Match->isAbgeschlossen)
         $OutputString.=("<td>$Match->ResTeam1Goals:$Match->ResTeam2Goals</td>");
       else
         $OutputString.=("<td>-:-</td>");
       $OutputString.=("</tr>");
     }
     $OutputString.=("</table>");

     return $OutputString;

   }

  }

  public function Output($MatchListe)
  {
    echo($this->GetOutputString($MatchListe));


  }

  public function OutputAdmin($MatchListe, $ErrorMsgs=NULL)
  {

   if (count($MatchListe)==0) echo("<p  align=\"center\">--- keine Spiele in dieser Liste ---</p>");
   else
   {
     echo('<table border="0" cellspacing="0" cellpadding="1" align="center">
     <th colspan="3">Mannschaften</th>

     <th>Datum/Zeit</th>
     <th colspan="3">Ergebnis</th>
     <th>Status</th>');

     $counter=0;
     foreach ($MatchListe as $Match)
     {
        if ($counter++%2 == 0)
          echo('<tr class="OddRow">');
        else
          echo('<tr class="EvenRow">');

        if (isset($ErrorMsgs[$Match->MatchNr]))
          $StatusHTML = '<img src="bilder/point_yellow.jpg" alt="'.$ErrorMsgs[$Match->MatchNr].'" />';
        else
        if ($Match->isAbgeschlossen)
          $StatusHTML = '<img src="bilder/point_green.jpg" alt="Ergebnis gespeichert" />';
        else
	  $StatusHTML = '<img src="bilder/point_gray.jpg" alt="noch kein Ergebnis gespeichert" />';

       echo( "<td align=\"center\">".$Match->Team1Short."</td><td>-</td>
              <td align=\"center\">".
            $Match->Team2Short."</td>".
            "<td>".date("d.m.y", strtotime($Match->MatchDate))." ".
            date("H:i",strtotime($Match->StartTime)))."</td>";
         echo('<td><input size="1" value="'.$Match->ResTeam1Goals.'" name="Match_'.$Match->MatchNr.'_1" /></td>');
         echo('<td>:</td>');
         echo('<td><input size="1" value="'.$Match->ResTeam2Goals.'" name="Match_'.$Match->MatchNr.'_2"  />
         <input type="hidden" value="wasSet" name="Match_'.$Match->MatchNr.'_3"  /></td>');
         echo('<td align="center">'.$StatusHTML.'</td>');
       echo("</tr>");
     }
       echo("</table>");

   }

  }

  public function OutputAdminTeams($MatchListe, $ErrorMsgs=NULL)
  {

   if (count($MatchListe)==0) echo("<p  align=\"center\">--- keine Spiele in dieser Liste ---</p>");
   else
   {
     echo('<table border="0" cellspacing="1" cellpadding="4" align="center">
     <th colspan="3">Begegnung</th>

     <th>Datum/Zeit</th>
     <th colspan="3">Mannschaften (Kürzel)</th>
     <th>Status</th>');

     $counter=0;
     foreach ($MatchListe as $Match)
     {
        if ($Match->MatchType == 32)
          continue;

        if ($counter++%2 == 0)
          echo('<tr class="OddRow">');
        else
          echo('<tr class="EvenRow">');

        if (isset($ErrorMsgs[$Match->MatchNr]))
          $StatusHTML = '<img src="bilder/point_yellow.jpg" alt="'.$ErrorMsgs[$Match->MatchNr].'" />';
        else
        if ($Match->Team1Short != "")
          $StatusHTML = '<img src="bilder/point_green.jpg" alt="Team gespeichert" />';
        else
	  $StatusHTML = '<img src="bilder/point_gray.jpg" alt="noch kein Team gespeichert" />';

       echo( "<td align=\"center\">".$Match->strTeam1Type."</td><td>-</td><td align=\"center\">".
            $Match->strTeam2Type."</td>".
            "<td>".date("d.m.y", strtotime($Match->MatchDate))." ".
            date("H:i",strtotime($Match->StartTime)))."</td>";
         echo('<td><input size="4" value="'.$Match->Team1Short.'" name="Match_'.$Match->MatchNr.'_1_TEAMSHORT" /></td>');
         echo('<td>:</td>');
         echo('<td><input size="4" value="'.$Match->Team2Short.'" name="Match_'.$Match->MatchNr.'_2_TEAMSHORT"  />
         <input type="hidden" value="wasSet" name="Match_'.$Match->MatchNr.'_3_TEAMSHORT"  /></td>');
         echo('<td align="center">'.$StatusHTML.'</td>');
       echo("</tr>");
     }
       echo("</table>");

   }
  }


}

?>