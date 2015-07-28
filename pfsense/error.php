<?php
require("globals.inc");
?>

<html>
  <head>
    <?php
      echo('<meta http-equiv="refresh" content="0; url=http://HOST/requestSms.php?error" />');
    ?>
  </head>
  <body>
    <?php
      echo('<a href="http://HOST/requestSms.php?error">go to authentication page</a>');
    ?>
  </body>
</html>
