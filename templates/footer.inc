<?

if (!isset($proj_list)) {
    $query_string = '';
    foreach ($_GET as $getvar => $getval) {
        if ($getvar != 'project_id') {
            $query_string .= '&amp;' . htmlentities($getvar) . '=' . htmlentities(stripslashes($getval));
        }
    }
?>
<div style="text-align: center;"> This page in other projects: <br />
<? if ($project_id != 3) { ?> <a href="<?=$myname?>?project_id=3<?=$query_string?>">RC5-56</a>  <? } 
   if ($project_id != 5) { ?> <a href="<?=$myname?>?project_id=5<?=$query_string?>">RC5-64</a>  <? } 
   if ($project_id != 205) { ?> <a href="<?=$myname?>?project_id=205<?=$query_string?>">RC5-64 (all)</a>  <? } 
   if ($project_id != 8) { ?> <a href="<?=$myname?>?project_id=8<?=$query_string?>">RC5-72</a>  <? } 
   if ($project_id != 24) { ?> <a href="<?=$myname?>?project_id=24<?=$query_string?>">OGR-24</a> <? } 
   if ($project_id != 25) { ?> <a href="<?=$myname?>?project_id=25<?=$query_string?>">OGR-25</a> <? } 
?>
</div>
<?
} 
?>
   <div style="text-align: center">
    <a href="http://www.php.net"><img src="/images/php.gif" alt="PHP4" /></a>
    <a href="http://www.postgresql.org/"><img src="/images/pg-power.jpg" alt="PostgreSQL" /></a>
   </div>
<?
/* if($debug >= DEBUG_TIME ) {
?>
    <p style="text-align: center"> Page generated in <?=timer_current()?> seconds </p>
<?
*/
    if($debug >= DEBUG_SHOW_QUERY) {
        echo '<table border="1">';
        $queries = $gdb->get_queries();
        echo "<tr><td>Total queries: " . count( $queries ) . "</td></tr>\n";
        foreach ( $gdb->get_queries() as $query ) {
            echo "<tr><td>" . $query[0] . "</td></tr>\n";
        }
        echo "</table>\n";
    }
    if($debug >= DEBUG_TRACE) { echo xdebug_dump_function_trace(); }
    if($debug >= DEBUG_PROFILE) { echo xdebug_dump_function_profile(); }
    if($log_timing == 1) {
        if ( !isset( $other ) ) {
            if ( isset( $source ) ) {
                $other = $source;
            } else {
                $other = '';
            }
        }
        log_timing($gdb, $project_id, $other);
    }
//} 

?>
 </body>
</html>
