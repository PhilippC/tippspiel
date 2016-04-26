<?php

require_once("UIWeb1Classes.inc.php5");
require_once("UIWeb1TippErgVergleichGrafik.class.php5");

class CUIWeb1TippErgVergleich extends CUIWeb1Component
{
  public function Output($VergleichListe)
  {
   //Teams laden, um anzeigen zu können:
   $TeamDB = new CTeamDB();
   $Teams = $TeamDB->LiesAlle();
   $BenutzerVerw = new CBenutzerVerw();
   $AnzSpieler = count($BenutzerVerw->GetUserList(TIPPSPIEL_USERGROUP));

   if (count($VergleichListe)==0)
     $this->parent->OutputMessageBox("Statistik noch nicht vorhanden!","Bisher wurde noch kein Spiel abgeschlossen! Erst dann kann die Statistik erstellt werden!");
   else
   {
      echo('<table border="0" width="100%" cellspacing="0" cellpadding="1">');
      echo('<tr>');
      echo('<th>Datum</th><th>Zeit</th><th colspan="5">Mannschaften</th><th colspan="3">Ergebnis</th><th width="328">Tipp/Erg-Vergleich</th>');
      echo('</tr>');

     $TippErgVerglGrafik = new CUIWeb1TippErgVergleichGrafik();
     $counter=0;
     foreach ($VergleichListe as $Vergleich)
     {
        $Match = $Vergleich->Spiel;
        $TippErgVerglGrafik->TippErgVergleichEintrag = $Vergleich;
        $TippErgVerglGrafik->AnzSpieler = $AnzSpieler;
        if ($counter++%2 == 0)
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
              ');

         echo('<td align="right">'.$Match->ResTeam1Goals.'</td>');
         echo('<td align="center">:</td>');
         echo('<td align="left">'.$Match->ResTeam2Goals.'</td>');
         echo('<td align="center"><br />');
         $TippErgVerglGrafik->Output();

         echo('<br /></td>');
       echo("</tr>");
     }
       echo("</table>");

   }

  }



}

?>