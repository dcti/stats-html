<?
// vi: ts=2 sw=2 tw=120
// $Id: tmsummary.php,v 1.22 2003/03/10 23:32:50 paul Exp $

// Variables Passed in url:
//  team == team id to display

include "../etc/config.inc";
include "../etc/modules.inc";
include "../etc/project.inc";
include "../etc/markup.inc";

$title = "Team #$tm Summary";
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

$qs = "select *
        from Daily_Summary nolock
        where PROJECT_ID = $project_id
          and DATE = (select max(DATE) from Daily_Summary where project_id=$project_id)";
sybase_query("set rowcount 0");
$result = sybase_query($qs);
$yest_totals = sybase_fetch_object($result);


debug_text("<!-- Neighbors -- qs: $qs, neighbors: $neighbors, numneighbors: $numneighbors -->\n",$debug);

if (private_markupurl_safety($par->logo) != "") {
  $logo = "<img src=\"$par->logo\">";
} else {
  $logo = "";
}
?>
<h1><center><?= safe_display($par->name) ?></center></h1>
<center>
  <table>
    <tr>
      <td><?= $logo ?></td>
      <td><center><?= markup_to_html($par->description) ?><center></td>
    </tr>
    <tr>
    <tr>
      <td colspan=2>
        <center>Contact: <?= safe_display($par->contactemail) ?><center>
      </td>
    </tr>
  </table>
  <br>
  <br>
  <table cellspacing="4">
    <tr>
      <td></td>
      <td align="center"><font <?= $fontd ?> size="+1">Overall</font></td>
<? if ($par->WORK_TODAY > 0) { ?>
      <td align="center"><font <?= $fontd ?> size="+1">Yesterday</font></td>
<? } ?>
    </tr>
    <tr>
      <td align="left"><font <?= $fontd ?> size="+1">Rank:</font></td>
      <td align="right" size="+2"><font <?= $fontf ?>><?= $par->OVERALL_RANK . " " . html_rank_arrow($par->Overall_Change) ?></font></td>
<? if ($par->WORK_TODAY > 0) { ?>
      <td align="right" size="+2"><font <?= $fontf ?>><?= $par->DAY_RANK . " " . html_rank_arrow($par->Day_Change) ?></font></td>
<? } ?>
    </tr>
    <tr>
      <td align="left"><font <?= $fontd ?> size="+1"><?= $proj_scaled_unit_name ?>:</font></td>
      <td align="right" size="+2"><font <?= $fontf ?>><?= number_style_convert($par->WORK_TOTAL * $proj_scale) ?></font></td>
<? if ($par->WORK_TODAY > 0) { ?>
      <td align="right" size="+2"><font <?= $fontf ?>><?= number_style_convert($par->WORK_TODAY * $proj_scale) ?></font></td>
<? } ?>
    </tr>
    <? if ($par->Days_Working > 0) { ?>
    <tr>
      <td align="left"><font <?= $fontd ?> size="+1"><?= $proj_scaled_unit_name ?>/sec:</font></td>
      <td align="right" size="+2"><font <?= $fontf ?>><?= number_style_convert($par->WORK_TOTAL * $proj_scale / (86400 * $par->Days_Working), 3) ?></font></td>
<? if ($par->WORK_TODAY > 0) { ?>
      <td align="right" size="+2"><font <?= $fontf ?>><?= number_style_convert($par->WORK_TODAY * $proj_scale / 86400, 3) ?></font></td>
    <? } ?>
    </tr>
<? } ?>
    <?if($par->MEMBERS_OVERALL > 0) {?>
    <tr>
      <td align="left"><font <?= $fontd ?> size="+1"><?= $proj_scaled_unit_name ?>/member:</font></td>
      <td align="right" size="+2"><font <?= $fontf ?>><?= number_style_convert($par->WORK_TOTAL * $proj_scale / $par->MEMBERS_OVERALL) ?></font></td>
<? if ($par->WORK_TODAY > 0) { ?>
      <td align="right" size="+2"><font <?= $fontf ?>><?= number_style_convert($par->WORK_TODAY * $proj_scale / $par->MEMBERS_TODAY) ?></font></td>
<? } ?>
    </tr>
    <?}?>
    <tr>
      <td align="left"><font <?= $fontd ?> size="+1"><?= $proj_unscaled_unit_name ?>:</font></td>
      <td align="right" size="+2"><font <?= $fontf ?>><?= number_style_convert($par->WORK_TOTAL) ?></font></td>
<? if ($par->WORK_TODAY > 0) { ?>
      <td align="right" size="+2"><font <?= $fontf ?>><?= number_style_convert($par->WORK_TODAY) ?></font></td>
<? } ?>
    </tr>
    <?if ($par->Days_Working > 0) { ?>
    <tr>
      <td align="left"><font <?= $fontd ?> size="+1"><?= $proj_unscaled_unit_name ?>/sec:</font></td>
      <td align="right" size="+2"><font <?= $fontf ?>><?= number_style_convert($par->WORK_TOTAL / (86400 * $par->Days_Working)) ?></font></td>
<? if ($par->WORK_TODAY > 0) { ?>
      <td align="right" size="+2"><font <?= $fontf ?>><?= number_style_convert($par->WORK_TODAY / 86400) ?></font></td>
<? } ?>
    </tr>
    <?}?>
    <?if($par->MEMBERS_OVERALL > 0) {?>
    <tr>
      <td align="left"><font <?= $fontd ?> size="+1"><?= $proj_unscaled_unit_name ?>/member:</font></td>
      <td align="right" size="+2"><font <?= $fontf ?>><?= number_style_convert($par->WORK_TOTAL / $par->MEMBERS_OVERALL) ?></font></td>
<? if ($par->WORK_TODAY > 0) { ?>
      <td align="right" size="+2"><font <?= $fontf ?>><?= number_style_convert($par->WORK_TODAY / $par->MEMBERS_TODAY) ?></font></td>
<? } ?>
    </tr>
    <?}?>
    <tr>
      <td align="left"><font <?= $fontd ?> size="+1">Time Working:</font></td>
      <td align="right" colspan="<?= ($par->WORK_TODAY > 0) ? 3 : 2 ?>" size="+2"><font <?= $fontf ?>><?= number_style_convert($par->Days_Working) ?> days</font></td>
    </tr>
  </table>
  <br>
  <br>
  <? if($proj_totalunits > 0 && $par->WORK_TODAY == 0)
     {
   ?>
  The odds are 1 in a zillion-trillion that this team will find the key before anyone else does.
  <?} else if ($proj_totalunits > 0 && $par->WORK_TODAY > 0) { ?>
  The odds are 1 in <?= number_style_convert($yest_totals->WORK_UNITS / $par->WORK_TODAY) ?> that this team will
    find the key before anyone else does. 
  <? } ?>
  <br>
  <p>
    This team has had <?= number_style_convert($par->MEMBERS_OVERALL) ?> participants contribute blocks.
    Of those, <?= number_style_convert($par->MEMBERS_CURRENT) ?> are still on this team,
    and <?= number_style_convert($par->MEMBERS_TODAY) ?> submitted work today.
  </p>
  <?
  //Some buttons to view team history will go here
  if ($par->showmembers=="NO") {
    ?>	
    <center><p>This team wishes to keep its membership private.<p>
  <? } else {  
    if ($par->WORK_TODAY == 0) {
      print "<center><p>Click here to view this team's 
      <a href=\"tmember.php?project_id=$project_id&team=$tm\">overall</a> participant stats";
    } else {
      print "<center><p>Click here to view this team's participant stats for
      <a href=\"tmember.php?project_id=$project_id&team=$tm&source=y\">yesterday</a> or
      <a href=\"tmember.php?project_id=$project_id&team=$tm\">overall</a>";
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
    <table border="1" cellspacing="0">
      <tr>
        <th>Rank</th>
        <th>Team</th>
        <th align="right">Days</th>
        <th align="right"><?= $proj_scaled_unit_name ?></th>
      </tr>
      <?
      $totalwork = 0;
      for ($i = 0; $i < $numneighbors; $i++) {
      ?>
        <tr class="<?= row_background_color($i) ?>">
        <?        
        sybase_data_seek($neighbors,$i);
        $teamrec = sybase_fetch_object($neighbors);
        $totalwork += $teamrec->WORK_TOTAL;
        ?>
          <td><?= $teamrec->OVERALL_RANK . " " . html_rank_arrow($teamrec->Change) ?></td>
          <td>
            <font color="#cc0000">
              <a href="tmsummary.php?project_id=<?= $project_id ?>&team=<?= $teamrec->TEAM_ID + 0 ?>"><?= $teamrec->name ?></a>
            </font>
          </td>
          <td align="right"><?= number_style_convert($teamrec->Days_Working) ?></td>
          <td align="right"><?= number_style_convert($teamrec->WORK_TOTAL * $proj_scale) ?></td>
        </tr>
      <?
      }
      ?>
      <tr bgcolor=<?= $footer_bg ?>>
        <td align="right" colspan="3"><font <?= $footer_font ?>>Total</font></td>
        <td align="right"><font <?= $footer_font ?>><?= number_style_convert($totalwork * $proj_scale) ?></td>
      </tr>
    </table>
    <hr>
    <a href="/participant/pjointeam.php?team=<?=$team?>">I want to join this team!</a>
    <hr>
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
