<?
// vi: ts=2 sw=2 tw=120 syntax=php
// $Id: psummary.php,v 1.48 2003/08/01 23:51:51 paul Exp $
// Variables Passed in url:
// id == Participant ID
include "../etc/config.inc";
include "../etc/modules.inc";
include "../etc/project.inc";
include "../etc/markup.inc";
include "../etc/participant.php";
include "../etc/participantstats.php";

function par_list($i, $par, $totaltoday, $totaltotal, $proj_scale, $color_a = "", $color_b = "")
{
    global $project_id;
    $parid = 0 + $par -> id;
    $totaltoday += $par -> today;
    $totaltotal += $par -> total;
    $decimal_places = 0;
    $participant = participant_listas($par -> listmode, $par -> email, $parid, $par -> contact_name);

    ?>
    <tr class=<?echo row_background_color($i, $color_a, $color_b);

    ?>>
      <td><?echo $par -> overall_rank . html_rank_arrow($par -> overall_change) ?></td>
      <td><a href="psummary.php?project_id=<?=$project_id?>&amp;id=<?=$parid?>"><?=$participant?></a></td>
      <td align="right"><?echo number_style_convert($par -> days_working);

    ?> </td>
      <td align="right"><?echo number_style_convert($par -> total * $proj_scale) ?> </td>
      <td align="right"><?echo number_style_convert($par -> today * $proj_scale) ?> </td>
    </tr>
  <?
} 
function par_footer($totaltoday, $totaltotal, $proj_scale)
{

    ?>
  <tr>
    <td align="right" colspan="3">Total</td>
    <td align="right"><?echo number_style_convert($totaltotal * $proj_scale)?></td>
    <td align="right"><?echo number_style_convert($totaltoday * $proj_scale)?></td>
  </tr>
<?
} 
// Get the participant's record from STATS_Participant and store it in $person
$gpart = new Participant($gdb, $project_id, $id);
$gpartstats = new ParticipantStats($gdb, $id, $project_id, null);
// ###
// Is this person retired?
if($gpart -> get_retire_to() > 0) {
    header("Location: psummary.php?project_id=$project_id&amp;id=".$gpart -> get_retire_to());
    exit();
} 

$title = "Participant Summary for " . $gpart -> get_display_name();

if($gpart -> get_motto() <> "") {
    $motto = "<i>" . markup_to_html($gpart -> get_motto()) . "</i><hr>";
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
    <table>
      <tr>
        <td colspan="3">
          <br>
          <strong><?=$gpart -> get_display_name()?>'s stats</strong>
          <hr>
          <? if(isset($motto)) { echo $motto; } ?>
      </td>
      </tr>
      <tr>
        <td></td>
        <td align="center">Overall</td>
        <td align="center">Yesterday</td>
      </tr>
      <tr>
        <td align="left">Rank:</td>
        <td align="right">
            <?echo $gpartstats -> get_stats_item('overall_rank') . html_rank_arrow($gpartstats -> get_stats_item('overall_change'));

?>
        </td>
        <td align="right">
          <? echo $gpartstats -> get_stats_item('day_rank') . html_rank_arrow($gpartstats -> get_stats_item('day_change'));

?>
        </td>
      </tr>
      <tr>
        <td align="left"><?=$gproj -> get_scaled_unit_name()?>:</td>
        <td align="right"><?=number_style_convert($gpartstats -> get_stats_item('total') * $gproj -> get_scale()) ?></td>
        <td align="right"><? echo number_style_convert($gpartstats -> get_stats_item('today') * $gproj -> get_scale()); ?></td>
      </tr>
      <tr>
        <td align="left"><?=$gproj -> get_scaled_unit_name()?>/sec:</td>
        <td align="right">
          <? if ($gpartstats -> get_stats_item('days_working') > 0) {
    number_style_convert($gpartstats -> get_stats_item('total') * $gproj -> get_scale() / (86400 * $gpartstats -> get_stats_item('days_working')), 3);
} 

?>
        </td>
        <td align="right">
          <? echo number_style_convert($gpartstats -> get_stats_item('today') * $gproj -> get_scale() / 86400, 3);

?>
        </td>
      </tr>
      <tr>
        <td align="left"><?=$gproj -> get_unscaled_unit_name()?>:</td>
        <td align="right"><?=number_style_convert($gpartstats -> get_stats_item('total'));

?></td>
        <td align="right"><? echo number_style_convert($gpartstats -> get_stats_item('today'));

?></td>
      </tr>
      <tr>
        <td align="left"><?=$gproj -> get_unscaled_unit_name()?>/sec:</td>
        <td align="right">
          <? if ($gpartstats -> get_stats_item('days_working') > 0) {
    number_style_convert($gpartstats -> get_stats_tem('total') / (86400 * $gpartstats -> get_stats_item('days_working')), 0);
} 

?>
        </td>
        <td align="right">
          <? echo number_style_convert($gpartstats -> get_stats_item('today') / 86400, 0);

?>
        </td>
      </tr>
      <tr>
        <td>Time Working:</td>
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
<? if ($proj_totalunits > 0 ) { ?>
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
if (($gproj -> get_type() == 'RC5' or $gproj -> get_type() == 'R72') && ($gpartstats -> get_stats_item('TODAY') > 0)) {
    $odds = number_format($yest_totals -> WORK_UNITS / $gpartstats -> get_stats_item('TODAY'));

    ?>
        <p>
        The odds are 1 in <?=$odds?> that this participant will find the key before anyone else does.
        </p>
      <?
} 

?>

    <table border="1" cellspacing="0">
      <tr>
        <th colspan="6" align="center"><strong><?=$gpart -> Get_display_name()?>'s neighbors</strong></th>
      </tr>
      <tr>
        <th>Rank</th>
        <th>Participant</th>
        <th align="right">Days</th>
        <th align="right">Overall <?=$gproj -> get_scaled_unit_name()?></th>
        <th align="right">Current <?=$gproj -> get_scaled_unit_name()?></th>
      </tr>
      <?
$totaltoday = 0;
$totaltotal = 0;
$neighbors = $gpartstats -> getNeighborsObj();
for ($i = 0; $i < count($neighbors); $i++) {
    if($id <> $neighbors[$i] -> id) {
        par_list($i, $neighbors[$i], &$totaltoday, &$totaltotal, $gproj -> get_scale());
    } else {
        par_list($i, $neighbors[$i], &$totaltoday, &$totaltotal, $gproj -> get_scale(), "row3", "row3");
    } 
} 
par_footer($totaltoday, $totaltotal, $gproj -> get_scale());
if($numfriends > 1) {

    ?>
      <tr>
        <th colspan="6" align="center"><strong><?=$gpart -> GetDisplayName()?>'s friends</strong></th>
      </tr>
      <tr>
        <th>Rank</th>
        <th>Participant</th>
        <th align="right">Days</th>
        <th align="right">Overall <?=$gproj -> get_scaled_unit_name()?></th>
        <th align="right">Current <?=$gproj -> get_scaled_unit_name()?></th>
      </tr>
      <?
    $totaltoday = 0;
    $totaltotal = 0;
    for ($i = 0; $i < $numfriends; $i++) {
        sybase_data_seek($friends, $i);
        $par = sybase_fetch_object($friends);
        if($id <> $par -> id) {
            par_list($i, $par, &$totaltoday, &$totaltotal, $gproj -> get_scale());
        } else {
            par_list($i, $par, &$totaltoday, &$totaltotal, $gproj -> get_scale(), "row3", "row3");
        } 
    } 
    par_footer($footer_font, $totaltoday, $totaltotal, $gproj -> get_scale());
} 

?>
    </table>
    <hr>
    <p>
    <form action="ppass.php">
		<div>
			<input type="hidden" name="id" value="<?=$id?>">
			<input type="submit" value="Please email me my password.">
		</div>
	</form>
    </p>
<?include "../templates/footer.inc";

?>
