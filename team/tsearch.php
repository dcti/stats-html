<?
# vi: ts=2 sw=2 tw=120
# $Id: tsearch.php,v 1.14 2003/04/20 21:37:07 paul Exp $

// Variables Passed in url:
//   st == Search Term

include "../etc/config.inc";
include "../etc/modules.inc";
include "../etc/project.inc";

$title = "Team Search: [".safe_display($st)."]";

$qs = "select tr.TEAM_ID, name, FIRST_DATE, LAST_DATE,
          WORK_TOTAL as WORK_TOTAL, WORK_TODAY as WORK_TODAY,
          MEMBERS_CURRENT, OVERALL_RANK,
          datediff(day, FIRST_DATE, LAST_DATE)+1 as Days_Working,
          OVERALL_RANK_PREVIOUS-OVERALL_RANK as Overall_Change
        from  Team_Rank tr, STATS_team st
        where  (name like '%$st%' or convert(char(10),st.team) like '%$st%')
          and st.team = tr.TEAM_ID
          and listmode <= 9
          and PROJECT_ID = $project_id
        order by  OVERALL_RANK";
sybase_query("set rowcount 50");
$QRSLTteams = sybase_query($qs);

err_check_query_results($QRSLTteams);

$rows = sybase_num_rows($QRSLTteams);
if($rows == 1) {
  # Only one hit, let's jump straight to psummary
  $par = sybase_fetch_object($QRSLTteams);
  $id = (int) $par->TEAM_ID;
  header("Location: tmsummary.php?project_id=$project_id&team=$id");
  exit;
}

$lastupdate = last_update('t');

include "../templates/header.inc";

?>
<center>
  <br>
  <table border="1" cellspacing="0" bgcolor=<?=$header_bg?>>
    <tr>
      <td><font <?=$header_font?>>Rank</font></td>
      <td><font <?=$header_font?>>Team</font></td>
      <td align="right"><font <?=$header_font?>>First Unit</font></td>
      <td align="right"><font <?=$header_font?>>Last Unit</font></td>
      <td align="right"><font <?=$header_font?>>Days</font></td>
      <td align="right"><font <?=$header_font?>>Current Members</font></td>
      <td align="right"><font <?=$header_font?>><?=$proj_scaled_unit_name?> Overall</font></td>
      <td align="right"><font <?=$header_font?>><?=$proj_scaled_unit_name?> Yesterday</font></td>
    </tr>
    <? 

    $totalblocks = 0;
    $totalblocksy = 0;
    for ($i = 0; $i<$rows; $i++) {
      sybase_data_seek($QRSLTteams,$i);
      $par = sybase_fetch_object($QRSLTteams);
      $firstd = substr($par->FIRST_DATE,4,2);
      $firstm = substr($par->FIRST_DATE,0,3);
      $firsty = substr($par->FIRST_DATE,7,4);
      $lastd = substr($par->LAST_DATE,4,2);
      $lastm = substr($par->LAST_DATE,0,3);
      $lasty = substr($par->LAST_DATE,7,4);
      $members = number_format($par->MEMBERS_CURRENT);
      $teamid = 0 + $par->TEAM_ID;
      $totalblocks += (double) $par->WORK_TOTAL * $proj_scale;
      $totalblocksy += (double) $par->WORK_TODAY * $proj_scale;

    ?>
    <tr class="<?=row_background_color($i)?>">
      <td><? echo $par->OVERALL_RANK. html_rank_arrow($par->Overall_Change)?></td>
      <td><a href="tmsummary.php?project_id=<?=$project_id?>&team=<?=$teamid?>"><font color="#cc0000"><?=safe_display($par->name)?></font></a></td>
      <td align="right"><? echo "$firstd-$firstm-$firsty"?></td>
      <td align="right"><? echo "$lastd-$lastm-$lasty"?></td>
      <td align="right"><? echo number_format($par->Days_Working)?></td>
      <td align="right"><?=$members?></td>
      <td align="right"><? echo number_format( (double) $par->WORK_TOTAL * $proj_scale)?> </td>
      <td align="right"><? echo number_format( (double) $par->WORK_TODAY * $proj_scale)?> </td>
    </tr>
    <?
    }
    ?>
    <tr bgcolor=<?=$footer_bg?>>
      <td><font <?=$footer_font?>><?=$rows?></font></td>
      <td colspan="5" align="right"><font <?=$footer_font?>>Total</font></td>
      <td align="right"><font <?=$footer_font?>><? echo number_format($totalblocks, 0)?> </font></td>
      <td align="right"><font <?=$footer_font?>><? echo number_format($totalblocksy, 0)?> </font></td>
    </tr>
  </table>
</center>
<?include "../templates/footer.inc";?>
