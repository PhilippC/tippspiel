<?php
if (!defined('__BASECONFIG.PHP__INCLUDED'))
{
    define('__BASECONFIG.PHP__INCLUDED',1);

    //Datei zum Aktivieren von User-Accounts: (vorangestellt wird der Servername, z.B. http://myhope.de)
    define ('ACCOUNT_AKTIVIERUNGS_DATEI','/wm2014/accountaktivieren.php5');
	define ('PWDRESET_DATEI','/wm2014/neuespasswort.php5');
    
    //---------------------------------------
    //Wer soll als Absender in Mails von der Benutzerverwaltung
    // (Passwort vergessen, Account aktivieren) angegeben werden?
    define('USER_MANAGER_NAME','Tippspiel Benutzerverwaltung');
    define('USER_MANAGER_MAIL','admin@deinekirche.de');
    

    //Verzeichnis zu AdoDB:
    define ('ADODB_PATH',$_SERVER["DOCUMENT_ROOT"]."/inc/adodb");
    //Verzeichnis zu Authentifizierungsskripts:
    define ('AUTH_PATH',$_SERVER["DOCUMENT_ROOT"]."/inc/auth");

   
     //---------------------------------------
    // Verzeichnis fuer Log-Dateien
    define ('LOGDIR',     $_SERVER["DOCUMENT_ROOT"].'/inc/log');


    //Tabellennamen entsprechend der Standard-Datenbank-Initialisierung definieren:
    define ('USER_TABELLE', "Benutzer");
    define ('USERGROUP_TABELLE', "Benutzer_Gruppen");
    define ('GROUP_TABELLE', "Gruppen");


	//---------------------------------------
    //Datenbank-Zugriffswerte setzen (aus dbconstants.php)
    require_once($_SERVER["DOCUMENT_ROOT"]."/inc/dbconstants.php"); 
    define ('DBNAME',     $database);
    define ('DBUSER',     $DB_User);
    define ('DBPASSWORD', $DB_Pwd);
    define ('DBSERVER',   $DB_Address);	

    // Treiber fuer ADOdb-Zugriff
    define ('DBDRIVER', 'mySQL');

    define ('DBDEBUG',   false);

    // Flag, ob Fehler am Browser ausgegeben werden sollen.
    // Wird zum Setzen des php-ini-Parameters display_errors verwendet.
    // Sollte 0 sein im produktiven Betrieb.
    define ('DISPLAYERRORS', 1);

    // Flag, ob PHP-Ausgaben geloggt werden sollen.
    define ('LOGERRORS', 0);

    // Name des Anwendungsspezifischen Logfiles,
    // in das Logging- und Fehlermeldungen
    // geschrieben werden.
    define ('LOGFILENAME', LOGDIR . '/' . 'logoutput.'.date('Ymd').'.log');

}

?>