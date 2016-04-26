<?php
// ---------------------------------------------------------------------------
//
// WM 2006
//
// Autoren:  Alex Homburg, Philipp Crocoll
//
// ---------------------------------------------------------------------------

    require_once( 'baseconfig.php5');
    require_once( 'log.class.php5' );
    require_once( '_exceptionhandling.php5');
    require_once( ADODB_PATH.'/adodb.inc.php' );
	
	function microtime_float()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}

    class NoSingleValueException extends Exception
    {
      function __construct($message)
      {
        parent::__construct($message);
      }
    }

    // BaseDbClass ist abstract, da nur die
    // vererbten Klassen instanziiert werden sollen.
    abstract class CDBBase
    {

    	// statische Parameter fuer Datenbankzugriff
    	private static $dbname     = false;
    	private static $dbuser     = false;
    	private static $dbpassword = false;
    	private static $dbserver   = false;

    	// statische Parameter fuer Verbindungsverwaltung
    	// Datenbankverbindung muss zugreifbar durch
    	// die Klassen sein - daher protected.
    	// Alle anderen Attribute sind privat
    	private static $connection         = false;
    	private static $instancecounter    = 0;


        //---
        //
        // contructor
        //
        //---
    	public function __construct( $dbserver=DBSERVER, $dbname=DBNAME, $dbuser=DBUSER, $dbpassword=DBPASSWORD )
    	{

            CLogClass::Log("BaseDB-Konstruktor aufgerufen. Instanzzï¿½hler ist bei ".self::$instancecounter);
            // gibt es schon eine Connection?
    	    if ((self::$instancecounter > 0) && (self::$connection))
    		{
    		    // Pruefe, ob neue Verbindung mit
    		    // bestehender uebereinstimmt
    		    if (  $dbname     != self::$dbname
  			       or $dbuser     != self::$dbuser
  			       or $dbpassword != self::$dbpassword )
    			{
    			    CLogClass::log( "Datenbankname ($dbname), User ($dbuser) oder Passwort ist nicht korrekt" ,LOG_TYPE_ERROR);
    			    throw new Exception( "Datenbankname, User oder Passwort ist nicht korrekt" );
    			}
                     CLogClass::Log("Benutze bestehende Connection.");
    		}
    		// Connection herstellen
    	    else
    		{
                     self::$dbname     = $dbname;
    		    self::$dbuser     = $dbuser;
    		    self::$dbpassword = $dbpassword;
                	    self::$dbserver   = $dbserver;

                     CLogClass::Log("Versuche, neue Connection zu erstellen...");
    		    try
    			{
    			    self::$connection = NewADOConnection( DBDRIVER );
                    self::$connection->debug = DBDEBUG;
    			    self::$connection->Connect( $dbserver, $dbuser , $dbpassword, $dbname );
    			    CLogClass::log( "Ok. Setze Verbindung auf UTF-8");
    			    
    			    self::_execSQLNoReturn("SET CHARACTER SET 'utf8'");
    			    self::_execSQLNoReturn("SET NAMES utf8");
    			    CLogClass::log( "Ok. Setze Verbindung ADODB_FETCH_ASSOC.");

    			    // force indexing auf recordset fields by name
    			    self::$connection->SetFetchMode( ADODB_FETCH_ASSOC );
    			    

    			    CLogClass::log( "Ok! Verbunden zu $dbserver.$dbname als $dbuser" , LOG_TYPE_DEBUG_MESSAGE );
    			    
   		        }
    		    catch( Exception $e )
    			{
    			    CLogClass::log( "Fehler beim DB-Verbindungsaufbau zu $dbserver.$dbname als $dbuser" , LOG_TYPE_ERROR );
    			    throw new Exception( "Fehler beim DB-Verbindungsaufbau" );
    			}
    		}
             CLogClass::Log("Erhöhe Instanz-Zähler von ".self::$instancecounter."...");
    	    self::$instancecounter++;
             CLogClass::Log("  ... auf ".self::$instancecounter);

    	}


        //---
        //
        // destructor
        //
        //---
    	public function __destruct()
    	{            

	    CLogClass::Log("BaseDB-Destruktor aufgerufen. Instanzzähler ist bei ".self::$instancecounter);
    	    self::$instancecounter--;
    	    if (self::$instancecounter <= 0)
    		{
    		    try
    			{
    			    self::$connection->Close();
			    self::$connection = false;
    			}
    		    catch( Exception $e )
    			{
                             CLogClass::Log("Kann connection nicht schließen.", LOG_TYPE_ERROR);
                             // hier keine Ausnahme werfen, da Destruktor
    			}

    		}
    	}




    	// die DB-Verbindung muss ueber eine geschuetzte
    	// Methode zurueckgegeben werden, da direkter
    	// Zugriff auf geschuetzte statische Variablen
    	// nicht moeglich ist.
    	protected function getConnection()
    	{
    	    return self::$connection;
    	}


     //Dient zum Ausfï¿½hren eines SQL, der einen Wert zurï¿½ckgeben soll.
     //Dies ist z.B. eine Anzahl (count) oder eine ID.
     //Der Wert wird zurï¿½ckgegeben, im Fehlerfall wird eine Exception geworfen.
     protected function _execSingleValueSQL($SQL, $ValueField)
     {
        try
        {
          if( $result = $this->getConnection()->Execute($SQL) )
          {
               $strMsg = "SQL Erfolgreich ausgefï¿½hrt.
                           SQL: $SQL";
               CLogClass::Log($strMsg,LOG_TYPE_DEBUG_MESSAGE);
               if ($result->RecordCount()!=1) throw new NoSingleValueException("SingleValueSQL lieferte ".$result->RecordCount()." Ergebnisse!");
               return $result->fields[$ValueField];
          }
          else //ein Fehler ist aufgetreten
          {
               $strMsg = "Fehler beim Ausführen eines SQL:
                           SQL: $SQL";
             CLogClass::log  ($strMsg, LOG_TYPE_ERROR);
             throw new Exception ($strMsg);
          };
        }
        catch (Exception $e)
        {
          CLogClass::log("Exception in _execSingleValueSQL: ".$e->getMessage(), LOG_TYPE_ERROR);
        }

        return -1;

     }


      //Dient zum Ausfï¿½hren eines SQL, der einen Datensatz in eine Tabelle einfï¿½gt.
     //Der Wert von Insert_ID() wird zurï¿½ckgegeben.
     protected function _execInsertSQL($SQL)
     {
        try
        {
          if( $result = $this->getConnection()->Execute($SQL) )
          {
               $strMsg = "SQL Erfolgreich ausgeführt.
                           SQL: $SQL";
               CLogClass::Log($strMsg,LOG_TYPE_DEBUG_MESSAGE);
               return $this->getConnection()->Insert_Id();
          }
          else //ein Fehler ist aufgetreten
          {
               $strMsg = "Fehler beim Ausführen eines SQL:
                           SQL: $SQL
                           ADODB-Meldung: ".$this->getConnection()->ErrorMsg();
             throw new Exception ($strMsg);
          };
        }
        catch( Exception $e )
        {
             CLogClass::log  ($e->getMessage(), LOG_TYPE_ERROR);
          throw new Exception ($e->getMessage());
        }
        return -1;

     }

     //Führt einen SQL aus und gibt true zurück, falls erfolgreich
     protected function _execSQLNoReturn($SQL)
     {
     	CLogClass::log($SQL);

          if( $result = $this->getConnection()->Execute($SQL) )
          {
               $strMsg = "SQL ohne Rückgabe erfolgreich ausgeführt.
                           SQL: $SQL";
               CLogClass::log($strMsg,LOG_TYPE_DEBUG_MESSAGE);

               return true;
          }
          else //ein Fehler ist aufgetreten
          {
          	CLogClass::log("Fehler");
               $strMsg = "Fehler beim Ausführen eines SQL ohne Rückgabe :
                           SQL: $SQL
                           ADODB-Meldung: ".$this->getConnection()->ErrorMsg();
             CLogClass::log  ($strMsg, LOG_TYPE_ERROR);
             throw new Exception ($strMsg);
          };

        return -1;

     }

    }

?>