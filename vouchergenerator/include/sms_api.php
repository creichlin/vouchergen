<?php

if (!isset($settings)){
$settings_q = "SELECT * FROM voucher_settings";
$settings_r = mysql_query($settings_q);
$settings = array();
while ($row = mysql_fetch_array($settings_r, MYSQL_ASSOC)) {
	$settings[$row["name"]] = $row["value"];
	if(json_decode($row["value"], true)!=NULL)
   	$settings[$row["name"]] = json_decode($row["value"], true);
}
}
function send_code($empf) { //SMS verschicken
global $settings;
$mysql = mysql_query("SELECT code FROM " . $settings['sms_voutbl'] . "  WHERE printed = 0 ORDER BY id LIMIT 1"); //Code abholen
mysql_query("UPDATE `" . $settings['sms_voutbl'] . "` SET `printed`=1 WHERE printed = 0 ORDER BY id LIMIT 1"); //als gedruckt markieren
$data = array(); //Code in Array laden
$i = 0;
while ($row = mysql_fetch_assoc($mysql)) {
	$data = $row['code'];
}
$dest="00491" . $empf; //Handynummer im int. Format zusammensetzen
$text= $settings['sms_text'] . $data; //Text zusammensetzen
$text = urlencode($text); //Text URL-Encodieren
$fileOpenTRI = "https://www.smsflatrate.net/schnittstelle.php?key=" . $settings['sms_gtwkey'] . "&to=" . $dest . "&text=" . $text . "&type=20";
$gatewayAnswer = @file($fileOpenTRI); //SMS verschicken
return $gatewayAnswer[0]; //Antwort des Gateways zurÃ¼ckschicken
}

function verify_number($empf) {
$sms_logtbl = "sms_log";
$mysql = mysql_query('SELECT timestamp FROM ' . $sms_logtbl . ' WHERE nummer = ' . $empf); //PrÃ¼fen ob bereits in DB
if (mysql_num_rows($mysql)>0) { //Ist in Datenbank
	$data = array();
	$i = 0;
	while ($row = mysql_fetch_assoc($mysql)) {
		$data = $row['timestamp'];
	}
	if ($data!=date('Y-m-d')) return 1; //letzer Abruf ist ungleich heute, gebe 1 zurÃ¼ck
	else return 0; //letzer Abruf ist heute, gebe 0 zurÃ¼ck
}
return 1; //ist nicht in Datenbank, gebe 1 zurÃ¼ck
}

function block_number($empf) {
global $settings;
$sms_logtbl = "sms_log";
$mysql = mysql_query('SELECT timestamp FROM ' . $sms_logtbl . ' WHERE nummer = ' . $empf); //PrÃ¼fen ob bereits in DB
if (mysql_num_rows($mysql)>0) { //Ist in Datenbank
	$sql = "UPDATE " . $sms_logtbl . " SET timestamp = CURDATE() WHERE nummer = " . $empf; //Timestamp auf heutiges Datum Ã¤ndern
	mysql_query($sql);
}
else { //Ist nicht in der Datenbank
	$sql = "INSERT INTO " . $sms_logtbl . " VALUES ('', " . $empf . ", CURDATE())"; //Eintrag zur Datenbank hinzufÃ¼gen
	mysql_query($sql);
}
}
?>
