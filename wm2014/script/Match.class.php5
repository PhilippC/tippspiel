<?php
class CMatch
{

  protected $m_MatchNr;
  protected $m_Team1Type;
  protected $m_Team2Type;
  protected $m_Team1Short;
  protected $m_Team2Short;
  protected $m_MatchDate;
  protected $m_StartTime;
  protected $m_MatchType;
  protected $m_ResTeam1Goals;
  protected $m_ResTeam2Goals;
  protected $m_MatchSortId;
  protected $m_Group;

  public function __construct($MatchNr=NULL, $Team1Type=NULL, $Team2Type=NULL, $Team1Short=NULL, $Team2Short=NULL, $MatchDate=NULL, $StartTime=NULL, $MatchType=NULL, $ResTeam1Goals=NULL, $ResTeam2Goals=NULL, $MatchSortId = NULL, $Group=NULL)
  {

    $this->m_MatchNr = $MatchNr;
    $this->m_Team1Type = $Team1Type;
    $this->m_Team2Type = $Team2Type;
    $this->m_Team1Short = $Team1Short;
    $this->m_Team2Short = $Team2Short;
    $this->m_MatchDate = $MatchDate;
    $this->m_StartTime = $StartTime;
    $this->m_MatchType = $MatchType;
    $this->m_ResTeam1Goals = $ResTeam1Goals;
    $this->m_ResTeam2Goals = $ResTeam2Goals;
    $this->m_MatchSortId = $MatchSortId;
    $this->m_Group = $Group;

  }

  public function MatchTypeToString()
  {
    $res = "";
    switch ($this->m_MatchType)
    {
      case 32: $res = "Gruppenspiel";break;
      case 16: $res = "Achtelfinale";break;
      case 8: $res = "Viertelfinale";break;
      case 4: $res = "Halbfinale";break;
      case 3: $res = "Spiel um Platz 3";break;
      case 2: $res = "Finale";break;

    }
    return $res;
  }

  protected function TeamTypeToString($TeamType)
  {
    $res = "";
    $FirstChar = substr($TeamType,0,1);
    if (($FirstChar == "E") || ($FirstChar == "Z"))
    {
      if ($FirstChar == "E")
        $res = "Erster";
      else
        $res = "Zweiter";
      $res .= " Gruppe ".substr($TeamType,1,1);
    }
    else
    {
      if ($FirstChar == "V")
        $res = "Verlierer Spiel ";
      else if ($FirstChar == "W")
        $res = "Gewinner Spiel ";
      else
	  {
        throw new Exception("Ungültiger TeamType ('$TeamType') übergeben! SpielNr: $this->m_MatchNr. Bitte Kuerzel prüfen.");
	  }
      $res.=substr($TeamType,1,2);
    }
    return $res;
  }

  public function __get($FieldName)
  {
    switch($FieldName)
    {

      case "MatchNr": return $this->m_MatchNr;
      case "Team1Type": return $this->m_Team1Type;
      case "Team2Type": return $this->m_Team2Type;
      case "Team1Short": return $this->m_Team1Short;
      case "Team2Short": return $this->m_Team2Short;
      case "MatchDate": return $this->m_MatchDate;
      case "StartTime": return $this->m_StartTime;
      case "MatchType": return $this->m_MatchType;
      case "ResTeam1Goals": return $this->m_ResTeam1Goals;
      case "ResTeam2Goals": return $this->m_ResTeam2Goals;
      case "MatchSortId": return $this->m_MatchSortId;
      case "Group": return $this->m_Group;

      case "strMatchType": return $this->MatchTypeToString();
      case "strTeam1Type": return $this->TeamTypeToString($this->m_Team1Type);
      case "strTeam2Type": return $this->TeamTypeToString($this->m_Team2Type);

      case "isAbgeschlossen": return $this->IsAbgeschlossen();
      case "isTippbar": return $this->IsTippbar();
      case "isAktuell": return $this->IsAktuell();
      case "hatBegonnen": return $this->HatBegonnen();

      default: throw new Exception("Eine Eigenschaft $FieldName existiert nicht!");
    }
  }

  public function __set($FieldName,$Value)
  {
    switch($FieldName)
    {
      case "MatchNr": { $this->m_MatchNr = $Value; break; }
      case "Team1Type": { $this->m_Team1Type = $Value; break; }
      case "Team2Type": { $this->m_Team2Type = $Value; break; }
      case "Team1Short": { $this->m_Team1Short = $Value; break; }
      case "Team2Short": { $this->m_Team2Short = $Value; break; }
      case "MatchDate": { $this->m_MatchDate = $Value; break; }
      case "StartTime": { $this->m_StartTime = $Value; break; }
      case "MatchType": { $this->m_MatchType = $Value; break; }
      case "ResTeam1Goals": { $this->m_ResTeam1Goals = $Value; break; }
      case "ResTeam2Goals": { $this->m_ResTeam2Goals = $Value; break; }
      case "MatchSortId": { $this->m_MatchSortId = $Value; break; }
      case "Group": { $this->m_Group = $Value; break; }
      default: throw new Exception("Eine Eigenschaft $FieldName existiert nicht!");
    }
  }

  protected function IsAbgeschlossen()
  {
    return ($this->m_ResTeam1Goals!=NULL);
  }

  protected function IsTippbar()
  {
    $TippAbstand = 45*60; //3/4h vor Spielbeginn ist Ende!
    if ($this->IsAbgeschlossen()) return false;
    $MatchStart = strtotime($this->m_MatchDate." ".$this->m_StartTime);
    $Now = time();
    return ($MatchStart-$Now>$TippAbstand);
  }

  protected function HatBegonnen()
  {

    $MatchStart = strtotime($this->m_MatchDate." ".$this->m_StartTime);
    $Now = time();
    $res = ($MatchStart-$Now)<0;
    return $res;
  }

  protected function IsAktuell()
  {
      $MatchStart = strtotime($this->m_MatchDate." ".$this->m_StartTime);

      $strGestern = date("D, d M Y",time());
      $Gestern = strtotime($strGestern)-24*3600;
      $Uebermorgen = $Gestern+3*24*3600;
      return ($MatchStart>$Gestern) && ($MatchStart<$Uebermorgen);
  }


}
?>