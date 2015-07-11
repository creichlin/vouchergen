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

  // fetches the oldest non printed tickets, set printed to 1 and returns an array of arrays like [[id, ticket],...]
  function activateTickets($table, $count) {
    $mysql = mysql_query("SELECT id, code FROM `" . esc($table) . "` WHERE printed = 0 ORDER BY id LIMIT " . $count . ";"); // auszudruckende Voucher abfragen
    mysql_query("UPDATE `" . esc($table) . "` SET printed = 1 WHERE printed = 0 ORDER BY id LIMIT " . $count . ";"); // Voucher als gedruckt markieren
    $data = array(); // Array mit Vouchercode und ID erzeugen
    $i = 0;
    while($row = mysql_fetch_assoc($mysql)) {
      $data[$i][0] = $row['id'];
      $data[$i][1] = $row['code'];
      $i ++;
    }
    return $data;
  }

  function getStatisticsForTable($table) {
    $mysql = mysql_query("SELECT COUNT(*) AS c FROM " . esc($table) . " where printed = 1");
    $result = mysql_fetch_assoc($mysql);
    $used = $result['c'];

    $mysql = mysql_query("SELECT COUNT(*) AS c FROM " . esc($table) . " where printed = 0");
    $result = mysql_fetch_assoc($mysql);
    $unused = $result['c'];

    return [
        $used + $unused,
        $unused,
        $used
    ];
  }

  function logNumber($empf) {
    $mysql = mysql_query("SELECT timestamp FROM sms_log WHERE nummer = '" . esc($empf) . "'");
    if(mysql_num_rows($mysql) > 0) { // Ist in Datenbank
      $sql = "UPDATE sms_log SET timestamp = CURDATE() WHERE nummer = '" . esc($empf) . "'";
      mysql_query($sql);
    } else {
      $sql = "INSERT INTO smls_log (nummer, timestamp) VALUES('" . esc($empf) . "', CURDATE())";
      mysql_query($sql);
    }
  }

  function numberIsNotLocked($empf) {
    $mysql = mysql_query('SELECT timestamp FROM sms_log WHERE nummer = ' . $empf);
    if(mysql_num_rows($mysql) > 0) { // Ist in Datenbank
      while($row = mysql_fetch_assoc($mysql)) {
        $data = $row['timestamp'];
      }
      if($data != date('Y-m-d'))
        return 1; // letzer Abruf ist ungleich heute, gebe 1 zurÃ¼ck
      else
        return 0; // letzer Abruf ist heute, gebe 0 zurÃ¼ck
    }
    return 1; // ist nicht in Datenbank, gebe 1 zurÃ¼ck
  }
}

?>


