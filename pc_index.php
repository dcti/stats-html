<?
  # vi: ts=2 sw=2 tw=120 syntax=php
  # $Id: pc_index.php,v 1.17 2003/08/31 16:00:15 paul Exp $

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

  $qs = "select work_units from Daily_Summary where PROJECT_ID=$project_id order by date desc";
  $result = $gdb->query($qs);
  $gdb->data_seek(0);
  $par = $gdb->fetch_object();
  
  $yest_unscaled_work_units = number_format($par->work_units);
  $yest_scaled_work_units = number_format($par->work_units * $gproj->get_scale());
  if ( $gproj->get_total_units() > 0 ) {
  	$yest_per =  number_format(100*($par->work_units / $gproj->get_total_units()),6);
  }
  $yest_unscaled_rate = number_format(( ($par->work_units) / (86400) ),0);
  $yest_scaled_rate = number_format(( ($par->work_units * $gproj->get_scale()) / (86400) ),0);


  $odds = number_format($total_remaining / $par->work_units,0);
?>
   <div style="text-align:center">
   <br>
   <p>
     Aggregate Statistics
   </p>
   <table>
<? if ($gproj->get_total_units() > 0 ) { ?>
    <tr>
     <td>Total <?=$gproj->get_scaled_unit_name()?> to Search:</td>
     <td align="right"><?=$tot_scaled_units_to_search?></td>
    </tr>
<? } ?>
    <tr>
     <td>Total <?=$gproj->get_scaled_unit_name()?> Tested:</td>
     <td align="right"><?=$tot_scaled_work_units?></td>
    </tr>
    <tr>
     <td>Overall Rate:</td>
     <td align="right"><?=$overall_scaled_rate?> <?=$gproj->get_scaled_unit_name()?>/sec</td>
    </tr>
<? if ($gproj->get_total_units() > 0 ) { ?>
    <tr>
     <td>Total <?=$gproj->get_unscaled_unit_name()?> to Search:</td>
     <td align="right"><?=$tot_unscaled_units_to_search?></td>
    </tr>
<? } ?>
    <tr>
     <td>Total <?=$gproj->get_unscaled_unit_name()?> Tested:</td>
     <td align="right"><?=$tot_unscaled_work_units?></td>
    </tr>
    <tr>
     <td>Overall Rate:</td>
     <td align="right"><?=$overall_unscaled_rate?> <?=$gproj->get_unscaled_unit_name()?>/sec</td>
    </tr>
<? if ($gproj->get_total_units() > 0 ) { ?>
     <tr>
     <td>Percent Complete:</td>
     <td align="right"><?=$per_searched?>%</td>
    </tr>
<? } ?>
   <tr>
     <td>Time Working:</td>
     <td align="right"><?=$time_working?> days</td>
    </tr>
   </table>
   <br>
<? if ($gproj->get_total_units() > 0 ) { ?>
   <p>
     Progress Meter
   </p>
   <table width="300" border="1" cellspacing="0" cellpadding="0">
    <tr>
     <td align="left"><img src="/images/bar.jpg" width="<?=$bar_width?>" height="14"></td>
    </tr>
   </table>
   <br>
<? } ?>

  <p>
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
   </div>