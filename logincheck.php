<?php
if(!isset($_SESSION['UID']) || $_SESSION['sessionid']!=session_id())
{

  logger ("[ERR] - Session called 'UID' is empty, expecting a UID");
  header("Location:login.php");
  exit();

}

?>
