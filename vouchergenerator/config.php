<?php
require_once ("include/setup.inc.php");
require_once ("include/auth.inc.php");
$title = $lang->get("config");
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
require_once ("include/header.inc.php");
?>
<form method="post" action="config.php">
  <label for="vou_header"><?php echo $lang->get("voucher_heading"); ?></label>
  <input type="text" name="vou_header" value="<?php echo $settings['vou_header']; ?>"> <label for="vou_text"><?php echo $lang->get("voucher_text"); ?></label>
  <input type="text" name="vou_text" size="50" value="<?php echo $settings['vou_text']; ?>"> <label for="vou_label"><?php echo $lang->get("voucher_prefix"); ?></label>
  <input type="text" name="vou_label" value="<?php echo $settings['vou_label']; ?>"> <label for="tbl_header"><?php echo $lang->get("tbl_header"); ?></label>
  <textarea cols="20" rows="5" name="tbl_header">
<?php
foreach($settings['tbl_header'] as $value) {
  echo $value;
  echo "\n";
}
?>
</textarea>
  <label for="dbtables"><?php echo $lang->get("dbtables"); ?></label>
  <textarea cols="20" rows="5" name="dbtables">
<?php
if(!empty($settings['dbtables'])) {
  foreach($settings['dbtables'] as $key => $value) {
    echo $key . "|" . $value;
    echo "\n";
  }
}
?>
</textarea>

  <label for="sms_voutbl"><?php echo $lang->get("sms_voutbl"); ?></label> <input type="text" name="sms_voutbl"
    value="<?php echo $settings['sms_voutbl']; ?>"> <label for="sms_text"><?php echo $lang->get("sms_txt"); ?></label> <input
    type="text" name="sms_text" size="50" value="<?php echo $settings['sms_text']; ?>"> <label for="sms_gtwkey"><?php echo $lang->get("sms_gtwkey"); ?></label>
  <input type="text" name="sms_gtwkey" value="<?php echo $settings['sms_gtwkey']; ?>"> <input name="submit"
    type="submit" value="Speichern" />
</form>
<?php
require_once("include/footer.inc.php");
?>