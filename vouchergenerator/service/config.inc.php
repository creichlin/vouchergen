<?php

namespace config;

class Config {
  private $name = NULL;
  private $values = [];
  private $ownValues = [];
  private $defaults = [
      # only settings from system envs/config file
      "db-host" => "localhost",
      "db-username" => "voucher",
      "db-password" => "voucher",
      "db-schema" => "voucher",
      "username" => "",
      "password" => "",

      # settings from db
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

  function __construct($name) {
    $this->values = $this->defaults; // arrays are copied
    $this->name = $name;
    if(file_exists("/etc/vogen/{$this->name}")) {
      $data = parse_ini_file("/etc/vogen/{$this->name}");
      foreach($data as $key => $value) {
        $this->values[$key] = $value;
        $this->ownValues[$key] = $value;
      }
    }
  }

  function initDbValues($values) {
    foreach($values as $key => $value) {
      $this->values[$key] = $value;
      $this->ownValues[$key] = $value;
    }
  }

  function get($key) {
    return $this->values[$key];
  }

  function has($key) {
    return array_key_exists($key, $this->ownValues);
  }
}

?>