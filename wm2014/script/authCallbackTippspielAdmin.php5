<?php

define('AUTH_CALLBACK', 'authCallbackTippspiel');

require_once( dirname(__FILE__).'/../tippconfig.php5' );
require_once("./script/TippSpielUIWeb1.class.php5");

require_once($_SERVER["DOCUMENT_ROOT"]."/inc/Liste.php");


function authCallbackTippspiel($action, $message = '', &$auth) {
	if(!isset($_GET)) {
		$_COOKIE = &$GLOBALS['HTTP_COOKIE_VARS'];
		$_ENV = &$GLOBALS['HTTP_ENV_VARS'];
		$_GET = &$GLOBALS['HTTP_GET_VARS'];
		$_POST = &$GLOBALS['HTTP_POST_VARS'];
		$_SERVER = &$GLOBALS['HTTP_SERVER_VARS'];
		$_SESSION = &$GLOBALS['HTTP_SESSION_VARS'];
		$_REQUEST = array_merge($_GET, $_POST, $_COOKIE);
	}

	// Configuration.
	$logging = false;
	$logType = 0;
	$logDest = '';
	$logHeaders = '';


  //default:
  $mainMessage = "F&uuml;r diese Aktion ist ein Einloggen als Administrator erforderlich!";
  $AccessNotDenied = 1;

  switch($action) {
		case AUTH_NEED_LOGIN:
                   $additionalMessage="";
                 break;

		case AUTH_INVALID_USER:
                   $additionalMessage="Es besteht keine solche Benutzerkonto/Passwort-Kombination. Bitte Eingabe korrigieren.";
                 break;

		case AUTH_EXPIRED:
                   $additionalMessage="Session war zu lange inaktiv. Bitte melden Sie sich erneut an.";
                 break;

		case AUTH_ACCESS_DENIED:
		default:
                 $AccessNotDenied = 0;
                 break;
  };

  $MyHopeArea = "wm2014";

  $TippSpielUI = new CTippSpielUIWeb1();

  $TippSpielUI->OutputHeader(TIPPSPIEL_NAME);


?>

<?php

  if ($AccessNotDenied)
  {
  ?>

<br /><br />
    <table width="100%"><tr><td align="center">

    <table cellspacing="10" class="Box" width="300">
      <tr>
        <td class="BoxTitle" colspan="2">
           <?php echo("$mainMessage");    ?>
        </td>
      </tr>
<?php
  if ($additionalMessage != "")
    echo ("<tr><td colspan=\"2\"> $additionalMessage</td></tr>");
?>
<tr><td align="center">
    <form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post">

    <?php
    //-----------
    //
    // alle GET und POST Parameter der Seite durchreichen!
    //
    //----------
    foreach( $_GET as $key=>$value)
    {
    		print "<input type=\"hidden\" name=\"$key\" value=\"$value\" />";
    }
    foreach( $_POST as $key=>$value)
    {
    		print "<input type=\"hidden\" name=\"$key\" value=\"$value\" />";
    }

    ?>
      </td></tr>
      <tr>
        <td align="center">
<?php
  $LoginForm = $TippSpielUI->CreateUIComponent("LoginForm","LoginForm","tippspiel.php5","post", "admin", false);
  $LoginForm->Output();
?>

        </td>
        <!--td align="left">Benutzername:</td>
        <td>
        <select name="<?php echo $auth->_options['usernameField'];?>"  class="wideFormElement">
      <option>- Bitte wählen -</option-->
    <?php




    ?>

    <!--/select>
        </td-->
      <!--tr>
        <td align="left">Passwort:</th>
        <td><input name="<?php echo $auth->_options['passwordField']; ?>" type="password" "  class="wideFormElement" /></td>
      </tr>
      <tr>
        <td colspan="2"><input class="LoginFormButton" type="submit" value="Login"/> <input class="LoginFormButton" onclick="history.go(-1);" type="button" value="zurück"/></td>
      </tr-->
    </form>



    </table></td></tr></table><br />
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />

<?php
  }
  else    //wenn "Access denied"
  {
  	//Rausfinden, ob der User noch nicht bestätigt ist oder ob er nicht für die WM angemeldet ist.

         $bUserIsPending = isset($auth->user['::groups::']['users_pending']);
         if ($bUserIsPending)
         {
           $Message = "Hallo ".$auth->user["Name"].",<br />
                       du hast dich erfolgreich registriert. Bevor du aber loslegen kannst, möchten wir
                       noch deine E-Mail-Adresse überprüfen. Dazu wurde gleich nach deiner Registrierung eine E-Mail versendet,
                       in der dir alle weiteren Schritte erklärt werden.<br />
                       Solltest du diese E-Mail nicht innerhalb von zwei Tagen erhalten, kontaktiere bitte <a href=\"mailto:"
                       .USER_MANAGER_MAIL."\">".USER_MANAGER_NAME."</a><br /><br /><br />";
         }
         else
         {
           $Message = "Hallo ".$auth->user["Name"].",<br />
                       du bist f&uuml;rs Tippspiel angemeldet, aber nicht als Administrator eingetragen. Daher hast du keinen Zugriff
                       auf diese Seite!<br />
                       <br />
                       ";
         }

  ?>
        <?php echo($Message); ?>
            <?php

	       echo("<form action=\"index.php5\" method=\"post\">");
	       $ButtonCaption = "Zur Tippspiel-Startseite";
            ?>

        <input class="smallSubmitButton" type="submit" value="Zur Tippspiel-Startseite" />

        </form>

<?php
}
?>

    <?php

      $TippSpielUI->OutputFooter();

	if($logging) {
		error_log("AUTH MESSAGE: $message", logType, $logDest,
			$logHeaders);
	}
	die();
}


?>