<?php
 // $Id: limit.inc,v 1.5 2000/07/14 03:42:41 decibel Exp $

 if ( $limit > 100 || $limit < 1 ) {
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
