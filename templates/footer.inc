<?
$query_string = str_replace ("project_id=$project_id", "", $_SERVER['QUERY_STRING']);
$query_string = str_replace("&", "&amp;", $query_string);
if (!isset($proj_list)) {

    ?>
<div style="text-align: center;"> This page in other projects: <br>
<?if ($project_id != 5) {

        ?> <a href="<?=$myname?>?project_id=5<?=$query_string?>">RC5-64</a>  <? } 

    ?>
<?if ($project_id != 205) {

        ?> <a href="<?=$myname?>?project_id=205<?=$query_string?>">RC5-64 (all)</a>  <? } 

    ?>
<?if ($project_id != 8) {

        ?> <a href="<?=$myname?>?project_id=8<?=$query_string?>">RC5-72</a>  <? } 

    ?>
<?if ($project_id != 24) {

        ?>  <a href="<?=$myname?>?project_id=24<?=$query_string?>">OGR-24</a> <? } 

    ?>
<?if ($project_id != 25) {

        ?>  <a href="<?=$myname?>?project_id=25<?=$query_string?>">OGR-25</a> <? } 

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
if($debug > 0 ) {

    ?>
		<p style="text-align: center"> Page generated in <?=timer_current()?> seconds </p>
		<?
    if($debug >= 2) { echo xdebug_dump_function_trace(); }
    echo xdebug_dump_function_profile();
} 

?>
 </body>
</html>
