<?php
require_once ("include/setup.inc.php");
require_once ("include/auth.inc.php");
require_once ("include/sms_api.php");

$view->setTitle($lang->get("sms"));

if(isset($_POST['config'])) {
  // get config and number
  $conf = $config->get('sms_gateway')[intval($_POST['config'])];

  $sms = new \sms\Sms($conf, $_POST['nummer']);

  if(!$sms->isValid()) {
    $view->addWarning("invalid-mobile-number");
  } else {

    // check if number is locked
    if(isset($_POST['test'])) {
      if($sms->isLocked()) {
        $view->addInfo('number-is-not-allowed');
      } else {
        $view->addInfo('number-is-allowed');
      }
    }

    // Nummer blocken
    if(isset($_POST['block'])) {
      $sms->block($number);
      $view->addInfo('blocked-number');
    }

    // send code to number
    if(isset($_POST['send'])) {
      try {
        $sms->send();
        $view->addInfo("sendt-sms");
      } catch(\sms\NoUnusedTicketsException $e) {
        $view->addInfo("no-unused-tickets");
      } catch(\sms\NumberIsLockedException $e) {
        $view->addInfo("number-is-locked");
      }
    }
  }
}

$view->set('configs', $config->get('sms_gateway'));

$view->render("sms.html");

?>