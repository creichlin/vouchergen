<?php
require_once ("include/setup.inc.php");
require_once ("include/auth.inc.php");

$model['title'] = $lang->get("stats");



$statistics = [];

foreach($settings['dbtables'] as $key => $value) {
  $stat = $db->getStatisticsForTable($key);
  $element = [];
  $element['name'] = $value;
  $element['total'] = $stat[0];
  $element['unused'] = $stat[1];
  $element['used'] = $stat[2];
  $statistics[] = $element;
}

$model['statistics'] = $statistics;

print($twig->render('statistics.html', $model));

?>

