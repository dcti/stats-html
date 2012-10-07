<?
// $Id: global.inc,v 1.12 2005/04/01 19:26:01 decibel Exp $

include "error.php";
require "config.inc";

global $random_stats, $random_function;
$random_function = "*(random()*1000)";
$random_stats = 0;
/*
if ( rand(1,100) <= 20 ) {
    $random_stats = 1;
}
*/

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
