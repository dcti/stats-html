<?
// $Id: tmsummary.php,v 1.14 2002/06/06 11:56:47 paul Exp $

// Variables Passed in url:
//  team == team id to display

$tm = (int) $team;
if ($tm <= 0){
  $tm = 1;
}

$title = "Team #$tm Summary";

include "../etc/config.inc";
include "../etc/modules.inc";
include "../etc/project.inc";
include "../etc/markup.inc";

$lastupdate = last_update('t');
include "../templates/header.inc";

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
	include "../templates/footer.inc";
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

$qs = "select * from Daily_Summary nolock
	where PROJECT_ID = $project_id and DATE = (select max(DATE) from Daily_Summary where project_id=$project_id)";
sybase_query("set rowcount 0");
$result = sybase_query($qs);
$yest_totals = sybase_fetch_object($result);


debug_text("<!-- Neighbors -- qs: $qs, neighbors: $neighbors, numneighbors: $numneighbors -->\n",$debug);


$constant_keys_per_block = 268435456;
$overall_rate = ((((double)$par->WORK_TOTAL)*$constant_keys_per_block)/(86400*$par->Days_Working))/1000;

if (private_markupurl_safety($par->logo) != "") {
  $logo = "<IMG src=\"$par->logo\">";
} else {
  $logo = "";
}
?>
<H1><CENTER><? echo safe_display($par->name)?></CENTER></H1>
<CENTER><TABLE>
   <TR>
    <TD><?echo $logo?></TD>
    <TD><CENTER><?echo markup_to_html($par->description)?><CENTER></TD>
   <TR>
   <TR><TD COLSPAN=2><CENTER>Contact: <?echo safe_display($par->contactemail)?><CENTER></TD></TR>
  </TABLE></CENTER>
<BR><BR>
<CENTER><TABLE cellspacing="4">
    <tr>
     <td></td>
     <td align="center"><font <?=$fontd?> size="+1">Rank </font></td>
     <td align="center"><font <?=$fontd?> size="+1"><?=$proj_unitname?> </font></td>
     <td align="center"><font <?=$fontd?> size="+1"><?=$proj_unitname?><br>Member</font></td>
    </tr>
    <tr>
     <td><font <?=$fontd?> size="+1">Overall:</font></td>
     <td align="right" size="+2"><font <?=$fontf?>><? echo $par->OVERALL_RANK .
html_rank_arrow($par->Overall_Change) ?> </font></td>
     <td align="right" size="+2"><font <?=$fontf?>> <? echo number_style_convert( (double) $par->WORK_TOTAL/$proj_divider ) ?></font></td>
     <td align="right" size="+2"><font <?=$fontf?>> 
	<? echo number_style_convert( (double) $par->WORK_TOTAL/$proj_divider/$par->MEMBERS_OVERALL ) ;?> </font></td>
    </tr>
<?
if ( $par->WORK_TODAY > 0 ) 
?>
    <tr>
     <td><font <?=$fontd?> size="+1">Yesterday:</font></td>
     <td align="right" size="+2"><font <?=$fontf?>><? echo $par->DAY_RANK .
		html_rank_arrow($par->Day_Change)?> </font></td>
     <td align="right" size="+2"><font <?=$fontf?>> <?echo number_style_convert( (double) $par->WORK_TODAY/$proj_divider )?></font></td>
     <td align="right" size="+2"><font <?=$fontf?>>
	<?echo number_style_convert( (double) $par->WORK_TODAY/$proj_divider/$par->MEMBERS_TODAY )?></font></td>
    </tr>
    <tr>
     <td><font <?=$fontd?> size="+1">Time Working: </font></td>
     <td colspan="3" align="right" size="+2"><font <?=$fontf?>><?echo  number_format($par->Days_Working)?> days</font></td>
    </tr>
<? if ($proj_totalunits > 0 ) { ?>
    <tr>
     <td><font <?=$fontd?> size="+1">Overall Rate: </font></td>
     <td colspan="3" align="right" size="+2"><font <?=$fontf?>><?echo  number_format($overall_rate)?> Kkeys/second</font></td>
    <tr>
<? } ?> 
   </tr>
   </table>
<BR><BR>

<? if ($proj_totalunits > 0 ) { ?>
The odds are 1 in <?=number_format((double)$yest_totals->WORK_UNITS / (double) $par->WORK_TODAY)?> that this team will
	find the key before anyone else does. <BR>
<? } ?>


  <p>
    This team has had <?echo number_style_convert( $par->MEMBERS_OVERALL )?> participants contribute blocks.
    Of those, <?echo number_style_convert( $par->MEMBERS_CURRENT )?> are still on this team,
    and <?echo number_style_convert( $par->MEMBERS_TODAY )?> submitted work today.
  </p>
<?

//Some buttons to view team history will go here

if ($par->showmembers=="NO") {
?>	
  	<center><p>This team wishes to keep its membership private.<p>
<? } else {  
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
?> 
   </p>
	<center>
    <table border="1" cellspacing="0" bgcolor=<?=$header_bg?>>
     <tr>
      <th>Rank</th>
      <th>Team</th>
      <th align="right">Days</th>
      <th align="right"><?=$proj_unitname?></th>
     </tr>
<?
 for ($i = 0; $i < $numneighbors; $i++) {
?>
	<tr class="<?=row_background_color($i)?>">
<?        
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
?>
	<tr bgcolor=<?=$footer_bg?>>
		<td align="right" colspan="3"><font <?=$footer_font?>>Total</font></td>
		<td align="right" colspan="3"><font <?=$footer_font?>><? echo number_style_convert($totalblocks)?></td>
	</tr>
   </table>
   <hr>
   <a href="/participant/pjointeam.php?team=<?=$team?>">I want to join this team!</a>
   <hr>
  </center>
  <center>
   <form action="tmedit.php" method="post">
    <p>
     Edit this team's information 
     <br>
     Password:
     <input name="pass" size="8" maxlength="8" type="password">
     <input name="team" type="hidden" value="<?=$team?>">
     <input value="Edit" type="submit">
    </p>
   </form>
   <p>
    If you are the team coordinator, and you've forgotten your team password, click
    <form action="tmpass.php"><input type="hidden" name="team" value="<?=$team?>">
    <input type="submit" value="here"></form> and the password will be mailed to
    <?=$par->contactemail?>.
   </p>
  </center>

<? include "../templates/footer.inc"; ?>
