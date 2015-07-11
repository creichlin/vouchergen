<?php
require_once ("include/setup.inc.php");
require_once ("include/auth.inc.php");
$title = $lang->get("sms");
require_once ("include/sms_api.php");
require_once ("include/header.inc.php");
$message = "";
// Nummer prüfen
if(isset($_POST['test'])) {
  if(verify_number($_POST['handyvorwahl'] . $_POST['nummer'])) {
    $message = $lang->get("sms_number_allowed");
  } else {
    $message = $lang->get("sms_number_blocked");
  }
}
// Code an Nummer senden
if(isset($_POST['send'])) {
  $answer = send_code($_POST['handyvorwahl'] . $_POST['nummer']);
  $message = $lang->get("sms_gtw_response") . " " . $answer;
}
// Nummer blocken
if(isset($_POST['block'])) {
  block_number($_POST['handyvorwahl'] . $_POST['nummer']);
  $message = $lang->get("sms_number_blocked");
}
?>
<form action="sms.php" method="post">
  <h3><?php echo $message; ?></h3>
  <h2><?php echo $lang->get("sms_heading_manage"); ?></h2>
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
  <input name="nummer" value="" type="text" /> <input name="test" type="submit"
    value="<?php echo $lang->get("sms_test"); ?>" /> <input name="send" type="submit"
    value="<?php echo $lang->get("sms_send"); ?>" /> <input name="block" type="submit"
    value="<?php echo $lang->get("sms_block"); ?>" />
</form>
<?php
include("include/footer.inc.php");
?>