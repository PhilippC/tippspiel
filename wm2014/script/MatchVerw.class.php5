<?php

require_once("Match.class.php5");
require_once("Match.db.class.php5");

require_once( dirname(__FILE__).'/../tippconfig.php5' );

define('MATCH_FILTER_ALLE', 1);
define('MATCH_FILTER_ABGESCHL',2);  //Filter für alle Spiele, für die ein Ergebnis vorliegt
define('MATCH_FILTER_TIPPBAR',3);   //Filter für alle Spiele, die noch getippt werden können
define('MATCH_FILTER_AKTUELL',4);   //Filter für alle Spiele, die gestern, morgen oder heute starten
define('MATCH_FILTER_HATBEGONNEN',6);   //Filter für alle Spiele, die bereits begonnen haben oder beendet sind
define('MATCH_FILTER_HATBEGONNEN_OHNEERG',5);   //Filter für alle Spiele, die bereits begonnen haben oder beendet sind, für die noch kein Ergebnis eingegeben ist

class CMatchVerw
{
  protected static $m_Matches = NULL;
  protected static $m_Teams = NULL;
  protected $m_OrderByClause = "MatchDate, StartTime, MatchNr";

  public function SetOrderByClause($orderby)
  {
    $this->m_OrderByClause = $orderby;
  }

  public function HoleMatches($Filter = MATCH_FILTER_ALLE )
  {
    $this->LadeMatches();
    if ($Filter == MATCH_FILTER_ALLE)
      return self::$m_Matches;

    if (count(self::$m_Matches) == 0)
      return self::$m_Matches;

    $res = array();
    foreach(self::$m_Matches as $key=>$Match)
    {

      switch($Filter)
      {
        case MATCH_FILTER_ABGESCHL:
        {
          $Gefiltert = !$Match->isAbgeschlossen;
          break;
        }
        case MATCH_FILTER_TIPPBAR:
        {
          $Gefiltert = !$Match->isTippbar;
          break;
        }
        case MATCH_FILTER_AKTUELL:
        {
          $Gefiltert = !$Match->isAktuell;
          break;
        }
        case MATCH_FILTER_HATBEGONNEN_OHNEERG:
        {
          $Gefiltert = !$Match->hatBegonnen || $Match->isAbgeschlossen;
          break;
        }
     	case MATCH_FILTER_HATBEGONNEN:
        {
          $Gefiltert = (!$Match->hatBegonnen) && (!$Match->isAbgeschlossen);
          break;
        }
        default: throw new Exception("Fehlerhafter Filter für HoleMatches()");
      }
      if (!$Gefiltert)
      {
        $res[$key]=$Match;
      }
    }
    return $res;
  }

  public function HoleTeamMatches($TeamShort)
  {
    $this->LadeMatches();

    $ResMatches = array();

    foreach (self::$m_Matches as $Match)
    {
      if (($Match->Team1Short == $TeamShort)
        || ($Match->Team2Short == $TeamShort))
        $ResMatches[$Match->MatchNr] = $Match;
    }

    return $ResMatches;

  }

  protected function LadeMatches()
  {
    if (self::$m_Matches != NULL) return;
    $MatchDB = new CMatchDB();
    $MatchDB->OrderByClause = $this->m_OrderByClause;
    self::$m_Matches = $MatchDB->LiesAlle();
  }

  //Mehrere Matches speichern:
    //Liefert ein Array mit den Fehlermeldungen zum jeweiligen Match zurück.
    public function SpeichereErgebnisse($Matches)
    {
      $MatchDB = new CMatchDB();

      $Error = array();
      foreach ($Matches as $key=>$Match)
      {
        //Löschen, also beides leer setzen ist erlaubt:
        if (($Match->ResTeam1Goals != "") && ($Match->ResTeam2Goals != ""))
        {
          //prüfen, ob die Eingaben gültig (numerisch und größer 0) sind :
          //Diese Prüfung ließe auch Kommazahlen zu, dann fliegt das Skript aber später auf die Nase...
          $Valid = is_numeric($Match->ResTeam1Goals) && is_numeric($Match->ResTeam2Goals)
                   && ($Match->ResTeam1Goals>=0) && ($Match->ResTeam2Goals>=0);
          //Falls nicht: Fehler
          if (!$Valid)
          {
            $Error[$key] = "Ungültige Eingabe";
          }
        }

        if (!isset($Error[$key]))
        {
          //ansonsten speichern:
          try
          {
            $MatchDB->ErgaenzeMatchUmErgebnis($Match);
          }
          catch (BenutzerMsgException $e)
          {
            $Error[$key] = $e->getMessage();
          }
          catch (Exception $e)
          {
            $Error[$key] = "Fehler beim Speichern in der Datenbank";
          }
        }
      }
      return $Error;
    }

    public function SpeichereTeams($Matches)
    {
      if (self::$m_Teams == NULL)
      {
        $TeamDB = new CTeamDB();
        self::$m_Teams = $TeamDB->LiesAlle();
      }


      $MatchDB = new CMatchDB();

      $Error = array();
      foreach ($Matches as $key=>$Match)
      {
        //Löschen, also beides leer setzen ist erlaubt:
        if (($Match->Team1Short != "") && ($Match->Team1Short != ""))
        {
          //wenn aber eins gefüllt ist, müssen beides gültige Teams sein:
          if (!isset(self::$m_Teams[$Match->Team1Short]))
            $Error[$key] = "Ungültige Eingabe: Team1 ($Match->Team1Short) nicht gefunden!";
          if (!isset(self::$m_Teams[$Match->Team2Short]))
            $Error[$key] = "Ungültige Eingabe: Team2 ($Match->Team2Short) nicht gefunden!";
        }
        //Kein Fehler?
        if (!isset($Error[$key]))
        {
          //ja, also speichern:
          try
          {
            $MatchDB->ErgaenzeMatchUmTeams($Match);
          }
          catch (BenutzerMsgException $e)
          {
            $Error[$key] = $e->getMessage();
          }
          catch (Exception $e)
          {
            $Error[$key] = "Fehler beim Speichern in der Datenbank";
          }
        }
      }
      return $Error;
    }

}

?>