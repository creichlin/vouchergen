<?php
require_once ("service/config.inc.php");
require_once ("service/db.inc.php");
require_once ("service/view.inc.php");

set_error_handler(function($errno, $errstr, $errfile, $errline, array $errcontext) {
  // error was suppressed with the @-operator
  if (0 === error_reporting()) {
    return false;
  }

  throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

// read config file and check if properly configured
$config = new \config\Config("captivout");

if(!$config->has("username") || !$config->has("password")) {
  die("please create config file with at least username and password");
}

// create database connection
$db = new \db\Db($config->get("db-host"),
                 $config->get("db-username"),
                 $config->get("db-password"),
                 $config->get("db-schema"));

// get settings
$config->initDbValues($db->getSettings());


foreach($config->get('dbtables') as $key => $value) {
  $db->createTicketTable($key);
}

$view = new \view\View();
$view->set('tables', $config->get('dbtables'));

// probably best here ?
session_start();

if(isset($_SESSION['angemeldet'])) {
  if($_SESSION['angemeldet']) {
    $view->setLoggedIn();
  }
}

?>