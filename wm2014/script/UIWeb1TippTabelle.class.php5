<?php

  /**
  Anzeigeklasse für die Tabelle des Tippspiels

  Parameter der Output-Methode:
   - TippTabelle: Objekt der TippTabelle
   - Size:
      0 = winzig
      1 = klein
      2 = groß
   - ShowDetails:
      true, wenn Tendenz/Ergebnis Spalten angezeigt werden sollen


  Jan. 06, Philipp Crocoll

  **/

  require_once("TippErgebnisse.class.php5");



  class CUIWeb1TippTabelle extends CUIWeb1Component
  {
    private $m_TippTabelle=NULL;
	private $m_TippTabelleVergleich=NULL;

    public function __construct($TippTabelle,$TippTabelleVergleich=NULL)
    {
      $this->m_TippTabelle = $TippTabelle;
	  $this->m_TippTabelleVergleich = $TippTabelleVergleich;
    }


    public function Output( $Size=0, $ShowDetails=false)
    {
		
	
      if (($this->m_TippTabelle == NULL)
          || (count($this->m_TippTabelle) == 0))
      {

        return false;
      }
      switch ($Size)
      {

        case 1:
         echo '
 <table border="0" cellspacing="1" cellpadding="4" class="SmallTippTable">
<tr>
<th>Platz</th>
<th>Spielername</th>
<th>Punkte</th>
</tr>
';
$counter=0;
foreach ($this->m_TippTabelle as $Eintrag)
{


if (($counter++%2 == 0) || (count($this->m_TippTabelle)<4))
  echo '<tr class="EvenRow">';
else
  echo '<tr class="OddRow">';
echo'
<td>'.$Eintrag->iPlatz.'</td>
<td>'.$Eintrag->BenutzerName.'</td>
<td align="center">'.$Eintrag->iPunkte.'</td>
</tr>';
}
echo'</table>';
	break;
         case 2:
         echo '
<table border="0" width="100%" cellspacing="0" cellpadding="0" ><tr>
<td>&nbsp;</td>
<td width="440">

<table border="0" cellspacing="1" cellpadding="4" align="center">
<tr>
<th>Platz</th>
<th>Spielername</th>
<th>S</th>
<th>T</th>
<th>D</th>
<th>E</th>
<th>Punkte</th>
</tr>';
$counter=0;
foreach ($this->m_TippTabelle as $Eintrag)
{

$platzDelta = "";
if ($this->m_TippTabelleVergleich != NULL)
{
	$platzVorher = CTippErgebnisse::FindeEintrag($Eintrag->BenutzerName, $this->m_TippTabelleVergleich)->iPlatz;
	$platzDelta = " <font color=\"#ccc\">(0)</font>";
	if ($Eintrag->iPlatz > $platzVorher)
		$platzDelta = " <font color=\"#c44\">(-".($Eintrag->iPlatz - $platzVorher).")</font>";
	if ($Eintrag->iPlatz < $platzVorher)
		$platzDelta = " <font color=\"#0a0\">(+".($platzVorher - $Eintrag->iPlatz).")</font>";
		
}
if (($counter++%2 == 0) || (count($this->m_TippTabelle)<4))
  echo '<tr class="EvenRow">';
else
  echo '<tr class="OddRow">';
echo'
<td width="80">'.$Eintrag->iPlatz.$platzDelta.'</td>
<td width="300">'.$Eintrag->BenutzerName.'</td>
<td>'.$Eintrag->iAnzSpieleGetippt.'</td>
<td>'.$Eintrag->iAnzTendenz.'</td>
<td>'.$Eintrag->iAnzDifferenz.'</td>
<td>'.$Eintrag->iAnzErgebnis.'</td>
<td align="right">'.$Eintrag->iPunkte.'</td>
</tr>
';
}
echo'
</table>
<div id="FootNote">
S: Anzahl gewerteter Spiele<br />
T: Anzahl richtig getippter Tendenzen<br />
D: Anzahl richtig getippter Tor-Differenzen<br />
E: Anzahl richtig getippter Ergebnisse';
if ($this->m_TippTabelleVergleich != NULL)
echo'<br />Die Zahl in Klammern hinter dem Platz gibt die Veränderung seit dem letzten Spieltag an. "(+2)" bedeutet eine Verbesserung um 2 Plätze.';
echo'
</div>
</td><td>&nbsp;</td></tr></table>';


	break;

      }

     return true;
    }
  }
?>