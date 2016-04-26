<?php
require_once("log.class.php5");
//Exceptions von dieser Art beinhalten Strings, die dem User präsentiert werden können
class BenutzerMsgException extends Exception {
  function __construct($message)
  {
    CLogClass::log($message,LOG_TYPE_USERMSG_EXCEPTION);
    parent::__construct($message);
  }
}

?>