<?php
  session_start();
  require('common.php');
  session_unset();
  session_destroy();
  session_write_close();
  header('Location: /index.php');
?>
