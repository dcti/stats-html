<?
 # $Id: psearch.php,v 1.8 2002/04/07 21:34:58 paul Exp $

 // Variables Passed in url:
 //   st == Search Term


 include "../etc/config.inc";
 include "../etc/modules.inc";
 include "etc/project.inc";

 $title = "Participant Search: [".safe_display($st)."]";
 $QRSLTsearch = "";

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
	header("Location: psummary.php?project_id=$project_id&id=$id");
	exit;
 }

 $lastupdate = last_update('e');

 include "templates/header.inc";

 // Print debug info from the first query we ran
 if ($debug == "yes") print "<!-- result: '$QRSLTsearch', par: '$ROWparticipant', rows: $rows -->";

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
       <th align="right"><?=$proj_unitname?></th>
      </tr>
 <?

 $totalblocks = (double) 0;
 for ($i = 0; $i<$rows; $i++) {
	// Retrieve the records returned and format appropriately
	sybase_data_seek($QRSLTsearch, $i);
	$ROWparticipant = sybase_fetch_object($QRSLTsearch);
	$id = (int) $ROWparticipant->id;
	if ($debug=="yes") print "<!-- ID: $id -->";

	?>
	<tr class="<?=row_background_color($i)?>">
	<?
	$totalblocks += (double) $ROWparticipant->WORK_TOTAL/$proj_divider;

        print "   <td>$ROWparticipant->OVERALL_RANK " . html_rank_arrow($ROWparticipant->Overall_Change) . "</td>\n";

	if ($debug == yes) print "<!--- listmode: $ROWparticipant->listmode, WORK_TOTAL: " . (double) $ROWparticipant->WORK_TOTAL \
		. ", totalblocks: $totalblocks. --->\n";

	print "
		<td><a href=\"psummary.php?project_id=$project_id&id=$id\"><font color=\"#cc0000\">" . safe_display(participant_listas($ROWparticipant->listmode,
			$ROWparticipant->email,$id,$ROWparticipant->contact_name)) . "</font></a></td>
		<td align=\"right\">" . sybase_date_format_long($ROWparticipant->first_date) . "</td>
		<td align=\"right\">" . sybase_date_format_long($ROWparticipant->last_date) . "</td>
		<td align=\"right\">" . number_style_convert($ROWparticipant->Days_Working) . "</td>
		<td align=\"right\">" . $blocks=number_style_convert( (double) $ROWparticipant->WORK_TOTAL/$proj_divider) . "</td>
		</tr>
	";
 }

?>
	 <tr bgcolor=<?=$footer_bg?>>
	  <td><font <?=$footer_font?>><?=$i?></font></td>
	  <td colspan="4" align="right"><font <?=$footer_font?>>Total</font></td>
	  <td align="right"><font <?=$footer_font?>><? echo number_format($totalblocks, 0) ?></font></td>
	 </tr>
	</table>
<?
 if( $rows == 0 ) {
?>   
	<p>
	 <a href="http://www.distributed.net/FAQ/"><font size="+3" color="red">Confused?  Look here</font></a>
	</p>
<?
 }
?>
<?include "templates/footer.inc";?>
