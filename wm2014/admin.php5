<?php
  require("./script/TippspielAdminLogin.php5");

  require_once("./script/UIWeb1Classes.inc.php5");

  require_once("./script/Tipp.db.class.php5");
  require_once("./script/Team.db.class.php5");
  require_once("./script/Match.db.class.php5");
  require_once("./script/TippVerw.class.php5");
  require_once("./script/MatchVerw.class.php5");
  require_once("./script/StatistikVerw.class.php5");

  require_once('./script/UIFactory.inc.php5');
 $TippSpielUI = createWebUi1();

    $strAktion = "";
  if (isset($_POST["Aktion"]))
    $strAktion = $_POST["Aktion"];
  if (isset($_GET["Aktion"]))
    $strAktion = $_GET["Aktion"];
 

  $TippSpielUI->OutputHeader("Administrationsbereich");

  $ErrorMsgs = array();
  $ErrorMsgsTeams = array();

  if (($strAktion == "Korrigieren") || ($strAktion=="Eingeben"))
  {
    $Matches = array();
    //Daten einlesen
    foreach ($_POST as $key=>$ToreErg)
    {


      $KeyParts = explode("_", $key);
      if ($KeyParts[0] == "Match")
      {
        if ($ToreErg == "") continue;

        $MatchNr = $KeyParts[1];
        $TeamNr = $KeyParts[2];
        if (!isset($Matches[$MatchNr]))
        {
          $Matches[$MatchNr] = new CMatch($MatchNr);
          }

        if ($TeamNr == 1)
          $Matches[$MatchNr]->ResTeam1Goals = $ToreErg;
        if ($TeamNr == 2)
          $Matches[$MatchNr]->ResTeam2Goals = $ToreErg;
      }
    }
    $MatchVerw = new CMatchVerw();
    $ErrorMsgs = $MatchVerw->SpeichereErgebnisse($Matches);
    
    $TippTabellenCache = new CTippTabellenCacheDB();
    $TippTabellenCache->LeereCache(0);//das ist am Sichersten. Auch bei Eintragen kann es passieren, dass ältere Spiele eingetragen werden als maxMatchSortId

    if (count($ErrorMsgs)>0) echo('<div class="FehlerMeldung">Achtung! Es konnten nicht alle Eingaben gespeichert werden!</div>
    Die gelben Punkte zeigen, wo ein Fehler aufgetreten ist. Bei diesen Spielen wurden die alten Ergebnisse beibehalten (falls welche existierten).<br>
    Wenn du mit der Maus auf den gelben Punkt f&auml;hrst, erscheint die jeweilige Fehlermeldung! ');
    else
      echo("Daten gespeichert!<br /><br />");

    //Statistik-Bilder aktualisieren:
    if ((!USE_DYNAMIC_STATIMAGES) && (ENABLE_STATIMAGES))
    {
      $StatVerw = new CStatistikVerw();
      echo("Statistik-Grafiken werden erzeugt...<br />");
      echo("Bei Timeout bitte Einstellungen in tippconfig.php5 ändern (STAT_FILTER_STEPS erhöhen oder notfalls USE_DYNAMIC_STATIMAGES
      auf true setzen.<br />");
      $StatVerw->UpdateAllImages();
      echo("Grafiken erzeugt.<br />");
    }


  }
  if ($strAktion == "TeamsSetzen")
  {
    $Matches = array();
    //Daten einlesen
    foreach ($_POST as $key=>$TeamShort)
    {
      $KeyParts = explode("_", $key);
      if ($KeyParts[0] == "Match")
      {
        if ($TeamShort == "") continue;

        $MatchNr = $KeyParts[1];
        $TeamNr = $KeyParts[2];
        if (!isset($Matches[$MatchNr]))
        {
          $Matches[$MatchNr] = new CMatch($MatchNr);
        }

        if ($TeamNr == 1)
          $Matches[$MatchNr]->Team1Short = $TeamShort;
        if ($TeamNr == 2)
          $Matches[$MatchNr]->Team2Short = $TeamShort;
      }
    }

    $MatchVerw = new CMatchVerw();
    $ErrorMsgsTeams = $MatchVerw->SpeichereTeams($Matches);


    if (count($ErrorMsgsTeams)>0) echo('<div class="FehlerMeldung">Achtung! Es konnten nicht alle Eingaben gespeichert werden!</div>
    Die gelben Punkte zeigen, wo ein Fehler aufgetreten ist. Bei diesen Spielen wurden die alten Ergebnisse beibehalten (falls welche existierten).<br>
    Wenn du mit der Maus auf den gelben Punkt f&auml;hrst, erscheint die jeweilige Fehlermeldung! ');

  }



  echo('<h2>Ergebnisse eingeben</h2>');

  $MatchVerw = new CMatchVerw();
  $Matches = $MatchVerw->HoleMatches(MATCH_FILTER_HATBEGONNEN_OHNEERG);

  $SpielListe = $TippSpielUI->CreateUIComponent("SpielListe");

  echo('<form action="admin.php5" method="post"><input type="hidden" name="Aktion" value="Eingeben"></input>');
  $SpielListe->OutputAdmin($Matches, $ErrorMsgs);

  echo('<p align="center"><input type="submit" value="Speichern"></input><br/>');
  if ((!USE_DYNAMIC_STATIMAGES) && (ENABLE_STATIMAGES)) echo("(Bitte etwas Geduld. Statistik-Grafiken müssen erzeugt werden.)");
  echo('</p></form>');

  echo('<h2>Ergebnisse korrigieren</h2>');
  $Matches = $MatchVerw->HoleMatches(MATCH_FILTER_ABGESCHL);

  $SpielListe = $TippSpielUI->CreateUIComponent("SpielListe");

  echo('<form action="admin.php5" method="post"><input type="hidden" name="Aktion" value="Korrigieren"></input>');
  $SpielListe->OutputAdmin($Matches, $ErrorMsgs);
  echo('<p align="center"><input type="submit" value="Speichern"></input><br/>');
  if ((!USE_DYNAMIC_STATIMAGES) && (ENABLE_STATIMAGES)) echo("(Bitte etwas Geduld. Statistik-Grafiken müssen erzeugt werden.)");
  echo('</p></form>');

  echo('<h2>Mannschaften für kommende Begegnungen setzen</h2>');
  if ($strAktion == "ShowAbbr")
  {
	$TeamDB = new CTeamDB();
	$TeamDB->orderByClause = "NameLong";
    $teams = $TeamDB->LiesAlle();
    foreach ($teams as $Team)
	{
		echo("$Team->NameLong: $Team->NameShort<br>");
	}

  }
  else echo('<a href="admin.php5?Aktion=ShowAbbr">Kürzel der Mannschaften anzeigen</a><br>');
  $Matches = $MatchVerw->HoleMatches(MATCH_FILTER_ALLE);

  echo('<form action="admin.php5" method="post"><input type="hidden" name="Aktion" value="TeamsSetzen"></input>');
  $SpielListe->OutputAdminTeams($Matches, $ErrorMsgsTeams);
  echo('<p align="center"><input type="submit" value="Speichern"></input></p></form>');
?>

<h2>Benutzerverwaltung</h2>
<a href="admin.php5?Aktion=AddAllToPhpBB">Alle Benutzer im phpBB-Forum eintragen</a>
<a href="admin.php5?Aktion=ShowMailAddresses">Alle Mail-Adressen anzeigen</a>
<?php
  if ($strAktion == "AddAllToPhpBB")
  {
    $BenutzerVerw = new CBenutzerVerw();
    $BenutzerVerw->AddGroupToPhpBB(TIPPSPIEL_USERGROUP);

  }
  if ($strAktion == "ShowMailAddresses")
  {
    $BenutzerVerw = new CBenutzerVerw();
    $BenutzerListe = $BenutzerVerw->GetUserList(TIPPSPIEL_USERGROUP);
	foreach ($BenutzerListe as $tippuser)
    {
      echo($tippuser->Mail.", ");
    }

  }
?>

<?php

$TippSpielUI->OutputFooter();
?>