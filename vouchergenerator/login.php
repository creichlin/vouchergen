<?php
require_once ("include/setup.inc.php");


if($_SERVER['REQUEST_METHOD'] == 'POST') {
  $username = $_POST['username'];
  $password = $_POST['passwort'];

  $lUsername = $config->get("username", "");
  $lPassword = $config->get("password", "");


  if(strlen($password) > 3 && $username == $lUsername && $password == $lPassword) {
    $_SESSION['angemeldet'] = true;
    if($_SERVER['SERVER_PROTOCOL'] == 'HTTP/1.1') {
      if(php_sapi_name() == 'cgi') {
        header('Status: 303 See Other');
      } else {
        header('HTTP/1.1 303 See Other');
      }
    }
    $host = $_SERVER['HTTP_HOST'];
    $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $extra = 'index.php';
    header("Location: http://$host$uri/$extra");
    exit();
  } else {
    $model['message'] = "Es ist ein Fehler aufgetreten.";
  }
}

$model['title'] = 'Login';

print($twig->render('login.html', $model));

?>