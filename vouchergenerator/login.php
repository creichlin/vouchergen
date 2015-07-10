<?php

require_once("include/setup.inc.php");


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$username = $_POST['username'];
	$password = $_POST['passwort'];
	
	$lUsername = $config->get("username", "");
	$lPassword = $config->get("password", "");
	
	print($username);
	print($password);
	print($lUsername);
	print($lPassword);
	
	
	if(strlen($password) > 3 && $username == $lUsername && $password == $lPassword) {
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
	}	else {
		$message = "Es ist ein Fehler aufgetreten.";
	}
}
$title = $lang->get("login");
include("include/header.inc.php");

if (isset($message)) {
	echo "<h2>".$message."</h2>";
}

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