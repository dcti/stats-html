<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
	"http://www.w3.org/TR/REC-html40/loose.dtd">
<?
// $Id: tmember.php,v 1.1 2002/03/08 21:50:41 decibel Exp $

// Variables Passed in url:
//  team == team id to display
//  low == ID to start at, assume 0 if not specified
//  limit == Number of records to display at once - default 100
//  pass == Password for viewing member listing
//  source == y for yesterday's contributors only

$myname = "tmember.php3";

include "../etc/config.inc";
include "../etc/modules.inc";
include "etc/project.inc";

debug_text("<!-- team: $team, low: $low, limit: $limit, source: $source, pass: $pass. -->\n",$debug);

if ($team == ""){
	$tm = 1;
} else {
	$tm = 0+$team;
}

if ($low == ""){
	$low = 0;
} else {
	// Make external interface 1 based, internal 0 based
	$low = $low - 1;
	if ($low < 0){
		$low = 0;
	}
}

if ($limit == "") {
	$limit = 100;
}
$lim = $limit;

// Connect to server
sybase_pconnect($interface, $username, $password);

// Query server for basic team information
$qs = "select name, showpassword, showmembers
	from STATS_team
	where team = $tm";
$result = sybase_query($qs);
$info = sybase_fetch_object($result);


$rows = sybase_num_rows($result);
$rows = 0+$rows;

// Verify the team exists
$title = "Team Members Listing - Team #".$tm;

// Do the password checking
if ($info->showmembers == "NO") {
	
	include "templates/header.inc";

	print "<h1>Hey, you ain't supposed to be here!</h1>
		<p>This team does not want to list it's members, so you might as well
		quit asking for them!</p>";
	include "templates/footer.inc";
	exit;
}

if ($info->showmembers == "PAS") {
	if ($pass != $info->showpassword ) {
	
		include "etc/lastupdate.inc";

		if ($pass == "") {
			print "	<h1>Password required</h1>
				<p>Not so fast!  You need a password to view this page!</p>";
		} else {
			print "	<h1>Invalid password</h1>
				<p>Sorry, but the password you entered doesn't match the one I'm
				looking for... please try again.</p>";
		}
		print "	<form action=\"$myname\" method=\"get\">
			Password: <input type=\"password\" length=\"16\" name=\"pass\">
			<input type=\"hidden\" name=\"team\" value=\"$tm\">
			<input type=\"hidden\" name=\"source\" value=\"$source\">
			<input type=\"submit\" value=\"Go\">
			</form>";
		include "templates/footer.inc";
		exit;
	}
}

// Check when this info was last updated
$qs = "p_lastupdate t, new, @project_id=$project_id";
$result = sybase_query($qs);
if($result) {
	$par = sybase_fetch_object($result);
	$lastupdate = sybase_date_format_long($par->lastupdate);
} else {
	$lastupdate = "some day, not too long ago";
}

// See how many blocks this team did
$qs = "	SELECT	WORK_TOTAL/$divider as WORK_TOTAL, WORK_TODAY/$divider as WORK_TODAY
	FROM	Team_Rank
	WHERE	TEAM_ID = $tm
		and PROJECT_ID = $project_id";
$result = sybase_query("set rowcount 0");
$result = sybase_query($qs);
if ($result == "") {
	$totblocks = FALSE;
} else {
	$totblocks = TRUE;
	$blocksresult = sybase_fetch_object($result);
	$yblocks = (double) $blocksresult->WORK_TODAY;
	if ( $yblocks == 0 ) $yblocks = 1;
	$oblocks = (double) $blocksresult->WORK_TOTAL;
}

debug_text("<!-- TOTAL BLOCKS -- qs: $qs, totblocks: $totblocks, yblocks: ${yblocks}, oblocks: ${oblocks}. -->\n",$debug);

// Query server for member listing
if ($source == y) {		// $qs_source is an easy way around re-doing $qs based on $source
	$qs_source = "";
} else {
	$qs_source = "*";
}

$qs = "	SELECT	tm.WORK_TOTAL/$divider as WORK_TOTAL, tm.FIRST_DATE, tm.LAST_DATE,
		p.id, p.listmode, p.contact_name, p.email, p.team,
		tm.WORK_TODAY/$divider as WORK_TODAY,";
if ($source == y) {
$qs .= "
		er.DAY_RANK as eRANK, (er.DAY_RANK_PREVIOUS - er.DAY_RANK) as eRANK_CHANGE";
} else {
$qs .= "
		er.OVERALL_RANK as eRANK, (er.OVERALL_RANK_PREVIOUS - er.OVERALL_RANK) as eRANK_CHANGE";
}
$qs .= "
	FROM	Team_Members tm, STATS_Participant p, Email_Rank er
	WHERE	tm.PROJECT_ID = $project_id
		and er.PROJECT_ID = $project_id
		and tm.PROJECT_ID = er.PROJECT_ID
		and tm.TEAM_ID = $tm
		and p.id = tm.ID
		and er.ID = tm.ID
		and p.id = er.ID";
if ($source == y) {
$qs .= "
		and p.team = $tm
	ORDER BY	er.WORK_TODAY desc, tm.WORK_TOTAL desc";
} else {
$qs .= "
	ORDER BY	tm.WORK_TOTAL desc, er.WORK_TODAY desc";
}

$result = sybase_query("set rowcount 0");
$result = sybase_query($qs);
$rows = sybase_num_rows($result);
$rows = 0+$rows;

debug_text("<!-- Query: \"$qs\" Rows: \"$rows\" -->\n",$debug);

// Sanity check $low and $limit
if ($low > $rows) {
	$low = $rows - 1;
	$limit = 1;
}

if ($low + $limit > $rows)
{
	$limit = $rows - $low;
	if ($limit > $rows) $limit = $rows;
	$low = $rows - $limit;
}

$hi = $low + $limit;
$lo = $low + 1;
$title = "$info->name Members ";
if ($source == y) $title = $title . "Yesterday ";
    else $title = $title . "Overall ";
$title = $title . "$lo - $hi";

include "etc/project.inc";
include "templates/header.inc";

// Display how many members
print "<BR><TABLE border=\"0\"><tr>
	<td align=left>Total Members:</td>
	<td align=right>$rows</td>
	</tr></table><br>";

// Provide a link back to tmsummary.php3
print "<center>Return to the <a href=\"tmsummary.php3?team=$tm\">team summary page</a>.</center>";

// Start the table
print "<CENTER>
	<BR>
	 <TABLE border=\"1\" cellspacing=\"0\" bgcolor=$header_bg>
	  <tr>
	   <td><font $header_font>Team Rank</font></td>
	   <td><font $header_font>Participant</font></td>
           <td><font $header_font>Project Rank</font></td>
	   <td><font $header_font>First</font></td>
	   <td><font $header_font>Last</font></td>
	   <td><font $header_font>Yesterday</font></td>
";

if ($totblocks) print "<td><font $header_font>%</font></td>\n";

print "	   <td><font $header_font>Total</font></td>\n";

if ($totblocks) print "<td><font $header_font>%</font></td>\n";

print "	  </tr>\n";

// Generate the listing here.
for ($i = $low; $i < $low + $limit; $i++)
{
	sybase_data_seek($result, $i);
	$member = sybase_fetch_object($result);
	$rnk = number_style_convert($i+1);
	$prnk = $member->eRANK;
	$prnkchg = $member->eRANK_CHANGE;
	$n_yesterday = (double) $member->WORK_TODAY;
	$yesterday = number_style_convert($n_yesterday);
	$n_blocks = (double) $member->WORK_TOTAL;
	$blocks = number_style_convert($n_blocks);
	$first = sybase_date_format_long($member->FIRST_DATE);
	$last = sybase_date_format_long($member->LAST_DATE);
	$linkid = (int) $member->id;
	$fmtid = number_style_convert($linkid);

	$listas = participant_listas($member->listmode, $member->email, $member->id, $member->contact_name);
	debug_text("<!--- y:$n_yesterday o:$n_blocks yt:$yblocks ot:$oblocks y%:$n_yesterday/$yblocks --->\n",$debug);

	print "
		<tr bgcolor=" . row_background_color($i, $color_a, $color_b) . ">
		  <td>$rnk</td>
		  <td><a href=\"psummary.php3?id=$linkid\"><font color=\"#cc0000\">$listas</font></a></td>";
	if ($n_yesterday < 1 and $source == y) {
		print "
		  <td align=\"center\">--</td>";
	} else {
		print "
		  <td>$prnk " . html_rank_arrow($prnkchg) . "</td>";
	}
	print "
		  <td align=\"right\">$first</td>
		  <td align=\"right\">$last</td>
		  <td align=\"right\">$yesterday</td>";
	if ($totblocks) print "
		  <td align=\"right\">" . number_style_convert($n_yesterday/$yblocks*100 ,2) . "</td>";
	print "
		  <td align=\"right\">$blocks</td>";
	if ($totblocks) print "
		  <td align=\"right\">" . number_style_convert($n_blocks/$oblocks*100, 2) . "</td>";
	print "
		</tr>";
}

print "
	 </TABLE>
       </CENTER>";

// Navigation Buttons here
print "<TABLE border=\"0\" width=100%><tr>";
if ($low > 0)
{
	print "<td align = left>";
	$newlow = $low - $lim + 1;
	$newlimit=$lim;
	if ($newlow < 1)
	{
		$newlow=1;
		$newlimit=$low - $newlow + 1;
	}
	print "<a href=\"$myname?pass=" . urlencode($pass) . "&team=$tm&source=$source&low=$newlow&limit=$newlimit\">$newlow - $low</a> </TD>";
}
else {
	// needed for alignment
	print "<td></td>";
}
if ($low + $lim < $rows)
{
	print "<td align = right>";
	$newlow = $low + $lim + 1;
	$newlimit = $lim;
	if ($newlow + $newlimit > $rows)
	{
		$newlimit = $rows - $newlow + 1;
	}
	$high = $newlow + $newlimit - 1;
	print "<a href=\"$myname?pass=" . urlencode($pass) . "&team=$tm&source=$source&low=$newlow&limit=$lim\">$newlow - $high</a> </TD>";
}
else
{
	// Needed for alignment
	print "<td></td>";
}
print "</tr></TABLE>";

// Provide a link back to tmsummary.php3
if ($rows > 25) {
	print "<center>Return to the <a href=\"tmsummary.php3?team=$tm\">team summary page</a>.</center>";
}

include "templates/footer.inc";

?>
