<?php
ob_start();
session_start();
$systemName = "Accounts | Debox Global";
include 'function.php';
error_reporting(0);

// Program to display URL of current page.
if($_SERVER['HTTP_HOST']=='localhost' || $_SERVER['HTTP_HOST']=='127.0.0.1'){
  $serverurl = "http://";
  $serverurl .= $_SERVER['HTTP_HOST'];
  $serverurlapi = "http://".$_SERVER['HTTP_HOST']."/debox/Accounts/";
}else{
  if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'){
    $serverurl = "https://";
  }else{
    $serverurl = "http://";
  }

  $serverurl .= $_SERVER['HTTP_HOST'];
  $serverurl .= "/debox/Accounts/";
  $serverurlapi = $_SERVER['HTTP_HOST']."/debox/Accounts/";
  //$mountImagePath = "/u01/";
  //$targetImagePath = $mountImagePath."uploads_uat/";

}

?>
