<?
// $Id: global.inc,v 1.2 2004/07/16 21:25:54 decibel Exp $

include "error.php";
include "config.inc";

if ( isset($log_timing) ) {
    if ( $log_timing == 1 ) {
        include_once "timing.inc";
        timer_start('log');
        }
    } else {
        $log_timing = 0;
    }

include "debug.inc";

?>
