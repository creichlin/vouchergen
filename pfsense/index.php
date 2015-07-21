<?php
require("globals.inc");

$pr = urlencode("$PORTAL_REDIRURL$");

?>

<html>
  <head>
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      //echo('<meta http-equiv="refresh" content="0; url=http://google.com" />');
    } else {
      //echo('<meta http-equiv="refresh" content="0; url=http://172.20.88.11/requestSms.php?pa=$PORTAL_ACTION$&pr=$PORTAL_REDIRURL$" />');
    }
    ?>
  </head>
  <body>
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      echo('<a href="http://google.com">go to google</a>');
    } else {
      echo('<a href="http://172.20.88.11/requestSms.php?pa=$PORTAL_ACTION$&pr=$PORTAL_REDIRURL$">go to authentication page</a>');
    }
    ?>

  </body>
</html>
