<?php
  // vi: ts=2 sw=2 tw=120 syntax=php
  // $Id: debug.inc,v 1.2 2002/12/16 20:00:31 decibel Exp $

  global $debug;
  function debug_text($text, $debug) {
    if ($debug == "yes") print $text;
  }

?>
