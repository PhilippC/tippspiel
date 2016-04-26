<?php
  require_once('./script/authCallbackTippspielAdmin.php5');
  require_once($_SERVER["DOCUMENT_ROOT"].'/inc/authconf.php');
  $auth = &newGroupAuth();
  $auth->requireAtLeast('admin');


?>