<?php

namespace db;

class Db {

  function __construct($host, $username, $password, $schema) {
    @mysql_connect ( $host, $username, $password ) or die ( "Connection to MySQL failed!" );
    @mysql_select_db ( $schema ) or die ( "Access to database failed!" );
  }
}

?>


