<?
 # $Id: psummary.php,v 1.3 2002/03/08 23:29:15 paul Exp $

 // Variables Passed in url:
 //   id == Participant ID

 include "../etc/config.inc";
 include "../etc/modules.inc";
 include "etc/project.inc";
 include "../etc/markup.inc";

 function par_list($i, $par, $totaltoday, $totaltotal, $color_a = "", $color_b = "") {
        $parid = 0+$par->id;
        $totaltoday += $par->TODAY;
        $totaltotal += $par->TOTAL;
        $decimal_places=0;
        $units=number_style_convert( $par->units );
	$participant = participant_listas($par->listmode,$par->email,$parid,$par->contact_name);

        print "	  <tr bgcolor=" . row_background_color($i, $color_a, $color_b) . ">
		<td>$par->OVERALL_RANK" . html_rank_arrow($par->Overall_Change) . "
		<td><a href=\"psummary.php?project_id=$project_id&id=$parid\"><font color=\"#cc0000\">$participant</font></a></td>
		<td align=\"right\">" . number_style_convert( $par->Days_Working ) . "</td>
		<td align=\"right\">" . number_style_convert( (double) $par->TOTAL) . "</td>
		<td align=\"right\">" . number_style_convert( (double) $par->TODAY) . "</td>
	  </tr>
        ";
 }
 function par_header($header_font) {
   print "
     <tr>
      <td><font $header_font>Rank</font></td>
      <td><font $header_font>Participant</font></td>
      <td align=\"right\"><font $header_font>Days</font></td>
      <td align=\"right\"><font $header_font>Overall Gnodes</font></td>
      <td align=\"right\"><font $header_font>Current Gnodes</font></td>
     </tr>";
 }
 function par_footer($footer_font, $totaltoday, $totaltotal) {
   print "
     <tr>
      <td align=\"right\" colspan=\"3\"><font $footer_font>Total</font></td>
      <td align=\"right\"><font $footer_font>" . number_style_convert( $totaltotal ) . "</font></td>
      <td align=\"right\"><font $footer_font>" . number_style_convert( $totaltoday ) . "</font></td>
     </tr>
   ";
 }

 sybase_pconnect($interface, $username, $password);

 // Get the participant's record from STATS_Participant and store it in $person

 //$qs = "p_participant_all $id";
$qs = "select * from STATS_Participant where id = $id and listmode < 10";
 sybase_query("set rowcount 0");
 $result = sybase_query($qs);
 sybase_data_seek($result,0);
 $person = sybase_fetch_object($result);

 if ($person == "") {
   if ($debug=="yes") {
     include "templates/debug.inc";
   } else {
     include "templates/error.inc";
   }
   exit();
 }

 $retire_to = 0+$person->retire_to;
 if( $retire_to > 0 ) {
   header("Location: psummary.php?project_id=$project_id&id=$retire_to");
   exit();
 }

 print "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\"
        \"http://www.w3.org/TR/REC-html40/loose.dtd\">\n";

 debug_text("<!-- p_participant_all returned: '$person' -->\n", $debug);

 $participant = participant_listas($person->listmode,$person->email,$id,$person->contact_name);

 $title = "Participant Summary for $participant";

 if($person->motto <> "") {
   $motto="<font size=\"+1\"><i>".markup_to_html($person->motto)."</i></font><hr>";
 }

 // Find out when the last update was, store formatted result in $lastupdate

 $qs = "p_lastupdate e, @contest='new', @project_id=$project_id";
 $result = sybase_query($qs);
 if($result) {
   $par = sybase_fetch_object($result);
   $lastupdate = sybase_date_format_long($par->lastupdate);
 } else {
  $lastupdate = "some day, not too long ago";
 }

 include "templates/header.inc";

 // Get the participant's ranking info, store in $rs_rank

 $qs = "select *, datediff(day, FIRST_DATE, LAST_DATE)+1 as Days_Working,
	WORK_TODAY/$divider as TODAY,
	WORK_TOTAL/$divider as TOTAL,
	OVERALL_RANK_PREVIOUS-OVERALL_RANK as Overall_Change,
	DAY_RANK_PREVIOUS-DAY_RANK as Day_Change
	from Email_Rank
	where id = $id
		and PROJECT_ID = $project_id";
 sybase_query("set rowcount 0");
 $result = sybase_query($qs);
 sybase_data_seek($result,0);
 $rs_rank = sybase_fetch_object($result);
 debug_text("<!-- Participant ranking -- qs: $qs, result: $result, rs_rank: $rs_rank -->\n", $debug);

 // Grab the participant's neighbors and store in $neighbors (number of neighbors in $numneighbors)

 $qs = "select p.*, r.*, datediff(day, r.FIRST_DATE, r.LAST_DATE)+1 as Days_Working,
	WORK_TODAY/$divider as TODAY,
	WORK_TOTAL/$divider as TOTAL,
	(r.OVERALL_RANK_PREVIOUS-r.OVERALL_RANK) as Overall_Change,
	(r.DAY_RANK_PREVIOUS-r.DAY_RANK) as Day_Change
	from STATS_Participant p, Email_Rank r
	where p.id = r.id 
		and PROJECT_ID = $project_id
		and (r.OVERALL_RANK < ($rs_rank->OVERALL_RANK+5))
		and (r.OVERALL_RANK > ($rs_rank->OVERALL_RANK-5))
	order by r.OVERALL_RANK";
 sybase_query("set rowcount 18");
 $neighbors = sybase_query($qs);
 $numneighbors = sybase_num_rows($neighbors);
 debug_text("<!-- Participant neighbors -- qs: $qs, neighbors: $neighbors, numneighbors: $numneighbors -->\n", $debug);

 // Grab the participant's list of friends, store in $friends (number of friends in $numfriends)

 $qs = "select r.*, p.*, datediff(day, r.FIRST_DATE, r.LAST_DATE)+1 as Days_Working,
	WORK_TODAY/$divider as TODAY,
	WORK_TOTAL/$divider as TOTAL
	from STATS_Participant p, Email_Rank r
        where (r.id = $person->friend_a or
               r.id = $person->friend_b or
               r.id = $person->friend_c or
               r.id = $person->friend_d or
               r.id = $person->friend_e or
	       r.id = $id                 ) and
	       p.id = r.id
		and PROJECT_ID = $project_id
        order by r.OVERALL_RANK";
 sybase_query("set rowcount 0");
 $friends = sybase_query($qs);
 $numfriends = sybase_num_rows($friends);
 debug_text("<!-- Participant friends -- qs: $qs, friends: $friends, numfriends: $numfriends -->\n", $debug);

 // Get the participant's best day, store result in $best_day

/*
******************************
 // First, build the where clause that contains all the ID's that are retired to this ID
 sybase_query("set rowcount 0");
 $qs = "select id from STATS_Participant where retire_to = $id";
 $result = sybase_query($qs);
 $rows = sybase_num_rows($result);
 $whereline = "id = $id";
 for ($i = 0; $i<$rows; $i++) {
   sybase_data_seek($result,$i);
   $par = sybase_fetch_object($result);
   $rt = (int) $par->id;
   $whereline = "$whereline or id = $rt";
 }
 // Run the query, but only if statsrun isn't in progress
******************************
*/

 $qs = "p_phistory @project_id = $project_id, @id = $id, @sort_field = 'WORK_UNITS', @sort_dir = 'desc'";
 sybase_query("set rowcount 0");
 $result = sybase_query($qs);
 $best_day = sybase_fetch_object($result);
 $best_day_units = (double) $best_day->WORK_UNITS;
 debug_text("<!-- Best Day -- qs: $qs, best_day: $best_day, best_day_units: $best_day_units -->\n", $debug);

 // Get the latest record from Daily_Summary, store in $yest_totals

 $qs = "select * from Daily_Summary nolock
	where PROJECT_ID = $project_id and DATE = (select max(DATE) from Daily_Summary)";
 sybase_query("set rowcount 0");
 $result = sybase_query($qs);
 $yest_totals = sybase_fetch_object($result);

 print "
  <center>
   <table>
    <tr>
     <td colspan=\"3\" align=\"center\">
      <br>
      <font size=\"+2\"><strong>$participant's stats</strong></font>
      <hr>
      $motto
     </td>
    </tr>
    <tr>
     <td></td>
     <td align=\"center\"><font $fontd size=\"+1\">Rank</font></td>
     <td align=\"center\"><font $fontd size=\"+1\">Gnodes</font></td>
    </tr>
    <tr>
     <td><font $fontd size=\"+1\">Overall:</font></td>
     <td align=\"right\" size=\"+2\"><font $fontf>$rs_rank->OVERALL_RANK" .
		html_rank_arrow($rs_rank->Overall_Change) . "</font></td>
     <td align=\"right\" size=\"+2\"><font $fontf>" . number_style_convert($rs_rank->TOTAL) . "</font></td>
    </tr>
    <tr>
     <td><font $fontd size=\"+1\">Yesterday:</font></td>
     <td align=\"right\" size=\"+2\"><font $fontf>$rs_rank->DAY_RANK" .
		html_rank_arrow($rs_rank->Day_Change) . "</font></td>
     <td align=\"right\" size=\"+2\"><font $fontf>" . number_style_convert($rs_rank->TODAY) . "</font></td>
    </tr>
    <tr>
     <td><font $fontd size=\"+1\">Time Working:</font></td>
     <td colspan=\"2\" align=\"right\" size=\"+2\"><font $fontf>" . number_format($rs_rank->Days_Working) . " days</font></td>
    </tr>
    <tr>
     <td colspan=\"3\">
      <hr>
     </td>
    </tr>
   </table>
  <p>
	";
  $pct_of_best = (double) $rs_rank->TODAY / $best_day_units;
  debug_text("<!-- pct_of_best: $pct_of_best, rs_rank->WORK_TODAY: $rs_rank->WORK_TODAY, best_day->WORK_UNITS: $best_day->WORK_UNITS -->\n", $debug);
  if($pct_of_best == 1) {
    print "
	<br>
	<font color=\"red\">Yesterday was this participant's best day ever!</font>
	</p>";
  } elseif ( $best_day_units > 0 ) {
    print "
	</p>
	<p>
	This is " . number_format($pct_of_best*100,0) . "% of this participant's best day ever, which was
	<br>
	" . sybase_date_format_long($best_day->DATE) . " when " . number_format($best_day->WORK_UNITS,0)
	. " units were completed.
	</p>\n<!-- Thanks, Havard! -->\n";
  }
 
 print "
	<p>
	<a href=\"phistory.php?project_id=$project_id&id=$id\">View this Participant's Work Unit Submission History</a>
	</p>
    <table border=\"1\" cellspacing=\"0\" bgcolor=$header_bg>
     <tr>
      <td colspan=\"6\" align=\"center\"><font $header_font><strong>$participant's neighbors</strong></font></td>
     </tr>";
 par_header($header_font);
 $totaltoday = 0;
 $totaltotal = 0;
 for ($i = 0; $i < $numneighbors; $i++) {
        sybase_data_seek($neighbors,$i);
        $par = sybase_fetch_object($neighbors);
	if($id<>$par->id) {
	  par_list($i,$par,&$totaltoday,&$totaltotal);
	} else {
	  par_list($i,$par,&$totaltoday,&$totaltotal, "#ffffff","#ffffff");
	}
 }
 par_footer($footer_font,$totaltoday,$totaltotal);
 if($numfriends>1) {
   print "
     <tr>
      <td colspan=\"6\" align=\"center\"><font $header_font><strong>$participant's friends</strong></font></td>
     </tr>";
   par_header($header_font);
   $totaltoday = 0;
   $totaltotal = 0;
   for ($i = 0; $i < $numfriends; $i++) {
        sybase_data_seek($friends,$i);
        $par = sybase_fetch_object($friends);
	if($id<>$par->id) {
	  par_list($i,$par,&$totaltoday,&$totaltotal);
	} else {
	  par_list($i,$par,&$totaltoday,&$totaltotal, "#ffffff","#ffffff");
	}
   }
   par_footer($footer_font,$totaltoday,$totaltotal);
 }
 print "
   </table>
   <br>
   <hr>
   <p>
    <form action=\"/ppass.php\"><input type=\"hidden\" name=\"id\" value=\"$id\"><input type=\"submit\" value=\"Please email me my password.\"></form>
   </p>
  </center>
	";

?>
 </body> 
</html>
