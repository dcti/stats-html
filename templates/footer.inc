<?
$query_string = $_SERVER['QUERY_STRING'];
if (isset($project_id)) {
	$query_string = str_replace ("project_id=$project_id", "", $query_string );
}

// Trim off leading and trailing &'s, then add an & at the front if necessary
$query_string = preg_replace("/^[&]+(.*)\$/", "\$1", $query_string);
$query_string = preg_replace("/^(.*)[&]+\$/", "\$1", $query_string);
$query_string = preg_replace("/^(.*)&&(.*)\$/", "\$1&\$2", $query_string);

$query_string = str_replace("&", "&amp;", $query_string);
if($query_string != '') $query_string = "&amp;$query_string";
if (!isset($proj_list)) {

?>
<div style="text-align: center;"> This page in other projects: <br>
<? if ($project_id != 3) { ?> <a href="<?=$myname?>?project_id=3<?=$query_string?>">RC5-56</a>  <? } 
   if ($project_id != 5) { ?> <a href="<?=$myname?>?project_id=5<?=$query_string?>">RC5-64</a>  <? } 
   if ($project_id != 205) { ?> <a href="<?=$myname?>?project_id=205<?=$query_string?>">RC5-64 (all)</a>  <? } 
   if ($project_id != 8) { ?> <a href="<?=$myname?>?project_id=8<?=$query_string?>">RC5-72</a>  <? } 
   if ($project_id != 24) { <a href="<?=$myname?>?project_id=24<?=$query_string?>">OGR-24</a> <? } 
   if ($project_id != 25) { <a href="<?=$myname?>?project_id=25<?=$query_string?>">OGR-25</a> <? } 
?>
</div>
<?
} 
?>
   <div style="text-align: center">
    <a href="http://www.php.net"><img src="/images/php.gif" alt="PHP4"></a>
    <a href="http://www.postgresql.org/"><img src="/images/pg-power.jpg" alt="PostgreSQL"></a>
   </div>
<?
/* if($debug >= DEBUG_TIME ) {

    ?>
		<p style="text-align: center"> Page generated in <?=timer_current()?> seconds </p>
		<?
*/
    if($debug >= DEBUG_TRACE) { echo xdebug_dump_function_trace(); }
    if($debug >= DEBUG_PROFILE) { echo xdebug_dump_function_profile(); }
} 

?>
 </body>
</html>
