<?
if(!isset($project_filter))
{
  $project_filter = array(3, 5, 205, 8, 24, 25, 26, 27, 28);
}

function show_project_link($projectId, $projectFilter)
{
  global $project_id;
  if($projectId == $project_id)
  {
    return false;
  }

  if(isset($projectFilter) && 
     is_array($projectFilter) &&
     !in_array($projectId, $projectFilter))
  {
    return false;
  } 

  return true;
}

    $query_string = '';
    foreach ($_GET as $getvar => $getval) {
        if ($getvar != 'project_id') {
            $query_string .= '&amp;' . htmlentities($getvar) . '=' . htmlentities(stripslashes($getval));
        }
    }
?>
<hr/>
<div style="text-align: center;"> This page in other projects: <br />
<? if (show_project_link(3, $project_filter)) { ?> <a href="<?=$myname?>?project_id=3<?=$query_string?>">RC5-56</a>  <? } 
   if (show_project_link(5, $project_filter)) { ?> <a href="<?=$myname?>?project_id=5<?=$query_string?>">RC5-64</a>  <? } 
   if (show_project_link(205, $project_filter)) { ?> <a href="<?=$myname?>?project_id=205<?=$query_string?>">RC5-64 (all)</a>  <? } 
   if (show_project_link(8, $project_filter)) { ?> <a href="<?=$myname?>?project_id=8<?=$query_string?>">RC5-72</a>  <? } 
   if (show_project_link(24, $project_filter)) { ?> <a href="<?=$myname?>?project_id=24<?=$query_string?>">OGR-24</a> <? } 
   if (show_project_link(25, $project_filter)) { ?> <a href="<?=$myname?>?project_id=25<?=$query_string?>">OGR-25</a> <? } 
   if (show_project_link(26, $project_filter)) { ?> <a href="<?=$myname?>?project_id=26<?=$query_string?>">OGR-26</a> <? } 
   if (show_project_link(27, $project_filter)) { ?> <a href="<?=$myname?>?project_id=27<?=$query_string?>">OGR-27</a> <? } 
   if (show_project_link(28, $project_filter)) { ?> <a href="<?=$myname?>?project_id=28<?=$query_string?>">OGR-28</a> <? } 
?>
</div>
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

<script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
</script>
<script type="text/javascript">
_uacct = "UA-327688-4";
urchinTracker();
</script>

 </body>
</html>
