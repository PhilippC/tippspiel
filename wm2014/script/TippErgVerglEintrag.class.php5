<?php

class CTippErgVergl
{
  //$iAnzTippsUnbekannt ist > 0, wenn das Ergebnis noch nicht bekannt ist
  public $iAnzTippsUnbekannt=0;
  //Wenn AnzFalsch>0 sind die anderen =0 und wenn mind. eins der anderen >0 ist ist AnzFalsch=0
  public $iAnzFalsch=0;
  public $iAnzErgebnis=0;
  public $iAnzDifferenz=0;
  public $iAnzTendenz=0;
  

  public function __get($Name)
  {
    switch ($Name)
    {
      case "iGesamt": return $this->iAnzFalsch+$this->iAnzErgebnis+$this->iAnzDifferenz+$this->iAnzTendenz+$this->iAnzTippsUnbekannt;
      default: throw new Exception("Eine Eigenschaft $Name existiert in CTippErgVergl nicht!");
    }
  }
}

class CTippErgVerglEintrag
{
  protected $m_Spiel;

  public $SiegTeam1Vergl=NULL;
  public $UnentschVergl=NULL;
  public $SiegTeam2Vergl=NULL;

  public function __get($Name)
  {
    switch ($Name)
    {
      case "Spiel": return $this->m_Spiel;
      default: throw new Exception("Eine Eigenschaft $Name existiert in Klasse CTippErgVerglEintrag nicht!");
    }
  }

  public function GetVerglByErgZahl($ErgZahl)
  {
    switch ($ErgZahl)
    {
      case -1: return $this->SiegTeam1Vergl;
      case  0: return $this->UnentschVergl;
      case  1: return $this->SiegTeam2Vergl;
    }
  }

  public function __construct($Spiel)
  {
    $this->m_Spiel = $Spiel;

    $this->SiegTeam1Vergl=new CTippErgVergl;
    $this->UnentschVergl=new CTippErgVergl;
    $this->SiegTeam2Vergl=new CTippErgVergl;
  }



}

?>