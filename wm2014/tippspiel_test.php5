<?php
  require("./script/TippspielAdminLogin.php5");

  require_once("./script/UIWeb1Classes.inc.php5");

  require_once("./script/TippVerw.class.php5");
  require_once("./script/Team.db.class.php5");
  require_once("./script/Match.db.class.php5");

  $TippSpielUI = new CTippSpielUIWeb1();

  $TippSpielUI->OutputHeader("Anlegen von Test-Daten");

  $strAktion ="";
  if (isset($_POST["Aktion"]))
    $strAktion = $_POST["Aktion"];

  if ($strAktion == "")

  {

?>

Dieses Skript trägt zufällige Testdaten ein. So können z.B. die Server-Geschwindigkeit und auch
die Statistiken getestet werden, wenn viele Benutzer viele Tipps abgegeben haben.<br />
Da das Skript viele Datenbank-Einträge erstellt und bisher noch keine Löschen-Funktion anbietet,
sollte unbedingt vorher eine Sicherung erstellt werden!<br />

<?php
  }
  else if ($strAktion == "SpielerErstellen")
  {
    $AnzSpieler = $_POST["AnzSpieler"];
    $BenutzerVerw = new CBenutzerVerw();
    for ($SpielerCounter=1;$SpielerCounter<=$AnzSpieler;$SpielerCounter++)
    {
      $SpielerName = "Spieler$SpielerCounter";
      $SpielerMail = $SpielerName."@myhope.de";
      if ($BenutzerVerw->DoesUserExist($SpielerName))
      {
        echo("Spieler $SpielerName existiert bereits!<br />");
      }
      else
      {
        $BenutzerVerw->RegisterNewUser($SpielerName,$SpielerMail,"test",TIPPSPIEL_USERGROUP,false,false);
        echo("Spieler $SpielerName erzeugt. <br />");
      }
    }
  }
  else if ($strAktion == "TippsErstellen")
  {
    $AnzSpieler = $_POST["AnzSpieler"];
    $MatchType = $_POST["MatchType"];
    $UnentschErlaubt = (isset($_POST["UnentschErlaubt"]) && ($_POST["UnentschErlaubt"] == "on"));

    $Tipps = array();
    $BenutzerVerw = new CBenutzerVerw();
    $MatchVerw = new CMatchVerw();
    $Matches = $MatchVerw->HoleMatches();
    foreach ($Matches as $Match)
    {
      if ($Match->MatchType != $MatchType) continue;

      for ($SpielerCounter=1;$SpielerCounter<=$AnzSpieler;$SpielerCounter++)
      {
        $SpielerName = "Spieler$SpielerCounter";
        $TippIndex = count($Tipps);
        $Tipps[$TippIndex] = new CTipp($SpielerName, $Match->MatchNr);
        $ToreTeam1 = rand(0,3);
        $ToreTeam2 = rand(0,3);
        while ((!$UnentschErlaubt) && ($ToreTeam1 == ToreTeam2))
          $ToreTeam2 = rand(0,3);

        $Tipps[$TippIndex]->SetzeTipp($ToreTeam1, $ToreTeam2);
      }
    }
    $ErrorMsgs = CTippVerw::SpeichereTipps($Tipps);
    echo("Fehlermeldungen: <br /> <pre>");
    if (count($ErrorMsgs) == 0) echo("keine");
    else print_r($ErrorMsgs);
    echo("</pre>");
  }
  else if ($strAktion == "ErgErstellen")
  {
    $MatchType = $_POST["MatchType"];
    $UnentschErlaubt = (isset($_POST["UnentschErlaubt"]) && ($_POST["UnentschErlaubt"] == "on"));

    $MatchVerw = new CMatchVerw();
    $Matches = $MatchVerw->HoleMatches();
    foreach ($Matches as $key=>$Match)
    {
      if ($Match->MatchType != $MatchType)
      {
        unset($Matches[$key]);
        continue;
      }

      $ToreTeam1 = rand(0,3);
      $ToreTeam2 = rand(0,3);
      while ((!$UnentschErlaubt) && ($ToreTeam1 == $ToreTeam2))
        $ToreTeam2 = rand(0,3);

      $Matches[$Match->MatchNr]->ResTeam1Goals = $ToreTeam1;
      $Matches[$Match->MatchNr]->ResTeam2Goals = $ToreTeam2;

      if ($MatchType<=16)
      {
        $Matches[$Match->MatchNr]->Team1Short="GER";
        $Matches[$Match->MatchNr]->Team2Short="GER";
      }

    }
    $ErrorMsgs = $MatchVerw->SpeichereErgebnisse($Matches);
    if ($MatchType<=16)
    {
      $ErrorMsgs2 = $MatchVerw->SpeichereTeams($Matches);
      $ErrorMsgs = array_merge($ErrorMsgs,$ErrorMsgs2);
    }
    echo("Fehlermeldungen: <br /> <pre>");
    if (count($ErrorMsgs) == 0) echo("keine");
    else print_r($ErrorMsgs);
    echo("</pre>");
  }





?>
<br />
<div class="Box">
<h2>Spieler erstellen</h2>
<form action="tippspiel_test.php5" method="post">
<table>
<tr>
<td>
Anzahl
</td>
<td><input type="text" name="AnzSpieler" value="12"></input></td>
</tr>
</table>
<input type="hidden" name="Aktion" value="SpielerErstellen"></input>
<input type="submit" value="Erstellen!"></input>
</form>
</div>

<br />

<div class="Box">
<h2>Tipps erstellen</h2>
<form action="tippspiel_test.php5" method="post">
<table>
<tr>
<td>Anzahl Test-Spieler:</td>
<td><input type="text" name="AnzSpieler" value="12"></input></td>
</tr>

<tr>
<td>MatchType (32,16,8,4,3 oder 2):</td>
<td><input type="text" name="MatchType" value="32"></input></td>
</tr>
<tr>
<td>Unentschieden zulassen:</td>
<td><input type="checkbox" name="UnentschErlaubt" checked="checked"></input></td>
</tr>
</table>
<input type="hidden" name="Aktion" value="TippsErstellen"></input>
<input type="submit" value="Erstellen"></input>
</form>
</div>

<br />
<div class="Box">
<h2>Ergebnisse erstellen</h2>
<form action="tippspiel_test.php5" method="post">
<table>
<tr>
<td>MatchType (32,16,8,4,3 oder 2):</td>
<td><input type="text" name="MatchType" value="32"></input></td>
</tr>
<tr>
<td>Unentschieden zulassen:</td>
<td><input type="checkbox" name="UnentschErlaubt" checked="checked"></input></td>
</tr>
</table>
<input type="hidden" name="Aktion" value="ErgErstellen"></input>
<input type="submit" value="Erstellen"></input>
</form>
</div>


<?php


  $TippSpielUI->OutputFooter();

?>