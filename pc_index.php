<?
  # vi: ts=2 sw=2 tw=120 syntax=php
  # $Id: pc_index.php,v 1.39 2005/09/22 12:52:17 fiddles Exp $

  $title = "Overall Project Stats";

  include "etc/global.inc";
  include "etc/modules.inc";
  include "etc/projectstats.php";
  include "etc/project.inc";
  
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
      $pct_searched = number_format(100*($gprojstats->get_tot_units()/$gproj->get_total_units()),3);
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
      $yest_pct =  number_format(100*($gprojstats->get_stats_item('work_units') / $gproj->get_total_units()),6);
  }
  $yest_unscaled_rate = number_format(( ($gprojstats->get_stats_item('work_units')) / (86400) ),0);
  $yest_scaled_rate = number_format(( ($gprojstats->get_stats_item('work_units') * $gproj->get_scale()) / (86400) ),0);
  $yest_pct_remaining = number_format(100*($gprojstats->get_stats_item
('work_units') / ($gproj->get_total_units() - $gprojstats->get_tot_units() + $gprojstats->get_stats_item('work_units'))),6);



  $odds = number_format($total_remaining / $gprojstats->get_stats_item('work_units'),0);

  ###
  # Percentage for OGR Phase 1 and Phase 2
  if ($gproj->get_id() == 24 || $gproj->get_id() == 25) {
    $ogrdb = new DB("dbname=ogr");
    $ogrstats = $ogrdb->query_first("SELECT * FROM recent_complete WHERE project_id = " . $gproj->get_id());
    if($ogrstats) {
      $ogr_rundate = $ogrstats->rundate;
      $ogrp1_pct_searched = $ogrstats->tot_pct;
    } else {
      $ogrp1_pct_searched = 100;   // if failed, then just assume 100 since we know its done.
    }
    $ogrp1_bar_width = number_format(3*$ogrp1_pct_searched, 0);
    $ogrp1_pct_link = "cache/ogr_graph_" . $gproj->get_id() . ".png";
  }
  if ($gproj->get_id() == 24) {
    $ogrp2_pct_searched = 100;
    $ogrp2_bar_width = number_format(3*$ogrp2_pct_searched, 0);
    $ogrp2_pct_link = "http://n0cgi.distributed.net/statistics/ogr/ogr24p2-percent.png";
  } elseif ($gproj->get_id() == 25) {
    $ogrp2_pct_searched = 10.63;        // @todo: Hardcoded manual estimate
    $ogrp2_bar_width = number_format(3*$ogrp2_pct_searched, 0);
    $ogrp2_pct_link = "#ogrfootnote";
  }

?>
   <div style="text-align:center">
   <br />
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
<? if ($gproj->get_id() == 24 || $gproj->get_id() == 25) { ?>
     <tr>
     <td align="left" class="phead2">Percent Complete (Phase 1):</td>
     <td align="right"><a href="<?=$ogrp1_pct_link?>"><?=$ogrp1_pct_searched?>%</a></td>
     </tr>
     <tr>
     <td align="left" class="phead2">Percent Complete (Phase 2):</td>
     <td align="right"><a href="<?=$ogrp2_pct_link?>"><?= $gproj->get_id() == 25 ? "~" : "" ?><?=$ogrp2_pct_searched?>%</a></td>
     </tr>
<? } elseif ($gproj->get_total_units() > 0) { ?>
     <tr>
     <td align="left" class="phead2">Percent Complete:</td>
     <td align="right"><?=$pct_searched?>%</td>
     </tr>
<? } ?>
   <tr>
     <td align="left" class="phead2">Time Working:</td>
     <td align="right"><?=$time_working?> days</td>
    </tr>
   </table>
   <br />

<? if ($gproj->get_total_units() > 0 || $gproj->get_id() == 24 || $gproj->get_id() == 25) { ?>
   <p class="phead2">
     Progress Meters
   </p>
   <table style="margin: auto" width="300" border="1" cellspacing="0" cellpadding="0">

   <? if ($gproj->get_id() == 24 || $gproj->get_id() == 25) { ?>
    <tr>
     <td align="left">Phase 1</td>
     <td align="left"><img src="/images/bar.jpg" width="<?=max($ogrp1_bar_width,1)?>" height="14" /></td>
    </tr>
    <tr>
     <td align="left">Phase 2</td>
     <td align="left"><img src="/images/bar.jpg" width="<?=max($ogrp2_bar_width,1)?>" height="14" /></td>
    </tr>
   <? } else { ?>
    <tr>
     <td align="left"><img src="/images/bar.jpg" width="<?=max($bar_width,1)?>" height="14" /></td>
    </tr>
   <? } ?>

   </table>

   <br />
<? } ?>


  <p class="phead2">
     Current Information
  </p>
  <p>
      <?=$yest_scaled_work_units?> <?=$gproj->get_scaled_unit_name()?> were completed yesterday
        <? if ($gproj->get_total_units() > 0 ) { ?>
        (<?=$yest_pct?>% of the keyspace)(<?=$yest_pct_remaining?>% of the remaining keysapce)<br />
        <? } ?>
       at a sustained rate of <?=$yest_scaled_rate?> <?=$gproj->get_scaled_unit_name()?>/sec.
  </p>
  <p>
      <?=$yest_unscaled_work_units?> <?=$gproj->get_unscaled_unit_name()?> were completed yesterday
        <? if ($gproj->get_total_units() > 0 ) { ?>
        (<?=$yest_pct?>% of the keyspace)(<?=$yest_pct_remaining?>% of the remaining keysapce)<br />
        <? } ?>
       at a sustained rate of <?=$yest_unscaled_rate?> <?=$gproj->get_unscaled_unit_name()?>/sec.
  </p>
<? if ($gproj->get_total_units() > 0 ) { ?>
  <p>
     The odds are 1 in <?=$odds?> that we will wrap this thing<br />
     up in the next 24 hours. (This also means that we'll<br />
     hit 100% in <?=$odds?> days at yesterday's rate.)
  </p>
<? } ?>
   <p>
      There have been <?=$total_emails?> participants<br />
      since the beginning of this project.<br />
      <?=$yest_emails?> of them were active yesterday<br />
      and of those, <?=$new_emails?>
      <? if($new_emails=='1') {
        echo ' was a brand-new participant.';
      } else {
        echo ' were brand-new participants.';
      }?> 
   </p>
   <p>
     There are <?=$total_teams?> registered teams.<br />
     <?=$yest_teams?> of them submitted work units yesterday.<br />
     (<?=$new_teams?> of them <?=($new_teams==1 ? 'is' : 'are')?> brand new!)
   </p>

   <? if ($gproj->get_id() == 8) { ?>
   <p><a href="http://n0cgi.distributed.net/rc5-proxyinfo.html">Current Proxy Rates</a></p>
   <? } elseif ($gproj->get_id() == 24 || $gproj->get_id() == 25) { ?>
   <p><a href="http://n0cgi.distributed.net/ogr-proxyinfo.html">Current Proxy Rates</a></p>
   <? } elseif ($gproj->get_id() == 3) { ?>
   <p><a href="http://n0cgi.distributed.net/statistics/rc5-56/index.html">Additional Stats</a></p>
   <? } ?>

   <hr />

   <?if( $gproj->get_id() == 24 || $gproj->get_id() == 25 ){?>
   <a name="ogrfootnote"></a>
   <p><font size="-2">
      For more information about OGR Phase 1 and Phase 2, please <a href="http://n0cgi.distributed.net/faq/cache/230.html">read our FAQ page</a>.
   </font></p>
   <br /><br />
   <?}?>


   </div>
