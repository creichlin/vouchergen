<?php

namespace config;

class Config {
  private $name = NULL;
  private $values = [ ];

  function __construct($name) {
    $this->name = $name;
    if (file_exists ( "/etc/vogen/{$this->name}" )) {
      $this->values = parse_ini_file ( "/etc/vogen/{$this->name}" );
    }
  }

  function get($key, $default) {
    if ($this->has ( $key )) {
      return $this->values [$key];
    }
    return $default;
  }

  function has($key) {
    return array_key_exists ( $key, $this->values );
  }
}

?>