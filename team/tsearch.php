<?
# vi: ts=2 sw=2 tw=120
# $Id: tsearch.php,v 1.18 2004/07/16 20:45:27 decibel Exp $

// Variables Passed in url:
//   st == Search Term

include "../etc/global.inc";
include "../etc/modules.inc";
include "../etc/project.inc";
include "../etc/team.php";
include "../etc/teamstats.php";

$title = "Team Search: [".safe_display($st)."]";
#$team = new Team($gdb, $gproj);
$result = Team::get_search_list($st, 50, $gdb, $gproj);
$rows = count($result);

if($rows == 1) {
  # Only one hit, let's jump straight to tmsummary
  $team = $result[0];
  $id = (int) $team->get_id();
  header("Location: tmsummary.php?project_id=$project_id&team=$id");
  exit;
}

$lastupdate = last_update('t');

include "../templates/header.inc";

?>
  <div class="phead"><br></div>
  <table border="1" cellspacing="0" width="100%">
    <tr>
      <td class="thead">Rank</td>
      <td class="thead">Team</td>
      <td align="right" class="thead">First Unit</td>
      <td align="right" class="thead">Last Unit</td>
      <td align="right" class="thead">Days</td>
      <td align="right" class="thead">Current Members</td>
      <td align="right" class="thead"><?=$gproj->get_scaled_unit_name()?> Overall</td>
      <td align="right" class="thead"><?=$gproj->get_scaled_unit_name()?> Yesterday</td>
    </tr>
    <? 

    $totalblocks = 0;
    $totalblocksy = 0;
    if($rows <= 0)
    {
      echo "<tr><td colspan=\"8\" align=\"center\">No Matching Records Found</td></tr>\n";
    }
    for ($i = 0; $i < $rows; $i++) {
      $teamTmp =& $result[$i];
      $statsTmp =& $teamTmp->get_current_stats();
      $members = number_format($statsTmp->get_stats_item('members_current'));
      $teamid = $teamTmp->get_id();
      $totalblocks += (double) $statsTmp->get_stats_item('work_total') * $gproj->get_scale();
      $totalblocksy += (double) $statsTmp->get_stats_item('work_today') * $gproj->get_scale();

    ?>
    <tr class="<?=row_background_color($i)?>">
      <td><?= $statsTmp->get_stats_item('overall_rank') . html_rank_arrow($statsTmp->get_stats_item('rank_change'))?></td>
      <td><a href="tmsummary.php?project_id=<?=$project_id?>&team=<?=$teamid?>"><font color="#cc0000"><?=safe_display($teamTmp->get_name())?></font></a></td>
      <td align="right"><?= $statsTmp->get_stats_item('first_date')?></td>
      <td align="right"><?= $statsTmp->get_stats_item('last_date')?></td>
      <td align="right"><?= number_format($statsTmp->get_stats_item('days_working'))?></td>
      <td align="right"><?=$members?></td>
      <td align="right"><?=number_format( (double) $statsTmp->get_stats_item('work_total') * $gproj->get_scale())?> </td>
      <td align="right"><?=number_format( (double) $statsTmp->get_stats_item('work_today') * $gproj->get_scale())?> </td>
    </tr>
    <?
    }
    ?>
    <tr>
      <td class="tfoot"><?=$rows?></td>
      <td class="tfoot" colspan="5" align="right">Total</td>
      <td class="tfoot" align="right"><?=number_format($totalblocks, 0)?></td>
      <td class="tfoot" align="right"><?=number_format($totalblocksy, 0)?></td>
    </tr>
  </table>
<?include "../templates/footer.inc";?>
