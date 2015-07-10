<?php

require_once("include/setup.inc.php");


require_once("include/auth.inc.php");
$title = $lang->get("vouchermgmt");
require_once("include/header.inc.php");

//Soll eine Tabelle geleert werden? Dann entsprechend löschen
if (isset($_POST['submit_delete'])) {
	mysql_query("TRUNCATE " . mysql_real_escape_string($_POST['select_delete']));
	$message = $lang->get("message_db_del");
}
//Datei hochladen und in Datenbank einfügen
if (isset($_POST['submit_upload'])) {
	if (isset($_FILES['datei'])) { //existiert eine Datei?
		$tempname = $_FILES['datei']['tmp_name'];
		$dateiname = $_FILES['datei']['name'];
		move_uploaded_file($tempname, $settings['tempvz'] . $dateiname); //Datei ins Temp-Verzeichnis schieben
		$uploaded_csv = array(); //Datei in Array laden
		$uploaded_csv = file($settings['tempvz'] . $dateiname);
		array_splice($uploaded_csv, 0, 7); //Zeilen 1-7 löschen (Kommentare)
		foreach ($uploaded_csv as $key => $value) {
			set_time_limit(30);
			$uploaded_csv[$key] = str_replace('"','',$uploaded_csv[$key]); //Anführungszeichen entfernen
			$uploaded_csv[$key] = trim($uploaded_csv[$key]); //Leerzeichen entfernen
			mysql_query("INSERT INTO " . mysql_real_escape_string($_POST['select_upload']) . " VALUES ('', '" . $uploaded_csv[$key] . "', '0')"); //in Datenbank schreiben
		}
		unlink($settings['tempvz'] . $dateiname); //hochgeladene Datei aus Temp-Verzeichnis löschen
		$message = $lang->get("message_upload_succeed");
	}
}
if (empty($settings['dbtables'])) {
$message = $lang->get("dbtables_empty");
}
if (isset($message)) echo "<h3>".$lang->get("notice").": " . $message . "</h3>"; //Statusnachricht ausgeben
?>
<h2><?php echo $lang->get("gen_voucher_pdf"); ?></h2>
<form method="post" action="print.php">
<?php echo $lang->get("number_of_vouchers"); ?><input name="number" value="24" type="text" /> <br />
<?php echo $lang->get("database"); ?>
<select name="select_print">
<?php //Tabellenauswahl generieren
if (!empty($settings['dbtables'])) {
foreach($settings['dbtables'] as $key => $value){
	echo '<option value="' . $key . '">' . $value . '</option>';
	echo "\n";
}
}
?>
</select>
<input name="submit_print" type="submit" value="<?php echo $lang->get("generate"); ?>" />
</form>
<h2><?php echo $lang->get("upload_vouchers"); ?></h2>
<?php echo $lang->get("upload_vouchers_desc"); ?>
<form action="index.php" method="post" enctype="multipart/form-data">
<?php echo $lang->get("database"); ?><br />
<select name="select_upload">
<?php
if (!empty($settings['dbtables'])) {
foreach($settings['dbtables'] as $key => $value){
	echo '<option value="' . $key . '">' . $value . '</option>';
	echo "\n";
}
}
?>
</select>
<?php echo $lang->get("file"); ?>:<br />
<input type="file" name="datei" />
<input type="submit" name="submit_upload" value="<?php echo $lang->get("upload"); ?>" />
<br /><?php echo $lang->get("upload_please_patient"); ?>
</form>
<h2><?php echo $lang->get("database_empty"); ?></h2>
<b><?php echo $lang->get("database_empty_warning"); ?></b><br />
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<select name="select_delete">
<?php //Tabellenauswahl generieren
if (!empty($settings['dbtables'])) {
foreach($settings['dbtables'] as $key => $value){
	echo '<option value="' . $key . '">' . $value . '</option>';
	echo "\n";
}
}
?>
</select>
<input name="submit_delete" type="submit" value="<?php echo $lang->get("database_empty"); ?>" onclick="return window.confirm('<?php echo $lang->get("are_you_sure"); ?>');" />
</form>
<?php
include("include/footer.inc.php");
?>