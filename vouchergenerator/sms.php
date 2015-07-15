<?php
require_once ("include/setup.inc.php");
require_once ("include/auth.inc.php");
require_once ("include/sms_api.php");

$model['title'] = $lang->get("sms");


if(isset($_POST['config'])) {
  // get config and number
  $conf = $config->get('sms_gateway')[intval($_POST['config'])];

  $sms = new \sms\Sms($conf, $_POST['nummer']);

  if(!$sms->isValid()) {
    $model['message'] = "Invalid number";
  } else {

    // check if number is locked
    if(isset($_POST['test'])) {
      if(!$sms->isLocked()) {
        $model['message'] = "$number is allowed";
      } else {
        $model['message'] = "$number is not allowed";
      }
    }

    // Nummer blocken
    if(isset($_POST['block'])) {
      $sms->block($number);
      $model['message'] = "blocked number $number";
    }

    // send code to number
    if(isset($_POST['send'])) {
      $sms->send();
      $model['message'] = "sendt sms to number $number";
    }
  }
}

$model['configs'] = $config->get('sms_gateway');

print($twig->render("sms.html", $model));

?>