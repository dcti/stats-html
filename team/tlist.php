<?

// Variables Passed in url:
//   low == lowest rank used
//   limit == how many lines to return
//   source == "y" for yesterday, all other values ignored.

 include "../etc/limit.inc";	// Handles low, high, limit calculations
 include "../etc/config.inc";
 include "../etc/modules.inc";
 include "etc/project.inc";

if ("$source" == "y") {
  $title = "Team Listing by Yesterday's Rank: $lo to $hi";
  $qs2 = "select	tr.TEAM_ID, name, FIRST_DATE, LAST_DATE, WORK_TOTAL/$proj_divider as WORK_TOTAL, WORK_TODAY/$proj_divider as WORK_TODAY,
		MEMBERS_CURRENT, DAY_RANK as Rank,
		datediff(day, FIRST_DATE, LAST_DATE)+1 as Days_Working,
		DAY_RANK_PREVIOUS-DAY_RANK as Change
	from	STATS_Team st, Team_Rank tr
	where	st.team = tr.TEAM_ID
		and st.listmode <= 9
		and DAY_RANK <= $hi
		and DAY_RANK >= $lo
		and tr.PROJECT_ID = $project_id
	order by	DAY_RANK, WORK_TOTAL desc";
} else {
  $source = "o";
  $title = "Team Listing by Overall Rank: $lo to $hi";
  $qs2 = "select	tr.TEAM_ID, name, FIRST_DATE, LAST_DATE, WORK_TOTAL/$proj_divider as WORK_TOTAL, WORK_TODAY/$proj_divider as WORK_TODAY,
		MEMBERS_CURRENT, OVERALL_RANK as Rank,
		datediff(day, FIRST_DATE, LAST_DATE)+1 as Days_Working,
		OVERALL_RANK_PREVIOUS-OVERALL_RANK as Change
	from	STATS_Team st, Team_Rank tr
	where	st.team = tr.TEAM_ID
		and st.listmode <= 9
		and OVERALL_RANK <= $hi
		and OVERALL_RANK >= $lo
		and tr.PROJECT_ID = $project_id
	order by	OVERALL_RANK, WORK_TOTAL desc";
}

 // Find out when the last update was
 $qs = "p_lastupdate @section=t, @project_id=$project_id, @contest=new";
 $result = sybase_query($qs);
 $par = sybase_fetch_object($result);
 $lastupdate = sybase_date_format_long($par->lastupdate);

 include "templates/header.inc";
 debug_text("<!-- Last Update -- qs: $qs, result: $result, par: $par -->\n",$debug);

 // Get the results
 sybase_query("set rowcount $limit");
 $result = sybase_query($qs2);

 debug_text("<!-- Last Update -- qs2: $qs2, result: $result -->\n",$debug);
 err_check_query_results($result);

 $rows = sybase_num_rows($result);

 // Figure out what navagation buttons we should have
 if ( $lo > $rows ) {
   $btn_back = "<a href=\"$myname?project_id=$project_id&low=$prev_lo&limit=$limit&source=$source\">Back $limit</a>";
 } else if ( $lo > 1 and $lo < $limit ) {
   $btn_back = "<a href=\"$myname?project_id=$project_id&low=1&limit=$limit&source=$source\">Back " . ($lo-1) ."</a>";
 } else {
   $btn_back = "&nbsp;";
 }

 if ( $rows >= $limit ) {
   $btn_fwd = "<a href=\"$myname?project_id=$project_id&low=$next_lo&limit=$limit&source=$source\">Next $limit</a>";
 } else {
   $btn_fwd = "&nbsp;";
 }

 print "
    <center>
     <br>
     <table border=\"1\" cellspacing=\"0\" bgcolor=$header_bg>
     <tr bgcolor=$footer_bg>
	 <td><font $footer_font>$btn_back</font></td>
	 <td colspan=\"6\"><font $footer_font>&nbsp;</font></td>
	 <td align=\"right\"><font $footer_font>$btn_fwd</font></td>
     </tr>
     <tr>
	<td><font $header_font>Rank</font></td>
	<td><font $header_font>Team</font></td>
	<td align=\"right\"><font $header_font>First Block</font></td>
	<td align=\"right\"><font $header_font>Last Block</font></td>
	<td align=\"right\"><font $header_font>Days</font></td>
	<td align=\"right\"><font $header_font>Current Members</font></td>
	<td align=\"right\"><font $header_font>$proj_unitname Overall</font></td>
	<td align=\"right\"><font $header_font>$proj_unitname Yesterday</font></td>
      </tr>
 ";

	
 for ($i = 0; $i<$rows; $i++) {
	sybase_data_seek($result,$i);
	$par = sybase_fetch_object($result);

	$row_bgnd_color = row_background_color($i);

	$totalblocks += (double) $par->WORK_TOTAL;
	$totalblocksy += (double) $par->WORK_TODAY;
	$decimal_places=0;
	$first = sybase_date_format_long($par->FIRST_DATE);
        $last = sybase_date_format_long($par->LAST_DATE);

	$teamid=0+$par->TEAM_ID;

	print "
	      <tr bgcolor=$row_bgnd_color>
		<td>$par->Rank" . html_rank_arrow($par->Change) . "</td>
		<td><a href=\"tmsummary.php?project_id=$project_id&team=$teamid\"><font color=\"#cc0000\">$par->name</font></a></td>
		<td align=\"right\">$first</td>
		<td align=\"right\">$last</td>
		<td align=\"right\">" . number_format($par->Days_Working, 0) . "</td>
		<td align=\"right\">" . number_format($par->MEMBERS_CURRENT, 0) . "</td>
		<td align=\"right\">" . number_format( (double) $par->WORK_TOTAL, 0) . "</td>
		<td align=\"right\">" . number_format( (double) $par->WORK_TODAY, 0) . "</td>
	      </tr>
	";
 }

 // Print out the bottom of the table
 print "
	 <tr bgcolor=$footer_bg>
	  <td><font $footer_font>$lo-$hi</font></td>
	  <td align=\"right\" colspan=\"5\"><font $footer_font>Total</font></td>
	  <td align=\"right\"><font $footer_font>" . number_format($totalblocks) . "</font></td>
	  <td align=\"right\"><font $footer_font>" . number_format($totalblocksy) . "</font></td>
	 </tr>
	 <tr bgcolor=$footer_bg>
	  <td><font $footer_font>$btn_back</font></td>
	  <td colspan=\"6\"><font $footer_font>&nbsp;</font></td>
	  <td align=\"right\"><font $footer_font>$btn_fwd</font></td>
	 </tr>
	</table>
	";
?>
<?include "templates/footer.inc";?>
