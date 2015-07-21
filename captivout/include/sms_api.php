<?php
namespace sms;

class NoUnusedTicketsException extends \Exception { }
class NumberIsLockedException extends \Exception { }

class Sms {

  private $config;
  private $number;
  private $valid = FALSE;
  private $answer;


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

    if($this->isLocked()) {
      throw new NumberIsLockedException();
    }

    $ticketCodes = $db->activateTickets($this->config['table'], 1);

    if(count($ticketCodes) == 0) {
      throw new NoUnusedTicketsException();
    }


    $text = preg_replace("/{TICKET}/", $ticketCodes[0][1], $this->config['text']);

    $url = $this->config['httpGet'];

    $url = preg_replace("/{TEXT}/", urlencode($text), $url);
    $url = preg_replace("/{NUMBER}/", urlencode($this->number), $url);

    $this->answer = @file($url);

    $this->block();
  }

  function getNumber() {
    return $this->number;
  }

  function isLocked() {
    global $db;
    return !$db->numberIsNotLocked($this->number);
  }

  function block() {
    global $db;
    return $db->logNumber($this->number);
  }

  function unblock() {
    global $db;
    return $db->removeLog($this->number);
  }

  function getAnswer() {
    return $this->answer;
  }
}


?>