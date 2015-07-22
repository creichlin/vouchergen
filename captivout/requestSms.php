<?php
require_once ("include/setup.inc.php");
require_once ("include/sms_api.php");

$qs = $_SERVER['QUERY_STRING'];
$qs = explode("[DEL]", $qs);
if(count($qs) == 2) {
  $_SESSION['pa'] = $qs[0];
  $_SESSION['pr'] = $qs[1];
}

$view->set("portalAction", $_SESSION['pa']);
$view->set("portalRedirect", $_SESSION['pr']);

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
