<?
# vi: ts=2 sw=2 tw=120 syntax=php
# $Id: psummary.php,v 1.34.2.3 2003/03/10 15:19:49 decibel Exp $

// Variables Passed in url:
//   id == Participant ID

include "../etc/config.inc";
include "../etc/modules.inc";
include "../etc/project.inc";
include "../etc/markup.inc";

function par_list($i, $par, $totaltoday, $totaltotal, $proj_scale, $color_a = "", $color_b = "") {
  global $project_id;
  $parid = 0+$par->id;
  $totaltoday += $par->TODAY;
  $totaltotal += $par->TOTAL;
  $decimal_places=0;
  $participant = participant_listas($par->listmode,$par->email,$parid,$par->contact_name);
  ?>
    <tr class=<?echo row_background_color($i, $color_a, $color_b);?>>
      <td><?echo $par->OVERALL_RANK . html_rank_arrow($par->Overall_Change) ?></td> 
      <td><a href="psummary.php?project_id=<?=$project_id?>&id=<?=$parid?>"><font color="#cc0000"><?=$participant?></font></a></td>
      <td align="right"><?echo number_style_convert( $par->Days_Working );?> </td>
      <td align="right"><?echo number_style_convert( $par->TOTAL * $proj_scale) ?> </td>
      <td align="right"><?echo number_style_convert( $par->TODAY * $proj_scale) ?> </td>
    </tr>
  <?
}
function par_footer($footer_font, $totaltoday, $totaltotal, $proj_scale) {
?>
  <tr>
    <td align="right" colspan="3"><font <?=$footer_font?>>Total</font></td>
    <td align="right"><font <?=$footer_font?>><?echo number_style_convert( $totaltotal * $proj_scale )?> </font></td>
    <td align="right"><font <?=$footer_font?>><?echo number_style_convert( $totaltoday * $proj_scale )?> </font></td>
  </tr>
<?
}

// Get the participant's record from STATS_Participant and store it in $person

//$qs = "p_participant_all $id";
$qs = "select retire_to,listmode,email,contact_name,motto,friend_a,friend_b,friend_c,friend_d,friend_e
        from STATS_Participant
        where id = $id and listmode < 10";
sybase_query("set rowcount 0");
$result = sybase_query($qs);
sybase_data_seek($result,0);
$person = sybase_fetch_object($result);
debug_text("<!-- STATS_Participant returned: '$person' -->\n", $debug);
err_check_query_results($person);

####
# Is this person retired?
$retire_to = 0+$person->retire_to;
if( $retire_to > 0 ) {
  header("Location: psummary.php?project_id=$project_id&id=$retire_to");
  exit();
}

####
# Find out how to list this participant's name
$participant = participant_listas($person->listmode,$person->email,$id,$person->contact_name);

$title = "Participant Summary for $participant";

if($person->motto <> "") {
   $motto="<font size=\"+1\"><i>".markup_to_html($person->motto)."</i></font><hr>";
}


$lastupdate = last_update('e');

include "../templates/header.inc";

// Get the participant's ranking info, store in $rs_rank

$qs = "select DAY_RANK, OVERALL_RANK, datediff(day, FIRST_DATE, LAST_DATE)+1 as Days_Working,
          WORK_TODAY as TODAY,
          WORK_TOTAL as TOTAL,
          OVERALL_RANK_PREVIOUS-OVERALL_RANK as Overall_Change,
          DAY_RANK_PREVIOUS-DAY_RANK as Day_Change
        from Email_Rank
        where id = $id
          and PROJECT_ID = $project_id";
sybase_query("set rowcount 0");
$result = sybase_query($qs);
sybase_data_seek($result,0);
$rs_rank = sybase_fetch_object($result);
debug_text("<!-- Participant ranking -- qs: $qs, result: $result, rs_rank: $rs_rank -->\n", $debug);

// Grab the participant's neighbors and store in $neighbors (number of neighbors in $numneighbors)

$qs = "select r.id, p.listmode, p.email, p.contact_name, r.OVERALL_RANK,
          datediff(day, r.FIRST_DATE, r.LAST_DATE)+1 as Days_Working,
          WORK_TODAY as TODAY,
          WORK_TOTAL as TOTAL,
          (r.OVERALL_RANK_PREVIOUS-r.OVERALL_RANK) as Overall_Change,
          (r.DAY_RANK_PREVIOUS-r.DAY_RANK) as Day_Change
        from STATS_Participant p, Email_Rank r
        where p.id = r.id 
          and PROJECT_ID = $project_id
          and (r.OVERALL_RANK < ($rs_rank->OVERALL_RANK+5))
          and (r.OVERALL_RANK > ($rs_rank->OVERALL_RANK-5))
        order by r.OVERALL_RANK";
sybase_query("set rowcount 18");
$neighbors = sybase_query($qs);
$numneighbors = sybase_num_rows($neighbors);
debug_text("<!-- Participant neighbors -- qs: $qs, neighbors: $neighbors, numneighbors: $numneighbors -->\n", $debug);

// Grab the participant's list of friends, store in $friends (number of friends in $numfriends)

$qs = "select r.*, p.*, datediff(day, r.FIRST_DATE, r.LAST_DATE)+1 as Days_Working,
          WORK_TODAY as TODAY,
          WORK_TOTAL as TOTAL,
          (r.OVERALL_RANK_PREVIOUS-r.OVERALL_RANK) as Overall_Change
        from STATS_Participant p, Email_Rank r
        where (r.id = $person->friend_a or
               r.id = $person->friend_b or
               r.id = $person->friend_c or
               r.id = $person->friend_d or
               r.id = $person->friend_e or
               r.id = $id                 )
          and p.id = r.id
          and PROJECT_ID = $project_id
        order by r.OVERALL_RANK";
sybase_query("set rowcount 0");
$friends = sybase_query($qs);
$numfriends = sybase_num_rows($friends);
debug_text("<!-- Participant friends -- qs: $qs, friends: $friends, numfriends: $numfriends -->\n", $debug);

// Get the participant's best day, store result in $best_day
/* removed for now - killing sybase
$qs = "p_phistory @project_id = $project_id, @id = $id, @sort_field = 'WORK_UNITS', @sort_dir = 'desc'";
sybase_query("set rowcount 0");
$result = sybase_query($qs);
$best_day = sybase_fetch_object($result);
$best_day_units = (double) $best_day->WORK_UNITS;
debug_text("<!-- Best Day -- qs: $qs, best_day: $best_day, best_day_units: $best_day_units -->\n", $debug);
$best_rate = number_format((($best_day_units*$constant_keys_in_one_block)/(86400))/1000,0);
*/
// Get the latest record from Daily_Summary, store in $yest_totals

$qs = "select *
        from Daily_Summary nolock
        where PROJECT_ID = $project_id
          and DATE = (select max(DATE) from Daily_Summary where project_id = $project_id)";
sybase_query("set rowcount 0");
$result = sybase_query($qs);
$yest_totals = sybase_fetch_object($result);

?>
  <center>
    <table>
      <tr>
        <td colspan="3">
          <br>
          <font size="+2"><center><strong><?=$participant?>'s stats</strong></center></font>
          <hr>
          <? if(isset($motto)) {echo $motto;}?>
      </td>
      </tr>
      <tr>
        <td></td>
        <td align="center"><font <?=$fontd?> size="+1">Overall</font></td>
        <td align="center"><font <?=$fontd?> size="+1">Yesterday</font></td>
      </tr>
      <tr>
        <td align="left"><font <?=$fontd?> size="+1">Rank:</font></td>
        <td align="right" size="+2">
          <font <?=$fontf?>>
            <?echo $rs_rank->OVERALL_RANK.  html_rank_arrow($rs_rank->Overall_Change); ?>
          </font>
        </td>
        <td align="right" size="+2">
          <font <?=$fontf?>><? echo $rs_rank->DAY_RANK.  html_rank_arrow($rs_rank->Day_Change);?> </font>
        </td>
      </tr>
      <tr>
        <td align="left"><font <?=$fontd?> size="+1"><?=$proj_scaled_unit_name?>:</font></td>
        <td align="right" size="+2"><font <?=$fontf?>><?=number_style_convert($rs_rank->TOTAL * $proj_scale);?></font></td>
        <td align="right" size="+2"><font <?=$fontf?>><? echo number_style_convert($rs_rank->TODAY * $proj_scale);?></font></td>
      </tr>
      <tr>
        <td align="left"><font <?=$fontd?> size="+1"><?=$proj_scaled_unit_name?>/sec:</font></td>
        <td align="right" size="+2">
          <font <?=$fontf?>><?=number_style_convert($rs_rank->TOTAL * $proj_scale / (86400 * $rs_rank->Days_Working), 3);?></font>
        </td>
        <td align="right" size="+2">
          <font <?=$fontf?>><? echo number_style_convert($rs_rank->TODAY * $proj_scale / 86400, 3);?></font>
        </td>
      </tr>
      <tr>
        <td align="left"><font <?=$fontd?> size="+1"><?=$proj_unscaled_unit_name?>:</font></td>
        <td align="right" size="+2"><font <?=$fontf?>><?=number_style_convert($rs_rank->TOTAL);?></font></td>
        <td align="right" size="+2"><font <?=$fontf?>><? echo number_style_convert($rs_rank->TODAY);?></font></td>
      </tr>
      <tr>
        <td align="left"><font <?=$fontd?> size="+1"><?=$proj_unscaled_unit_name?>/sec:</font></td>
        <td align="right" size="+2">
          <font <?=$fontf?>><?=number_style_convert($rs_rank->TOTAL / (86400 * $rs_rank->Days_Working), 0);?></font>
        </td>
        <td align="right" size="+2">
          <font <?=$fontf?>><? echo number_style_convert($rs_rank->TODAY / 86400, 0);?></font>
        </td>
      </tr>
      <tr>
        <td><font <?=$fontd?> size="+1">Time Working:</font></td>
        <td colspan="2" align="right" size="+2">
          <font <?=$fontf?>>
            <? echo number_format($rs_rank->Days_Working) . " day" . plural($rs_rank->Days_Working); ?>
          </font>
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
  $pct_of_best = (double) $rs_rank->TODAY * $proj_scale / $best_day_units;
  if($pct_of_best == 1) {
?>
  <br>
  <font color="red">Yesterday was this participant's best day ever!</font>
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
    <a href="phistory.php?project_id=<?=$project_id?>&id=<?=$id?>">View this Participant's Work Unit Submission History</a>
    </p>
    <?
    if (($proj_type == 'RC5' or $proj_type == 'R72') && ($rs_rank->TODAY > 0)) {
      $odds = number_format($yest_totals->WORK_UNITS/$rs_rank->TODAY);
      ?>
        <p>
        The odds are 1 in <?=$odds?> that this participant will find the key before anyone else does.
        </p>
      <?
    }
    ?>

    <table border="1" cellspacing="0" bgcolor=<?=$header_bg?>>
      <tr>
        <th colspan="6" align="center"><font <?=$header_font?>><strong><?=$participant?>'s neighbors</strong></font></th>
      </tr>
      <!-- This header html should be put in a function or something since it's duplicated here and 20 lines down -->
      <tr>
        <th><font <?=$header_font;?>>Rank</font></td>
        <th><font <?=$header_font;?>>Participant</font></td>
        <th align="right"><font <?=$header_font;?>>Days</font></td>
        <th align="right"><font <?=$header_font;?>>Overall <?=$proj_scaled_unit_name?></font></td>
        <th align="right"><font <?=$header_font;?>>Current <?=$proj_scaled_unit_name?></font></td>
      </tr>
      <?
      $totaltoday = 0;
      $totaltotal = 0;
      for ($i = 0; $i < $numneighbors; $i++) {
        sybase_data_seek($neighbors,$i);
        $par = sybase_fetch_object($neighbors);
        if($id<>$par->id) {
          par_list($i,$par,&$totaltoday,&$totaltotal, $proj_scale);
        } else {
          par_list($i,$par,&$totaltoday,&$totaltotal, $proj_scale, "row3","row3");
        }
      }
      par_footer($footer_font,$totaltoday,$totaltotal, $proj_scale);
      if($numfriends>1) {
      ?>
      <tr>
        <td colspan="6" align="center"><font <?=$header_font?>><strong><?=$participant?>'s friends</strong></font></td>
      </tr>
      <!-- This header html should be put in a function or something since it's duplicated here and 20 lines down -->
      <tr>
        <th><font <?=$header_font;?>>Rank</font></td>
        <th><font <?=$header_font;?>>Participant</font></td>
        <th align="right"><font <?=$header_font;?>>Days</font></td>
        <th align="right"><font <?=$header_font;?>>Overall <?=$proj_scaled_unit_name?></font></td>
        <th align="right"><font <?=$header_font;?>>Current <?=$proj_scaled_unit_name?></font></td>
      </tr>
      <?
      $totaltoday = 0;
      $totaltotal = 0;
      for ($i = 0; $i < $numfriends; $i++) {
        sybase_data_seek($friends,$i);
        $par = sybase_fetch_object($friends);
        if($id<>$par->id) {
          par_list($i,$par,&$totaltoday,&$totaltotal, $proj_scale);
        } else {
          par_list($i,$par,&$totaltoday,&$totaltotal, $proj_scale, "row3","row3");
        }
      }
      par_footer($footer_font,$totaltoday,$totaltotal, $proj_scale);
      }
      ?>
    </table>
    <br>
    <hr>
    <p>
    <form action="ppass.php"><input type="hidden" name="id" value="<?=$id?>"><input type="submit" value="Please email me my password."></form>
    </p>
  </center>
<?include "../templates/footer.inc";?>
