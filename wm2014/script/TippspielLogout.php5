<?php
  require_once('./script/authCallbackTippspiel.php5');
  require_once($_SERVER["DOCUMENT_ROOT"].'/inc/authconf.php');
  $auth = &newGroupAuth();
  $auth->Logout();
?>