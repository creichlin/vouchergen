<?php

require_once ("include/setup.inc.php");

if(isset($_GET['number'])) {
  $db->addSms($_GET['number'], $_GET['text']);
}

$view->set('sms', $db->getAllSms());

$view->render('testSmsProvider.html');
?>