<html>
  <head>
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      echo('<meta http-equiv="refresh" content="0; url=' . $_POST['pr'] . '" />');
    } else {
      echo('<meta http-equiv="refresh" content="0; url=http://HOST/requestSms.php?$PORTAL_ACTION$[DEL]$PORTAL_REDIRURL$" />');
    }
    ?>
  </head>
  <body>
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      echo('<a href="' . $_POST['pr'] . '">continue</a>');
    } else {
      echo('<a href="http://HOST/requestSms.php?$PORTAL_ACTION$[DEL]$PORTAL_REDIRURL$">go to authentication page</a>');
    }
    ?>

  </body>
</html>
