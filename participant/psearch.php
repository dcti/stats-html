<?
 # $Id: psearch.php,v 1.3 2002/03/08 23:29:15 paul Exp $

 // Variables Passed in url:
 //   st == Search Term

 $title = "Participant Search: [$st]";

 include "../etc/config.inc";
 include "../etc/modules.inc";
 include "etc/project.inc";

 sybase_pconnect($interface, $username, $password);
 $QRSLTsearch = "";

// Execute the procedure to get the results
// Parameters to rc5_64_search are searchtext, maxrows (50), escapewildcards (true)
$QRSLTsearch = sybase_query("p_psearch @project='new', @project_id=$project_id, @searchtext='$st', @maxrows=50, @escapewildcards=1");

 // If $QRSLTsearch is still blank, we ain't getting anything back...
 if ($QRSLTsearch == "") {
	if ($debug=="yes") {
	  include "templates/debug.inc";
	} else {
	  include "templates/error.inc";
	}
	exit();
 }

 $rows = sybase_num_rows($QRSLTsearch);

 if($rows == 1) {
	# Only one hit, let's jump straight to psummary
	$ROWparticipant = sybase_fetch_object($QRSLTsearch);
	$id = (int) $ROWparticipant->id;
#	header("Location: http://stats.distributed.net/$project/psummary.php?project_id=$project_id&id=$id");
	header("Location: psummary.php?project_id=$project_id&id=$id");
	exit;
 }

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
        "http://www.w3.org/TR/REC-html40/loose.dtd">
<?
 $qs = "p_lastupdate e, @contest='new', @project_id=$project_id";
 $QRSLTupdate = sybase_query($qs);
 if($QRSLTupdate) {
   $ROWupdate = sybase_fetch_object($QRSLTupdate);
   $lastupdate = sybase_date_format_long($ROWupdate->lastupdate);
 } else {
  $lastupdate = "A long time ago, in a galaxy far, far away...";
 }

 include "templates/header.inc";

 // Print debug info from the first query we ran
 if ($debug == "yes") print "<!-- result: '$QRSLTsearch', par: '$ROWparticipant', rows: $rows -->";

 print "
    <center>
     <br>
     <table border=\"1\" cellspacing=\"0\" bgcolor=$header_bg>
      <tr>
       <td><font $header_font>Rank</font></td>
       <td><font $header_font>Participant</font></td>
       <td align=\"right\"><font $header_font>First Block</font></td>
       <td align=\"right\"><font $header_font>Last Block</font></td>
       <td align=\"right\"><font $header_font>Days</font></td>
       <td align=\"right\"><font $header_font>Gnodes</font></td>
      </tr>
 ";

 $totalblocks = (double) 0;
 for ($i = 0; $i<$rows; $i++) {
	// Retrieve the records returned and format appropriately
	sybase_data_seek($QRSLTsearch, $i);
	$ROWparticipant = sybase_fetch_object($QRSLTsearch);
	$id = (int) $ROWparticipant->id;
	if ($debug=="yes") print "<!-- ID: $id -->";

	// Process the info
	if( ($i/2) == (round($i/2)) ) {
		echo "  <tr bgcolor=$bar_color_a>\n";
	} else {
		echo "  <tr bgcolor=$bar_color_b>\n";
	}

	$totalblocks += (double) $ROWparticipant->WORK_TOTAL/$divider;

        print "   <td>$ROWparticipant->OVERALL_RANK " . html_rank_arrow($ROWparticipant->Overall_Change) . "</td>\n";

	if ($debug == yes) print "<!--- listmode: $ROWparticipant->listmode, WORK_TOTAL: " . (double) $ROWparticipant->WORK_TOTAL \
		. ", totalblocks: $totalblocks. --->\n";

	print "
		<td><a href=\"psummary.php?project_id=$project_id&id=$id\"><font color=\"#cc0000\">" . participant_listas($ROWparticipant->listmode,
			$ROWparticipant->email,$id,$ROWparticipant->contact_name) . "</font></a></td>
		<td align=\"right\">" . sybase_date_format_long($ROWparticipant->first_date) . "</td>
		<td align=\"right\">" . sybase_date_format_long($ROWparticipant->last_date) . "</td>
		<td align=\"right\">" . number_style_convert($ROWparticipant->Days_Working) . "</td>
		<td align=\"right\">" . $blocks=number_style_convert( (double) $ROWparticipant->WORK_TOTAL/$divider) . "</td>
		</tr>
	";
 }

 print "
	 <tr bgcolor=$footer_bg>
	  <td><font $footer_font>$i</font></td>
	  <td colspan=\"4\" align=\"right\"><font $footer_font>Total</font></td>
	  <td align=\"right\"><font $footer_font>" . number_format($totalblocks, 0) . "</font></td>
	 </tr>
	</table>
	";
 if( $rows == 0 ) {
   print "
	<p>
	 <a href=\"http://www.distributed.net/FAQ/\"><font size=\"+3\" color=\"red\">Confused?  Look here</font></a>
	</p>";
 }
?>
   <p>
    <a href="http://www.sybase.com"><img border="0" alt="Sybase" src="/images/sybase.gif"></a>
    <br>
    Sybase rocks!
   </p>
  </center>
 </body>
</html>
