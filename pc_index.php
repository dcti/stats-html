<?

 # $Id: pc_index.php,v 1.5 2002/03/23 12:38:18 paul Exp $

 $title = "Overall Project Stats";

 include "../etc/config.inc";
 include "../etc/modules.inc";
 include "etc/project.inc";

 sybase_query("set rowcount 1"); 
 $qs = "select * from Daily_Summary where PROJECT_ID=$project_id order by date desc";
 $result = sybase_query($qs);
 sybase_data_seek($result,0);
 $daysum = sybase_fetch_object($result);
 $lastupdate = sybase_date_format_long($daysum->DATE);

 include "templates/header.inc";

 debug_text("<!-- Daily Summary -- qs: $qs, daysum: $daysum, par: $par -->\n",$debug);

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

 $time_working = number_format($par->time_working);;
 $tot_work_units = number_format( (double) $par->TOT_UNITS);

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
?>
  <center>
   <br>
   <p>
    <font <?=$fontd;?> size="+2">
     Aggregate Statistics
    </font>
   </p>
   <table>
    <tr>
     <td><font <?=$fontd;?> size="+1">Total <?=$proj_unitname?> Tested:</font></td>
     <td align="right" size="+2"><font <?=$fontf?>><?=$tot_work_units?></font></td>
    </tr>
    <tr>
     <td><font <?=$fontd?> size="+1">Time Working:</font></td>
     <td align="right" size="+2"><font <?=$fontf?>><?=$time_working?> days</font></td>
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
     <?=$yest_work_units?> <?=$proj_unitname?> were completed yesterday.
    </font>
   </p>
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
<?include "templates/footer.inc";?>
