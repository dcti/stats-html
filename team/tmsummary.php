<?
// vi: ts=2 sw=2 tw=120
// $Id: tmsummary.php,v 1.23 2003/03/12 21:37:22 thejet Exp $

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
  $logo = "<img src=\"$par->logo\" alt=\"team logo\">";
} else {
  $logo = "";
}
?>
<h1 class="phead"><center><?= safe_display($par->name) ?></center></h1>
<center>
  <table>
    <tr>
      <td><?= $logo ?></td>
      <td align="center"><?= markup_to_html($par->description) ?></td>
    </tr>
    <tr>
      <td colspan="2" align="center">
        Contact: <?= safe_display($par->contactemail) ?>
      </td>
    </tr>
  </table>
  <br>
  <br>
  <table cellspacing="4">
    <tr>
      <td></td>
      <td align="center" class="phead2">Overall</td>
<? if ($par->WORK_TODAY > 0) { ?>
      <td align="center" class="phead2">Yesterday</td>
<? } ?>
    </tr>
    <tr>
      <td align="left" class="phead2">Rank:</td>
      <td align="right"><?= $par->OVERALL_RANK . " " . html_rank_arrow($par->Overall_Change) ?></td>
<? if ($par->WORK_TODAY > 0) { ?>
      <td align="right"><?= $par->DAY_RANK . " " . html_rank_arrow($par->Day_Change) ?></td>
<? } ?>
    </tr>
    <tr>
      <td align="left" class="phead2"><?= $proj_scaled_unit_name ?>:</td>
      <td align="right"><?= number_style_convert($par->WORK_TOTAL * $proj_scale) ?></td>
<? if ($par->WORK_TODAY > 0) { ?>
      <td align="right"><?= number_style_convert($par->WORK_TODAY * $proj_scale) ?></td>
<? } ?>
    </tr>
    <? if ($par->Days_Working > 0) { ?>
    <tr>
      <td align="left" class="phead2"><?= $proj_scaled_unit_name ?>/sec:</td>
      <td align="right"><?= number_style_convert($par->WORK_TOTAL * $proj_scale / (86400 * $par->Days_Working), 3) ?></td>
<? if ($par->WORK_TODAY > 0) { ?>
      <td align="right"><?= number_style_convert($par->WORK_TODAY * $proj_scale / 86400, 3) ?></td>
    <? } ?>
    </tr>
<? } ?>
    <?if($par->MEMBERS_OVERALL > 0) {?>
    <tr>
      <td align="left" class="phead2"><?= $proj_scaled_unit_name ?>/member:</td>
      <td align="right"><?= number_style_convert($par->WORK_TOTAL * $proj_scale / $par->MEMBERS_OVERALL) ?></td>
<? if ($par->WORK_TODAY > 0) { ?>
      <td align="right"><?= number_style_convert($par->WORK_TODAY * $proj_scale / $par->MEMBERS_TODAY) ?></td>
<? } ?>
    </tr>
    <?}?>
    <tr>
      <td align="left" class="phead2"><?= $proj_unscaled_unit_name ?>:</td>
      <td align="right"><?= number_style_convert($par->WORK_TOTAL) ?></td>
<? if ($par->WORK_TODAY > 0) { ?>
      <td align="right"><?= number_style_convert($par->WORK_TODAY) ?></td>
<? } ?>
    </tr>
    <?if ($par->Days_Working > 0) { ?>
    <tr>
      <td align="left" class="phead2"><?= $proj_unscaled_unit_name ?>/sec:</td>
      <td align="right"><?= number_style_convert($par->WORK_TOTAL / (86400 * $par->Days_Working)) ?></td>
<? if ($par->WORK_TODAY > 0) { ?>
      <td align="right"><?= number_style_convert($par->WORK_TODAY / 86400) ?></td>
<? } ?>
    </tr>
    <?}?>
    <?if($par->MEMBERS_OVERALL > 0) {?>
    <tr>
      <td align="left" class="phead2"><?= $proj_unscaled_unit_name ?>/member:</td>
      <td align="right"><?= number_style_convert($par->WORK_TOTAL / $par->MEMBERS_OVERALL) ?></td>
<? if ($par->WORK_TODAY > 0) { ?>
      <td align="right"><?= number_style_convert($par->WORK_TODAY / $par->MEMBERS_TODAY) ?></td>
<? } ?>
    </tr>
    <?}?>
    <tr>
      <td align="left" class="phead2">Time Working:</td>
      <td align="right" colspan="<?= ($par->WORK_TODAY > 0) ? 3 : 2 ?>"><?= number_style_convert($par->Days_Working) ?> days</td>
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
    <center><p>This team wishes to keep its membership private.<p></center>
  <? } else {  
    if ($par->WORK_TODAY == 0) {
      print "<center><p>Click here to view this team's 
      <a href=\"tmember.php?project_id=$project_id&amp;team=$tm\">overall</a> participant stats";
    } else {
      print "<center><p>Click here to view this team's participant stats for
      <a href=\"tmember.php?project_id=$project_id&amp;team=$tm&amp;source=y\">yesterday</a> or
      <a href=\"tmember.php?project_id=$project_id&amp;team=$tm\">overall</a>";
    }
	
    if ($par->showmembers=="PAS") {
      print " (Password required)";
    }

    print ".</p></center>";
  }

  //A list of teams goes here
  ?> 
	<center>
    <table border="1" cellspacing="0">
      <tr>
        <th class="thead">Rank</th>
        <th class="thead">Team</th>
        <th class="thead" align="right">Days</th>
        <th class="thead" align="right"><?= $proj_scaled_unit_name ?></th>
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
              <a href="tmsummary.php?project_id=<?= $project_id ?>&amp;team=<?= $teamrec->TEAM_ID + 0 ?>"><?= $teamrec->name ?></a>
          </td>
          <td align="right"><?= number_style_convert($teamrec->Days_Working) ?></td>
          <td align="right"><?= number_style_convert($teamrec->WORK_TOTAL * $proj_scale) ?></td>
        </tr>
      <?
      }
      ?>
      <tr>
        <td class="tfoot" align="right" colspan="3">Total</td>
        <td class="tfoot" align="right"><?= number_style_convert($totalwork * $proj_scale) ?></td>
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
    <form action="tmpass.php"><p>
    If you are the team coordinator, and you've forgotten your team password,<br> click
    <input type="hidden" name="team" value="<?=$team?>">
    <input type="submit" value="here"> and the password will be mailed to
    <?=$par->contactemail?>.
    </p></form>
  </center>

<? include "../templates/footer.inc"; ?>
