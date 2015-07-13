<?php

namespace db;

class Db {

  function __construct($host, $username, $password, $schema) {
    $this->db = mysqli_connect($host, $username, $password, $schema);

    $tables = [];
    $result = $this->db->query("show tables;");
    while ($row = $result->fetch_row()) {
      $tables[] = $row[0];
    }

    if(!in_array('sms_log', $tables)) {
      $this->db->query("CREATE TABLE IF NOT EXISTS `sms_log` (`id` int(11) NOT NULL auto_increment, `nummer` text NOT NULL, `timestamp` date NOT NULL, PRIMARY KEY  (`id`))");
    }

    if(!in_array('voucher_settings', $tables)) {
      $this->db->query("CREATE TABLE IF NOT EXISTS `voucher_settings` (`name` varchar(100) NOT NULL, `value` text NOT NULL)");
      $this->db->query("ALTER TABLE `voucher_settings` ADD PRIMARY KEY(`name`)");
    }
  }

  function esc($i) {
    return $this->db->real_escape_string($i);
  }

  function deleteAllRows($table) {
    $this->db->query("TRUNCATE `" . $this->esc($table) . "`");
  }

  function addTicketToTable($table, $value) {
    $this->db->query("INSERT INTO `" . $this->esc($table) . "` VALUES ('', '" . $this->esc($value) . "', '0')");
  }

  function updateSetting($key, $value) {
    $result = $this->db->query("select count(*) as num from voucher_settings where `name` = '" . $this->esc($key) . "';");
    $data = $result->fetch_assoc();
    if($data['num'] > 0) {
      $query = "UPDATE voucher_settings SET `value`='" . $this->esc($value) . "' WHERE `name`='" . $this->esc($key) . "';";
    } else {
      $query = "insert into voucher_settings (name, value) values('" . $this->esc($key) . "', '" . $this->esc($value) . "');";

    }
    $this->db->query($query);
  }

  function createTicketTable($name) {
    $q = "CREATE TABLE IF NOT EXISTS `" . $this->esc($name) . "` (`id` int(11) NOT NULL auto_increment, `code` varchar(15) NOT NULL, `printed` tinyint(4) default NULL, PRIMARY KEY  (`id`), UNIQUE KEY `code` (`code`))";
    $this->db->query($q);
  }

  function getSettings() {
    $settings_r = $this->db->query("SELECT * FROM voucher_settings");
    $settings = array();
    while($row = $settings_r->fetch_assoc()) {
      $settings[$row["name"]] = $row["value"];
      if(json_decode($row["value"], true) != NULL)
        $settings[$row["name"]] = json_decode($row["value"], true);
    }
    return $settings;
  }

  // fetches the oldest non printed tickets, set printed to 1 and returns an array of arrays like [[id, ticket],...]
  function activateTickets($table, $count) {
    $mysql = $this->db->query("SELECT id, code FROM `" . $this->esc($table) . "` WHERE printed = 0 ORDER BY id LIMIT " . $count . ";"); // auszudruckende Voucher abfragen
    $this->db->query("UPDATE `" . $this->esc($table) . "` SET printed = 1 WHERE printed = 0 ORDER BY id LIMIT " . $count . ";"); // Voucher als gedruckt markieren
    $data = array(); // Array mit Vouchercode und ID erzeugen
    while($row = $mysql->fetch_assoc()) {
      $data[]= [$row['id'], $row['code']];
    }
    return $data;
  }

  function getStatisticsForTable($table) {
    $mysql = $this->db->query("SELECT COUNT(*) AS c FROM `" . $this->esc($table) . "` where printed = 1");
    $result = $mysql->fetch_assoc();
    $used = $result['c'];

    $mysql = $this->db->query("SELECT COUNT(*) AS c FROM `" . $this->esc($table) . "` where printed = 0");
    $result = $mysql->fetch_assoc();
    $unused = $result['c'];

    return [
        $used + $unused,
        $unused,
        $used
    ];
  }

  function logNumber($empf) {
    $mysql = $this->db->mysql_query("SELECT timestamp FROM sms_log WHERE nummer = '" . $this->esc($empf) . "'");
    if(mysql_num_rows($mysql) > 0) { // Ist in Datenbank
      $sql = "UPDATE sms_log SET timestamp = CURDATE() WHERE nummer = '" . $this->esc($empf) . "'";
      $this->db->query($sql);
    } else {
      $sql = "INSERT INTO smls_log (nummer, timestamp) VALUES('" . $this->esc($empf) . "', CURDATE())";
      $this->db->query($sql);
    }
  }

  function numberIsNotLocked($empf) {
    $mysql = $this->db->mysql_query('SELECT timestamp FROM sms_log WHERE nummer = ' . $empf);
    if($mysql->num_rows() > 0) { // Ist in Datenbank
      while($row = $mysql->fetch_assoc()) {
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


