<?
// $Id: debug.inc,v 1.5 2004/07/16 21:25:54 decibel Exp $

if ($_SERVER["SERVER_NAME"] != "stats.distributed.net") {
    if (isset($_GET['debug'])) {
        $debug = (int)$_REQUEST['debug'];
    } 
    if($debug >= DEBUG_TIME)
    {
      if(!file_exists(getenv('DOCUMENT_ROOT').'/xdebug.so'))
      {
        trigger_error("I can't find xdebug.so to enable debugging!");
        $debug = 1;
      }
      else if(!dl('xdebug.so'))
      {
        echo "Unable to load dynamic library for debugging!\n";
        $debug = 1;
      }
      else
      {
        if($debug >= DEBUG_TRACE) {
          xdebug_start_trace();
        }
        xdebug_start_profiling();
        include_once "timing.inc";
        timer_start();
      }
    }
} 

?>
