<?php
  // $Id: tmsecure.inc,v 1.4 2003/09/05 15:29:07 thejet Exp $

  if(!isset($team)) {
    if(!isset($sbteam)) {
      if(!isset($_SERVER['PHP_AUTH_USER'])) {
        Header("WWW-Authenticate: Basic realm=\"Team Configuration\"");
        Header("HTTP/1.0 401 Unauthorized");
        exit;
      }
      $team = $_SERVER['PHP_AUTH_USER'];
      $tpass = $_SERVER['PHP_AUTH_PW'];
      $authfrom = "headers";
    } else {
      $team = $sbteam;
      $tpass = $sbtpass;
      $authfrom = "cookie";
    }
  } else {
    $authfrom = "url";
    $tpass = substr($pass,0,8);
  }
?>

