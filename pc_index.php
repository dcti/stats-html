<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
        "http://www.w3.org/TR/REC-html40/loose.dtd">
<?

 # $Id: pc_index.php,v 1.1 2002/03/08 21:50:41 decibel Exp $

 $title = "Overall Project Stats";

 include "../etc/config.inc";
 include "../etc/modules.inc";
 include "etc/project.inc";

 sybase_pconnect($interface,$username,$password);

 sybase_query("set rowcount 1"); 
 $qs = "select * from Daily_Summary where PROJECT_ID=$project_id order by date desc";
 $result = sybase_query($qs);
 sybase_data_seek($result,0);
 $daysum = sybase_fetch_object($result);
 $lastupdate = sybase_date_format_long($daysum->DATE);

 include "templates/header.inc";

 debug_text("<!-- Daily Summary -- qs: $qs, daysum: $daysum, par: $par -->\n",$debug);

 $yest_work_units = number_format( (double) $daysum->WORK_UNITS/$divider);
 $yest_emails = number_format($daysum->PARTICIPANTS);
 $yest_teams = number_format($daysum->TEAMS);
 $new_emails = number_format($daysum->PARTICIPANTS_NEW);
 $new_teams = number_format($daysum->TEAMS_NEW);

 sybase_query("set rowcount 0");
 $qs = "select sum(WORK_UNITS)/$divider as TOT_UNITS, datediff(dd,min(date),max(date))+1 as time_working
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

 print "
  <center>
   <br>
   <p>
    <font $fontd size=\"+2\">
     Aggregate Statistics
    </font>
   </p>
   <table>
    <tr>
     <td><font $fontd size=\"+1\">Total Gnodes Tested:</font></td>
     <td align=\"right\" size=\"+2\"><font $fontf>$tot_work_units</font></td>
    </tr>
    <tr>
     <td><font $fontd size=\"+1\">Time Working:</font></td>
     <td align=\"right\" size=\"+2\"><font $fontf>$time_working days</font></td>
    </tr>
   </table>
   <br>
   <p>
    <font $fontd size=\"+2\">
     Current Information
    </font>
   </p>
   <p>
    <font $fontd>
     $yest_work_units Gnodes were completed yesterday.
    </font>
   </p>
   <p>
    <font $fontd>
     There have been $total_emails participants<br>
     since the beginning of this project.<br>
     $yest_emails of them were active yesterday<br>
     and of those, $new_emails were brand-new participants. 
    </font>
   </p>
   <p>
    <font $fontd>
     There are $total_teams registered teams.<br>
     $yest_teams of them submitted work units yesterday.<br>
     ($new_teams of them are brand new!)
    </font>
   </p>
   <hr>
   <p>
    <a href=\"http://www.php.net\"><img src=\"/images/php.gif\" alt=\"PHP3\" border=\"0\"></a>
    <a href=\"http://www.sybase.com/\"><img src=\"/images/sybase.gif\" alt=\"Sybase\" border=\"0\"></a>
   </p>
  </center>
";
?>
 </body>
</html>
