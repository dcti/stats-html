<?
 # $Id: tsearch.php,v 1.5 2002/03/09 18:31:29 paul Exp $

 // Variables Passed in url:
 //   st == Search Term

 $title = "Team Search: [$st]";

 include "../etc/config.inc";
 include "../etc/modules.inc";
 include "etc/project.inc";

 sybase_pconnect($interface, $username, $password);
 $qs = "select	tr.TEAM_ID, name, FIRST_DATE, LAST_DATE, WORK_TOTAL/$proj_divider as WORK_TOTAL, WORK_TODAY/$proj_divider as WORK_TODAY,
		MEMBERS_CURRENT, OVERALL_RANK,
		datediff(day, FIRST_DATE, LAST_DATE)+1 as Days_Working,
		OVERALL_RANK_PREVIOUS-OVERALL_RANK as Overall_Change
	from	Team_Rank tr, STATS_team st
	where	(name like \"%$st%\" or convert(char(10),st.team) like \"%$st%\")
		and st.team = tr.TEAM_ID
		and listmode <= 9
		and PROJECT_ID = $project_id
	order by	OVERALL_RANK";
 sybase_query("set rowcount 50");
 $QRSLTteams = sybase_query($qs);

 debug_text("<!-- qs: $qs, result: $result -->\n",$debug);

 if ($QRSLTteams == "") {
   include "templates/header.inc";
   if ($debug=="yes") {
     include "templates/debug.inc";
   } else {
     include "templates/error.inc";
   }
   exit();
 }

 $rows = sybase_num_rows($QRSLTteams);

 if($rows == 1) {
	# Only one hit, let's jump straight to psummary
	$par = sybase_fetch_object($QRSLTteams);
	$id = (int) $par->TEAM_ID;
	header("Location: tmsummary.php?project_id=$project_id&team=$id");
	exit;
 }

 // Find out when the last update was
 $qs = "p_lastupdate @section=t, @project_id=$project_id, @contest=new";
 $result = sybase_query($qs);
 $par = sybase_fetch_object($result);
 $lastupdate = sybase_date_format_long($par->lastupdate);

 include "templates/header.inc";

 print "
    <center>
     <br>
     <table border=\"1\" cellspacing=\"0\" bgcolor=$header_bg>
      <tr>
       <td><font $header_font>Rank</font></td>
       <td><font $header_font>Team</font></td>
       <td align=\"right\"><font $header_font>First Unit</font></td>
       <td align=\"right\"><font $header_font>Last Unit</font></td>
       <td align=\"right\"><font $header_font>Days</font></td>
       <td align=\"right\"><font $header_font>Current Members</font></td>
       <td align=\"right\"><font $header_font>$proj_unitname Overall</font></td>
       <td align=\"right\"><font $header_font>$proj_unitname Yesterday</font></td>
      </tr>
 ";

 $totalblocks = 0;
 $totalblocksy = 0;
 for ($i = 0; $i<$rows; $i++) {
	sybase_data_seek($QRSLTteams,$i);
	$par = sybase_fetch_object($QRSLTteams);
        $firstd = substr($par->FIRST_DATE,4,2);
        $firstm = substr($par->FIRST_DATE,0,3);
        $firsty = substr($par->FIRST_DATE,7,4);
        $lastd = substr($par->LAST_DATE,4,2);
        $lastm = substr($par->LAST_DATE,0,3);
        $lasty = substr($par->LAST_DATE,7,4);
	$members = number_format($par->MEMBERS_CURRENT);
	$teamid = 0 + $par->TEAM_ID;
	$totalblocks += (double) $par->WORK_TOTAL;
	$totalblocksy += (double) $par->WORK_TODAY;

	print "
		<tr bgcolor=" . row_background_color($i) . ">
		<td>$par->OVERALL_RANK" . html_rank_arrow($par->Change) . "</td>
		<td><a href=\"tmsummary.php?project_id=$project_id&team=$teamid\"><font color=\"#cc0000\">$par->name</font></a></td>
		<td align=\"right\">$firstd-$firstm-$firsty</td>
		<td align=\"right\">$lastd-$lastm-$lasty</td>
		<td align=\"right\">" . number_format($par->Days_Working) . "</td>
		<td align=\"right\">$members</td>
		<td align=\"right\">" . number_format( (double) $par->WORK_TOTAL) . "</td>
		<td align=\"right\">" . number_format( (double) $par->WORK_TODAY) . "</td>
		</tr>
	";
 }

 print "
	 <tr bgcolor=$footer_bg>
	  <td><font $footer_font>$rows</font></td>
	  <td colspan=\"5\" align=\"right\"><font $footer_font>Total</font></td>
	  <td align=\"right\"><font $footer_font>" . number_format($totalblocks, 0) . "</font></td>
	  <td align=\"right\"><font $footer_font>" . number_format($totalblocksy, 0) . "</font></td>
	 </tr>
	</table>
	";
?>
<?include "templates/footer.inc";?>
