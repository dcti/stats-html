<?
// $Id: tlist.php,v 1.20 2004/07/01 10:26:01 fiddles Exp $

# vi: ts=2 sw=2 tw=120 syntax=php

// Variables Passed in url:
//   low == lowest rank used
//   limit == how many lines to return
//   source == "y" for yesterday, all other values ignored.

include "../etc/limit.inc";  // Handles low, high, limit calculations
include "../etc/config.inc";
include "../etc/modules.inc";
include "../etc/project.inc";
include "../etc/team.php";
include "../etc/teamstats.php";

if ("$source" == "y") {
  $title = "Team Listing by Yesterday's Rank: $lo to $hi";
} else {
  $source = "o";
  $title = "Team Listing by Overall Rank: $lo to $hi";
}

$lastupdate = last_update('t');

include "../templates/header.inc";

// Get the results
#$team = new Team($gdb, $gproj);
$result =& Team::get_ranked_list($source, $lo, $limit, $rows, $gdb, $gproj);

// Figure out what navagation buttons we should have
if ( $lo > $rows ) {
 $btn_back = "<a href=\"$myname?project_id=$project_id&amp;low=$prev_lo&amp;limit=$limit&amp;source=$source\">Back $limit</a>";
} else if ( $lo > 1 and $lo < $limit ) {
 $btn_back = "<a href=\"$myname?project_id=$project_id&amp;low=1&amp;limit=$limit&amp;source=$source\">Back " . ($lo-1) ."</a>";
} else {
 $btn_back = "&nbsp;";
}

if ( $rows >= $limit ) {
 $btn_fwd = "<a href=\"$myname?project_id=$project_id&amp;low=$next_lo&amp;limit=$limit&amp;source=$source\">Next $limit</a>";
} else {
 $btn_fwd = "&nbsp;";
}

?>
  <div><br></div>
  <table border="1" cellspacing="0" cellpadding="1" width="100%" class="tborder">
    <tr>
       <td class="tfoot"><?=$btn_back?></td>
       <td colspan="6" class="tfoot">&nbsp;</td>
       <td align="right" class="tfoot"><?=$btn_fwd?></td>
    </tr>
    <tr>
      <td align="center" class="thead">Rank</td>
      <td align="center" class="thead">Team</td>
      <td align="center" class="thead">First Unit</td>
      <td align="center" class="thead">Last Unit</td>
      <td align="right" class="thead">Days</td>
      <td align="right" class="thead">Current Members</td>
      <td align="right" class="thead"><?=$gproj->get_scaled_unit_name()?> Overall</td>
      <td align="right" class="thead"><?=$gproj->get_scaled_unit_name()?> Yesterday</td>
    </tr>
    <?
    $totalblocks=0;
    $totalblocksy=0;

    $cnt = count($result);
    for ($i = 0; $i < $cnt; $i++) {
      $teamTmp =& $result[$i];
      $statsTmp =& $teamTmp->get_current_stats();

      $row_bgnd_color = row_background_color($i);

      $totalblocks += (double) $statsTmp->get_stats_item('work_total') * $gproj->get_scale();
      $totalblocksy += (double) $statsTmp->get_stats_item('work_today') * $gproj->get_scale();
      $decimal_places=0;
      $first = $statsTmp->get_stats_item('first_date');
      $last = $statsTmp->get_stats_item('last_date');

      $teamid = $teamTmp->get_id();
      ?>
      <tr class="<?=$row_bgnd_color?>">
        <td><?=$statsTmp->get_stats_item('rank')?><?=html_rank_arrow($statsTmp->get_stats_item('rank_change'))?></td>
        <td><a href="tmsummary.php?project_id=<?=$project_id?>&amp;team=<?=$teamid?>"><?=$teamTmp->get_name()?></a></td>
        <td align="right"><?=$first?></td>
        <td align="right"><?=$last?></td>
        <td align="right"><?=number_format($statsTmp->get_stats_item('days_working'), 0)?></td>
        <td align="right"><?=number_format($statsTmp->get_stats_item('members_current'), 0)?></td>
        <td align="right"><?=number_format( (double) $statsTmp->get_stats_item('work_total') * $gproj->get_scale(), 0)?></td>
        <td align="right"><?=number_format( (double) $statsTmp->get_stats_item('work_today') * $gproj->get_scale(), 0)?></td>
      </tr>
      <?
      unset($teamTmp);
      unset($statsTmp);
    }
    ?>
    <tr>
      <td class="tfoot"><? echo "$lo-$hi"?></td>
      <td align="right" colspan="5" class="tfoot">Total</td>
      <td align="right" class="tfoot"><?=number_format($totalblocks)?></td>
      <td align="right" class="tfoot"><?=number_format($totalblocksy)?></td>
    </tr>
    <tr>
      <td class="tfoot"><?=$btn_back?></td>
      <td colspan="6" class="tfoot">&nbsp;</td>
      <td align="right" class="tfoot"><?=$btn_fwd?></td>
    </tr>
  </table>
<? include "../templates/footer.inc";?>
