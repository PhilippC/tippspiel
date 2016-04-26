<?php



/* Auth.php - User authentication component.
 * Copyright (C) 2002 Julio César Carrascal Urquijo <jcesar@phreaker.net>
 *
 * This  library  is  free  software;  you can redistribute it and/or modify it
 * under  the  terms  of the GNU Library General Public License as published by
 * the  Free  Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 *
 * This  library is distributed in the hope that it will be useful, but WITHOUT
 * ANY  WARRANTY;  without  even  the  implied  warranty  of MERCHANTABILITY or
 * FITNESS  FOR  A  PARTICULAR  PURPOSE.  See  the  GNU  Library General Public
 * License for more details.
 *
 * You  should  have  received a copy of the GNU Library General Public License
 * along  with  this  library;  if  not, write to the Free Software Foundation,
 * Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA. */


require_once(dirname(__FILE__).'/../BaseClasses/log.class.php5');
require_once(dirname(__FILE__).'/../BaseClasses/baseconfig.php5');

/**
 * Need a valid username/password pair.
 *
 * @const AUTH_NEED_LOGIN
 */
define('AUTH_NEED_LOGIN',    -1);

/**
 * The username/password pair is invalid.
 *
 * @const AUTH_INVALID_USER
 * @access public
 */
define('AUTH_INVALID_USER',  -2);

/**
 * The session has expired.
 *
 * @const AUTH_EXPIRED
 * @access public
 */
define('AUTH_EXPIRED',       -3);

/**
 * You don't have access to this area.
 *
 * @const AUTH_ACCESS_DENIED
 * @access public
 */
define('AUTH_ACCESS_DENIED', -4);

/**
 * Allow the browser to cache the page but proxys can't.
 *
 * @const AUTH_CACHE
 * @access public
 */
define('AUTH_CACHE', 2);        /// Cache

/**
 * Do not allow anyone to cache the page.
 *
 * @const AUTH_NO_CACHE
 * @access public
 */
define('AUTH_NO_CACHE',   1);


/**
 * User authentication component.
 * This is the basic authentication component; you can use this class if you only need to
 * allow/disallow access to a page. If you need groups and roles support see the class
 * GroupAuth and the class RoleAuth documentation.
 *
 * @package icreativa
 * @author Julio César Carrascal Urquijo <jcesar@phreaker.net>
 * @version 2.4 pl3
 * @access public
 */
class Auth {
//public:


        /**
         * Holds user information.
         *
         * @var array
         * @access public
         */
        var $user = array();

        /**
         * If the user has been identified
         *
         * @var boolean
         * @access public
         */
        var $isIdentified = false;

        /**
         * Wich cache level to use.
         *
         * @var integer
         * @access public
         */
        var $cacheLevel = AUTH_CACHE;

        // Database connection information for ADOdb. Consult the ADOdb readme.html
        // file. This information is for the ADONewConnection() function and the
        // ADOConnection::Connect() method.

        /**
         * Database driver. Example 'mysql', 'mssql', 'oci8'...
         *
         * @var string
         * @access public
         */
        var $dbdriver = 'mysql';

        /**
         * Database hostname server.
         *
         * @var string
         * @access public
         */
        var $hostname = 'localhost';

        /**
         * Database username
         *
         * @var string
         * @access public
         */
        var $username = 'root';

        /**
         * Database password
         *
         * @var string
         * @access public
         */
        var $password = '';

        /**
         * Database.
         *
         * @var string
         * @access public
         */
        var $database = 'U160954AA';

        /**
         * Database.
         *
         * @var string
         * @access public
         */
        var $encodePWD = false;


        /**
         * Constructor.
         *
         * @param array                key => val of configuration parameters.
         * @access public
         */
        function Auth($options = null) {
                if (!isset($_SERVER)) { // PHP 4.0.x
                        $_SERVER = &$GLOBALS['HTTP_SERVER_VARS'];
                }

                ob_start();

                // print $this->_options['groupname'];

                // Database Squema. Change this so it reflects the names of tables and columns
                // on your site.
                $this->_options['usersTable'] = USER_TABELLE;
                $this->_options['userIdField'] = 'BenutzerID';
                $this->_options['usernameField'] = 'Name';
                $this->_options['passwordField'] = 'Pwd';

                // This settings affect the way the session is handled by PHP. See
                // http://www.php.net/session for an explanation of each one.
                $this->_options['cookieLifetime'] = 2592000;        // one month.
                $this->_options['cookiePath'] = '/';
                $this->_options['cookieDomain'] = null;
                $this->_options['sessionName'] = null;        // 'AUTHSESSID' for example

                // This settings affects the way the session works. 'sessionVariable' is the name
                // of the registered variable ($_SESSION['user'] by default).'expires'
                // is the time that a user has to refresh the session. 'forceRedirect' tells the
                // script to redirect the request to the page 'redirect'.
                $this->_options['sessionVariable'] = 'user';
                $this->_options['expires'] = 1800;        // half hour.
                $this->_options['forceRedirect'] = false;
                $this->_options['redirect'] = $_SERVER['PHP_SELF'];

                $this->_options = array_merge($this->_options, (array)$options);
        }


        /**
         * Initialize the session.
         * Use this method only if loggin in to the current page is optional but you will
         * want to have access to the user's information if he has already been identified.
         *
         * @access public
         */
        function startSession($allowCallback=true) {
                if ($this->_sessionStarted) {
                        return;
                }

                $this->_sessionStarted = true;

                $GLOBALS[$this->_options['sessionVariable']] = null;        // paranoia.

                if ($this->cacheLevel == AUTH_CACHE) {
                        session_cache_limiter('private, must-revalidate');
                        header('Cache-Control: private, must-revalidate');
                } else {
                        session_cache_limiter('no-cache, must-revalidate');
                        header('Cache-Control: no-cache, must-revalidate');
                }


                // Start the session.
                if (preg_match('!^\w+$!', $this->_options['sessionName'])) {
                        session_name($this->_options['sessionName']);
                }

                session_set_cookie_params($this->_options['cookieLifetime'],
                        $this->_options['cookiePath'], $this->_options['cookieDomain']);
                session_register($this->_options['sessionVariable']);


                if (!isset($_SESSION)) { // PHP 4.0.x
                        $_SESSION = &$GLOBALS['HTTP_SESSION_VARS'];
                }

                if (!isset($_SESSION[$this->_options['sessionVariable']])) {
                        $_SESSION[$this->_options['sessionVariable']] = array();
                }
                $this->user = &$_SESSION[$this->_options['sessionVariable']];

                // In case the user has already identified.
                $this->isIdentified = $this->_checkSession($allowCallback);
        }


        /**
         * Force the user to identify him self.
         *
         * @access public
         */
        function forceLogin() {
                $this->startSession();
                if (!isset($_POST)) { // PHP 4.0.x
                        $_POST = &$GLOBALS['HTTP_POST_VARS'];
                        $_SERVER = &$GLOBALS['HTTP_SERVER_VARS'];
                        $_SESSION = &$GLOBALS['HTTP_SESSION_VARS'];
                }

                if (!$this->isIdentified) {
                        if (isset($_POST[$this->_options['usernameField']])) {
                                $user = $this->_findByUsername(
                                        $_POST[$this->_options['usernameField']],
                                        $_POST[$this->_options['passwordField']]);

                                // Update session.
                                $user['::lastLogin::'] = time();

                                // Save session.
                                $_SESSION[$this->_options['sessionVariable']] = $user;
                                $this->user = &$_SESSION[$this->_options['sessionVariable']];

                                // Redirect so the username/password doesn't get saved in browser's post
                                // history.
                                // philipp crocoll: Rausgenommen, weil sonst durchzureichende Parameter
                                //auch nicht gespeichert werden!!!
                                //if (null !== $this->_options['redirect']) {
                                //        header('Location: '.$this->_options['redirect']);
                                //        exit();
                                //}
                        } else {
                                $this->_callback(AUTH_NEED_LOGIN,
                                        'A valid username/password pair is needed.');
                        }
                }
                // Redirect if requested.
                if ($this->_options['forceRedirect'] && null !== $this->_options['redirect']) {
                        header('Location: '.$this->_options['redirect']);
                        exit();
                }
        }


        /**
         * Delete all session information and logout the user.
         *
         * @access public
         */
        function logout() {
		//$this->forceLogin();
            	session_start();

                if (!isset($_SESSION)) { // PHP 4.0.x
                        $_SESSION = &$GLOBALS['HTTP_SESSION_VARS'];
                }

                $GLOBALS[$this->_options['sessionVariable']] = null;        // paranoia.
                unset($_SESSION[$this->_options['sessionVariable']]);        // more paranoia.
                session_destroy();
                $this->user = array();
				$this->isIdentified = false;
        }


        /**
         * Updates the user's information from the database.
         * The user must be identified already. Usefull if you just updated the database and
         * you need to update your session variable.
         *
         * @access public
         */
        function refreshInfo() {
                if (!$this->isIdentified)
                        $this->forceLogin();

                if (!isset($_SESSION)) { // PHP 4.0.x
                        $_SESSION = &$GLOBALS['HTTP_SESSION_VARS'];
                }

                $userId = $this->user[$this->_options['userIdField']];
                $_SESSION[$this->_options['sessionVariable']] =
                        $this->_findById($userId);
                $this->user = &$_SESSION[$this->_options['sessionVariable']];
        }


//protected:

        /**
         * Holds an ADOConnection instance.
         *
         * @var object ADOConnection
         */
        var $_conn = null;

        /**
         * This array hold database configuration and execution options.
         *
         * @var array
         */
        var $_options = array();

        /**
         * Flag that states if the session has already started.
         *
         * @var bool
         */
        var $_sessionStarted = false;

        /**
         * Just calls the callback function and dies.
         *
         * @param int                        What action should the callback function take. Has to be one
         *                      of AUTH_NEED_LOGIN, AUTH_INVALID_USER, AUTH_ACCESS_DENIED or
         *                      AUTH_EXPIRED.
         * @param string                message to show to the user, optional.
         */
        function _callback($action, $message = '') {
                // include the default callback function.

                if (!defined('AUTH_CALLBACK'))
                {
                        include_once(dirname(__FILE__).'/authCallback.php');
                }


                call_user_func(AUTH_CALLBACK, $action, $message, &$this);
                exit();
        }


        /**
         * Connect to the database only if necesary.
         */
        function _connect() {
                if ($this->_conn === null) {
                        $conn = &ADONewConnection($this->dbdriver);
                        if ($conn === null) {
                                trigger_error("$this->dbdriver is not supported", E_USER_ERROR);
                                die();
                        }
                        $res = $conn->PConnect($this->hostname, $this->username,
                                $this->password, $this->database);
                        if ($res === false) {
                                trigger_error("Can't connect to $this->hostname", E_USER_ERROR);
                                die();        // paranoia.
                        }
                        $this->_conn = &$conn;
						
						$this->_conn->Execute("SET CHARACTER SET 'utf8'");
						$this->_conn->Execute("SET NAMES utf8");
                }
        }


        /**
         * Search the user in the database by his username and password.
         *
         * @param string
         * @param string
         * @return array                users information.
         * @see _findById()
         */
        function _findByUsername($username, $password) {
                $this->_connect();

                $adodbFetchMode = $GLOBALS['ADODB_FETCH_MODE'];
                $GLOBALS['ADODB_FETCH_MODE'] = ADODB_FETCH_ASSOC;

                $user = array();
				
				if ($password == "")
				{
					$this->_callback(AUTH_INVALID_USER,
                                "Empty password not allowed!"); //this ensures that users which were added from Facebook cannot login outside the canvas app.
				}


                /*******************
                *
                * change by Alex Homburg
                *
                * option encodePWD = false has been included
                * Password is stored as is in the database
                *
                ************/
                if( $this->encodePWD ){
                     $sqlPassword = md5($password);
                }
                else {
                     $sqlPassword = $password;
                }


                $sql = sprintf('SELECT * FROM %s WHERE %s = %s AND %s = \'%s\'',
                        $this->_options['usersTable'], $this->_options['usernameField'],
                        $this->_conn->qstr($username, get_magic_quotes_gpc()),
                        $this->_options['passwordField'],
                        $sqlPassword);
						

                $rs = $this->_conn->Execute($sql);
				
                if ($rs === false || $rs->EOF) {
                        CLogClass::log("Falscher Login: $username $password");
                        $this->_callback(AUTH_INVALID_USER,
                                "Can't find the user's information in the database");
                } else {
                        $user = $rs->fields;
                }

                $GLOBALS['ADODB_FETCH_MODE'] = $adodbFetchMode;
                return $user;
        }


        /**
         * Search the user in the database by his user_id field.
         *
         * @param int                        Of the user.
         * @return array                users information.
         * @see _findByUsername()
         */
        function _findById($userId) {
                $this->_connect();

                $adodbFetchMode = $GLOBALS['ADODB_FETCH_MODE'];
                $GLOBALS['ADODB_FETCH_MODE'] = ADODB_FETCH_ASSOC;

                $user = array();
                $sql = sprintf('SELECT * FROM %s WHERE %s = %s',
                        $this->_options['usersTable'], $this->_options['userIdField'],
                        $userId);
                $rs = $this->_conn->Execute($sql);
                if ($rs === false || $rs->EOF) {
                        CLogClass::log("Falsche UserID: $userId", LOG_TYPE_ERROR);
                        $this->_callback(AUTH_INVALID_USER,
                                "Can't find the user's information in the database");
                } else {
                        $user = $rs->fields;
                }

                $GLOBALS['ADODB_FETCH_MODE'] = $adodbFetchMode;
                return $user;
        }


        /**
         * Validates the current session.
         *
         * @return bool
         */
        function _checkSession($allowCallback=true) {
                $identified = false;

                if (isset($this->user[$this->_options['userIdField']])) {
                        $lastLogin = $this->user['::lastLogin::'];
                        $time = time();
                        if (($lastLogin + $this->_options['expires']) < $time)
                        {
						
                         $this->_onLoginExpired($allowCallback);

                        } else {
                                $this->user['::lastLogin::'] = $time;
                                $identified = true;
                        }
                }
                return $identified;
        }
		
		function _onLoginExpired($allowCallback)
		{
			if ($allowCallback)
			{
				if (!isset($_POST[$this->_options['usernameField']])) 
				{
					//echo("calling back");
					$this->_callback(AUTH_EXPIRED, 'Your session just expired');
				}
			}
			  //else not identified
		}
}

?>