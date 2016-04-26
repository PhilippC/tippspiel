<?php

// die im folgenden genutzten Konstanten müssen außerhalb dieser Datei definiert werden:

 $fb_app_id = "";  //15stellig
 $fb_secret = ""; //32stellig
 $fb_canvas_page = "http://apps.facebook.com/DEINE_APP/";  //DEINE_APP ersetzen!
 $fb_debug = true;


try{ 
require_once($_SERVER["DOCUMENT_ROOT"].'/inc/facebook_sdk/src/facebook.php');
} catch (Exception $e)
{
	if ($fb_debug)
		die ($e->getMessage());
}

try{ 
 $facebook = new Facebook(array(
  'appId'  => $fb_app_id,
  'secret' => $fb_secret,
  'cookie' => true, // enable optional cookie support
));
} catch (Exception $e)
{
	if ($fb_debug)
		die ($e->getMessage());
}

function initializeFacebookApp()
{
session_start();
	if (!empty($_POST["signed_request"]))
	{

		$fbuser = tryGetLoggedInFacebookUser(true);
		if (!empty($fbuser))
		{
			$_SESSION["fbdata"] = base64_encode(serialize($fbuser));
		} else 
		{
			$_SESSION["fbdata"] = "";
			unset($_SESSION["fbdata"]);
		}
		
		
		$_SESSION["fbapp"] = true;

	}


}

function isFacebookApp()
{
	return $_SESSION["fbapp"] == true;
}

function tryGetLoggedInFacebookUser($forceUpdate=false)
{
	if (!$forceUpdate)
	{
		$fbdata = $_SESSION["fbdata"];
		if (!empty($fbdata))
		{
			$u = unserialize(base64_decode($fbdata));
			return $u;
		}
	}

	global $facebook;
	if (empty($facebook))
	{
		return;
	}
	try 
	{
		$me = $facebook->api('/me');
	} 
	catch (FacebookApiException $e) 
	{
		return;
	}
	return $me;
}

initializeFacebookApp();

?>
