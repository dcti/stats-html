<?
// $Id: tmsummary.php,v 1.5 2002/03/09 18:31:29 paul Exp $

// Variables Passed in url:
//  team == team id to display

$tm = (int) $team;
if ($tm <= 0){
  $tm = 1;
}

$title = "Team #$tm Summary";

include "../etc/config.inc";
include "../etc/modules.inc";
include "etc/project.inc";
include "../etc/markup.inc";

sybase_pconnect($interface, $username, $password);

$qs = "p_lastupdate @section=t, @contest='new', @project_id=$project_id";
$result = sybase_query($qs);
if(!$result) {
  $qs = "p_lastupdate @section=t, @contest='new', @project_id=$project_id";
  $result = sybase_query($qs);
}
if($result) {
  $par = sybase_fetch_object($result);
  $lastupdate = sybase_date_format_long($par->lastupdate);
} else {
  $lastupdate = "some day, not too long ago";
}

include "templates/header.inc";

// Query server
$qs = "select t.*, r.*,
	datediff(day, FIRST_DATE, LAST_DATE)+1 as Days_Working,
	OVERALL_RANK_PREVIOUS-OVERALL_RANK as Overall_Change,
	DAY_RANK_PREVIOUS-DAY_RANK as Day_Change
	from STATS_team t, Team_Rank r
	where r.TEAM_ID = t.team
	and t.team=$tm
	and r.TEAM_ID=$tm
	and PROJECT_ID = $project_id";
$result = sybase_query($qs);
$rows = sybase_num_rows($result);

debug_text("<!-- Team Info -- qs: $qs, result: $result, rows: $rows -->\n",$debug);

if ($rows == 0) {
	echo "<H2>That team is not known.</H2><BR>";
	include "templates/footer.inc";
	exit;
	}

$par = sybase_fetch_object($result);

$qs = "select t.name, r.*,
	datediff(day, FIRST_DATE, LAST_DATE)+1 as Days_Working,
	OVERALL_RANK_PREVIOUS-OVERALL_RANK as Change
	from STATS_Team t, Team_Rank r
	where ( OVERALL_RANK < ($par->OVERALL_RANK+5) )
		and ( OVERALL_RANK > ($par->OVERALL_RANK-5) )
		and t.team=r.TEAM_ID
		and t.listmode<=9
		and PROJECT_ID = $project_id
	order by OVERALL_RANK";
sybase_query("set rowcount 18");
$neighbors = sybase_query($qs);
$numneighbors = sybase_num_rows($neighbors);

debug_text("<!-- Neighbors -- qs: $qs, neighbors: $neighbors, numneighbors: $numneighbors -->\n",$debug);

if (private_markupurl_safety($par->logo) != "") {
  $logo = "<IMG src=\"$par->logo\">";
} else {
  $logo = "";
}

echo "<H1><CENTER>$par->name</CENTER></H1>\n";
echo "<CENTER><TABLE>
   <TR>
    <TD>$logo</TD>
    <TD><CENTER>".markup_to_html($par->description)."<CENTER></TD>
   <TR>
   <TR><TD COLSPAN=2><CENTER>Contact: ".htmlspecialchars($par->contactemail)."</CENTER></TD></TR>
  </TABLE></CENTER>\n";
echo "<BR><BR>\n";

print "<CENTER><TABLE cellspacing=\"4\">\n
    <tr>
     <td></td>
     <td align=\"center\"><font $fontd size=\"+1\">Rank </font></td>
     <td align=\"center\"><font $fontd size=\"+1\">$proj_unitname </font></td>
     <td align=\"center\"><font $fontd size=\"+1\">$proj_unitname/<br>Member</font></td>
    </tr>
    <tr>
     <td><font $fontd size=\"+1\">Overall:</font></td>
     <td align=\"right\" size=\"+2\"><font $fontf>$par->OVERALL_RANK" .
		html_rank_arrow($par->Overall_Change) . "</font></td>
     <td align=\"right\" size=\"+2\"><font $fontf> " . number_style_convert( (double) $par->WORK_TOTAL/$proj_divider ) . "</font></td>
     <td align=\"right\" size=\"+2\"><font $fontf> " .
	number_style_convert( (double) $par->WORK_TOTAL/$proj_divider/$par->MEMBERS_OVERALL ) . "</font></td>
    </tr>";

if ( $par->WORK_TODAY > 0 ) print "
    <tr>
     <td><font $fontd size=\"+1\">Yesterday:</font></td>
     <td align=\"right\" size=\"+2\"><font $fontf>$par->DAY_RANK" .
		html_rank_arrow($par->Day_Change) . "</font></td>
     <td align=\"right\" size=\"+2\"><font $fontf> " . number_style_convert( (double) $par->WORK_TODAY/$proj_divider ) . "</font></td>
     <td align=\"right\" size=\"+2\"><font $fontf> " .
	number_style_convert( (double) $par->WORK_TODAY/$proj_divider/$par->MEMBERS_TODAY ) . "</font></td>
    </tr>";
print "
    <tr>
     <td><font $fontd size=\"+1\">Time Working: </font></td>
     <td colspan=\"3\" align=\"right\" size=\"+2\"><font $fontf>" . number_format($par->Days_Working) . " days</font></td>
    </tr>
    </tr>
   </table>
<BR><BR>

  <p>
    This team has had " . number_style_convert( $par->MEMBERS_OVERALL ) . " participants contribute blocks.
    Of those, " . number_style_convert( $par->MEMBERS_CURRENT ) . " are still on this team,
    and " . number_style_convert( $par->MEMBERS_TODAY ) . " submitted work today.
  </p>
";

//Some buttons to view team history will go here

if ($par->showmembers=="NO") {
	print "
		<center><p>This team wishes to keep its membership private.<p>";
} else {  
	if ($par->WORK_TODAY == 0) {
		print "<center><p>Click here to view this team's 
			<a href=\"tmember.php?project_id=$project_id&team=$tm\"><font color=\"#000000\">overall</font></a> participant stats";
	} else {
		print "<center><p>Click here to view this team's participant stats for
			<a href=\"tmember.php?project_id=$project_id&team=$tm&source=y\"><font color=\"#000000\">yesterday</font></a> or
			<a href=\"tmember.php?project_id=$project_id&team=$tm\"><font color=\"#000000\">overall</font></a>";
	}
	
	if ($par->showmembers=="PAS") {
		print " (Password required)";
	}

	print ".</p>";
}

//A list of teams goes here
 print "
   </p>
	<center>
    <table border=\"1\" cellspacing=\"0\" bgcolor=$header_bg>
     <tr>
      <td><font $header_font>Rank</font></td>
      <td><font $header_font>Team</font></td>
      <td align=\"right\"><font $header_font>Days</font></td>
      <td align=\"right\"><font $header_font>$proj_unitname</font></td>
     </tr>";
 for ($i = 0; $i < $numneighbors; $i++) {
        if( ($i/2) == (round($i/2)) ) {
          echo "  <tr bgcolor=$bar_color_a>\n";
        } else {
          echo "  <tr bgcolor=$bar_color_b>\n";
        }
        sybase_data_seek($neighbors,$i);
        $teamrec = sybase_fetch_object($neighbors);
        $teamrecid = 0 + $teamrec->TEAM_ID;
        $totalblocks += (double) $teamrec->WORK_TOTAL/$proj_divider;
        $decimal_places=0;
        $blocks=number_style_convert( (double) $teamrec->WORK_TOTAL/$proj_divider );

        print "   <td>$teamrec->OVERALL_RANK ";
        if ($teamrec->Change > 0) {
          print "<font color=\"#009900\">(<img src=\"/images/up.gif\" alt=\"+\">$teamrec->Change)</font></td>\n";
        } else {
          if ($teamrec->Change < 0) {
            $offset = -$teamrec->Change;
            print "<font color=\"#990000\">(<img src=\"/images/down.gif\" alt=\"-\">$offset)</font></td>\n";
          }
        }
        print "
		<td><a href=\"tmsummary.php?project_id=$project_id&team=$teamrecid\"><font color=\"#cc0000\">$teamrec->name</font></a></td>
		<td align=\"right\">$teamrec->Days_Working</td>
		<td align=\"right\">$blocks</td>
	</tr>
        ";
 }
 print "
	<tr bgcolor=$footer_bg>
		<td align=\"right\" colspan=\"3\"><font $footer_font>Total</font></td>
		<td align=\"right\" colspan=\"3\"><font $footer_font>" . number_style_convert($totalblocks) . "</td>
	</tr>
   </table>
   <hr>
   <a href=\"/pjointeam.php?team=$team\">I want to join this team!</a>
   <hr>
  </center>
	";

 print "
  <center>
   <form action=\"/tmedit.php\" method=\"post\">
    <p>
     Edit this team's information 
     <br>
     Password:
     <input name=\"pass\" size=\"8\" maxlength=\"8\" type=\"password\">
     <input name=\"team\" type=\"hidden\" value=\"$team\">
     <input value=\"Edit\" type=\"submit\">
    </p>
   </form>
   <p>
    If you are the team coordinator, and you've forgotten your team password, click
    <form action=\"/tmpass.php\"><input type=\"hidden\" name=\"team\" value=\"$team\">
    <input type=\"submit\" value=\"here\"></form> and the password will be mailed to
    $par->contactemail.
   </p>
  </center>";

include "templates/footer.inc";

?>
</HTML>
