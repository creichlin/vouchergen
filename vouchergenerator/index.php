<?php

require_once ("include/setup.inc.php");
require_once ("include/auth.inc.php");



// delete table action
if(isset($_POST['submit_delete'])) {
  $db->deleteAllRows($_POST['select_delete']);
  $view->addInfo('db-deleted');
}
// upload tickets into database
if(isset($_POST['submit_upload'])) {
  if(isset($_FILES['datei'])) {
    if(file_exists($_FILES['datei']['tmp_name'])) {
      $uploaded_csv = file($_FILES['datei']['tmp_name']); // results in an array
      array_splice($uploaded_csv, 0, 7); // delete lines 1..7 (comments)
      // ads tickets only if all are 6..20 chars long, it's a very weak file format check
      $ticketsToAdd = [];
      foreach($uploaded_csv as $value) {
        $value = trim(str_replace('"', '', $value)); // remove " and leading space
        if(strlen($value) > 5 && strlen($value) < 21) {
          $ticketsToAdd[] = $value;
        } else {
          $ticketsToAdd = [];
          break;
        }
      }
      if(count($ticketsToAdd) > 0) {
        foreach($ticketsToAdd as $ticket) {
          $db->addTicketToTable($_POST['select_upload'], $ticket);
        }
        $view->addInfo('upload-succeded', ['count' => count($ticketsToAdd)]);
      } else {
        $view->addWarning("invalid-csv-export-message");
      }
    } else {
      $view->addWarning("invalid-csv-export-message");
    }
  }
}

$view->render('index.html');

?>