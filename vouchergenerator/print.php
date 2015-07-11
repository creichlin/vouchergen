<?php
require_once ("include/fpdf.php");
require_once ("include/zugriff.inc.php");
require_once ("include/auth.inc.php");

$settings = $db->getSettings();

class PDF extends FPDF // Klasse für FPDF-Tabelle
{

  function FancyTable($header, $data) {
    // Colors, line width and bold font
    $this->SetFillColor(255, 0, 0);
    $this->SetTextColor(255);
    $this->SetDrawColor(128, 0, 0);
    $this->SetLineWidth(.3);
    $this->SetFont('', 'B');
    // Header
    $w = array(
        10,
        45,
        80,
        35
    );
    for($i = 0; $i < count($header); $i ++)
      $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', true);
    $this->Ln();
    // Color and font restoration
    $this->SetFillColor(224, 235, 255);
    $this->SetTextColor(0);
    $this->SetFont('');
    // Data
    $fill = false;
    foreach($data as $row) {
      $this->Cell($w[0], 6, $row[0], 'LR', 0, 'C', $fill);
      $this->SetFont('Arial', '', 12);
      // $this->SetFont('isonorm_becker','',12);
      $this->Cell($w[1], 6, $row[1], 'LR', 0, 'C', $fill);
      $this->SetFont('Arial', '', 14);
      $this->Cell($w[2], 6, '', 'LR', 0, 'R', $fill);
      $this->Cell($w[3], 6, '', 'LR', 0, 'R', $fill);
      $this->Ln();
      $fill = !$fill;
    }
    // Closing line
    $this->Cell(array_sum($w), 0, '', 'T');
  }
}


$pdf = new PDF(); // Neues PDF-Objekt
                  // $pdf->AddFont('isonorm_becker','','isonorm_becker.php'); //Schriftart hinzufügen
if(is_numeric($_POST['number']))
  $count = $_POST['number']; // Wenn übergebene Voucheranzahl numerisch ist, übernehme diese
else
  $count = 24; // sonst 24 (eine DIN-A4-Seite)
$mysql = mysql_query("SELECT id, code FROM " . mysql_real_escape_string($_POST['select_print']) . " WHERE printed = 0 ORDER BY id LIMIT " . $count . " "); // auszudruckende Voucher abfragen
mysql_query("UPDATE `" . $_POST['select_print'] . "` SET `printed`=1 WHERE printed = 0 ORDER BY id LIMIT " . $count . " "); // Voucher als gedruckt markieren (wenn nicht im Testmodus)
$data = array(); // Array mit Vouchercode und ID erzeugen
$i = 0;
while($row = mysql_fetch_assoc($mysql)) {
  $data[$i][0] = $row['id'];
  $data[$i][1] = $row['code'];
  $i ++;
}
// PDF-Tabelle generieren
$pdf->SetFont('Arial', '', 14);
$pdf->AddPage();
$pdf->FancyTable($settings['tbl_header'], $data);
// PDF-Voucher generieren
$rows = 8;
$cols = 3;
$width = 179; // Genutzte Breite in mm
$height = 269; // Genutzte Höhe in mm
$pdf->SetAutoPageBreak(false);
$pdf->SetMargins(15, 15);
while(true) {
  $pdf->AddPage();
  for($row = 1; $row <= $rows; $row ++) {
    for($col = 1; $col <= $cols; $col ++) {
      $x = $pdf->GetX();
      $y = $pdf->GetY();
      $w = $width / $cols;
      $h = $height / $rows;
      $dataEntry = array_shift($data);
      $pdf->SetFont('Arial', 'U', 15);
      $pdf->Cell($w, 10, $settings['vou_header'], 0, 2, 'C');
      $pdf->SetFont('Arial', '', 9);
      $pdf->Cell($w, 8, $settings['vou_text'], 0, 2, 'C');
      $pdf->SetFont('Arial', '', 12);
      // $pdf->SetFont('isonorm_becker','',12);
      $pdf->Cell($w, 8, $settings['vou_label'] . $dataEntry[1], 0, 2, 'C');
      $pdf->SetFont('Arial', '', 8);
      $pdf->Cell($w, 8, $settings['dbtables'][mysql_real_escape_string($_POST['select_print'])] . ' ID ' . $dataEntry[0], 0, 2, 'R');
      $pdf->SetXY($x, $y);
      $pdf->Cell($w, $h, '', 1, $col == $cols);
      if(count($data) == 0) {
        break 3;
      }
    }
  }
}
$pdf->Output();
?>