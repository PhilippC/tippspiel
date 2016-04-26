<?php
// ---------------------------------------------------------------------------
//
// www. F A C H P R E S S E T A G E .de
//
// Autor:  Alex Homburg
// E-Mail: ahomburg@rbsonline.de
//
// erstellt am: 18.04.2005
//
// Historie:
//
//
// ---------------------------------------------------------------------------

if(! defined('EXCEPTIONHANDLINGPHP'))
{
    define( 'EXCEPTIONHANDLINGPHP', 'EXCEPTIONHANDLINGPHP' );

    require_once("baseconfig.php5");
    require_once("log.class.php5");
    require_once("BenutzerMsgException.class.php5");

    // setze die php-ini-Variablen fuer das Error-Handling
    ini_set('display_errors',   DISPLAYERRORS);
    ini_set('log_errors',       LOGERRORS);
    ini_set('error_log',        LOGFILENAME);

    // Benutzerdefinierter Errorhandler
    function errorhandler( $errno, $errmsg, $errfile, $errline )
	{
        print "Ein Fehler ist aufgetreten: " .$errmsg;
	    // ... hier noch geeignetes errorhandling einfuegen ...
	} // function spferrorhandling ()



    // Um den benutzerdefinierten Errorhandler
    // zu aktivieren, muss die folgende Zeile entkommentiert
    // werden.

    // $olderrhandler = set_error_handler ('errorhandler');





    // Benutzerdefinierter Exceptionhandler zum Abfangen aller
    // Exceptions, die sonst nicht behandelt werden.
    function exceptionhandler ($e)
	{

        	    CLogClass::log( $e->getMessage() ,LOG_TYPE_UNCAUGHT_EXCEPTION);
        	    print "Schwerer Ausnahmefehler aufgetreten!";


	}

    $old_exceptionhandler = set_exception_handler ('exceptionhandler');
}
?>