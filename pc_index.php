<?
  # vi: ts=2 sw=2 tw=120 syntax=php
  # $Id: pc_index.php,v 1.21 2003/09/23 20:50:36 paul Exp $

  $title = "Overall Project Stats";

  include "etc/config.inc";
  include "etc/modules.inc";
  include "etc/project.inc";
  include "etc/projectstats.php";
  
  ####
  # Daily summary
  $gprojstats = $gproj->get_current_stats();

  // @todo - this returns date in wrong format for lastupdate
  $lastupdate = $gprojstats->get_stats_item('date');
  display_last_update('i');

  $yest_scaled_work_units = number_format( (double) $gprojstats->get_stats_item('work_units') * $gproj->get_scale());
  $yest_unscaled_work_units = number_format( (double) $gprojstats->get_stats_item('work_units'));
  $yest_emails = number_format($gprojstats->get_stats_item('participants'));
  $yest_teams = number_format($gprojstats->get_stats_item('teams'));
  $new_emails = number_format($gprojstats->get_stats_item('participants_new'));
  $new_teams = number_format($gprojstats->get_stats_item('teams_new'));

  ####
  # Total work, time working

  $time_working_raw = $gprojstats->get_time_working();
  $time_working = number_format($gprojstats->get_time_working());
  $tot_unscaled_work_units = number_format( (double) $gprojstats->get_tot_units());
  $tot_scaled_work_units = number_format( (double) $gprojstats->get_tot_units() * $gproj->get_scale());

  $total_emails = number_format($gprojstats->get_total_emails());
  $total_teams = number_format($gprojstats->get_total_teams());

  ####
  # Percent complete
  $tot_unscaled_units_to_search = number_style_convert($gproj->get_total_units());
  $tot_scaled_units_to_search = number_style_convert($gproj->get_total_units() * $gproj->get_scale());
  $total_remaining = $gproj->get_total_units() - $gprojstats->get_tot_units();
  if ( $gproj->get_total_units() > 0 ) {
  	$per_searched = number_format(100*($gprojstats->get_tot_units()/$gproj->get_total_units()),3);
  	$bar_width = number_format(300*($gprojstats->get_tot_units()/$gproj->get_total_units()),0);
  }
   
  ####
  # Overall Rate
  $overall_unscaled_rate = number_format(( ($gprojstats->get_tot_units()) / ($time_working_raw*86400) ),0);
  $overall_scaled_rate = number_format(( ($gprojstats->get_tot_units() * $gproj->get_scale()) / ($time_working_raw*86400) ),0);

  ####
  # Yesterday Rate
  $yest_unscaled_work_units = number_format($gprojstats->get_stats_item('work_units'));
  $yest_scaled_work_units = number_format($gprojstats->get_stats_item('work_units') * $gproj->get_scale());
  if ( $gproj->get_total_units() > 0 ) {
  	$yest_per =  number_format(100*($gprojstats->get_stats_item('work_units') / $gproj->get_total_units()),6);
  }
  $yest_unscaled_rate = number_format(( ($gprojstats->get_stats_item('work_units')) / (86400) ),0);
  $yest_scaled_rate = number_format(( ($gprojstats->get_stats_item('work_units') * $gproj->get_scale()) / (86400) ),0);


  $odds = number_format($total_remaining / $gprojstats->get_stats_item('work_units'),0);

  if(strtolower(substr($gproj->get_name(), 0, 3)) == "ogr")
  {
    $ogrdb = new DB("dbname=ogr");
    $ogrstats = $ogrdb->query_first("SELECT * FROM recent_complete WHERE project_id = " . $gproj->get_id());
    if($ogrstats)
    {
      $showOGRcomplete = true;
      $per_searched = $ogrstats->tot_pct;
      $ogr_rundate = $ogrstats->rundate;
      $bar_width = number_format(3*$per_searched, 0);
    }
  }

?>
   <div style="text-align:center">
   <br>
   <p class="phead">
     Aggregate Statistics
   </p>
   <table style="margin:auto">
<? if ($gproj->get_total_units() > 0 ) { ?>
    <tr>
     <td align="left" class="phead2">Total <?=$gproj->get_scaled_unit_name()?> to Search:</td>
     <td align="right"><?=$tot_scaled_units_to_search?></td>
    </tr>
<? } ?>
    <tr>
     <td align="left" class="phead2">Total <?=$gproj->get_scaled_unit_name()?> Tested:</td>
     <td align="right"><?=$tot_scaled_work_units?></td>
    </tr>
    <tr>
     <td align="left" class="phead2">Overall Rate:</td>
     <td align="right"><?=$overall_scaled_rate?> <?=$gproj->get_scaled_unit_name()?>/sec</td>
    </tr>
<? if ($gproj->get_total_units() > 0 ) { ?>
    <tr>
     <td align="left" class="phead2">Total <?=$gproj->get_unscaled_unit_name()?> to Search:</td>
     <td align="right"><?=$tot_unscaled_units_to_search?></td>
    </tr>
<? } ?>
    <tr>
     <td align="left" class="phead2">Total <?=$gproj->get_unscaled_unit_name()?> Tested:</td>
     <td align="right"><?=$tot_unscaled_work_units?></td>
    </tr>
    <tr>
     <td align="left" class="phead2">Overall Rate:</td>
     <td align="right"><?=$overall_unscaled_rate?> <?=$gproj->get_unscaled_unit_name()?>/sec</td>
    </tr>
<? if ($gproj->get_total_units() > 0 || $showOGRcomplete) { ?>
     <tr>
     <?if($showOGRcomplete) {?>
     <td align="left" class="phead2">Percent Complete<sup><A href="#footnote">*</A></sup>:</td>
     <td align="right"><a href="cache/ogr_graph_<?=$gproj->get_id()?>.png"><?=$per_searched?>%</a> *</td>
     <?} else {?>
     <td align="left" class="phead2">Percent Complete:</td>
     <td align="right"><?=$per_searched?>%</td>
     <?}?>
    </tr>
<? } ?>
   <tr>
     <td align="left" class="phead2">Time Working:</td>
     <td align="right"><?=$time_working?> days</td>
    </tr>
   </table>
   <br>
<? if ($gproj->get_total_units() > 0 || $showOGRcomplete) { ?>
   <p class="phead2">
     Progress Meter<?if($showOGRcomplete){?><sup><A href="#footnote">*</A></sup><?}?>
   </p>
   <table style="margin: auto" width="300" border="1" cellspacing="0" cellpadding="0">
    <tr>
     <td align="left"><img src="/images/bar.jpg" width="<?=($bar_width <= 0)?1:$bar_width?>" height="14"></td>
    </tr>
   </table>
   <br>
<? } ?>

  <p class="phead2">
     Current Information
  </p>
  <p>
      <?=$yest_scaled_work_units?> <?=$gproj->get_scaled_unit_name()?> were completed yesterday
        <? if ($gproj->get_total_units() > 0 ) { ?>
        (<?=$yest_per?>% of the keyspace)<br>
        <? } ?>
       at a sustained rate of <?=$yest_scaled_rate?> <?=$gproj->get_scaled_unit_name()?>/sec.
  </p>
  <p>
      <?=$yest_unscaled_work_units?> <?=$gproj->get_unscaled_unit_name()?> were completed yesterday
        <? if ($gproj->get_total_units() > 0 ) { ?>
        (<?=$yest_per?>% of the keyspace)<br>
        <? } ?>
       at a sustained rate of <?=$yest_unscaled_rate?> <?=$gproj->get_unscaled_unit_name()?>/sec.
  </p>
  <? if ($gproj->get_total_units() > 0 ) { ?>
  <p>
     The odds are 1 in <?=$odds?> that we will wrap this thing<br>
     up in the next 24 hours. (This also means that we'll<br>
     hit 100% in <?=$odds?> days at yesterday's rate.)
  </p>
<? } ?>
  <p>
      There have been <?=$total_emails?> participants<br>
      since the beginning of this project.<br>
      <?=$yest_emails?> of them were active yesterday<br>
      and of those, <?=$new_emails?>
      <? if($new_emails=='1') {
        echo 'was a brand-new participant.';
      } else {
        echo' were brand-new participants.';
      }?> 
   </p>
   <p>
     There are <?=$total_teams?> registered teams.<br>
     <?=$yest_teams?> of them submitted work units yesterday.<br>
     (<?=$new_teams?> of them <? if ($new_teams==1) { echo 'is'; } else {echo 'are';}?> brand new!)
   </p>
   <hr>
   <?if(isset($showOGRcomplete)){?>
   <A NAME="footnote"></A>
   <font size="-2">
     * The completion values are calculated in a separate stats run and may not be available at the same time as other values.
       In this case, the values from the previous day will be used.  This data is from <?=$ogr_rundate?>.
   </font>
   <br><br>
   <?}?>
   </div>
