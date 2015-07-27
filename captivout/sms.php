<?php
require_once ("include/setup.inc.php");
require_once ("include/auth.inc.php");
require_once ("service/sms.inc.php");

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
        $view->addInfo('number-is-blocked');
      } else {
        $view->addInfo('number-is-not-blocked');
      }
    }

    // Nummer blocken
    if(isset($_POST['block'])) {
      $sms->block();
      $view->addInfo('number-blocked', ['number' => $sms->getNumber()]);
    }

    // unblock number
    if(isset($_POST['unblock'])) {
      $sms->unblock();
      $view->addInfo('number-unblocked', ['number' => $sms->getNumber()]);
    }

    // send code to number
    if(isset($_POST['send'])) {
      try {
        $sms->send();
        $view->addInfo("sendt-sms");
      } catch(\sms\NoUnusedTicketsException $e) {
        $view->addInfo("no-unused-tickets");
      } catch(\sms\NumberIsLockedException $e) {
        $view->addInfo("number-is-blocked");
      } catch(\sms\GatewayErrorException $e) {
        $view->addInfo("gateway-error");
      }
    }
  }
}

$view->set('configs', $config->get('sms_gateway'));

$view->render("sms.html");

?>