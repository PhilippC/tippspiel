<?php
// ---------------------------------------------------------------------------
//
// WM 2006 Tippspiel; MyHope-Seite
//
// Autoren:  Alex Homburg, Philipp Crocoll
//
// ---------------------------------------------------------------------------

if (!defined('__LOG_CLASS_PHP__INCLUDED'))
{
    define('__LOG_CLASS_PHP__INCLUDED',1);

    //Verschiedene "Wichtigkeitsstufen" für Log-Messages:
    define('LOG_TYPE_DEBUG_MESSAGE',1);
    define('LOG_TYPE_USERMSG_EXCEPTION',4);
    define('LOG_TYPE_ERROR',6);
    define('LOG_TYPE_UNCAUGHT_EXCEPTION',8);
    define('LOG_TYPE_CRITICAL_ERROR',10);

    //Hier wird eingestellt, ab welcher Wichtigkeitsstufe geloggt wird:
    define('START_LOGGING',LOG_TYPE_USERMSG_EXCEPTION);

    //Soll bei Critical Errors eine Mail an den Admin geschickt werden?
    //Noch nicht implementiert!!!
    define('SEND_MAIL_WHEN_CRITICAL',false);

    require_once("baseconfig.php5");
    // Logging-Klasse
    // Verwaltet das Filehandle auf die Logdatei und kuemmert
    // sich um das Loggen aller Meldungen.
    // Die Logging-Klasse ist als Singleton implementiert.
    class CLogClass
    {
    	// statische private Instanz
    	private static $instance = false;
    	// Änderung Alex 19.12.: auf public gesetzt
        // Das hat sonst Probleme gemacht!
    	public static $instancecounter = 0;

    	// Filehandle auf Logdatei
    	// Änderung Alex 19.12.: auf public gesetzt
        // Das hat sonst Probleme gemacht!
    	public $logfile = false;

    	// Konstruktor.
    	// Der Konstruktor ist privat, da er nur von der Logging-Klasse
    	// selber aufgerufen wird.
    	private function __construct()
    	{
    	    self::$instancecounter++;

    	    // Logfile oeffnen
    	    $this->logfile = fopen (LOGFILENAME, 'a');

    	    // falls das Logfile nicht geoeffnet werden konnte
    	    // --> geordneter Rueckzug
    	    if (! $this->logfile )
    		{
    		    throw new Exception
    			("Fehler beim Oeffnen des Logfiles $myfilename");
    		}
    	    $this->_log("Logfile geoeffnet: ", LOG_TYPE_DEBUG_MESSAGE);
    	}

    	// Destruktor
    	public function __destruct()
    	{
    	    // Erst muss geprueft werden, ob das Filehandle ueberhaupt
    	    // vorhanden ist, da der Destruktor auch aufgerufen wird,
    	    // wenn der Konstruktor bereits mit einer Ausnahme
    	    // abgebrochen wurde.
    	    // Der Destruktor darf keine Ausnahme werfen!!
    	    if ($this->logfile)
    		{
    		    try {$this->_log ("Logfile wird geschlossen ...", LOG_TYPE_DEBUG_MESSAGE);}
    		    catch (Exception $e) {}
    		    fclose($this->logfile);
    		}
    	    self::$instancecounter--;
    	}

    	// getInstance()-Methode - konstruiert bei Bedarf eine
    	// Instanz und gibt diese zurueck.
    	public static function getInstance()
    	{
    	    // if ( ! LogClass::$instance instanceof LogClass )
    	    if (self::$instancecounter <= 0)
    		{
    		    // falls beim Instantiieren ein Fehler auftritt,
    		    // soll der nach oben weitergereicht werden.
    		    try { CLogClass::$instance = new CLogClass(); }
    		    catch (Exception $e)
    			{
    			    throw $e;
    			}
    		}
    	    return CLogClass::$instance;
    	}

    	// log-Methoden der Instanz - abweichend vom ueblichen
    	// Konzept privat, da statt dessen die statischen Methoden
    	// genutzt werden sollen
    	private function _log($message, $LogMsgType)
    	{
             if ($LogMsgType>=START_LOGGING)
             {
               $LogMsgStr = $this->LogTypeToString($LogMsgType);

    	      if (! fputs($this->logfile,
    			date('H:i:s') . "\t" .$_SERVER["PHP_SELF"]."\t".$LogMsgStr."\t". $message . "\n"))
    		{
    		    throw new Exception
    			("error writing to log file");
    		}
             }
             if (($LogMsgType == LOG_TYPE_CRITICAL_ERROR) && (SEND_MAIL_WHEN_CRITICAL))
             {
               throw new Exception("Mail senden bei kritischem Fehler in Log-Klasse noch nicht implementiert!");
             }
    	}

    	// Die oeffentlichen Log-Methoden. Sie rufen die
    	// privaten Log-Methoden der Singleton-Instanz auf.
    	public static function log($message, $LogMsgType = LOG_TYPE_DEBUG_MESSAGE)
    	{
    	    try { CLogClass::getInstance()->_log($message, $LogMsgType); }
    	    catch (Exception $e) { throw $e; }
    	}

         public static function LogTypeToString($LogType)
         {
           switch ($LogType)
           {
	    case LOG_TYPE_DEBUG_MESSAGE: return "DBG";
	    case LOG_TYPE_USERMSG_EXCEPTION: return "UMX";
	    case LOG_TYPE_ERROR: return "ERR";
	    case LOG_TYPE_UNCAUGHT_EXCEPTION: return "EXC";
	    case LOG_TYPE_CRITICAL_ERROR: return "CRT";
             default: return "???";

           }
         }


    }
}

?>