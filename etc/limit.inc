<?

if (isset($_GET['low'])) {
        $low = (int)$_GET['low'];
 } else {
        $low = 1;
 }

if (isset($_GET['limit'])) {
        $limit = (int)$_GET['limit'];
 } else {
        $limit = 100;
 }


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
