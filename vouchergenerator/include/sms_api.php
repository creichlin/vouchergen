<?php

function send_code($empf) { // SMS verschicken
  global $settings, $db;

  $ticketCode = $db->activateTickets($settings['sms_voutbl'], 1)[0][1];

  $dest = "00491" . $empf; // Handynummer im int. Format zusammensetzen

  $text = $settings['sms_text'] . $data; // Text zusammensetzen
  $text = urlencode($text); // Text URL-Encodieren
  $fileOpenTRI = "https://www.smsflatrate.net/schnittstelle.php?key=" . $settings['sms_gtwkey'] . "&to=" . $dest . "&text=" . $text . "&type=20";
  $gatewayAnswer = @file($fileOpenTRI); // SMS verschicken
  return $gatewayAnswer[0]; // Antwort des Gateways zurÃ¼ckschicken
}

function verify_number($empf) {
  global $db;
  return $db->numberIsNotLocked();
}

function block_number($empf) {
  global $db;
  $db->logNumber($empf);
}
?>