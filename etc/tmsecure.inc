<?php
  // $Id: tmsecure.inc,v 1.5 2003/09/23 21:48:00 paul Exp $

  if(!isset($team)) {
    if(!isset($_COOKIE['sbteam'])) {
      if(!isset($_SERVER['PHP_AUTH_USER'])) {
        Header("WWW-Authenticate: Basic realm=\"Team Configuration\"");
        Header("HTTP/1.0 401 Unauthorized");
        exit;
      }
      $team = $_SERVER['PHP_AUTH_USER'];
      $tpass = $_SERVER['PHP_AUTH_PW'];
      $authfrom = "headers";
    } else {
      $team = $_COOKIE['sbteam'];
      $tpass = $_COOKIE['sbtpass'];
      $authfrom = "cookie";
    }
  } else {
    $authfrom = "url";
    $tpass = substr($_REQUEST['pass'],0,8);
  }
?>

