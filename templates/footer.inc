<?
if (!isset($proj_list)) {
 $qs = "select project_id, name from projects where not status = 'h'";
 sybase_query("set rowcount 0"); 
 $result = sybase_query($qs);

 $rows = sybase_num_rows($result);
echo "<center> This page in other projects: <br>";
 for ($i = 0 ; $i<$rows ; $i++) 
 { 
 	sybase_data_seek($result,$i);
 	$prj_list = sybase_fetch_object($result);
	
	$query_string = str_replace ("project_id=$project_id","project_id=$prj_list->project_id",$_SERVER['QUERY_STRING']);
	echo "<a href=\"$myname?$query_string\">$prj_list->name</a> ";
 }
}
?>
</center>


  <center>
   <p>
    <a href="http://www.php.net"><img src="/images/php.gif" alt="PHP3" border="0"></a>
    <a href="http://www.sybase.com/"><img src="/images/sybase.gif" alt="Sybase" border="0"></a>
   </p>
<? if ($_SERVER["SERVER_NAME"]!="stats.distributed.net"){ ?>
   <p> Page generated in <?=timer_current()?> </p>
<? } ?>
  </center>
 </body>
</html>
