<?php 
require_once($_SERVER["DOCUMENT_ROOT"].'/inc/facebooktools.php');
$hasFbRoot = false;
function echoFacebookLink()
{
	//echo('<img src="bilder/fblogo.png" alt="Facebook f-logo" />');
  	$url = (isset($_SERVER['HTTPS']) == 'on' ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
  	$fbAppName = urlencode(FACEBOOK_APP_NAME);
  	$fbCaption = urlencode(FACEBOOK_APP_CAPTION);
  	$fbInfoText = urlencode(FACEBOOK_APP_INFOTEXT);
  	$fbPostDefault = urlencode(FACEBOOK_POST_DEFAULT);
  	//echo('<a target="_blank" href="http://www.facebook.com/dialog/apprequests?app_id='.FACEBOOK_APP_ID.'&link='.FACEBOOK_LINK.'&picture='.FACEBOOK_IMAGE_URL.'&name='.$fbAppName.'&caption='.$fbCaption.'&description='.$fbInfoText.'&message='.$fbPostDefault.'&redirect_uri='.$url.'">Lade deine Freunde ein!</a>');
  	//echo('<a href="http://www.facebook.com/dialog/feed?app_id='.FACEBOOK_APP_ID.'&link='.FACEBOOK_LINK.'&picture='.FACEBOOK_IMAGE_URL.'&name='.$fbAppName.'&caption='.$fbCaption.'&description='.$fbInfoText.'&message='.$fbPostDefault.'&redirect_uri='.$url.'">Lade deine Freunde ein!</a>');
	global $fb_app_id;
	
	global $hasFbRoot;
	if (!$hasFbRoot)
	{
		echo('<div id="fb-root">');
		$hasFbRoot = true;
	
		
		
	echo('</div><script src="http://connect.facebook.net/en_US/all.js">
      </script>
      <script>
	     
         FB.init({ 
            appId:"'.$fb_app_id.'", cookie:true, 
            status:true, xfbml:true 
         });
		 
		 </script>');
		}
	echo('<img src="bilder/fblogo.png" alt="Facebook f-logo" />');
	echo("<a href=\"#\" onClick=\"javascript:FB.ui({method: 'feed',  message: '".FACEBOOK_POST_DEFAULT."', data: '', link:'".FACEBOOK_LINK."', picture:'".FACEBOOK_IMAGE_URL."', name:'".FACEBOOK_APP_NAME."', caption:'".FACEBOOK_APP_CAPTION."', description:'".$FACEBOOK_APP_INFOTEXT."'},function(response) {if (response && response.post_id) { } else { alert('Fehler beim Posten!'); } });\">Poste auf deine Pinnwand!</a>");
	echo('<br>');
	
	echo('<img src="bilder/fblogo.png" alt="Facebook f-logo" />');
	echo("<a href=\"#\" onClick=\"javascript:FB.ui({method: 'apprequests',  message: '".FACEBOOK_POST_DEFAULT."', data: ''},function(response) {if (response) { } else { alert('Fehler beim Posten!'); } });\">Lade deine Freunde ein!</a>");
}

function echoFacebookPostResultLink($strTeam1, $strTeam2, $strRes, $strTipp, $points)
{
	//echo('<img src="bilder/fblogo.png" alt="Facebook f-logo" />');
  	
  	$fbAppName = urlencode(FACEBOOK_APP_NAME);
  	$fbCaption = urlencode(FACEBOOK_APP_CAPTION);
  	$fbInfoText = urlencode(FACEBOOK_APP_INFOTEXT);
  	$fbPostDefault = urlencode(FACEBOOK_POST_DEFAULT);
  	//echo('<a target="_blank" href="http://www.facebook.com/dialog/apprequests?app_id='.FACEBOOK_APP_ID.'&link='.FACEBOOK_LINK.'&picture='.FACEBOOK_IMAGE_URL.'&name='.$fbAppName.'&caption='.$fbCaption.'&description='.$fbInfoText.'&message='.$fbPostDefault.'&redirect_uri='.$url.'">Lade deine Freunde ein!</a>');
  	//echo('<a href="http://www.facebook.com/dialog/feed?app_id='.FACEBOOK_APP_ID.'&link='.FACEBOOK_LINK.'&picture='.FACEBOOK_IMAGE_URL.'&name='.$fbAppName.'&caption='.$fbCaption.'&description='.$fbInfoText.'&message='.$fbPostDefault.'&redirect_uri='.$url.'">Lade deine Freunde ein!</a>');
	global $fb_app_id;
	
	global $hasFbRoot;
	if (!$hasFbRoot)
	{
		echo('<div id="fb-root">');
		$hasFbRoot = true;
	
		
		
		echo('</div><script src="http://connect.facebook.net/en_US/all.js">
      </script>
      <script>
	     
         FB.init({ 
            appId:"'.$fb_app_id.'", cookie:true, 
            status:true, xfbml:true 
         });
		 
		 </script>');
	}	 
	$text = "Mein Tipp: $strTipp ($points von ".PUNKTE_FUER_ERGEBNIS." Punkten)";
	echo("<a href=\"#\" onClick=\"FB.ui({method: 'feed', data: '', link:'".FACEBOOK_LINK."', picture:'".FACEBOOK_IMAGE_URL."', caption:'".$strTeam1." - ".$strTeam2." $strRes', name:'".FACEBOOK_APP_NAME."', description:'".$text."'},function(response) {if (response && response.post_id) { } else { alert('Fehler beim Posten!'); } });\"><img src=\"bilder/fblogo.png\" alt=\"Facebook f-logo\" style=\"border:none\" /></a>");
}
?>