<?php

namespace config;

class Config {
  private $name = NULL;
  private $file = [];
  private $env = [];
  private $db = [];
  public $default = [
      // only settings from system envs/config file
      "db-host" => "localhost",
      "db-username" => "voucher",
      "db-password" => "voucher",
      "db-schema" => "voucher",
      "username" => "",
      "password" => "",

      // settings from db
      "dbtables" => [
          'default' => 'Default',
          'sms' => 'SMS'
      ],
      "tbl_header" => [
          "A",
          "B",
          "C",
          "D"
      ],
      "vou_header" => "WLAN Ticket",
      "vou_text" => "connecto to guestweb",
      "vou_label" => "Code ",
      "sms_gateway" => [["label" => "Default","table" => "sms", "language" => "de", "countryPrefix" => "+41","example" => "079 123 45 67","text" => "Der code für das netz lala lautet {TICKET}","validator" => "0[0-9]{9}","httpGet" => "http://www.sms-revolution.ch/API/httpsms.php?user=test&password=pw&text={TEXT}&to={NUMBER}&action=info"]]
  ];

  private $sources = [];

  function __construct($name) {
    $this->name = $name;

    $this->sources['default'] = &$this->default;
    $this->sources['file'] = &$this->file;
    $this->sources['env'] = &$this->env;
    $this->sources['db'] = &$this->db;

    // read values from config file
    if(file_exists("/etc/captivout/{$this->name}")) {
      $data = parse_ini_file("/etc/captivout/{$this->name}");
      foreach($data as $key => $value) {
        $this->set($key, $value, "file");
      }
    }

    // read from env variables
    foreach($this->default as $key => $value) {
      $env = str_replace("-", "_", $key);
      $env = strtoupper($key);
      $val = getenv("CPO_$env");
      if($val) {
        $this->set($key, $val, 'env');
      }
    }
  }

  /**
   * to set a config key this method must be used
   */
  private function set($key, $value, $source) {
    if(array_key_exists($key, $this->default)) {

      // if value is a valid json string, convert it to json
      if(json_decode($value, true) != NULL) {
        $value = json_decode($value, true);
      }

      $this->sources[$source][$key] = $value;
    } else {
      print("invalid config key $key for source $source");
    }
  }

  function initDbValues($values) {
    $this->db = [];
    foreach($values as $key => $value) {
      $this->set($key, $value, 'db');
    }
  }

  function get($key, $source = NULL) {
    if($source) {
      if($this->isFrom($key, $source)) {
        return $this->sources[$source][$key];
      }
      return "";
    }
    if(array_key_exists($key, $this->db)) {
      return $this->db[$key];
    }
    if(array_key_exists($key, $this->env)) {
      return $this->env[$key];
    }
    if(array_key_exists($key, $this->file)) {
      return $this->file[$key];
    }
    if(array_key_exists($key, $this->default)) {
      return $this->default[$key];
    }
    die("invalid config key $key");
  }

  function isFrom($key, $source) {
    return array_key_exists($key, $this->sources[$source]);
  }

  function has($key) {
    return $this->isFrom($key, "file") || $this->isFrom($key, "env") || $this->isFrom($key, "db");
  }
}

?>