<?
// $Id: global.inc,v 1.3 2005/04/01 16:03:54 decibel Exp $

include "error.php";
include "config.inc";

if rand(1,100) <= 20 {
    $random_stats = 1;
} else {
    $random_stats = 0;
}

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
