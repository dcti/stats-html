<?

# $Id: plist.php,v 1.6 2002/03/16 15:47:26 paul Exp $

$hour = 3;
$now = getdate();
if ($now['hours'] >= 0 and $now['hours'] < $hour) {
	$now = time();
} else {
	$now = time() + 86400;
}

Header("Cache-Control: must-revalidate");
Header("Expires: " . gmdate("D, d M Y", $now) . " $hour:00 GMT");

// Variables Passed in url:
//   low == lowest rank used
//   limit == how many lines to retuwn
//   source == "y" for yseterday, all other values ignored.

include "../etc/limit.inc";	// Handles low, high, limit calculations

include "../etc/config.inc";
include "../etc/modules.inc";
include "etc/project.inc";

if ("$source" == "y") {
  $title = "Participant Listing by Yesterday's Rank: $lo to $hi";
  $QSlist = "select r.id, r.first_date as first, r.LAST_DATE as last, r.WORK_TODAY/$divider as blocks,
	datediff(day, r.FIRST_DATE, r.LAST_DATE)+1 as Days_Working,
	r.DAY_RANK as rank, r.DAY_RANK_PREVIOUS - r.DAY_RANK as change,
	p.email, p.listmode as listas, p.contact_name
	from Email_Rank r, STATS_Participant p
	where DAY_RANK <= $hi and DAY_RANK >= $lo and r.id = p.id and p.listmode < 10 and r.PROJECT_ID = $project_id
	order by r.DAY_RANK, r.WORK_TODAY desc";
} else {
  $source = "o";
  $title = "Participant Listing by Overall Rank: $lo to $hi";
  $QSlist = "select r.id, r.first_date as first, r.LAST_DATE as last, r.WORK_TOTAL/$divider as blocks,
	datediff(day, r.FIRST_DATE, r.LAST_DATE)+1 as Days_Working,
	r.OVERALL_RANK as rank, r.OVERALL_RANK_PREVIOUS - r.OVERALL_RANK as change,
	p.email, p.listmode as listas, p.contact_name
	from Email_Rank r, STATS_Participant p
	where OVERALL_RANK <= $hi and OVERALL_RANK >= $lo and r.id = p.id and p.listmode < 10 and r.PROJECT_ID = $project_id
	order by r.OVERALL_RANK, r.WORK_TOTAL desc";
}

 $qs = "p_lastupdate @section='e', @contest='new', @project_id=$project_id";
 $result = sybase_query($qs);
 $par = sybase_fetch_object($result);
 $lastupdate = sybase_date_format_long($par->lastupdate);
 include "templates/header.inc";

 sybase_query("set rowcount 100");
 $result = sybase_query($QSlist);

 debug_text("<!-- QSlist: $QSlist, result: $result -->", $debug);

 if ($result == "") {
   if ($debug=="yes") {
     include "templates/debug.inc";
   } else {
     include "templates/error.inc";
   }
   exit();
 }
 
 $rows = sybase_num_rows($result);

 print "
    <center>
     <br>
     <table border=\"1\" cellspacing=\"0\" bgcolor=$header_bg>
      <tr>
       <td><font $header_font>Rank</font></td>
       <td><font $header_font>Participant</font></td>
       <td align=\"right\"><font $header_font>First Unit</font></td>
       <td align=\"right\"><font $header_font>Last Unit</font></td>
       <td align=\"right\"><font $header_font>Days</font></td>
       <td align=\"right\"><font $header_font>$proj_unitname</font></td>
      </tr>
 ";

 $totalblocks = (double) 0;

 for ($i = 0; $i<$rows; $i++) {
	if( ($i/2) == (round($i/2)) ) {
	  echo "  <tr bgcolor=$bar_color_a>\n";
	} else {
	  echo "  <tr bgcolor=$bar_color_b>\n";
	}
	sybase_data_seek($result,$i);
	$par = sybase_fetch_object($result);

// Leave this as-is (instead of using debug_text() so as to eliminate all the text processing durring normal
// run conditions.
// if ($debug == yes) {
//	print "<!-- $i, $par->listas, $par->email,$par->id,$par->contact_name " .
//		participant_listas($par->listas, $par->email,$par->id,$par->contact_name) . " -->\n";
//}
        $parid = 0+$par->id;
	$totalblocks = $totalblocks + (double) $par->blocks;
	$decimal_places=0;
	$blocks=number_style_convert( (double) $par->blocks );
        $firstd = substr($par->first,4,2);
	$firstm = substr($par->first,0,3);
	$firsty = substr($par->first,7,4);
        $lastd = substr($par->last,4,2);
	$lastm = substr($par->last,0,3);
	$lasty = substr($par->last,7,4);

	debug_text("<!-- par->blocks: " . (double) $par->blocks . ", blocks: $blocks, totalblocks: $totalblocks. -->\n", $debug);

	print "   <td>$par->rank ";
        if ($par->change > 0) {
          print "<font color=\"#009900\">(<img src=\"/images/up.gif\" alt=\"+\">$par->change)</font></td>\n";
        } else {
          if ($par->change < 0) {
            $offset = -$par->change;
            print "<font color=\"#990000\">(<img src=\"/images/down.gif\" alt=\"-\">$offset)</font></td>\n";
          }
        }
	print "	<td><a href=\"psummary.php?project_id=$project_id&id=$parid\"><font color=\"#cc0000\">" . participant_listas($par->listas,
			$par->email,$par->id,$par->contact_name) . "</font></a></td>
		<td align=\"right\">$firstd-$firstm-$firsty</td>
		<td align=\"right\">$lastd-$lastm-$lasty</td>
		<td align=\"right\">$par->Days_Working</td>
		<td align=\"right\">$blocks</td>
		</tr>
	";
 }
 $totalblocks = number_format($totalblocks, 0);
 $pr_lo = $lo-$limit;
 $nx_lo = $lo+$limit;
 if ( $lo > $rows ) {
   $btn_back = "<a href=\"$myname?project_id=$project_id&low=$pr_lo&limit=$limit&source=$source\">Back $limit</a>";
 } else {
   $btn_back = "&nbsp;";
 }

 if ( 2 > 1 ) {
   $btn_fwd = "<a href=\"$myname?project_id=$project_id&low=$nx_lo&limit=$limit&source=$source\">Next $limit</a>";
 } else {
   $btn_fwd = "&nbsp;";
 }

 print "
	 <tr bgcolor=$footer_bg>
	  <td><font $footer_font>$lo-$hi</font></td>
	  <td align=\"right\" colspan=\"4\"><font $footer_font>Total</font></td>
	  <td align=\"right\"><font $footer_font>$totalblocks</font></td>
	 </tr>
	 <tr bgcolor=$footer_bg>
	  <td><font $footer_font>$btn_back</font></td>
	  <td colspan=\"4\"><font $footer_font>&nbsp;</font></td>
	  <td align=\"right\"><font $footer_font>$btn_fwd</font></td>
	 </tr>
	</table>
	";
?>
<?include "templates/footer.inc";?>
