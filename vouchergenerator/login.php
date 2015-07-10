<?php
include ("include/zugriff.inc.php");
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	session_start();
	$username = mysql_real_escape_string($_POST['username']);
	$passwort = sha1(mysql_real_escape_string($_POST['passwort']));
	$user_q = "SELECT * FROM voucher_settings WHERE name='username' AND value='$username'";
	$user_r = mysql_query($user_q);
	$pass_q = "SELECT * FROM voucher_settings WHERE name='password' AND value='$passwort'";
	$pass_r = mysql_query($pass_q);
	$count = @mysql_num_rows($user_r) + @mysql_num_rows($pass_r);
	if ($count==2) {
		$_SESSION['angemeldet'] = true;
		if ($_SERVER['SERVER_PROTOCOL'] == 'HTTP/1.1') {
			if (php_sapi_name() == 'cgi') {
				header('Status: 303 See Other');
			}
			else {
				header('HTTP/1.1 303 See Other');
			}
		}
	$host  = $_SERVER['HTTP_HOST'];
	$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
	$extra = 'index.php';
	header("Location: http://$host$uri/$extra");
	exit;
	}
	else {
	$message = "Es ist ein Fehler aufgetreten.";
	}
}
$title = $lang->get("login");
include("include/header.inc.php");
?>
<?php
if (isset($message)) echo "<h2>".$message."</h2>";
?>
<form action="login.php" method="post">
	<label id="Label1" for="username"><?php echo $lang->get("username"); ?></label>
	<input type="text" name="username" />
	<label id="Label2" for="passwort"><?php echo $lang->get("password"); ?></label>
	<input type="password" name="passwort" />
	<input type="submit" value="Anmelden" />
</form>
<?php
include("include/footer.inc.php");
?>