<?php
// Einstellungen laden und Datenbankverbindung herstellen
require_once ("include/setup.inc.php");

require_once ("include/auth.inc.php");
$title = $lang->get("stats");
require_once ("include/header.inc.php");
?>


<table>
  <tr>
    <th>Datenbank</th>
    <th>Total</th>
    <th>Frei</th>
    <th>Benutzt</th>
  </tr>

<?php
foreach($settings['dbtables'] as $key => $value) {

  $data = $db->getStatisticsForTable($key);

  echo("<tr>");
  echo("<td>$value</td>");
  echo("<td>$data[0]</td>");
  echo("<td>$data[1]</td>");
  echo("<td>$data[2]</td>");
  echo("</tr>\n");
}
?>
</table>

<?php
include ("include/footer.inc.php");
?>

