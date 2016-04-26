<?php

  require('./script/UIWeb1Classes.inc.php5');
  require('./script/TippspielClasses.inc.php5');

  require_once('./script/UIFactory.inc.php5');
 $TippSpielUI = createWebUi1();
 
  $TippSpielUI->OutputHeader("");

  require_once("./script/Match.class.php5");
  require_once("./script/Match.db.class.php5");

  require_once("./script/Team.class.php5");
  require_once("./script/Team.db.class.php5");
  
  require_once("./script/Table.db.class.php5");

  $strAktion="SP";
  if (isset($_GET["Aktion"]))
    $strAktion = $_GET["Aktion"];

  $TeamDB = new CTeamDB();
  $Teams = $TeamDB->liesAlle();

?>
 <table><tr>
 <td class="SubMenuPreTD">Spielplan &gt;&gt;</td>
<?php
  $TippSpielUI->OutputSubMenu("spielplan.php5?Aktion=SP","Spielplan", $strAktion =="SP");
  $TippSpielUI->OutputSubMenu("spielplan.php5?Aktion=T","Tabellen", $strAktion =="T");

?>
</tr></table>
<br/>
<?php
if ($strAktion == "SP")
{
function createOutput()
{
  global $Matches, $Teams;

  $CurrentGroup = "";
  $CurrentMatchType = 32;
  if (count($Matches)>0)
  {
	  foreach($Matches as $Match)
	  {
	    if (($CurrentGroup != $Match->Group && $Match->MatchType == 32) || ($Match->MatchType != $CurrentMatchType))
	    {
	      if (($CurrentGroup != "") || ($CurrentMatchType<32))
	      {
	        echo ("</table>
	        ");
	      }
	      $CurrentGroup = $Match->Group;
	      $CurrentMatchType = $Match->MatchType;
	      if ($CurrentMatchType == 32)
	        echo('<h2>Gruppe '.$CurrentGroup.'</h2>');
	      else
	        echo('<h2>'.$Match->strMatchType.'</h2>');
	      echo('
	<table border="0" cellspacing="1" cellpadding="1" width="100%">
	            <tr>
	            <th width="40">Spiel</th>
	            <th colspan="5">Mannschaften</th>
	            <th width="70">Datum</th>
	            <th width="60">Zeit</th>
	            <th width="60">Ergebnis</th>
	
	      </tr>');
	
	    }
	    $Zeit = date("H:i",strtotime($Match->StartTime));
	    $Erg = "-:-";
	    if ($Match->ResTeam1Goals!=NULL)
	      $Erg = $Match->ResTeam1Goals.":".$Match->ResTeam2Goals;
	echo("<tr>
	  <td align=\"center\">".$Match->MatchNr."</td>
	  ");
	    $FlagDir = "bilder/flaggen/";
	    if (isset($Teams[$Match->Team1Short]))
	    {
	      $Team1 = $Teams[$Match->Team1Short];
	      $Team2 = $Teams[$Match->Team2Short];
	echo("
	  <td width=\"18\"><img src=\"$FlagDir".$Team1->FlagURL."\" alt=\"$Team1->NameLong\" /></td>
	  <td width=\"170\" align=\"center\">".$Team1->NameLong."</td>
	  <td width=\"8\">-</td>
	  <td width=\"170\" align=\"center\">".$Team2->NameLong."</td>
	  <td width=\"18\"><img src=\"$FlagDir".$Team2->FlagURL."\" alt=\"$Team2->NameLong\" /></td>
	");
	    }
	    else
	    {
	
	echo("
	  <td width=\"18\">&nbsp;</td>
	  <td width=\"170\" align=\"center\">".$Match->strTeam1Type."</td>
	  <td width=\"8\">-</td>
	  <td width=\"170\" align=\"center\">".$Match->strTeam2Type."</td>
	  <td width=\"18\">&nbsp;</td>
	");
	    }
	echo("  <td align=\"center\">".date("d.m.y",strtotime($Match->MatchDate))."</td>
	  <td align=\"center\">".$Zeit."</td>
	  <td align=\"center\">".$Erg."</td>
	</tr>");
	  }
      echo ("</table>");
  }
  else
  {
  	echo("Noch keine Spiele in Datenbank eingetragen!");
  }
}

  $MatchDB = new CMatchDB();
  $MatchDB->OrderByClause = "M.MatchType DESC, T1.GroupNr ASC, M.MatchDate, M.StartTime ASC, M.MatchNr ASC";
  $Matches = $MatchDB->liesWhere("M.MatchType = 32");

  createOutput();

  $MatchDB->OrderByClause = "M.MatchType DESC, M.MatchDate, M.StartTime ASC, M.MatchNr ASC";
  $Matches = $MatchDB->liesWhere("M.MatchType < 32");

  createOutput();
}
else
{
  $TableDB = new CTableDB();
  $Table = $TableDB->liesAlle();

  $CurrentGroup = "";

	  foreach($Table as $Team)
	  {
	    if ($CurrentGroup != $Team["GroupNr"])
	    {
	      if ($CurrentGroup != "")
	      {
	        echo ("</table>
	        ");
	      }
	      $CurrentGroup = $Team["GroupNr"];
	      echo('<h2>Gruppe '.$CurrentGroup.'</h2>');
	      echo('
	<table border="0" cellspacing="1" cellpadding="1" width="100%">
	      <tr>
	            <th colspan="2">Mannschaft</th>
		    <th width="60">Spiele</th>
		    <th width="60">Punkte</th>
	            <th width="60">Tore</th>
	            <th width="60">Gegentore</th>
	      </tr>');
	    }
	echo("
	<tr>
	  <td width=\"18\"><img src=\"bilder/flaggen/".$Teams[$Team["NameShort"]]->FlagURL."\" alt=\"".$Team["NameLong"]."\" /></td>
	  <td align=\"center\">".$Team["NameLong"]."</td>
	  <td align=\"center\">".$Team["Matches"]."</td>
	  <td align=\"center\">".$Team["Points"]."</td>
	  <td align=\"center\">".$Team["Goals"]."</td>
	  <td align=\"center\">".$Team["GoalsAgainst"]."</td>
	</tr>");
	  }
      echo ("</table>");
}
?>

<?php
  $TippSpielUI->OutputFooter();
 ?>