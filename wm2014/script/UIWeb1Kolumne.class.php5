<?php

  /**
  Anzeigeklasse für die Kolumne

  Jan. 06, Philipp Crocoll

  **/

  require_once("KolumneEintrag.db.class.php5");


  class CUIWeb1Kolumne extends CUIWeb1Component
  {
    protected $Eintraege = NULL;

    private function OutputKurz()
    {
      $counter = 0;
      foreach ($this->Eintraege as $Eintrag)
      {
        //es werden zwei Einträge ausgegeben (es sei denn, es wurden heute mehr als zwei Einträge
        //erstellt, dann werden diese alle ausgegeben.
        $counter++;
        if (($counter>2) && ($Eintrag->Datum != date("Y-m-d",time())))
        {
           //es wird nicht alles angezeigt -> auf die restlichen verlinken:
           echo('<a href="kolumne.php5#Beitrag'.$Eintrag->ID.'">alle Einträge anzeigen...</a><br />');

           break;
        }


     echo '

<div><i>'.$Eintrag->Datum.'</i> <span class="BoxTitle">'.$Eintrag->Titel.'</span><br />
'.$Eintrag->ErsterAbschnitt.'
<br />';
if ($Eintrag->AnzahlAbschnitte()>1)
  echo('<a href="kolumne.php5#Beitrag'.$Eintrag->ID.'">weiter...</a><br />');
echo '
<br />
 </div>
     ';
      }

      if (FORUM_AVAILABLE)
      {
      echo('<br /><br />Was ist deine Meinung? <a target="blank" href="forum/">Im Forum kannst du diskutieren!</a>');
      }
    }

    private function OutputLang()
    {
	  if (FORUM_AVAILABLE)
      {
		echo('<div class="Box">Was ist deine Meinung? <a target="blank" href="forum/">Im Forum kannst du diskutieren!</a></div><br />');
	  }
      foreach ($this->Eintraege as $Eintrag)
      {
     echo '

<div class="Box">
<a id="Beitrag'.$Eintrag->ID.'"></a>
<i>'.$Eintrag->Datum.'</i> <span class="BoxTitle">'.$Eintrag->Titel.'</span><br />
'.$Eintrag->Text.'

 </div>
<br />
     ';
      }

    }
    protected function LadeEintraege()
    {
      $KolumneDB = new CKolumneEintragDB();
      $this->Eintraege = $KolumneDB->LiesAlle();
      if (count($this->Eintraege)>0)
      foreach ($this->Eintraege as $Eintrag)
        $Eintrag->CleanText();
    }

    public function Output($bLang = false)
    {
      if ($this->Eintraege == NULL)
      {
        $this->LadeEintraege();
      }
      if (count($this->Eintraege) == 0)
        return false;
      if ($bLang)
        $this->OutputLang();
      else
        $this->OutputKurz();
      return true;

    }
  }
?>