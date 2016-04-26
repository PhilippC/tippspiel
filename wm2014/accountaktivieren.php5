<?php
  require_once("./script/UIWeb1Classes.inc.php5");
  require_once('./script/UIFactory.inc.php5');
  require_once('./script/facebooklink.php5');
  require_once("./script/TippTabellenCache.db.class.php5");
  $TippSpielUI = createWebUi1();

  $TippSpielUI->OutputHeader("Benutzerverwaltung");


  require_once(BASECLASSES_PATH."/BaseClasses/BenutzerVerw.class.php5");

  if (!isset($_GET["ActivationCode"]))
  {
    echo("<b>Account konnte nicht aktiviert werden</b><br>Kein Parameter an die Webseite übergeben!");
  }
  else
  {
    $ConfirmString = $_GET["ActivationCode"];
    $BenutzerVerw = new CBenutzerVerw();
    try
    {
      $BenutzerVerw->ConfirmUser($ConfirmString);
	  
	  //Tipptabellen-Cache leeren, sonst wird der neue User in der Tabelle nicht angezeigt (und könnte sich darüber wundern)
	  $TippTabellenCache = new CTippTabellenCacheDB();
	  $TippTabellenCache->LeereCache(0);

      echo('<b>Account erfolgreich aktiviert!</b><br>
      <br>
      Glückwunsch! Dein Benutzerkonto wurde erfolgreich aktiviert!<br>
      <br>');
      
      if (ENABLE_FACEBOOK_SUPPORT)
      {
      	echoFacebookLink();
      	echo('<br />');
      }
      
      echo('Du kannst nun am Tippspiel teilnehmen! Am besten gibst du gleich die ersten Tipps ab! Du kannst sie sp&auml;ter immer
      noch &auml;ndern!<br>
      <br>
      <a href="index.html">Weiter zum Tippspiel</a>
      ');
    }
    catch(BenutzerMsgException $e)
    {

        echo("<b>Account konnte nicht aktiviert werden!</b><br>".$e->getMessage());
    }
    catch(Exception $e)
    {
        CLogClass::log("Exception bei Aktivierung: ".$e->getMessage());
        echo("<b>Account konnte nicht aktiviert werden!</b><br> Die Aktivierung konnte nicht durchgeführt werden, bitte Administrator kontaktieren!");
    }
  }

  $TippSpielUI->OutputFooter();

?>