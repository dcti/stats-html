<?php
  // $Id: tmsecure.inc,v 1.3 2000/01/18 03:52:00 decibel Exp $

  if(!isset($team)) {
    if(!isset($sbteam)) {
      if(!isset($PHP_AUTH_USER)) {
        Header("WWW-Authenticate: Basic realm=\"Team Configuration\"");
        Header("HTTP/1.0 401 Unauthorized");
        exit;
      }
      $team = $PHP_AUTH_USER;
      $tpass = $PHP_AUTH_PW;
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

