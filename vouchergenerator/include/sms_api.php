<?php
namespace sms;


class Sms {

  private $config;
  private $number;
  private $valid = FALSE;


  function __construct($config, $number) {
    $this->config = $config;
    $number = preg_replace("/[^0-9]/", '', $number);
    $this->valid = preg_match("/^" . $this->config['validator'] . "$/", $number);
    $number = preg_replace("/^0/", '', $number);
    $this->number = $this->config['countryPrefix'] . $number;
  }

  function isValid() {
    return $this->valid;
  }

  function send() {
    global $db;
    $ticketCodes = $db->activateTickets($this->config['table'], 1);
    $text = preg_replace("/{TICKET}/", $ticketCodes[0][1], $this->config['text']);

    $url = $this->config['httpGet'];

    $url = preg_replace("/{TEXT}/", urlencode($text), $url);
    $url = preg_replace("/{NUMBER}/", urlencode($this->number), $url);

    $gatewayAnswer = @file($url);
  }

  function getNumber() {
    return $this->number;
  }

  function isLocked() {
    global $db;
    return  $db->numberIsNotLocked($this->number);
  }

  function block() {
    global $db;
    return  $db->logNumber($this->number);
  }
}


?>