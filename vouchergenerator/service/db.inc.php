<?php

namespace db;

class Db {

  function __construct($host, $username, $password, $schema) {
    $this->db = new \PDO("mysql:host=$host;dbname=$schema", $username, $password);

    $tables = [];
    $result = $this->query("show tables");
    foreach($result as $row) {
      $tables[] = $row[0];
    }

    if(!in_array('sms_log', $tables)) {
      $this->query("CREATE TABLE IF NOT EXISTS `sms_log` (`id` int(11) NOT NULL auto_increment, `nummer` text NOT NULL, `timestamp` date NOT NULL, PRIMARY KEY  (`id`))");
    }

    if(!in_array('voucher_settings', $tables)) {
      $this->query("CREATE TABLE IF NOT EXISTS `voucher_settings` (`name` varchar(100) NOT NULL, `value` text NOT NULL)");
      $this->query("ALTER TABLE `voucher_settings` ADD PRIMARY KEY(`name`)");
    }
  }

  function query($statement, $params = array()) {
    $ps = $this->db->prepare($statement);

    $index = 1;
    foreach($params as $v) {
      $ps->bindParam($index, $v);
      $index++;
    }

    $ps->execute();

    $errors = $ps->errorInfo();
    if($errors[0] != 0) {
      print('db errors');
      print_r($errors);
    }

    return $ps;
  }

  function esc($i) {
    return $i;
  }

  function deleteAllRows($table) {
    $this->query("TRUNCATE `" . $this->esc($table) . "`");
  }

  function addTicketToTable($table, $value) {
    $this->query("INSERT INTO `" . $this->esc($table) . "` VALUES ('', '" . $this->esc($value) . "', '0')");
  }

  function updateSetting($key, $value) {
    $result = $this->query("select count(*) as num from voucher_settings where `name` = '" . $this->esc($key) . "';");
    $data = $result->fetch();
    print("data");
    print_r($data);
    if($data['num'] > 0) {
      $query = "UPDATE voucher_settings SET `value`='" . $this->esc($value) . "' WHERE `name`='" . $this->esc($key) . "';";
    } else {
      $query = "insert into voucher_settings (name, value) values('" . $this->esc($key) . "', '" . $this->esc($value) . "');";
    }
    $this->query($query);
  }

  function createTicketTable($name) {
    $q = "CREATE TABLE IF NOT EXISTS `" . $this->esc($name) . "` (`id` int(11) NOT NULL auto_increment, `code` varchar(15) NOT NULL, `printed` tinyint(4) default NULL, PRIMARY KEY  (`id`), UNIQUE KEY `code` (`code`))";
    $this->query($q);
  }

  function getSettings() {
    $settings_r = $this->query("SELECT * FROM voucher_settings");
    $settings = array();

    foreach($settings_r as $row) {
      $settings[$row["name"]] = $row["value"];
      if(json_decode($row["value"], true) != NULL)
        $settings[$row["name"]] = json_decode($row["value"], true);
    }
    return $settings;
  }

  // fetches the oldest non printed tickets, set printed to 1 and returns an array of arrays like [[id, ticket],...]
  function activateTickets($table, $count) {
    $mysql = $this->query("SELECT id, code FROM `" . $this->esc($table) . "` WHERE printed = 0 ORDER BY id LIMIT " . $count . ";"); // auszudruckende Voucher abfragen
    $this->query("UPDATE `" . $this->esc($table) . "` SET printed = 1 WHERE printed = 0 ORDER BY id LIMIT " . $count . ";"); // Voucher als gedruckt markieren
    $data = array(); // Array mit Vouchercode und ID erzeugen
    while($row = $mysql->fetch()) {
      $data[] = [
          $row['id'],
          $row['code']
      ];
    }
    return $data;
  }

  function getStatisticsForTable($table) {
    $mysql = $this->query("SELECT COUNT(*) AS c FROM `" . $this->esc($table) . "` where printed = 1");
    $row = $mysql->fetch();
    $used = $row['c'];

    $mysql = $this->query("SELECT COUNT(*) AS c FROM `" . $this->esc($table) . "` where printed = 0");
    $row = $mysql->fetch();
    $unused = $row['c'];

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
      $this->query($sql);
    } else {
      $sql = "INSERT INTO smls_log (nummer, timestamp) VALUES('" . $this->esc($empf) . "', CURDATE())";
      $this->query($sql);
    }
  }

  function numberIsNotLocked($empf) {
    $mysql = $this->db->mysql_query('SELECT timestamp FROM sms_log WHERE nummer = ' . $empf);
    if($mysql->num_rows() > 0) { // Ist in Datenbank
      while($row = $mysql->fetch()) {
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


