<?
// vi: ts=2 sw=2 tw=120 syntax=php
// $Id: plist.php,v 1.28 2004/07/01 10:26:01 fiddles Exp $
// Variables Passed in url:
// low == lowest rank used
// limit == how many lines to retuwn
// source == "y" for yseterday, all other values ignored.
include "../etc/limit.inc"; // Handles low, high, limit calculations

include "../etc/config.inc";
include "../etc/modules.inc";
include "../etc/project.inc";
include "../etc/participant.php";

if ("$source" == "y") {
    $title = "Participant Listing by Yesterday's Rank: $lo to $hi";
} else {
    $source = "o";
    $title = "Participant Listing by Overall Rank: $lo to $hi";
}
$lastupdate = last_update('e');
include "../templates/header.inc";

?>
     <div><br></div>
      <table border="1" cellspacing="0" cellpadding="1" width="100%" class="tborder">
      <tr>
       <th class="thead">Rank</th>
       <th class="thead">Participant</th>
       <th class="thead" align="right">First Unit</th>
       <th class="thead" align="right">Last Unit</th>
       <th class="thead" align="right">Days</th>
       <th class="thead" align="right"><?=$gproj->get_scaled_unit_name()?></th>
      </tr>
<?

$totalrows = 0;
$plist = Participant::get_ranked_list($source, $lo, $limit, $totalrows, $gdb, $gproj);
$totalblocks = (double) 0;
$i = 0;
if ($plist) {
	foreach ($plist as $par) {
		$statspar =& $par->get_current_stats();
	    $totalblocks = $totalblocks + (double) $statspar -> get_stats_item('blocks') * $gproj->get_scale();
	    ?>
		<tr class="<?=row_background_color($i)?>">
			<td><?=$statspar -> get_stats_item('rank')?><?=html_rank_arrow($statspar -> get_stats_item('change')) ?></td>
			<td><a href="psummary.php?project_id=<?=$project_id?>&amp;id=<?=$par -> get_id() ?>"><?=$par -> get_display_name() ?></a></td>
			<td align="right"><?=$statspar -> get_stats_item('first_date') ?></td>
			<td align="right"><?=$statspar -> get_stats_item('last_date') ?></td>
			<td align="right"><?=$statspar -> get_stats_item('days_working')?></td>
			<td align="right"><?=number_style_convert((double) $statspar -> get_stats_item('blocks') * $gproj->get_scale()) ?></td>
		</tr>
	 <?
	    $i++;
	}
} else {
  	trigger_error("Unable to get Participant List",E_USER_ERROR);
}
$totalblocks = number_format($totalblocks, 0);
if ($lo > sizeof($plist)) {
    $btn_back = "<a href=\"$myname?project_id=$project_id&amp;low=$prev_lo&amp;limit=$limit&amp;source=$source\">Back $limit</a>";
} else {
    $btn_back = "&nbsp;";
}

if (sizeof($plist) >= $limit) {
    $btn_fwd = "<a href=\"$myname?project_id=$project_id&amp;low=$next_lo&amp;limit=$limit&amp;source=$source\">Next $limit</a>";
} else {
    $btn_fwd = "&nbsp;";
}
?>
	 <tr>
	  <td class="tfoot"><? echo "$lo-$hi"?></td>
	  <td class="tfoot" align="right" colspan="4">Total</td>
	  <td class="tfoot" align="right"><?=$totalblocks?></td>
	 </tr>
	 <tr>
	  <td class="tfoot"><?=$btn_back?></td>
	  <td class="tfoot" colspan="4">&nbsp;</td>
	  <td class="tfoot" align="right"><?=$btn_fwd?></td>
	 </tr>
	</table>
<? include "../templates/footer.inc"; ?>
