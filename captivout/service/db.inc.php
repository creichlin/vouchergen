<?php

namespace db;

class Db {

  function __construct($host, $username, $password, $schema) {
    $this->db = new \PDO("mysql:host=$host;dbname=$schema", $username, $password);

    $this->query("SET NAMES utf8;");

    $tables = [];
    $result = $this->query("show tables");
    foreach($result as $row) {
      $tables[] = $row[0];
    }

    if(!in_array('sms_log', $tables)) {
      $this->query("CREATE TABLE IF NOT EXISTS sms_log (id int(11) NOT NULL auto_increment, `nummer` text NOT NULL, `date` datetime NOT NULL, PRIMARY KEY  (`id`)) DEFAULT CHARSET=utf8 ENGINE = INNODB;");
    }

    if(!in_array('test_sms', $tables)) {
      $this->query("CREATE TABLE IF NOT EXISTS test_sms (id int(11) NOT NULL auto_increment, `number` text NOT NULL, `text` text not null, `date` date NOT NULL, PRIMARY KEY  (`id`)) DEFAULT CHARSET=utf8 ENGINE = INNODB;");
    }

    if(!in_array('voucher_settings', $tables)) {
      $this->query("CREATE TABLE IF NOT EXISTS voucher_settings (`name` varchar(100) NOT NULL, `value` text NOT NULL) DEFAULT CHARSET=utf8 ENGINE = INNODB;");
      $this->query("ALTER TABLE voucher_settings ADD PRIMARY KEY(`name`)");
    }

    if(!in_array('sms_history', $tables)) {
      $this->query("CREATE TABLE IF NOT EXISTS sms_history (id int(11) NOT NULL auto_increment, `number` text NOT NULL, `date` datetime NOT NULL, `status` int not null, message text, PRIMARY KEY  (`id`)) DEFAULT CHARSET=utf8 ENGINE = INNODB;");
    }

  }

  function query($statement, $params = array()) {
    $ps = $this->db->prepare($statement);

    $index = 1;
    foreach($params as $v) {
      $ps->bindValue($index, $v);
      $index = $index + 1;
    }

    $ps->execute();

    $errors = $ps->errorInfo();
    if($errors[0] != 0) {
      print('db errors');
      print_r($errors);
    }

    return $ps;
  }

  function quote($i) {
    return "`" . str_replace("`", "", $i) . "`";
  }

  function atomic($wrapped) {
    try {
      $this->db->beginTransaction();
      $ret = $wrapped();
      $this->db->commit();
      return $ret;
    } catch(Exception $e) {
      $this->db->rollback();
      die();
    }
  }

  function deleteAllRows($table) {
    $this->atomic(function () use($table) {
      $this->query("TRUNCATE " . $this->quote($table));
    });
  }

  function addTicketToTable($table, $value) {
    $this->atomic(function () use($table, $value) {
      $this->query("INSERT INTO " . $this->quote($table) . " VALUES ('', ?, '0')", [
          $value
      ]);
    });
  }

  function addSms($number, $text) {
    $this->atomic(function () use($number, $text) {
      $this->query("insert into test_sms (number, `text`, `date`) values(?, ?, curdate());", [$number, $text]);
    });
  }

  function getAllSms() {
    return $this->atomic(function () {
      $sms = [];
      foreach($this->query("select * from test_sms;") as $row) {
        $sms[] = ['number' => $row['number'], 'text' => $row['text']];
      }
      return $sms;
    });
  }

  function updateSetting($key, $value) {
    $this->atomic(function () use($key, $value) {
      $result = $this->query("select count(*) as num from voucher_settings where `name` = ?;", [
          $key
      ]);
      $data = $result->fetch();
      if($data['num'] > 0) {
        $this->query("UPDATE voucher_settings SET `value`= ? WHERE `name`= ?;", [
            $value,
            $key
        ]);
      } else {
        $this->query("insert into voucher_settings (`name`, `value`) values(?, ?);", [
            $key,
            $value
        ]);
      }
    });
  }

  function createTicketTable($name) {
    $this->atomic(function () use($name) {
      $q = "CREATE TABLE IF NOT EXISTS " . $this->quote($name) . " (`id` int(11) NOT NULL auto_increment, `code` varchar(15) NOT NULL, `printed` tinyint(4) default NULL, PRIMARY KEY  (`id`), UNIQUE KEY `code` (`code`)) ENGINE = INNODB";
      $this->query($q);
    });
  }

  function getSettings() {
    return $this->atomic(function () {
      $settings_r = $this->query("SELECT * FROM voucher_settings");
      $settings = array();

      foreach($settings_r as $row) {
        $settings[$row["name"]] = $row["value"];
      }
      return $settings;
    });
  }

  // fetches the oldest non printed tickets, set printed to 1 and returns an array of arrays like [[id, ticket],...]
  function activateTickets($table, $count) {
    return $this->atomic(function () use($table, $count) {
      $mysql = $this->query("SELECT id, code FROM " . $this->quote($table) . " WHERE printed = 0 ORDER BY id LIMIT $count;"); // auszudruckende Voucher abfragen
      $this->query("UPDATE " . $this->quote($table) . " SET printed = 1 WHERE printed = 0 ORDER BY id LIMIT $count;"); // Voucher als gedruckt markieren
      $data = array(); // Array mit Vouchercode und ID erzeugen
      while($row = $mysql->fetch()) {
        $data[] = [
            $row['id'],
            $row['code']
        ];
      }
      return $data;
    });
  }

  function getStatisticsForTable($table) {
    return $this->atomic(function () use($table) {
      $mysql = $this->query("SELECT COUNT(*) AS c FROM " . $this->quote($table) . " where printed = 1");
      $row = $mysql->fetch();
      $used = $row['c'];

      $mysql = $this->query("SELECT COUNT(*) AS c FROM " . $this->quote($table) . " where printed = 0");
      $row = $mysql->fetch();
      $unused = $row['c'];

      return [
          $used + $unused,
          $unused,
          $used
      ];
    });
  }

  function logNumber($empf) {
    $this->atomic(function () use($empf) {
      $mysql = $this->query("SELECT `date` FROM sms_log WHERE nummer = ?", [
          $empf
      ]);
      if($mysql->fetch()) { // is it in the db? update it
        $this->query("UPDATE sms_log SET `date` = NOW() WHERE nummer = ?;", [
            $empf
        ]);
      } else { // otherwise create it anew
        $this->query("INSERT INTO sms_log (nummer, `date`) VALUES(?, NOW())", [
            $empf
        ]);
      }
    });
  }

  function addHistory($number, $code, $message) {
    $this->atomic(function () use($number, $code, $message) {
      $this->query("INSERT INTO sms_history (number, `date`, status, message) VALUES(?, NOW(), ?, ?)", [
          $number, $code, $message
          ]);
    });
  }

  function removeLog($number) {
    $this->atomic(function () use($number) {
      $mysql = $this->query("delete from sms_log WHERE nummer = ?", [
          $number
          ]);
    });
  }

  function numberIsNotLocked($empf, $time) {
    $time = round($time * 60); # from minutes to seconds
    return $this->atomic(function () use($empf, $time) {
      $mysql = $this->query('SELECT `date` FROM sms_log WHERE nummer = ?', [
          $empf
      ]);
      if($mysql->rowCount() > 0) { // Ist in Datenbank
        while($row = $mysql->fetch()) {
          $data = strtotime($row['date']);
        }

        if($data < strtotime("-$time seconds"))
          return 1; // older than expiration time
        else
          return 0; // still in expiration time
      }
      return 1; // not in db, not locked
    });
  }
}

?>


