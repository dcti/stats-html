<?
// vi: ts=2 sw=2 tw=120 syntax=php
// $Id: psummary.php,v 1.60 2003/09/05 19:29:32 thejet Exp $
// Variables Passed in url:
// id == Participant ID
include "../etc/config.inc";
include "../etc/modules.inc";
include "../etc/project.inc";
include "../etc/projectstats.php";
include "../etc/markup.inc";
include "../etc/participant.php";
include "../etc/participantstats.php";

function par_list($i, $par, $stats, &$totaltoday, &$totaltotal, $proj_scale, $color_a = "", $color_b = "")
{
    global $gproj;
    $parid = 0 + $par->get_id();
    $totaltoday += $stats->get_stats_item("work_today");
    $totaltotal += $stats->get_stats_item("work_total");
    $decimal_places = 0;
    $participant = $par->get_display_name();

    ?>
    <tr class="<?=row_background_color($i, $color_a, $color_b)?>">
      <td align="left"><?echo $stats->get_stats_item("overall_rank") . html_rank_arrow($stats->get_stats_item("overall_change")) ?></td>
      <td align="left"><a href="psummary.php?project_id=<?=$gproj->get_id()?>&amp;id=<?=$parid?>"><?=$participant?></a></td>
      <td align="right"><?echo number_style_convert($stats->get_stats_item("days_working"));

    ?> </td>
      <td align="right"><?echo number_style_convert($stats->get_stats_item("work_total") * $proj_scale) ?> </td>
      <td align="right"><?echo number_style_convert($stats->get_stats_item("work_today") * $proj_scale) ?> </td>
    </tr>
  <?
} 
function par_footer($totaltoday, $totaltotal, $proj_scale)
{
    ?>
  <tr>
    <td class="tfoot" align="right" colspan="3">Total</td>
    <td class="tfoot" align="right"><?echo number_style_convert($totaltotal * $proj_scale)?></td>
    <td class="tfoot" align="right"><?echo number_style_convert($totaltoday * $proj_scale)?></td>
  </tr>
<?
} 
// Get the participant's record from STATS_Participant and store it in $person
$gpart = new Participant($gdb, $gproj, $id);
if($gpart->get_id() == 0) {
	$title = "Participant Summary - Error occured ";
	include "../templates/header.inc";
	include "../templates/error.inc";
	include "../templates/footer.inc";
	exit();
}
$gpartstats = new ParticipantStats($gdb, $gproj, $id, null);
// ###
// Is this person retired?
if($gpart -> get_retire_to() > 0) {
    header("Location: psummary.php?project_id=$project_id&id=".$gpart -> get_retire_to());
    exit();
} 

$title = "Participant Summary for " . $gpart -> get_display_name();

if($gpart -> get_motto() <> "") {
    $motto = "<i>" . markup_to_html($gpart->get_motto()) . "</i><hr>";
} 

$lastupdate = last_update('e');
include "../templates/header.inc";
// Get the participant's best day, store result in $best_day
/* removed for now - killing sybase
$qs = "p_phistory @project_id = $project_id, @id = $id, @sort_field = 'WORK_UNITS', @sort_dir = 'desc'";
sybase_query("set rowcount 0");
$result = sybase_query($qs);
$best_day = sybase_fetch_object($result);
$best_day_units = (double) $best_day->WORK_UNITS;
$best_rate = number_format((($best_day_units*$constant_keys_in_one_block)/(86400))/1000,0);
*/

?>
  <div style="text-align:center">
    <h1 class="phead"><?=$gpart->get_display_name()?>'s stats</h1>
    <table border="0" style="margin: auto">
      <tr>
        <td colspan="3">
          <hr>
          <? if(isset($motto)) { echo $motto; } ?>
      </td>
      </tr>
      <tr>
        <td></td>
        <td class="phead2" align="center">Overall</td>
        <td class="phead2" align="center">Yesterday</td>
      </tr>
      <tr>
        <td align="left" class="phead2">Rank:</td>
        <td align="right">
            <?=$gpartstats->get_stats_item('overall_rank') . html_rank_arrow($gpartstats -> get_stats_item('overall_change'))?>
        </td>
        <td align="right">
          <?=$gpartstats->get_stats_item('day_rank') . html_rank_arrow($gpartstats -> get_stats_item('day_change'))?>
        </td>
      </tr>
      <tr>
        <td align="left" class="phead2"><?=$gproj->get_scaled_unit_name()?>:</td>
        <td align="right"><?=number_style_convert($gpartstats->get_stats_item('work_total') * $gproj->get_scale()) ?></td>
        <td align="right"><?=number_style_convert($gpartstats->get_stats_item('work_today') * $gproj->get_scale())?></td>
      </tr>
      <tr>
        <td align="left" class="phead2"><?=$gproj->get_scaled_unit_name()?>/sec:</td>
        <td align="right">
          <? if ($gpartstats->get_stats_item('days_working') > 0) {
               echo number_style_convert($gpartstats->get_stats_item('work_total') * $gproj->get_scale() / (86400 * $gpartstats->get_stats_item('days_working')), 3);
             } 
           ?>
        </td>
        <td align="right">
          <? echo number_style_convert($gpartstats -> get_stats_item('work_today') * $gproj -> get_scale() / 86400, 3);

?>
        </td>
      </tr>
      <!-- 
      <tr>
        <td align="left"><?=$gproj -> get_unscaled_unit_name()?>:</td>
        <td align="right"><?=number_style_convert($gpartstats -> get_stats_item('work_total')) ?></td>
        <td align="right"><? echo number_style_convert($gpartstats -> get_stats_item('work_today')) ?></td>
      </tr>
      <tr>
        <td align="left"><?=$gproj -> get_unscaled_unit_name()?>/sec:</td>
        <td align="right">
          <? if ($gpartstats->get_stats_item('days_working') > 0) {
    number_style_convert($gpartstats->get_stats_item('work_total') / (86400 * $gpartstats->get_stats_item('days_working')), 0);
} 
?>
        </td>
        <td align="right">
          <? echo number_style_convert($gpartstats -> get_stats_item('work_today') / 86400, 0);

?>
        </td>
      </tr>
      -->
      <tr>
        <td align="left" class="phead2">Time Working:</td>
        <td colspan="2" align="right">
            <? echo number_format($gpartstats -> get_stats_item('days_working')) . " day" . plural($gpartstats -> get_stats_item('days_working'));

?>
        </td>
      </tr>
      <tr>
        <td colspan="3">
          <hr>
        </td>
      </tr>
    </table>
    <p>

<?
/*
  $pct_of_best = (double) $rs_rank->TODAY * $gproj->get_scale() / $best_day_units;
  if($pct_of_best == 1) {
?>
  <br>
  Yesterday was this participant's best day ever!
  </p>
<?
  } elseif ( $best_day_units > 0 ) {
?>
  </p>
  <p>
  This is  <? echo number_format($pct_of_best*100,0)?>  % of this participant's best day ever, which was
  <br>
   <? echo sybase_date_format_long($best_day->DATE)?> when <? echo number_format($best_day->WORK_UNITS,0)?>
   units were completed.
<? if ($gproj->get_total_units() > 0 ) { ?>
were completed at a rate of <?=$best_rate?> Kkeys/sec.
<? } ?>
  </p><!-- Thanks, Havard! -->
<?
  }
*/
?>
    <p>
    <a href="phistory.php?project_id=<?=$project_id?>&amp;id=<?=$id?>">View this Participant's Work Unit Submission History</a>
    </p>
    <?
if (($gproj -> get_type() == 'RC5' or $gproj -> get_type() == 'R72') && ($gpartstats -> get_stats_item('work_today') > 0)) {
    $gprojstats = $gproj->get_current_stats();
    $odds = number_format($gprojstats->get_stats_item('work_units') / $gpartstats -> get_stats_item('work_today'));

    ?>
        <p>
        The odds are 1 in <?=$odds?> that this participant will find the key before anyone else does.
        </p>
      <?
} 

?>

    <table style="margin:auto;" border="1" cellspacing="0">
      <tr>
        <th class="phead2" colspan="6" align="center">Neighbors</th>
      </tr>
      <tr>
        <th class="thead">Rank</th>
        <th class="thead">Participant</th>
        <th class="thead" align="right">Days</th>
        <th class="thead" align="right">Overall <?=$gproj->get_scaled_unit_name()?></th>
        <th class="thead" align="right">Current <?=$gproj->get_scaled_unit_name()?></th>
      </tr>
      <?
$totaltoday = 0;
$totaltotal = 0;
$neighbors = $gpart->get_neighbors();
$numneighbors = count($neighbors);
for ($i = 0; $i < $numneighbors; $i++) {
    if($gpart->get_id() <> $neighbors[$i]->get_id()) {
        par_list($i, $neighbors[$i], $neighbors[$i]->get_current_stats(), $totaltoday, $totaltotal, $gproj->get_scale());
    } else {
        par_list($i, $neighbors[$i], $neighbors[$i]->get_current_stats(), $totaltoday, $totaltotal, $gproj->get_scale(), "row3", "row3");
    } 
} 
par_footer($totaltoday, $totaltotal, $gproj->get_scale());
?>
</table>
<br /><br />
<?
$numfriends = count($gpart->get_friends());
if($numfriends >= 1) {
    ?>
    <table style="margin:auto;" border="1" cellspacing="0">
      <tr>
        <th class="phead2" colspan="6" align="center">Friends</th>
      </tr>
      <tr>
        <th class="thead">Rank</th>
        <th class="thead">Participant</th>
        <th class="thead" align="right">Days</th>
        <th class="thead" align="right">Overall <?=$gproj->get_scaled_unit_name()?></th>
        <th class="thead" align="right">Current <?=$gproj->get_scaled_unit_name()?></th>
      </tr>
      <?
    $totaltoday = 0;
    $totaltotal = 0;
    $printed_self = false;
    for ($i = 0; $i < $numfriends; $i++) {
        $par = $gpart->get_friends($i);
        $stats = $par->get_current_stats();
        if($gpartstats->get_stats_item('work_total') >= $stats->get_stats_item('work_total') && !$printed_self) {
            par_list($i, $gpart, $gpartstats, $totaltoday, $totaltotal, $gproj->get_scale(), "row3", "row3");
            $printed_self = true;
        }
        par_list($i, $par, $stats, $totaltoday, $totaltotal, $gproj->get_scale());
    } 
    par_footer($totaltoday, $totaltotal, $gproj -> get_scale());
    echo("</table>\n");
} 
?>
    <hr>
    <!--
    <p>
    <form action="ppass.php">
		<div>
			<input type="hidden" name="id" value="<?=$id?>">
			<input type="submit" value="Please email me my password.">
		</div>
	</form>
    </p>
    -->
<?include "../templates/footer.inc";

?>
