<?php
require_once ("include/setup.inc.php");
require_once ("include/auth.inc.php");

if(isset($_POST['submit'])) {
  foreach(["vou_header", "sms_text", "sms_gtwkey", "sms_voutbl", "vou_text", "vou_label"] as $key) {
    if(!empty($_POST[$key])) {
      $db->updateSetting($key, $_POST[$key]);
    }
  }

  if(!empty($_POST['tbl_header'])) {
    $tbl_header = trim($_POST['tbl_header']);
    $tbl_header = explode("\n", $tbl_header);
    $tbl_header = array_filter($tbl_header);
    $db->updateSetting("tbl_header", json_encode($tbl_header));
  }
  if(!empty($_POST['dbtables'])) {
    $dbtables = trim($_POST['dbtables']);
    $dbtables = explode("\n", $dbtables);
    foreach($dbtables as $value) {
      $line = explode("|", $value);
      $final[$line[0]] = $line[1];
      $db->createTicketTable($line[0]);
    }
    array_filter($final);
    $db->updateSetting("dbtables", json_encode($final));
  }
}

$model['title'] = $lang->get("config");
$model['tableHeaders'] = $settings['tbl_header'];
$model['voucherHeading'] = $settings['vou_header'];
$model['voucherText'] = $settings['vou_text'];
$model['voucherLabel'] = $settings['vou_label'];
$model['smsVoucherTable'] = $settings['sms_voutbl'];
$model['smsText'] = $settings['sms_text'];
$model['smsGatewayKey'] = $settings['sms_gtwkey'];

print($twig->render('settings.html', $model));
?>