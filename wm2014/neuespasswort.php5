<?php
  require_once("./script/UIWeb1Classes.inc.php5");
  require_once('./script/UIFactory.inc.php5');
 $TippSpielUI = createWebUi1();

  $TippSpielUI->OutputHeader("Benutzerverwaltung");


  require_once(BASECLASSES_PATH."/BaseClasses/BenutzerVerw.class.php5");

  if (!isset($_GET["PwdResetKey"]))
  {
    echo("<b>Passwort konnte nicht geändert werden</b><br>Kein Parameter an die Webseite übergeben!");
  }
  else
  {
    $PwdResetKey = $_GET["PwdResetKey"];
	
	$newPwd = $_POST["newPwd"];
	if ($newPwd == "")
	{
	echo('<b>Passwort ändern</b><br>Bitte gib hier das neue Passwort für deinen Account ein: <form action="neuespasswort.php5?PwdResetKey='.$PwdResetKey.'" method="POST"><input name="newPwd" /><br><input type="submit" value="Passwort speichern!"/></form>');
	
	}
	else
	{
	
		$BenutzerVerw = new CBenutzerVerw();
		try
		{
		  $BenutzerVerw->ChangePwd($PwdResetKey, $newPwd);

		  echo('<b>Passwort zurückgesetzt!</b><br>
		  <br>
		  Glückwunsch! Dein Passwort würde erfolgreich geändert!<br>
		  <br>
		  Du kannst dich nun wieder am Tippspiel einloggen!
		  <br>
		  <a href="index.html">Weiter zum Tippspiel</a>
		  ');
		}
		catch(BenutzerMsgException $e)
		{

			echo("<b>Passwort konnte nicht zurückgesetzt werden!</b><br>".$e->getMessage());
		}
		catch(Exception $e)
		{
			CLogClass::log("Exception bei PwdReset: ".$e->getMessage());
			echo("<b>Passwort konnte nicht zurückgesetzt werden!</b><br> Das Passwort konnte nicht zurückgesetzt werden, bitte Administrator kontaktieren!");
		}
	}
  }

  $TippSpielUI->OutputFooter();

?>