<?php

namespace db;

function esc($i) {
  return mysql_real_escape_string($i);
}

class Db {

  function __construct($host, $username, $password, $schema) {
    @mysql_connect($host, $username, $password) or die("Connection to MySQL failed!");
    @mysql_select_db($schema) or die("Access to database failed!");
  }

  function deleteAllRows($table) {
    mysql_query("TRUNCATE " . esc($table));
  }

  function addTicketToTable($table, $value) {
    mysql_query("INSERT INTO " . esc($table) . " VALUES ('', '" . esc($value) . "', '0')");
  }

  function updateSetting($key, $value) {
    $query = "UPDATE voucher_settings SET `value`='" . esc($value) . "' WHERE `name`='" . esc($key) . "';";
    mysql_query($query);
  }

  function createTicketTable($name) {
    $q = "CREATE TABLE IF NOT EXISTS `" . esc($name) . "` (`id` int(11) NOT NULL auto_increment, `code` varchar(15) NOT NULL, `printed` tinyint(4) default NULL, PRIMARY KEY  (`id`), UNIQUE KEY `code` (`code`))";
    mysql_query($q);
  }

  function getSettings() {
    $settings_r = mysql_query("SELECT * FROM voucher_settings");
    $settings = array();
    while($row = mysql_fetch_array($settings_r, MYSQL_ASSOC)) {
      $settings[$row["name"]] = $row["value"];
      if(json_decode($row["value"], true) != NULL)
        $settings[$row["name"]] = json_decode($row["value"], true);
    }
    return $settings;
  }
}

?>


