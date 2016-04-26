<?php

require_once($_SERVER["DOCUMENT_ROOT"].'/inc/adodb/adodb.inc.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/inc/auth/GroupAuth.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/inc/auth/FacebookGroupAuth.php');
require_once($_SERVER["DOCUMENT_ROOT"]."/inc/dbconstants.php");

$cnf = new stdClass();

$cnf->dbdriver = 'mysql';
$cnf->hostname =  $DB_Address;
$cnf->username =  $DB_User;
$cnf->password =  $DB_Pwd;
$cnf->database =  $database;

/*Wenn die Passwörter nicht "plain" in der Datenbank gespeichert werden sollen, muss der Wert encodePWD auf "true" gesetzt werden. Dann werden nur MD5-Hashes in die DB
geschrieben. Nachteil: Der Mechanismus bei Vergessen des Passworts wird komplizierter (weil aus dem Hash ja das Pwd nicht mehr gewonnen und dem Benutzer geschickt werden
kann.

Bevor der Wert auf true gesetzt wird, sollten folgende SQL-Anweisungen (z.B. mit phpMyAdmin) ausgeführt werden:

update Benutzer set Pwd = md5(Pwd);

Die erste Zeile transformiert evtl. schon bestehende Passwörter (z.B. das Admin-Passwort), die zweite legt eine Spalte an, die für den "Passwort-Vergessen-Mechanismus" benötigt wird.
*/
$cnf->encodePWD = false;

function &newAuth($options = null) {
        global $cnf;

        $db = array(
        //       'usersTable' => 'users',
       //         'userIdField' => 'user_id',
       //         'usernameField' => 'username',
       //         'passwordField' => 'password'
        );
        $auth = new Auth(array_merge($options, $db));

        $auth->dbdriver = $cnf->dbdriver;
        $auth->hostname = $cnf->hostname;
        $auth->username = $cnf->username;
        $auth->password = $cnf->password;
        $auth->database = $cnf->database;
        $auth->encodePWD = $cnf->encodePWD;

        return $auth;
}

function &newGroupAuth($options = null) {

		global $theSingleGroupAuth;
		global $cnf;
		
		if ($theSingleGroupAuth)
		{
			return $theSingleGroupAuth;
		}
		
		if (ENABLE_FACEBOOK_SUPPORT)
			$auth = new FacebookGroupAuth( $options );
		else
			$auth = new GroupAuth( $options );

        $auth->cacheLevel = AUTH_NO_CACHE;

        $auth->dbdriver = $cnf->dbdriver;
        $auth->hostname = $cnf->hostname;
        $auth->username = $cnf->username;
        $auth->password = $cnf->password;
        $auth->database = $cnf->database;
		$auth->encodePWD = $cnf->encodePWD;
		
		if (ENABLE_FACEBOOK_SUPPORT)
		{
			$auth->_login(false);
		}
		
		$theSingleGroupAuth = $auth;
		
        return $auth;
}

 $authMyHopeArea = "";

 function authSetMyHopeArea($area)
 {
   global $authMyHopeArea;
   $authMyHopeArea = $area;
 }
 function authGetMyHopeArea()
 {
   global $authMyHopeArea;
   return $authMyHopeArea;
 }
?>