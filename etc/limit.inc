<?php
 // $Id $

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
