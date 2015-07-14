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
    $db->updateSetting("dbtables", json_encode($final));
  }
}

# they might have been changed above so update them
$config->initDbValues($db->getSettings());

$model['tables'] = $config->get('dbtables');
$model['title'] = $lang->get("config");
$model['tableHeaders'] = $config->get('tbl_header');
$model['voucherHeading'] = $config->get('vou_header');
$model['voucherText'] = $config->get('vou_text');
$model['voucherLabel'] = $config->get('vou_label');
$model['smsVoucherTable'] = $config->get('sms_voutbl');
$model['smsText'] = $config->get('sms_text');
$model['smsGatewayKey'] = $config->get('sms_gtwkey');

$all = [];
foreach($config->default as $key => $value) {
  $f = $config->get($key, 'file');
  $e = $config->get($key, 'env');
  $d = $config->get($key, 'db');

  if(!is_string($f)) {
    $f = json_encode($f);
  }

  if(!is_string($e)) {
    $e = json_encode($e);
  }

  if(!is_string($d)) {
    $d = json_encode($d);
  }

  if(!is_string($value)) {
    $value = json_encode($value);
  }

  $pw = strpos($key,'password') !== false;
  $all[] = [
    'key' => $key,
    'default' => $pw ? "********": $value,
    'file' => $pw && $f ? "********": $f,
    'env' => $pw && $e ? "********": $e,
    'db' => $pw && $d ? "********": $d,
  ];
}

$model['all'] = $all;

print($twig->render('settings.html', $model));
?>