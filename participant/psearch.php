<?
 # $Id: psearch.php,v 1.13 2003/03/22 18:42:53 paul Exp $

 // Variables Passed in url:
 //   st == Search Term

 include "../etc/config.inc";
 include "../etc/modules.inc";
 include "../etc/project.inc";

 $title = "Participant Search: [".safe_display($st)."]";
 $QRSLTsearch = "";

 $lastupdate = last_update('e');

if (strlen($st) < 3) {
 include "../templates/header.inc";
	?>
    <table align="center" width="400" border="0"><tr><td>
	<h2>There was an error processing your request</h2>
	<p>Search Text must be at least 3 characters</p>
	</p></td></tr></table>
	<?
	include "../templates/footer.inc";
	exit;
}

// Execute the procedure to get the results
// Parameters to rc5_64_search are searchtext, maxrows (50), escapewildcards (true)
$QRSLTsearch = sybase_query("p_psearch @project='new', @project_id=$project_id, @searchtext='$st', @maxrows=50, @escapewildcards=1");
 // If $QRSLTsearch is still blank, we ain't getting anything back...
 err_check_query_results($QRSLTsearch);

 $rows = sybase_num_rows($QRSLTsearch);

 if($rows == 1) {
	# Only one hit, let's jump straight to psummary
	$ROWparticipant = sybase_fetch_object($QRSLTsearch);
	$id = (int) $ROWparticipant->id;
	header("Location: psummary.php?project_id=$project_id&amp;id=$id");
	exit;
 }

 include "../templates/header.inc";

 ?> 
    <center>
     <br>
     <table border="1" cellspacing="0" bgcolor=<?=$header_bg?>>
      <tr>
       <th>Rank</th>
       <th>Participant</th>
       <th align="right">First Block</th>
       <th align="right">Last Block</th>
       <th align="right">Days</th>
       <th align="right"><?=$proj_scaled_unit_name?></th>
      </tr>
 <?

 $totalblocks = (double) 0;
 for ($i = 0; $i<$rows; $i++) {
	// Retrieve the records returned and format appropriately
	sybase_data_seek($QRSLTsearch, $i);
	$ROWparticipant = sybase_fetch_object($QRSLTsearch);
	$id = (int) $ROWparticipant->id;
	$totalblocks += (double) $ROWparticipant->WORK_TOTAL * $proj_scale;

	?>
	<tr class="<?=row_background_color($i)?>">
         <td><?=$ROWparticipant->OVERALL_RANK;?><?=html_rank_arrow($ROWparticipant->Overall_Change)?></td>
<?

	print "
		<td><a href=\"psummary.php?project_id=$project_id&amp;id=$id\">" . safe_display(participant_listas($ROWparticipant->listmode,
			$ROWparticipant->email,$id,$ROWparticipant->contact_name)) . "</a></td>
		<td align=\"right\">" . sybase_date_format_long($ROWparticipant->first_date) . "</td>
		<td align=\"right\">" . sybase_date_format_long($ROWparticipant->last_date) . "</td>
		<td align=\"right\">" . number_style_convert($ROWparticipant->Days_Working) . "</td>
		<td align=\"right\">" . $blocks=number_style_convert( (double) $ROWparticipant->WORK_TOTAL * $proj_scale) . "</td>
		</tr>
	";
 }

?>
	 <tr>
	  <td><?=$i?></td>
	  <td colspan="4" align="right">Total</td>
	  <td align="right"><? echo number_format($totalblocks, 0) ?></td>
	 </tr>
	</table>
<?
 if( $rows == 0 ) {
?>   
	<p>
	 <a href="http://www.distributed.net/FAQ/">Confused?  Look here</a>
	</p>
<?
 }
?>
<?include "../templates/footer.inc";?>
