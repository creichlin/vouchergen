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

// set up localization
require_once("lang.php");
$lang = new aLang("main", "de");

// probably best here ?
session_start();

?>