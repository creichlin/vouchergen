<?php
require_once("include/setup.inc.php");
require_once("include/auth.inc.php");
$title = $lang->get("config");
if (isset($_POST['submit'])) {
if (!empty($_POST['tempvz']))
	$q[] = "UPDATE voucher_settings SET `value`='".mysql_real_escape_string($_POST['tempvz'])."' WHERE `name`='tempvz'";
if (!empty($_POST['username']))
	$q[] = "UPDATE voucher_settings SET `value`='".mysql_real_escape_string($_POST['username'])."' WHERE `name`='username'";
if (!empty($_POST['password']))
	$q[] = "UPDATE voucher_settings SET `value`='".sha1(mysql_real_escape_string($_POST['password']))."' WHERE `name`='password'";
if (!empty($_POST['vou_header']))
	$q[] = "UPDATE voucher_settings SET `value`='".mysql_real_escape_string($_POST['vou_header'])."' WHERE `name`='vou_header'";
if (!empty($_POST['sms_text']))
	$q[] = "UPDATE voucher_settings SET `value`='".mysql_real_escape_string($_POST['sms_text'])."' WHERE `name`='sms_text'";
if (!empty($_POST['sms_gtwkey']))
	$q[] = "UPDATE voucher_settings SET `value`='".mysql_real_escape_string($_POST['sms_gtwkey'])."' WHERE `name`='sms_gtwkey'";
if (!empty($_POST['sms_voutbl']))
	$q[] = "UPDATE voucher_settings SET `value`='".mysql_real_escape_string($_POST['sms_voutbl'])."' WHERE `name`='sms_voutbl'";
if (!empty($_POST['vou_text']))
	$q[] = "UPDATE voucher_settings SET `value`='".mysql_real_escape_string($_POST['vou_text'])."' WHERE `name`='vou_text'";
if (!empty($_POST['vou_label']))
	$q[] = "UPDATE voucher_settings SET `value`='".mysql_real_escape_string($_POST['vou_label'])."' WHERE `name`='vou_label'";
if (!empty($_POST['tbl_header'])) {
	$tbl_header = trim($_POST['tbl_header']);
	$tbl_header = explode("\n", $tbl_header);
	$tbl_header = array_filter($tbl_header);
	$q[] = "UPDATE voucher_settings SET `value`='".mysql_real_escape_string(json_encode($tbl_header))."' WHERE `name`='tbl_header'";
	}
if (!empty($_POST['dbtables'])) {
	$dbtables = trim($_POST['dbtables']);
	$dbtables = explode("\n", $dbtables);
		foreach($dbtables as $value) {
		$line = explode("|", $value);
		$final[$line[0]] = $line[1];
		mysql_query("CREATE TABLE IF NOT EXISTS `" .  $line[0] ."` (`id` int(11) NOT NULL auto_increment, `code` varchar(15) NOT NULL, `printed` tinyint(4) default NULL, PRIMARY KEY  (`id`), UNIQUE KEY `code` (`code`))");
		}
	array_filter($final);	
	$q[] = "UPDATE voucher_settings SET `value`='".mysql_real_escape_string(json_encode($final))."' WHERE `name`='dbtables'";
	}		

foreach($q as $value){
mysql_query($value);
echo mysql_error();
}
}
require_once("include/header.inc.php");
?>
<form method="post" action="config.php">
<label for="tempvz"><?php echo $lang->get("temp_vz"); ?></label>
<input type="text" name="tempvz" value="<?php echo $settings['tempvz']; ?>">
<label for="username"><?php echo $lang->get("username"); ?></label>
<input type="text" name="username" value="<?php echo $settings['username']; ?>">
<label for="password"><?php echo $lang->get("password_change"); ?></label>
<input type="password" name="password" value="">
<label for="vou_header"><?php echo $lang->get("voucher_heading"); ?></label>
<input type="text" name="vou_header" value="<?php echo $settings['vou_header']; ?>">
<label for="vou_text"><?php echo $lang->get("voucher_text"); ?></label>
<input type="text" name="vou_text" size="50" value="<?php echo $settings['vou_text']; ?>">
<label for="vou_label"><?php echo $lang->get("voucher_prefix"); ?></label>
<input type="text" name="vou_label" value="<?php echo $settings['vou_label']; ?>">
<label for="tbl_header"><?php echo $lang->get("tbl_header"); ?></label>
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
if (!empty($settings['dbtables'])) {
foreach($settings['dbtables'] as $key => $value) {
echo $key."|".$value;
echo "\n";
}
}
?>
</textarea>

<label for="sms_voutbl"><?php echo $lang->get("sms_voutbl"); ?></label>
<input type="text" name="sms_voutbl" value="<?php echo $settings['sms_voutbl']; ?>">
<label for="sms_text"><?php echo $lang->get("sms_txt"); ?></label>
<input type="text" name="sms_text" size="50" value="<?php echo $settings['sms_text']; ?>">
<label for="sms_gtwkey"><?php echo $lang->get("sms_gtwkey"); ?></label>
<input type="text" name="sms_gtwkey" value="<?php echo $settings['sms_gtwkey']; ?>">
<input name="submit" type="submit" value="Speichern" />
</form>
<?php
require_once("include/footer.inc.php");
?>