<?php
  // $Id: psecure.inc,v 1.1 1999/07/11 19:20:49 nugget Exp $

  if(!isset($id)) {
    if(!isset($sbid)) {
      if(!isset($PHP_AUTH_USER)) {
        Header("WWW-Authenticate: Basic realm=\"Participant Configuration\"");
        Header("HTTP/1.0 401 Unauthorized");
        exit;
      }
      $email = $PHP_AUTH_USER;
      $pass = $PHP_AUTH_PW;
      $authfrom = "headers";
    } else {
      $id = $sbid;
      $pass = $sbpass;
      $authfrom = "cookie";
    }
  } else {
    $authfrom = "url";
    $pass = substr($pass,0,8);
  }
?>

