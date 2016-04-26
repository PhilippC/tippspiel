<?php
  require_once("./script/UIWeb1Classes.inc.php5");
 require_once('./script/UIFactory.inc.php5');

  require_once($_SERVER["DOCUMENT_ROOT"].'/inc/authconf.php');
  $auth = &newGroupAuth();
 
 $TippSpielUI = createWebUi1();
  $TippSpielUI->OutputHeader("Benutzerverwaltung");


  require_once(BASECLASSES_PATH."/BaseClasses/Benutzer.db.class.php5");
  $BenutzerDB = new CBenutzerDB();
  $BenutzerListe = $BenutzerDB->LiesAlleBenutzer();

  if (!isset($_POST["UserName"]))
  {
    echo("<b>Passwort vergessen?</b><br /><br />");
    echo('<form action="passwortsenden.php5" method="post">
    <select name="UserName">
    <option value="0">Bitte Benutzernamen wählen!</option>');
    foreach($BenutzerListe as $Benutzer)
      echo("  <option>$Benutzer->Name</option>\n");
    echo('</select>
    <input type="submit" value="Passwort anfordern!"></input></form>');

  }
  else
  {
    $strUserName = $_POST["UserName"];
    $BenutzerVerw = new CBenutzerVerw();
    try
    {
      if (!$BenutzerVerw->SendPasswordMail($strUserName))
      {
        throw new Exception("Fehler bei SendPasswordMail ($strUserName)");
      }
      echo("<b>Passwort verschickt</b><br><br>
      Dein Passwort wurde dir zugesendet! Es sollte in wenigen Minuten bei dir ankommen, ansonsten kontaktiere <a href=\"mailto:".USER_MANAGER_MAIL."\">den Administrator</a>");
    }
	 catch (BenutzerMsgException $e)
    {
      echo("<b>Fehler!</b><br><br>".$e->getMessage());

    }
    catch (Exception $e)
    {
      echo("<b>Fehler!</b><br><br>
      Dein Passwort konnte dir nicht zugesendet werden! Bitte kontaktiere <a href=\"mailto:".USER_MANAGER_MAIL."\">".USER_MANAGER_NAME."</a>
      <!-- ".$e->getMessage()." -->");

    }


  }

  $TippSpielUI->OutputFooter();


?>