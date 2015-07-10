<?php
$nolang = true;
require_once("../include/sms_api.php")
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<link rel="stylesheet" type="text/css" href="style.css" />
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
	<title>Voucher anfordern</title>
</head>

<body>
<div id="header">
	<h1 id="topic">Voucher anfordern</h1>
</div>
<div id="content">
<?php
if (isset($_POST['submit'])) {
	if (is_numeric($_POST['nummer']) and is_numeric($_POST['handyvorwahl'])) {
		$user = $_POST['handyvorwahl'] . $_POST['nummer'];
		if (verify_number($user)) {
			send_code($user);
			block_number($user);
			echo "<b>Code gesendet</b>";
		}
		else {
			echo "<b>Sie haben heute bereits einen Code angefordert</b>";
		}
	}
	else {
		echo "<b>Bitte eine g&uuml;ltige Nummer ohne Vorwahl angeben</b>";
	}
}
?>
<br />
<form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
<h2>Vouchercode anfordern</h2>
Bitte Vorwahl auswählen und Handynummer eingeben: <br />
<select name="handyvorwahl">
<option value="50">0150</option>
<option value="51">0151</option>
<option value="52">0152</option>
<option value="57">0157</option>
<option value="59">0159</option>
<option value="60">0160</option>
<option value="61">0161</option>
<option value="62">0162</option>
<option value="63">0163</option>
<option value="64">0164</option>
<option value="70">0170</option>
<option value="71">0171</option>
<option value="72">0172</option>
<option value="73">0173</option>
<option value="74">0174</option>
<option value="75">0175</option>
<option value="76">0176</option>
<option value="77">0177</option>
<option value="78">0178</option>
<option value="79">0179</option>
</select>
<input name="nummer" value="" type="text" />
<input name="submit" type="submit" value="Anfordern" />
<a href="javascript:history.go(-2)">Zur&uuml;ck zur Anmeldeseite</a>
</form>
</div>
</body>

</html>
