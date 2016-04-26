<?php

session_register('areSessionsWorking');

if (!isset($HTTP_GET_VARS['started'])) {
    $HTTP_SESSION_VARS['areSessionsWorking'] = true;
    header('Location: check.php?started=true');
    exit();
}

@include('adodb.inc.php');
@include('adodb/adodb.inc.php');
@include('../adodb.inc.php');
@include('../adodb/adodb.inc.php');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title>Check PHP.</title>
    <link href="styles/styles.css" rel="stylesheet" type="text/css" />
  </head>

  <body>
    <p>This script will check your PHP installation and will issue some warnings if some
    component is missing or isn&#39;t working.</p>

    <p>Checking Sessions...
    <?php if (isset($HTTP_SESSION_VARS['areSessionsWorking'])) { ?> ok.
    <?php } else { ?> FAIL.<br />
    Your sessions aren&#39;t working, please check if your cookies are enabled and check
    that the [Session] seccion of your PHP.INI is correct.
    <?php } ?></p>

    <p>Checking ADOdb...
    <?php if (defined('_ADODB_LAYER')) { ?> ok.
    <?php } else { ?>FAIL.<br />
    Please download latest version of <a href="http://php.weblogs.com/adodb">ADOdb</a>
    and unpack the distribution in where this script can find it.
    <?php } ?></p>

    <p>Checking PHP Version... <?php if ("4.1.2" != phpversion()) { ?> ok.
    <?php } else { ?>WARNING.<br />
    Some PHP versions (Especialy 4.1.2) has problems with sessions. If you are using
    this version of PHP please upgrade to a more stable version.
    <?php } ?></p>
  </body>
</html>

