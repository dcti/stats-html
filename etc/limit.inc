<?php
 // $Id: limit.inc,v 1.4 2000/01/18 03:52:00 decibel Exp $

 if ( $limit > 100 || $limit < 0 ) {
   $limit = 100;
 }

 if ( $low < 0 || $low == "" ) {
   $low = 1;
 }

 $lo = $low;
 $hi = $low + $limit - 1;
 $prev_lo = $lo - $limit;
 $next_lo = $lo + $limit;
 $nlow = $next_lo;
?>
