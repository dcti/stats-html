<?
// $Id: timing.inc,v 1.5 2004/07/21 05:24:56 decibel Exp $

function timer_start ($name = 'default') {
    global $timer_start_times;
    $timer_start_times[$name] = explode(' ', microtime());
}

function timer_stop ($name = 'default') {
    global $ss_timing_stop_times;
    $timer_stop_times[$name] = explode(' ', microtime());
}

function timer_current ($name = 'default') {
    global $timer_start_times, $timer_stop_times;
    if (!isset($timer_start_times[$name])) {
        return 0;
    }
    if (!isset($timer_stop_times[$name])) {
        $stop_time = explode(' ', microtime());
    }
    else {
        $stop_time = $timer_stop_times[$name];
    }
    // do the big numbers first so the small ones aren't lost
    $current = $stop_time[1] - $timer_start_times[$name][1];
    $current += $stop_time[0] - $timer_start_times[$name][0];
    return $current;
}

function log_timing(&$dbPtr) {
    $sql = 'SELECT page_log.log( $1, $2 )';
    $queryId = $dbPtr->query_bound( $sql, array( $_SERVER['PHP_SELF'], (string) timer_current('log') ) );
}
    
// vi: expandtab sw=4 ts=4
?>
