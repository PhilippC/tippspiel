<?php
  require_once("./script/UIWeb1LoginForm.class.php5");
  require_once($_SERVER["DOCUMENT_ROOT"].'/inc/facebooktools.php');

function PrintTransferUserForm()
{
echo'<div class="Box">
<b><font color="#555555">Du hast schon einen Account hier?</font></b>';

  $LoginForm = new CUIWeb1LoginForm("frmUseAccount",htmlentities(basename($_SERVER['PHP_SELF'])),"POST",FILTER_USERMAIL_IS_CONFIRMED);
  $LoginForm->SetButtonText("F&uuml;r Tippspiel anmelden");


  $AktionField = new FormItem('Aktion','','TransferUser','hidden');
  $LoginForm->addItemObject($AktionField);

  $LoginForm->Output();



echo '</div><br />';

}

function PrintNewUserForm()
{
echo '

<div class="Box">

<b><font color="#555555">Als neuer Spieler anmelden</font></b>
<form action="neuerspieler.php5" method="post">
<input type="hidden" name="Aktion" value="RegisterNewUser"></input>
<table border="0">
<tr><td>Vor- und Nachname:</td><td><input name="Name" class="wideFormElement"></input></td> </tr>
<tr><td>E-Mail-Adresse:</td><td><input name="Mail" class="wideFormElement"></input></td> </tr>
<tr><td>Passwort:</td><td><input type="password"  name="Pwd1" class="wideFormElement"></input></td> </tr>
<tr><td>Passwortwiederholung:</td><td><input type="password" name="Pwd2" class="wideFormElement"></input></td> </tr>

</table>
<input type="submit" value="Neuen Benutzer anmelden" class="wideSubmitButton"></input>
</form>
   </div>
      ';
	  
}


function PrintNewFacebookUserForm()
{
$fbUser = tryGetLoggedInFacebookUser();
$fbUsername = "";
if (empty($fbUser))
{
	
	global $fb_app_id;
	global $fb_canvas_page;
	$scope = "";
	if (FACEBOOK_COLLECT_MAILADDRESS)
	{
		$scope = "&scope=email";
	}
	
	$auth_url = "http://www.facebook.com/dialog/oauth?client_id=" 
				. $fb_app_id . $scope . "&redirect_uri=" . urlencode($fb_canvas_page);

	
	echo("Du wirst weitergeleitet zu <a href=\"$auth_url\" target=\"_top\">Facebook</a>");
	echo("<script> top.location.href='" . $auth_url . "'</script>");
	

}
else
{
  $fbUsername = $fbUser["name"];
  $fbMail = $fbUser["email"];
  
echo'<div class="Box">
<img src="bilder/fblogo.png" /><b><font color="#555555"> Nimm mit deinem Facebook-Account teil!</font></b>

  <form action="neuerspieler.php5" method="post">
<input type="hidden" name="Aktion" value="RegisterFacebookUser"></input>
<table border="0">
<tr><td>Vor- und Nachname:</td><td><input name="Name" class="wideFormElement" value="'.$fbUsername.'"></input></td> </tr>
</table>
<input type="hidden" value="'.$fbMail.'" name="Mail" />
<input type="submit" value="F&uuml;r Tippspiel anmelden" class="wideSubmitButton"></input>
</form>
';



echo '</div><br />';
}

}

?>