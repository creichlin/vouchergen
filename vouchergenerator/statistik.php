<?php
require_once ("include/setup.inc.php");
require_once ("include/auth.inc.php");

$view->setTitle($lang->get("stats"));

$statistics = [];

foreach($config->get('dbtables') as $key => $value) {
  $stat = $db->getStatisticsForTable($key);
  $element = [];
  $element['id'] = $key;
  $element['name'] = $value;
  $element['total'] = $stat[0];
  $element['unused'] = $stat[1];
  $element['used'] = $stat[2];
  $statistics[] = $element;
}

$view->set('statistics', $statistics);

$view->render('statistics.html');
?>

