<?
  include "security.inc";
?>  
	<div style="text-align:center">
	<table border="1" cellpadding="4" cellspacing="0">
	<tr align="center">
  		<td colspan="10">Current Connections Listing</td>
	</tr>
	<tr>
		<td align="right">datid</td>
		<td align="right">datname</td>
		<td>procpid</td>
		<td>usesysid</td>
		<td>username</td>
		<td>current_query</td>
	</tr>
<?
  	$ret = $gdb->query("select * from pg_stat_activity");
	$results = $gdb->fetch_paged_result($ret);
	foreach ($results as $result) {
	?>
	<tr>
		<td align=\"right\"><?=$result->datid?></td>
		<td align=\"right\"><?=$result->datname?></td>
		<td><?=$result->procpid?></td>
		<td><?=$result->usesysid?></td>
		<td><?=$result->usename?></td>
		<td><?=$result->current_query?></td>
		</tr>
<? } ?>
</table>
</div>
<? 	include "footer.inc"; ?>



