<?php

class CUIWeb1TippErgVergleichGrafik
{
  public $TippErgVergleichEintrag=NULL;

  public $AnzSpieler = 0;
  public $MaxBalkenBreite = 100;

  public function Output()
  {
    if ($this->TippErgVergleichEintrag == NULL)
      return;

    $TEVEintrag = $this->TippErgVergleichEintrag;
    $Match = $TEVEintrag->Spiel;

    echo('<table border="0" class="Box" style="width:320px">');
    echo('<tr>
           <td width="170">
              <span  style="color:#888888">Tipps auf Sieg von '.$Match->Team1Short.':
           </td>
           <td width="25" align="left">
            '.$TEVEintrag->SiegTeam1Vergl->iGesamt.'
           </td>
            <td>');

            $this->OutputBalken($TEVEintrag->SiegTeam1Vergl);

            echo ('
            </td>

          </tr>');
    echo('<tr>

            <td>
              <span  style="color:#888888">Tipps auf Unentschieden: </td>
            <td>
             '.$TEVEintrag->UnentschVergl->iGesamt.'
            </td>
            <td>');

            $this->OutputBalken($TEVEintrag->UnentschVergl);

            echo ('
            </td>

          </tr>');
    echo('<tr>
            <td>
              <span  style="color:#888888">Tipps auf Sieg von '.$Match->Team2Short.':
            </td>
            <td>'
            .$TEVEintrag->SiegTeam2Vergl->iGesamt.'
            </td>
            <td>');

            $this->OutputBalken($TEVEintrag->SiegTeam2Vergl);

            echo ('
            </td>

          </tr>');
    echo('</table>');
  }

  protected function OutputBalken($TippErgVergl)
  {
    if ($TippErgVergl->iGesamt > 0)
    {

      echo ('<table border="0" cellspacing="0" cellpadding="0"><tr><td>');

	  if ($TippErgVergl->iAnzTippsUnbekannt > 0)
	  {
	    $this->OutputSingleBalken($TippErgVergl->iAnzTippsUnbekannt, "#77ccFF", $TippErgVergl->iAnzTippsUnbekannt." Tipps");
	  }
	  else
	  {
      if ($TippErgVergl->iAnzFalsch > 0)
	  {
        $this->OutputSingleBalken($TippErgVergl->iAnzFalsch, "#FFCC33", $TippErgVergl->iAnzFalsch." falsche Tipps");
	  }  
      else
      {
        $Titel = "".$TippErgVergl->iAnzTendenz."x richtige Tendenz, "
                .$TippErgVergl->iAnzDifferenz."x richtige Tor-Differenz, "
	       .$TippErgVergl->iAnzErgebnis."x richtiges Ergebnis ";
        $this->OutputSingleBalken($TippErgVergl->iAnzTendenz, "#448844",$Titel);
        $this->OutputSingleBalken($TippErgVergl->iAnzDifferenz, "#22CC22",$Titel);
        $this->OutputSingleBalken($TippErgVergl->iAnzErgebnis, "#00FF00",$Titel);
      }
	  }

      echo('</td></tr></table>');
    }
  }
  protected function OutputSingleBalken($AnzTipps, $Farbe, $Titel = "")
  {

    if (($AnzTipps == 0) || ($this->AnzSpieler == 0)) return;

    $Breite = round($this->MaxBalkenBreite*$AnzTipps/$this->AnzSpieler);
    echo('
              <td style="width:'.$Breite.'px;height:16px; background-color:'.$Farbe.'" title="'.$Titel .'">
	     </td>
    ');

  }
}

?>