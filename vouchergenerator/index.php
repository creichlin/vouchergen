<?php

require_once ("include/setup.inc.php");
require_once ("include/auth.inc.php");



// delete table action
if(isset($_POST['submit_delete'])) {
  $db->deleteAllRows($_POST['select_delete']);
  $model["message"] = $lang->get("message_db_del");
}
// upload tickets into database
if(isset($_POST['submit_upload'])) {
  if(isset($_FILES['datei'])) {
    $uploaded_csv = file($_FILES['datei']['tmp_name']); // results in an array
    array_splice($uploaded_csv, 0, 7); // delete lines 1..7 (comments)

    foreach($uploaded_csv as $value) {
      $value = trim(str_replace('"', '', $value)); // remove " and leading space
      $db->addTicketToTable($_POST['select_upload'], $value);
    }
    $model["message"] = $lang->get("message_upload_succeed");
  }
}

$view->setTitle($lang->get("vouchermgmt"));
$view->render('index.html');

?>