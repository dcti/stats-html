<?
  # vi: ts=2 sw=2 tw=120 syntax=php
  # $Id: pc_index.php,v 1.10.2.2 2003/05/27 17:52:26 nerf Exp $

  $title = "Overall Project Stats";

  include "etc/config.inc";
  include "etc/modules.inc";
  include "etc/project.inc";

  ####
  # Daily summary
  $qs = "select *
          from Daily_summary
          where PROJECT_ID=$project_id
            and DATE = (select max(DATE) from Daily_summary where PROJECT_ID=$project_id)";
  $result = sybase_query($qs);
  sybase_data_seek($result,0);
  $daysum = sybase_fetch_object($result);
  $lastupdate = sybase_date_format_long($daysum->DATE);
  display_last_update('i');

  debug_text("<!-- Daily Summary -- qs: $qs, daysum: $daysum,  -->\n",$debug);

  $yest_scaled_work_units = number_format( (double) $daysum->WORK_UNITS * $proj_scale);
  $yest_unscaled_work_units = number_format( (double) $daysum->WORK_UNITS);
  $yest_emails = number_format($daysum->PARTICIPANTS);
  $yest_teams = number_format($daysum->TEAMS);
  $new_emails = number_format($daysum->PARTICIPANTS_NEW);
  $new_teams = number_format($daysum->TEAMS_NEW);

  ####
  # Total work, time working
  sybase_query("set rowcount 0");
  $qs = "select sum(WORK_UNITS) as TOT_UNITS, datediff(dd,min(date),max(date))+1 as TIME_WORKING
          from Daily_Summary
          where PROJECT_ID=$project_id";
  $result = sybase_query($qs);
  sybase_data_seek($result,0);
  $par = sybase_fetch_object($result);

  debug_text("<!-- Time Working -- qs: $qs, result: $result, par: $par -->\n",$debug);

  $time_working_raw = $par->TIME_WORKING;
  $time_working = number_format($par->TIME_WORKING);
  $TOT_UNITS = $par->TOT_UNITS;
  $tot_unscaled_work_units = number_format( (double) $TOT_UNITS);
  $tot_scaled_work_units = number_format( (double) $TOT_UNITS * $proj_scale);

  ####
  # Total Emails
  $qs = "select convert(char(8),count(*)) as emails from Email_Rank where PROJECT_ID=$project_id";
  $result = sybase_query($qs);
  sybase_data_seek($result,0);
  $par = sybase_fetch_object($result);
  $total_emails = number_format($par->emails);

  ####
  # Total Teams
  $qs = "select convert(char(8),count(*)) as teams from Team_Rank where PROJECT_ID=$project_id";
  $result = sybase_query($qs);
  sybase_data_seek($result,0);
  $par = sybase_fetch_object($result);
  $total_teams = number_format($par->teams);

  ####
  # Percent complete
  $tot_unscaled_units_to_search = number_style_convert($proj_totalunits);
  $tot_scaled_units_to_search = number_style_convert($proj_totalunits * $proj_scale);
  $total_remaining = $proj_totalunits - $TOT_UNITS;
  $per_searched = number_format(100*($TOT_UNITS/$proj_totalunits),3);
  $bar_width = number_format(300*($TOT_UNITS/$proj_totalunits),0);

  ####
  # Overall Rate
  $overall_unscaled_rate = number_format(( ($TOT_UNITS) / ($time_working_raw*86400) ),0);
  $overall_scaled_rate = number_format(( ($TOT_UNITS * $proj_scale) / ($time_working_raw*86400) ),0);

  ####
  # Yesterday Rate
  sybase_query("set rowcount 1");
  $qs = "select convert(char(20),work_units) as work_units from Daily_Summary where PROJECT_ID=$project_id order by date desc";
  $result = sybase_query($qs);
  sybase_data_seek($result,0);
  $par = sybase_fetch_object($result);
  sybase_query("set rowcount 0");
  $yest_unscaled_work_units = number_format($par->work_units);
  $yest_scaled_work_units = number_format($par->work_units * $proj_scale);
  $yest_per =  number_format(100*($par->work_units / $proj_totalunits),6);
  $yest_rate = number_format((($par->work_units*$constant_keys_in_one_block)/86400)/1000,0);
  $yest_unscaled_rate = number_format(( ($par->work_units) / (86400) ),0);
  $yest_scaled_rate = number_format(( ($par->work_units * $proj_scale) / (86400) ),0);


  $odds = number_format($total_remaining / $par->work_units,0);

if ($proj_totalunits == 0 ) {

$database = "ogr";
$conn=pg_connect("dbname=$database") or die("Couldn't Connect: ".pg_last_error());

$sql = "SELECT * FROM recent_complete where project_id = $project_id";
$result = pg_query($conn, $sql);
$num_rows = pg_num_rows($result);

while($row = pg_fetch_array($result,NULL,PGSQL_ASSOC))
{
    //print_r($row);
    // Uncomment the preceding line to see the entire array.
$per_searched = $row['tot_pct'];
$ver_rundate = $row['rundate'];
}
$bar_width = number_format(3*($per_searched),0);

}

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
     <td><font <?=$fontd?> size="+1">Total <?=$proj_scaled_unit_name?> to Search:</font></td>
     <td align="right" size="+2"><font <?=$fontf?>><?=$tot_scaled_units_to_search?></font></td>
    </tr>
<? } ?>
    <tr>
     <td><font <?=$fontd;?> size="+1">Total <?=$proj_scaled_unit_name?> Tested:</font></td>
     <td align="right" size="+2"><font <?=$fontf?>><?=$tot_scaled_work_units?></font></td>
    </tr>
    <tr>
     <td><font <?=$fontd?> size="+1">Overall Rate:</font></td>
     <td align="right" size="+2"><font <?=$fontf?>><?=$overall_scaled_rate?> <?=$proj_scaled_unit_name?>/sec</font></td>
    </tr>
<? if ($proj_totalunits > 0 ) { ?>
    <tr>
     <td><font <?=$fontd?> size="+1">Total <?=$proj_unscaled_unit_name?> to Search:</font></td>
     <td align="right" size="+2"><font <?=$fontf?>><?=$tot_unscaled_units_to_search?></font></td>
    </tr>
<? } ?>
    <tr>
     <td><font <?=$fontd;?> size="+1">Total <?=$proj_unscaled_unit_name?> Tested:</font></td>
     <td align="right" size="+2"><font <?=$fontf?>><?=$tot_unscaled_work_units?></font></td>
    </tr>
    <tr>
     <td><font <?=$fontd?> size="+1">Overall Rate:</font></td>
     <td align="right" size="+2"><font <?=$fontf?>><?=$overall_unscaled_rate?> <?=$proj_unscaled_unit_name?>/sec</font></td>
    </tr>
     <tr>
     <td><font <?=$fontd?> size="+1"> Percent Complete<? if ($proj_totalunits == 0 ) { ?><sup><A href=#footnote>*</A></sup><? } ?>:</font></td>
     <td align="right" size="+2"><font <?=$fontf?>><?=$per_searched?>%</font></td>
    </tr>
   <tr>
     <td><font <?=$fontd?> size="+1">Time Working:</font></td>
     <td align="right" size="+2"><font <?=$fontf?>><?=$time_working?> days</font></td>
    </tr>
   </table>
   <br>
   <p>
    <font <?=$fontd?> size="+2">
     Progress Meter
     <? if ($proj_totalunits == 0 ) { ?><sup><font size="+1"><A href=#footnote>*</A>
</font>
</sup>
<? } ?>
    </font>
   </p>
   <table width="300" border="1" cellspacing="0" cellpadding="0">
    <tr bgcolor="#dddddd">
     <td align="left"><img src="/images/bar.jpg" width="<?=$bar_width?>" height="14"></td>
    </tr>
   </table>
   <br>
  <p>
    <font <?=$fontd?> size="+2">
     Current Information
    </font>
  </p>
  <p>
    <font <?=$fontd?>>
      <?=$yest_scaled_work_units?> <?=$proj_scaled_unit_name?> were completed yesterday
        <? if ($proj_totalunits > 0 ) { ?>
        (<?=$yest_per?>% of the keyspace)<br>
        <? } ?>
       at a sustained rate of <?=$yest_scaled_rate?> <?=$proj_scaled_unit_name?>/sec.
    </font>
  </p>
  <p>
    <font <?=$fontd?>>
      <?=$yest_unscaled_work_units?> <?=$proj_unscaled_unit_name?> were completed yesterday
        <? if ($proj_totalunits > 0 ) { ?>
        (<?=$yest_per?>% of the keyspace)<br>
        <? } ?>
       at a sustained rate of <?=$yest_unscaled_rate?> <?=$proj_unscaled_unit_name?>/sec.
    </font>
  </p>
  <? if ($proj_totalunits > 0 ) { ?>
  <p>
    <font <?=$fontd?>>
     The odds are 1 in <?=$odds?> that we will wrap this thing<br>
     up in the next 24 hours. (This also means that we'll<br>
     hit 100% in <?=$odds?> days at yesterday's rate.)
    </font>
  </p>
<? } ?>

  <p>
    <font <?=$fontd?>>
      There have been <?=$total_emails?> participants<br>
      since the beginning of this project.<br>
      <?=$yest_emails?> of them were active yesterday<br>
      and of those, <?=$new_emails?>
      <? if($new_emails=='1') {
        echo 'was a brand-new participant.';
      } else {
        echo' were brand-new participants.';
      }?> 
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
		<? if ($proj_totalunits == 0 ) { ?>
<A NAME="footnote"></A>
<font size="-2">
* The completion values are calculated in a separate stats run and may not be available at the same time as other values.
In this case, the values from the previous day will be used.  This data is from <?=$ver_rundate?>.
</font>
<p>
		<? } ?>
  </center>
