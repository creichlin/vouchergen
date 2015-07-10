<?php

$settings_q = "SELECT * FROM voucher_settings";
$settings_r = mysql_query($settings_q);
$settings = array();
while ($row = mysql_fetch_array($settings_r, MYSQL_ASSOC)) {
	$settings[$row["name"]] = $row["value"];
	if(json_decode($row["value"], true)!=NULL)
   	$settings[$row["name"]] = json_decode($row["value"], true);
}
if (isset($_SESSION['angemeldet'])) {
 if ($_SESSION['angemeldet']) {
 $login = true;
} 
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<link rel="stylesheet" type="text/css" href="style.css" />
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<title><?php echo $title; ?></title>
</head>

<body>
<div id="header">
				<h1 id="topic"><?php echo $title; ?></h1>
		<div id="menu">
		<ul>
			<li><a href="index.php"><?php echo $lang->get("vouchermgmt"); ?></a></li><?php if (isset($login)) { if ($login) echo"<li><a href=\"statistik.php\">".$lang->get("stats")."</a></li><li><a href=\"sms.php\">".$lang->get("sms")."</a></li><li><a href=\"config.php\">".$lang->get("config")."</a></li><li><a href=\"logout.php\">".$lang->get("logout")."</a></li>";} ?>
		</ul>
		</div>
</div>
	<div id="content">
