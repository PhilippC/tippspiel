<?php

class CKolumneEintrag
{

  protected $m_ID;
  protected $m_Datum;
  protected $m_Titel;
  protected $m_Text;

  public function __construct($ID=NULL, $Datum=NULL, $Titel=NULL, $Text=NULL)
  {

    $this->m_ID = $ID;
    $this->m_Datum = $Datum;
    if ($Datum == NULL)
      $this->m_Datum = date("Y-m-d",time());
    $this->m_Titel = $Titel;
    $this->m_Text = $Text;
  }

  //Löscht <p></p>
  public function CleanText()
  {
    $this->m_Text = str_replace('<p></p>','',$this->m_Text);
  }

  //Zählt Anzahl der Abschnitte:
  public function AnzahlAbschnitte()
  {
    $paragraphs = explode("</p>", $this->m_Text);
    return count($paragraphs)-1;
  }


  public function __get($FieldName)
  {
    switch($FieldName)
    {

      case "ID": return $this->m_ID;
      case "Datum": return $this->m_Datum;
      case "Titel": return $this->m_Titel;
      case "Text": return $this->m_Text;
      case "ErsterAbschnitt": return $this->GetErsterAbschnitt();
      default: throw new Exception("Eine Eigenschaft $FieldName existiert nicht!");
    }
  }

  protected function GetErsterAbschnitt()
  {
    $paragraphs = explode("</p>", $this->m_Text);
    if (count($paragraphs) == 0)
      return "";
    $spos=strpos($paragraphs[0], "<p>");
    if (is_int($spos))
    {
      return substr($paragraphs[0],$spos+strlen("<p>"));

    }
    else return "";


  }

  public function __set($FieldName,$Value)
  {
    switch($FieldName)
    {
      case "ID": { $this->m_ID = $Value; break; }
      case "Datum": { $this->m_Datum = $Value; break; }
      case "Titel": { $this->m_Titel = $Value; break; }
      case "Text": { $this->m_Text = $Value; break; }
      default: throw new Exception("Eine Eigenschaft $FieldName existiert nicht!");
    }
  }
}

?>