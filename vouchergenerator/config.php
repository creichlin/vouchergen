<?php
require_once ("include/setup.inc.php");
require_once ("include/auth.inc.php");

if(isset($_POST['submit'])) {
  foreach(["vou_header", "sms_text", "sms_gtwkey", "sms_voutbl", "vou_text", "vou_label", "sms_gateway"] as $key) {
    if(!empty($_POST[$key])) {
      $db->updateSetting($key, $_POST[$key]);
    }
  }

  if(!empty($_POST['tbl_header'])) {
    $tbl_header = trim($_POST['tbl_header']);
    $tbl_header = explode("\n", $tbl_header);
    $db->updateSetting("tbl_header", json_encode($tbl_header, JSON_UNESCAPED_UNICODE));
  }
  if(!empty($_POST['dbtables'])) {
    $dbtables = trim($_POST['dbtables']);
    $dbtables = explode("\n", $dbtables);
    foreach($dbtables as $value) {
      $line = explode("|", $value);
      $final[$line[0]] = $line[1];
      $db->createTicketTable($line[0]);
    }
    $db->updateSetting("dbtables", json_encode($final, JSON_UNESCAPED_UNICODE));
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
$model['smsGateway'] = json_encode($config->get('sms_gateway'), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

$all = [];
foreach($config->default as $key => $value) {
  $f = $config->get($key, 'file');
  $e = $config->get($key, 'env');
  $d = $config->get($key, 'db');

  if(!is_string($f)) {
    $f = json_encode($f, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  }

  if(!is_string($e)) {
    $e = json_encode($e, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  }

  if(!is_string($d)) {
    $d = json_encode($d, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  }

  if(!is_string($value)) {
    $value = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
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