<?php
class CTeam
{

  protected $m_ID;
  protected $m_NameLong;
  protected $m_NameShort;
  protected $m_FlagURL;
  protected $m_GroupNr;

  public function __construct($ID=NULL, $NameLong=NULL, $NameShort=NULL, $FlagURL=NULL, $GroupNr=NULL)
  {

    $this->m_ID = $ID;
    $this->m_NameLong = $NameLong;
    $this->m_NameShort = $NameShort;
    $this->m_FlagURL = $FlagURL;
    $this->m_GroupNr = $GroupNr;
  }


  public function __get($FieldName)
  {
    switch($FieldName)
    {

      case "ID": return $this->m_ID;
      case "NameLong": return $this->m_NameLong;
      case "NameShort": return $this->m_NameShort;
      case "FlagURL": return $this->m_FlagURL;
      case "GroupNr": return $this->m_GroupNr;
      default: throw new Exception("Eine Eigenschaft $FieldName existiert nicht!");
    }
  }

  public function __set($FieldName,$Value)
  {
    switch($FieldName)
    {
      case "ID": { $this->m_ID = $Value; break; }
      case "NameLong": { $this->m_NameLong = $Value; break; }
      case "NameShort": { $this->m_NameShort = $Value; break; }
      case "FlagURL": { $this->m_FlagURL = $Value; break; }
      case "GroupNr": { $this->m_GroupNr = $Value; break; }
      default: throw new Exception("Eine Eigenschaft $FieldName existiert nicht!");
    }
  }
}

?>