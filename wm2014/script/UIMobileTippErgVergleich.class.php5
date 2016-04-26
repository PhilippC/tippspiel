<?php

require_once("UIWeb1Classes.inc.php5");
require_once("UIWeb1TippErgVergleichGrafik.class.php5");

class CUIMobileTippErgVergleich extends CUIWeb1Component
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

	echo(date("d.m.y",strtotime($Match->MatchDate)).' '.date("H:i",strtotime($Match->StartTime)).' '
				.'<img src="bilder/flaggen/'.$Teams[$Match->Team1Short]->FlagURL.'" alt="'.$Teams[$Match->Team1Short]->NameLong.'" />'
			 .$Match->Team1Short
			 .'-'
             .$Match->Team2Short
             .'<img src="bilder/flaggen/'.$Teams[$Match->Team2Short]->FlagURL.'" alt="'.$Teams[$Match->Team2Short]->NameLong.'" />'         
              );

         if ($Match->ResTeam1Goals != NULL)
         {
         	echo(' ');
         	echo($Match->ResTeam1Goals);
         	echo(':');
         	echo($Match->ResTeam2Goals);
         }
         echo('<br />');
         $TippErgVerglGrafik->Output();

         echo('<br /></td>');
       echo("</tr>");
     }
       echo("</table>");

   }

  }



}

?>