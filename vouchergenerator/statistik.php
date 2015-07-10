<?php
//Einstellungen laden und Datenbankverbindung herstellen
require_once("include/setup.inc.php");

require_once("include/auth.inc.php");
$title = $lang->get("stats");
require_once("include/header.inc.php");
?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<select name="select_stat">
<?php
foreach($settings['dbtables'] as $key => $value){
	echo '<option value="' . $key . '">' . $value . '</option>';
	echo "\n";
}
?>
</select>
<input name="submit_stat" type="submit" value="<?php echo $lang->get("show_button"); ?>" />
</form>
<?php
if (isset($_POST['submit_stat'])){ //Wenn Statistik abgefragt ist
	echo "<h2>".$lang->get("database").": " . $dbtables[$_POST['select_stat']] . "</h2>";
	echo "<br />";
	echo $lang->get("number_codes").": ";
	$mysql = mysql_query("SELECT COUNT(*) AS c FROM " . $_POST['select_stat']);
	$result = mysql_fetch_assoc($mysql);
	echo $result['c'];
	echo "<br />".$lang->get("number_printedcodes").": ";
	$mysql = mysql_query("SELECT COUNT(*) AS c FROM " . $_POST['select_stat'] . " WHERE printed != 0");
	$result = mysql_fetch_assoc($mysql);
	echo $result['c'];
	echo "<br />".$lang->get("number_freecodes").": ";
	$mysql = mysql_query("SELECT COUNT(*) AS c FROM " . $_POST['select_stat'] . " WHERE printed = 0");
	$result = mysql_fetch_assoc($mysql);
	echo $result['c'];
}
include("include/footer.inc.php");
?>


