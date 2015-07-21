<?php
require_once ("include/setup.inc.php");
require_once ("include/sms_api.php");


if(isset($_POST['submit'])) {
  $sms = new \sms\Sms($config->get("sms_gateway")[0], $_POST['nummer']);

  if($sms->isValid()) {
    try {
      $sms->send();
      $view->addInfo("sms-request-sendt-sms");
    } catch(\sms\NoUnusedTicketsException $e) {
      $view->addInfo("sms-request-no-unused-tickets");
    } catch(\sms\NumberIsLockedException $e) {
      $view->addInfo("sms-request-number-is-blocked");
    }
  } else {
    $view->addWarning("sms-request-invalid-number");
  }
}


$view->render('requestSms.html');

?>
