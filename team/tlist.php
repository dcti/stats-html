<?
# vi: ts=2 sw=2 tw=120 syntax=php

// Variables Passed in url:
//   low == lowest rank used
//   limit == how many lines to return
//   source == "y" for yesterday, all other values ignored.

include "../etc/limit.inc";  // Handles low, high, limit calculations
include "../etc/config.inc";
include "../etc/modules.inc";
include "../etc/project.inc";

if ("$source" == "y") {
  $title = "Team Listing by Yesterday's Rank: $lo to $hi";
  $qs = "select  tr.TEAM_ID, name, FIRST_DATE, LAST_DATE,
            WORK_TOTAL as WORK_TOTAL, WORK_TODAY as WORK_TODAY,
            MEMBERS_CURRENT, DAY_RANK as Rank,
            datediff(day, FIRST_DATE, LAST_DATE)+1 as Days_Working,
            DAY_RANK_PREVIOUS-DAY_RANK as Change
          from  STATS_Team st, Team_Rank tr
          where  st.team = tr.TEAM_ID
            and st.listmode <= 9
            and DAY_RANK <= $hi
            and DAY_RANK >= $lo
            and tr.PROJECT_ID = $project_id
          order by  DAY_RANK, WORK_TOTAL desc";
} else {
  $source = "o";
  $title = "Team Listing by Overall Rank: $lo to $hi";
  $qs = "select  tr.TEAM_ID, name, FIRST_DATE, LAST_DATE,
            WORK_TOTAL as WORK_TOTAL, WORK_TODAY as WORK_TODAY,
            MEMBERS_CURRENT, OVERALL_RANK as Rank,
            datediff(day, FIRST_DATE, LAST_DATE)+1 as Days_Working,
            OVERALL_RANK_PREVIOUS-OVERALL_RANK as Change
          from  STATS_Team st, Team_Rank tr
          where  st.team = tr.TEAM_ID
            and st.listmode <= 9
            and OVERALL_RANK <= $hi
            and OVERALL_RANK >= $lo
            and tr.PROJECT_ID = $project_id
          order by  OVERALL_RANK, WORK_TOTAL desc";
}

$lastupdate = last_update('t');

include "../templates/header.inc";

// Get the results
sybase_query("set rowcount $limit");
$result = sybase_query($qs);

err_check_query_results($result);

$rows = sybase_num_rows($result);

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
       <td class="thead"><?=$btn_back?></td>
       <td colspan="6" class="thead">&nbsp;</td>
       <td align="right" class="thead"><?=$btn_fwd?></td>
    </tr>
    <tr>
      <td align="center" class="thead">Rank</td>
      <td align="center" class="thead">Team</td>
      <td align="center" class="thead">First Unit</td>
      <td align="center" class="thead">Last Unit</td>
      <td align="right" class="thead">Days</td>
      <td align="right" class="thead">Current Members</td>
      <td align="right" class="thead"><?=$proj_scaled_unit_name?> Overall</td>
      <td align="right" class="thead"><?=$proj_scaled_unit_name?> Yesterday</td>
    </tr>
    <?
    $totalblocks=0;
    $totalblocksy=0;  

    for ($i = 0; $i<$rows; $i++) {
      sybase_data_seek($result,$i);
      $par = sybase_fetch_object($result);

      $row_bgnd_color = row_background_color($i);

      $totalblocks += (double) $par->WORK_TOTAL * $proj_scale;
      $totalblocksy += (double) $par->WORK_TODAY * $proj_scale;
      $decimal_places=0;
      $first = sybase_date_format_long($par->FIRST_DATE);
      $last = sybase_date_format_long($par->LAST_DATE);

      $teamid=0+$par->TEAM_ID;
      ?>
      <tr class="<?=$row_bgnd_color?>">
        <td><?=$par->Rank?><?=html_rank_arrow($par->Change)?></td>
        <td><a href="tmsummary.php?project_id=<?=$project_id?>&amp;team=<?=$teamid?>"><?=$par->name?></a></td>
        <td align="right"><?=$first?></td>
        <td align="right"><?=$last?></td>
        <td align="right"><?=number_format($par->Days_Working, 0)?></td>
        <td align="right"><?=number_format($par->MEMBERS_CURRENT, 0)?></td>
        <td align="right"><?=number_format( (double) $par->WORK_TOTAL * $proj_scale, 0)?></td>
        <td align="right"><?=number_format( (double) $par->WORK_TODAY * $proj_scale, 0)?></td>
      </tr>
      <?
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
