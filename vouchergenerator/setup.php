<?php
$host  = $_SERVER['HTTP_HOST'];
$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
$extra = 'setup.php';
$url = "http://$host$uri/$extra";

require_once("include/lang.php");
if (isset($_GET['en'])) {
$lang = new aLang("main", "en");
$languagelink = "<a href=\"".$url."\">Deutsche Version installieren</a>";
$cc = "en";
}
else {
$lang = new aLang("main", "de");
$languagelink = "<a href=\"".$url."?en\">Install English version</a>";
$cc = "de";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<link rel="stylesheet" type="text/css" href="style.css" />
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<title>Installation</title>
</head>

<body>
<div id="header">
<h1 id="topic">Setup</h1>
		<div id="menu">
		<ul>
			<li><a href="index.php">Voucherverwaltung</a></li>
		</ul>
		</div>
</div>
	<div id="content">
<h2>Install-Tool</h2><br /><?php
if (!isset($_POST['submit'])) {
echo $languagelink;
?>
<br />
<?php echo $lang->get("setup_pleaseenter"); ?><br />
<form method="post" action="">
<?php echo $lang->get("setup_dbhost"); ?><br />
<input name="dbhost" type="text" value="localhost" /><br />
<?php echo $lang->get("setup_dbuser"); ?><br />
<input name="dbuser" type="text" /><br />
<?php echo $lang->get("setup_dbpw"); ?><br />
<input name="dbpass" type="password" /><br />
<?php echo $lang->get("setup_dbname"); ?><br />
<input name="dbname" type="text" /><br />
<?php echo $lang->get("setup_tempvz"); ?><br />
<input name="tempvz" type="text" value="temp/" /><br />
<?php echo $lang->get("setup_username"); ?><br />
<input name="username" type="text" /><br />
<?php echo $lang->get("setup_password"); ?><br />
<input name="password" type="password" /><br /><br />
<input name="submit" type="submit" value="<?php echo $lang->get("setup_submit"); ?>" />
</form>
<?php
}
else {
$dbdata = "<?php\n";
$dbdata .= "@mysql_connect('".$_POST['dbhost']."', '".$_POST['dbuser']."', '".$_POST['dbpass']."') or die(\"Verbindung zu MySQL gescheitert! Connection to MySQL failed!\");";
$dbdata .= "\n";
$dbdata .= "@mysql_select_db('".$_POST['dbname']."') or die(\"Datenbankzugriff gescheitert! Access to database failed!\");\n";
$dbdata .= "if (!isset(\$nolang)){";
$dbdata .= "require_once(\"include/lang.php\");\n";
if ($cc=="de")
$dbdata .= "\$lang = new aLang(\"main\", \"de\");\n";
if ($cc=="en")
$dbdata .= "\$lang = new aLang(\"main\", \"en\");\n";
$dbdata .= "}";
$dbdata .= "?>";
file_put_contents("include/zugriff.inc.php",$dbdata);
include("include/zugriff.inc.php");
mysql_query("CREATE TABLE IF NOT EXISTS `sms_log` (`id` int(11) NOT NULL auto_increment, `nummer` text NOT NULL, `timestamp` date NOT NULL, PRIMARY KEY  (`id`))");
mysql_query("CREATE TABLE IF NOT EXISTS `voucher_settings` (`name` varchar(100) NOT NULL, `value` text NOT NULL)");
mysql_query("ALTER TABLE `voucher_settings` ADD PRIMARY KEY(`name`)");
if ($cc=="de")
$q = "INSERT INTO voucher_settings (`name`, `value`) VALUES ('tempvz','".$_POST['tempvz']."'), ('username', '".$_POST['username']."'), ('password', SHA1('".$_POST['password']."')), ('vou_header', 'Gastnetz-Voucher'), ('vou_text', 'Bitte mit Netzwerk Testnetz verbinden!'), ('vou_label', 'Code: '), ('tbl_header', '".mysql_real_escape_string(json_encode(array('ID', 'Voucher', 'Name', 'Bemerkung')))."'), ('sms_voutbl',''), ('sms_gtwkey',''), ('sms_text', 'Hallo! Ihr Wlan-Vouchercode lautet: '), ('dbtables', '')";
if ($cc=="en")
$q = "INSERT INTO voucher_settings (`name`, `value`) VALUES ('tempvz','".$_POST['tempvz']."'), ('username', '".$_POST['username']."'), ('password', SHA1('".$_POST['password']."')), ('vou_header', 'Guestnetwork-Voucher'), ('vou_text', 'Please connect to GuestNetwork!'), ('vou_label', 'Code: '), ('tbl_header', '".mysql_real_escape_string(json_encode(array('ID', 'Voucher', 'Name', 'Remarks')))."'), ('sms_voutbl',''), ('sms_gtwkey',''), ('sms_text', 'Hello! Your wifi-vouchercode is: '), ('dbtables', '')";
//echo $q;
mysql_query($q);
echo "<br />";
echo mysql_error();
if ($cc=="de")
echo "Setup abgeschlossen. Bitte setup.php l&ouml;schen und dann index.php aufrufen.";
if ($cc=="en")
echo "Setup completed. Please delete setup.php and navigate to index.php.";
}
include ("include/footer.inc.php");
?>