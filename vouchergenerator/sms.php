<?php
require_once ("include/setup.inc.php");
require_once ("include/auth.inc.php");
$title = $lang->get("sms");
require_once ("include/sms_api.php");


// Nummer prüfen
if(isset($_POST['test'])) {
  if(verify_number($_POST['handyvorwahl'] . $_POST['nummer'])) {
    $model['message'] = $lang->get("sms_number_allowed");
  } else {
    $model['message'] = $lang->get("sms_number_blocked");
  }
}
// Code an Nummer senden
if(isset($_POST['send'])) {
  $answer = send_code($_POST['handyvorwahl'] . $_POST['nummer']);
  $model['message'] = $lang->get("sms_gtw_response") . " " . $answer;
}
// Nummer blocken
if(isset($_POST['block'])) {
  block_number($_POST['handyvorwahl'] . $_POST['nummer']);
  $model['message'] = $lang->get("sms_number_blocked");
}

print($twig->render("sms.html", $model));

?>