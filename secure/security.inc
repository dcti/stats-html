<?php
  if(!isset($ss_login)) {
    if(!isset($PHP_AUTH_USER)) {
      Header("WWW-Authenticate: Basic realm=\"blower administration\"");
      Header("HTTP/1.0 401 Unauthorized");
      echo "denied!\n";
      exit;
    }
    $ss_login = $PHP_AUTH_USER;
    $ss_passwd = $PHP_AUTH_PW;
    $authfrom = "headers";
  } else {
    $authfrom = "cookie";
  }

  apache_note("REMOTE_USER",$ss_login);

?>
