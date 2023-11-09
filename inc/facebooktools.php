<?php

// die im folgenden genutzten Konstanten müssen außerhalb dieser Datei definiert werden:

 $fb_app_id = "";  //15stellig
 $fb_secret = ""; //32stellig
 $fb_canvas_page = "http://apps.facebook.com/DEINE_APP/";  //DEINE_APP ersetzen!
 $fb_debug = true;


try{ 
require_once($_SERVER["DOCUMENT_ROOT"].'/inc/facebook-php-sdk/facebook.php');
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
	if ($_SESSION["fbapp"])
		return true;
	
	$fbdata = (unserialize(base64_decode($_SESSION["fbdata"])));
	if (!empty($fbdata))
	{
		return true;
	}
	else 
	{
		return false;
	}
		
}

function tryGetLoggedInFacebookUser($forceUpdate=false)
{

	global $facebook;
	if (empty($facebook))
	{
		return;
	}
	try 
	{
		$uid = $facebook->getUser();
		
		if ($uid){
			 $me =$facebook->api('/me');
		} else 
		{
			return;
		}
		
	} 
	catch (FacebookApiException $e) 
	{
		return;
	}
	return $me;
}

initializeFacebookApp();

?>
