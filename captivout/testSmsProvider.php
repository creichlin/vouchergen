<?php

require_once ("include/setup.inc.php");

function endsWith($haystack, $needle) {
  $length = strlen($needle);
  if ($length == 0) {
    return true;
  }

  return (substr($haystack, -$length) === $needle);
}


if(isset($_GET['number'])) {
  if(endsWith($_GET['number'], '99')) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    die("goodbye");
  }
  $db->addSms($_GET['number'], $_GET['text']);
}

$view->set('sms', $db->getAllSms());

$view->render('testSmsProvider.html');
?>