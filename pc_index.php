<?
  # vi: ts=2 sw=2 tw=120 syntax=php
  # $Id: pc_index.php,v 1.8 2002/12/06 13:43:40 decibel Exp $

  $title = "Overall Project Stats";

  include "etc/config.inc";
  include "etc/modules.inc";
  include "etc/project.inc";

  $qs = "select * from daily_summary where project_id=$project_id and date = (select max(date) from daily_summary where project_id=$project_id)";
  $result = sybase_query($qs);
  sybase_data_seek($result,0);
  $daysum = sybase_fetch_object($result);
  $lastupdate = sybase_date_format_long($daysum->DATE);
  display_last_update('i');

  debug_text("<!-- Daily Summary -- qs: $qs, daysum: $daysum,  -->\n",$debug);

  $yest_work_units = number_format( (double) $daysum->WORK_UNITS/$proj_divider);
  $yest_emails = number_format($daysum->PARTICIPANTS);
  $yest_teams = number_format($daysum->TEAMS);
  $new_emails = number_format($daysum->PARTICIPANTS_NEW);
  $new_teams = number_format($daysum->TEAMS_NEW);

  sybase_query("set rowcount 0");
  $qs = "select sum(WORK_UNITS)/$proj_divider as TOT_UNITS, datediff(dd,min(date),max(date))+1 as time_working
  from Daily_Summary
  where PROJECT_ID=$project_id";
  $result = sybase_query($qs);
  sybase_data_seek($result,0);
  $par = sybase_fetch_object($result);

  debug_text("<!-- Time Working -- qs: $qs, result: $result, par: $par -->\n",$debug);

  $time_working_raw = $par->time_working;
  $time_working = number_format($par->time_working);
  $tot_work_units = number_format( (double) $par->TOT_UNITS);
  $TOT_UNITS = $par->TOT_UNITS;

  $qs = "select convert(char(8),count(*)) as emails from Email_Rank where PROJECT_ID=$project_id";
  $result = sybase_query($qs);
  sybase_data_seek($result,0);
  $par = sybase_fetch_object($result);
  $total_emails = number_format($par->emails);

  $qs = "select convert(char(8),count(*)) as teams from Team_Rank where PROJECT_ID=$project_id";
  $result = sybase_query($qs);
  sybase_data_seek($result,0);
  $par = sybase_fetch_object($result);
  $total_teams = number_format($par->teams);

  $tot_blocks_to_search = number_style_convert($proj_totalunits);
  $total_remaining = $proj_totalunits - $TOT_UNITS;
  $per_searched = number_format(100*($TOT_UNITS/$proj_totalunits),3);
  $bar_width = number_format(300*($TOT_UNITS/$proj_totalunits),0);

  $constant_keys_in_one_block = 268435456;
  $tot_keys_searched = number_format(($TOT_UNITS*$constant_keys_in_one_block),0);
  $overall_rate = number_format((($TOT_UNITS*$constant_keys_in_one_block)/($time_working_raw*86400))/1000,0);

  sybase_query("set rowcount 1");
  $qs = "select convert(char(20),work_units) as work_units from Daily_Summary where PROJECT_ID=$project_id order by date desc";
  $result = sybase_query($qs);
  sybase_data_seek($result,0);
  $par = sybase_fetch_object($result);
  sybase_query("set rowcount 0");
  $yest_blocks = number_format($par->work_units);
  $yest_per =  number_format(100*($par->work_units/$proj_totalunits),3);
  $yest_rate = number_format((($par->work_units*$constant_keys_in_one_block)/86400)/1000,0);


  $odds = number_format($total_remaining / $par->work_units,0);
?>
  <center>
   <br>
   <p>
    <font <?=$fontd;?> size="+2">
     Aggregate Statistics
    </font>
   </p>
   <table>
<? if ($proj_totalunits > 0 ) { ?>
    <tr>
     <td><font <?=$fontd?> size="+1">Total <?=$proj_unitname?> to Search:</font></td>
     <td align="right" size="+2"><font <?=$fontf?>><?=$tot_blocks_to_search?></font></td>
    </tr>
<? } ?>
    <tr>
     <td><font <?=$fontd;?> size="+1">Total <?=$proj_unitname?> Tested:</font></td>
     <td align="right" size="+2"><font <?=$fontf?>><?=$tot_work_units?></font></td>
    </tr>
<? if ($proj_totalunits > 0 ) { ?>
     <tr>
     <td><font <?=$fontd?> size="+1">Keyspace Checked:</font></td>
     <td align="right" size="+2"><font <?=$fontf?>><?=$per_searched?>%</font></td>
    </tr>
<? } ?>
<? if ($proj_totalunits > 0 ) { ?>
    <tr>
    <tr>
     <td><font <?=$fontd?> size="+1">Total Keys Tested:</font></td>
     <td align="right" size="+2"><font <?=$fontf?>><?=$tot_keys_searched?></font></td>
    </tr>
 <? } ?>
   <tr>
     <td><font <?=$fontd?> size="+1">Time Working:</font></td>
     <td align="right" size="+2"><font <?=$fontf?>><?=$time_working?> days</font></td>
    </tr>
<? if ($proj_totalunits > 0 ) { ?>
    <tr>
     <td><font <?=$fontd?> size="+1">Overall Rate:</font></td>
     <td align="right" size="+2"><font <?=$fontf?>><?=$overall_rate?> KKeys/sec</font></td>
    </tr>
 <? } ?>
   </table>
   <br>
<? if ($proj_totalunits > 0 ) { ?>
   <p>
    <font <?=$fontd?> size="+2">
     Progress Meter
    </font>
   </p>
   <table width="300" border="1" cellspacing="0" cellpadding="0">
    <tr bgcolor="#dddddd">
     <td align="left"><img src="/images/bar.jpg" width="<?=$bar_width?>" height="14"></td>
    </tr>
   </table>
   <br>
<? } ?>

   <p>
    <font <?=$fontd?> size="+2">
     Current Information
    </font>
   </p>
   <p>
    <font <?=$fontd?>>
     <?=$yest_work_units?> <?=$proj_unitname?> were completed yesterday
<? if ($proj_totalunits > 0 ) { ?>
 (<?=$yest_per?>% of the keyspace)<br> at a sustained rate of <?=$yest_rate?> KKeys/sec.
 <? } ?>
   </font>
   </p>
<? if ($proj_totalunits > 0 ) { ?>
   <p>
    <font <?=$fontd?>>
     The odds are 1 in <?=$odds?> that we will wrap this thing<br>
     up in the next 24 hours. (This also means that we'll<br>
     exhaust the keyspace in <?=$odds?> days at yesterday's rate.)
    </font>
   </p>
<? } ?>

   <p>
    <font <?=$fontd?>>
     There have been <?=$total_emails?> participants<br>
     since the beginning of this project.<br>
     <?=$yest_emails?> of them were active yesterday<br>
     and of those, <?=$new_emails?> <? if($new_emails==1){echo 'was a brand-new participant.'; } else { echo' were brand-new participants.';}?> 
    </font>
   </p>
   <p>
    <font <?=$fontd?>>
     There are <?=$total_teams?> registered teams.<br>
     <?=$yest_teams?> of them submitted work units yesterday.<br>
     (<?=$new_teams?> of them <? if ($new_teams==1) { echo 'is'; } else {echo 'are';}?> brand new!)
    </font>
   </p>
   <hr>
  </center>
