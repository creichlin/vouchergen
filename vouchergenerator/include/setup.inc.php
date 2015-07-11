<?php

require_once("service/config.inc.php");
require_once("service/db.inc.php");

// read config file and check if properly configured
$config = new \config\Config("vogen");

if(!$config->has("username") || !$config->has("password")) {
	die("please create config file with at least username and password");
}

// create database connection
$db = new \db\Db($config->get("db-host", "localhost"),
		$config->get("db-username", "voucher"),
		$config->get("db-pasword", "voucher"),
		$config->get("db-schema", "voucher"));

// get settings
$settings = $db->getSettings();


// set up localization
require_once("lang.php");
$lang = new aLang("main", "de");

// load the twig stuff
require_once('Twig/Autoloader.php');
Twig_Autoloader::register();

$twigLoader = new Twig_Loader_Filesystem('templates');
$twig = new Twig_Environment($twigLoader, array(
    #'cache' => '/var/www/vou_twig_cache',
));

// probably best here ?
session_start();

// remplate model goes in here, will be used for rendering at the end
$model = [];
$model['tables'] = $settings['dbtables'];



if(isset($_SESSION['angemeldet'])) {
  if ($_SESSION['angemeldet']) {
    $model['loggedIn'] = true;
  }
}


?>