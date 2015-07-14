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
      "vou_header" => "VOU_HEADER",
      "vou_text" => "VOU_TEXT",
      "vou_label" => "VOU_LABEL",
      "sms_voutbl" => "SMS_VOUTBL",
      "sms_text" => "SMS_TEXT",
      "sms_gtwkey" => "SMS_GTWKEY"
  ];

  private $sources = [];

  function __construct($name) {
    $this->name = $name;

    $this->sources['default'] = &$this->default;
    $this->sources['file'] = &$this->file;
    $this->sources['env'] = &$this->env;
    $this->sources['db'] = &$this->db;

    // read values from config file
    if(file_exists("/etc/vogen/{$this->name}")) {
      $data = parse_ini_file("/etc/vogen/{$this->name}");
      foreach($data as $key => $value) {
        if(array_key_exists($key, $this->default)) {
          $this->file[$key] = $value;
        } else {
          print("invalid config key: $key");
        }
      }
    }

    // read from env variables
    foreach($this->default as $key => $value) {
      $val = getenv("vg_$key");
      if($val) {
        $this->env[$key] = $val;
      }
    }
  }

  function initDbValues($values) {
    $this->db = [];
    foreach($values as $key => $value) {
      if(array_key_exists($key, $this->default)) {
        $this->db[$key] = $value;
      } else {
        print("invalid db config key: $key");
      }
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