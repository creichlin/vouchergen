<?php

if (! isset ( $_SESSION ['angemeldet'] ) || ! $_SESSION ['angemeldet']) {
  $host = $_SERVER ['HTTP_HOST'];
  $uri = rtrim ( dirname ( $_SERVER ['PHP_SELF'] ), '/\\' );
  $extra = 'login.php';
  header ( "Location: http://$host$uri/$extra" );

  exit ();
}
?>