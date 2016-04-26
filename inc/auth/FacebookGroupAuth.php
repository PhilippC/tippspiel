<?php
require_once(dirname(__FILE__).'/GroupAuth.php');
require_once(dirname(__FILE__).'/../facebooktools.php');


class FacebookGroupAuth extends GroupAuth 
{
	function FacebookGroupAuth($options = null)
	{
		$this->_options['facebookUserIdField'] = 'FacebookUserID';
		$this->GroupAuth($options);
	}

	function forceLogin() 
	{
		$this->_login(true);
		GroupAuth::forceLogin();
	}
	
	function _login($forceLogin)
	{
		
		$this->startSession(false);
		
		if (!isset($_POST)) { // PHP 4.0.x
				$_POST = &$GLOBALS['HTTP_POST_VARS'];
				$_SERVER = &$GLOBALS['HTTP_SERVER_VARS'];
				$_SESSION = &$GLOBALS['HTTP_SESSION_VARS'];
		}
		//if (!$this->isIdentified) 
		{
			if (isFacebookApp())
			{
				$user = $this->_tryFindFacebookUser();
				if (!empty($user))
				{
					// Update session.
					$user['::lastLogin::'] = time();

					
					// Save session.
					$_SESSION[$this->_options['sessionVariable']] = $user;
					$this->user = &$_SESSION[$this->_options['sessionVariable']];
					//
					$this->isIdentified = $this->_checkSession($allowCallback);
					return;
				}
				else
				{
					$this->isIdentified = false;
					if ($forceLogin)
						$this->_callback(AUTH_NEED_LOGIN,
                                        'A valid username/password pair is needed.');
				}
			} 
			
		}

	}

	function _tryFindFacebookUser() 
	{
		$facebookUser = tryGetLoggedInFacebookUser(true);
		if (empty($facebookUser))
		{
			return;
		}
		
		$facebookUserId = $facebookUser["id"];
		
		$this->_connect();

		$adodbFetchMode = $GLOBALS['ADODB_FETCH_MODE'];
		$GLOBALS['ADODB_FETCH_MODE'] = ADODB_FETCH_ASSOC;

		$user = array();
		$sql = sprintf('SELECT * FROM %s WHERE %s = "%s"',
				$this->_options['usersTable'], $this->_options['facebookUserIdField'],
				$facebookUserId);
		$rs = $this->_conn->Execute($sql);
		if ($rs === false || $rs->EOF) 
		{
				CLogClass::log("Falsche facebookUserID: $facebookUserId", LOG_TYPE_ERROR);
			   return;
		} else 
		{
				$user = $rs->fields;
		}

		$GLOBALS['ADODB_FETCH_MODE'] = $adodbFetchMode;
		return $user;
	}
	
	function _onLoginExpired($allowCallback)
	{
		$this->expired = true;
		$this->_login(false);
		if (!$this->isIdentified)
			GroupAuth::_onLoginExpired($allowCallback);
	}
}
?>
