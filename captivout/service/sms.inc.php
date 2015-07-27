<?php
namespace sms;

class NoUnusedTicketsException extends \Exception { }
class NumberIsLockedException extends \Exception { }
class GatewayErrorException extends \Exception { };

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
      $db->addHistory($this->number, 1, "number is locked");
      throw new NumberIsLockedException();
    }

    $ticketCodes = $db->activateTickets($this->config['table'], 1);

    if(count($ticketCodes) == 0) {
      $db->addHistory($this->number, 1, "no available tickets");
      throw new NoUnusedTicketsException();
    }


    $text = preg_replace("/{TICKET}/", $ticketCodes[0][1], $this->config['text']);

    $url = $this->config['httpGet'];

    $url = preg_replace("/{TEXT}/", urlencode($text), $url);
    $url = preg_replace("/{NUMBER}/", urlencode($this->number), $url);

    $response = $this->sendAsCurl($url, $ticketCodes[0][1]);

    $db->addHistory($this->number, 0, "sendt ticket " . $ticketCodes[0][1] . ". " . $response);

    $this->block();
  }

  function sendAsCurl($url, $code) {
    global $db;

    $c = \curl_init();
    \curl_setopt($c, CURLOPT_URL, $url);
    \curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    \curl_setopt($c, CURLOPT_MAXREDIRS, 10);
    \curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
    \curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 10);

    $data = \curl_exec($c);
    $status = \curl_getinfo($c);
    \curl_close($c);

    if($status['http_code'] != 200) {
      $db->addHistory($this->number, 1, "failed to send ticket " . $code . ". " . $data);
      throw new GatewayErrorException();
    }
    return $data;
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