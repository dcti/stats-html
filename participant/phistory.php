<?
 # $Id: phistory.php,v 1.5 2002/03/09 18:31:29 paul Exp $

 // Variables Passed in url:
 //   id == Participant ID

 include "../etc/config.inc";
 include "../etc/modules.inc";
 include "etc/project.inc";

 if(file_exists($lockfile)) {
   $title = "Participant History (Unavailable)";
   include "templates/header.inc";
   include "templates/updating.inc";
   exit;
 }

 sybase_pconnect($interface, $username, $password);
 $qs = "p_participant_all $id";
 sybase_query("set rowcount 0");
 $result = sybase_query($qs);

 if ($result == "") {
   if ($debug=="yes") {
     include "templates/debug.inc";
   } else {
     include "templates/error.inc";
   }
   exit();
 }

 sybase_data_seek($result,0);
 $person = sybase_fetch_object($result);
 $participant = participant_listas($person->listmode,$person->email,$id,$person->contact_name);

 $retire_to = (int) $person->retire_to;
 if( $retire_to > 0 ) {
   header("Location: http://stats.distributed.net/generic/phistory.php?project_id=$project_id&id=$retire_to");
   exit();
 }

 # Sybase's query optimizer sucks on stored procs, so we're stuck doing this
 #$qs = "p_lastupdate m, @contest='new', @project_id=$project_id";
 $qz = "select max(date)
		from email_contrib
		where date>dateadd(day,-10,getdate())
			and project_id=$project_id";
 $result = sybase_query($qs);
 if($result) {
   $par = sybase_fetch_object($result);
   $lastupdate = sybase_date_format_long($par->lastupdate);
 }

/*
*********************************
 $qs = "select id from STATS_Participant where retire_to = $id";
 $result = sybase_query($qs);
 $rows = sybase_num_rows($result);
 $whereline = "id = $id";
 for ($i = 0; $i<$rows; $i++) {
   sybase_data_seek($result,$i);
   $par = sybase_fetch_object($result);
   $rt = (int) $par->id;
   $whereline = "$whereline or id = $rt";
 }
 
 $qs = "select date, convert(char(10),date,101) as datefmt, sum(work_units)/$proj_divider as work_units
	from email_contrib
	where PROJECT_ID=$project_id and ( $whereline )
	group by date
	order by date desc";
*********************************
*/

 $qs = "p_phistory @project_id = $project_id, @id = $id";

 $title = "Participant History for $participant";

 include "templates/header.inc";

print "\n<!-- IMPORTANT NOTE TO SCRIPTERS!\n     This page, like many stats pages, has a version which is far more suitable\n     for machine parsing.  Please try the url:\n http://stats.distributed.net/ogr-24/phistory_raw.php?project_id=$project_id&id=$id\n-->\n";

 print "
	<center>
	 <table border=\"1\" cellspacing\"0\" bgcolor=$header_bg>
	  <tr>
	   <td><font $header_font>Date</font></td>
	   <td align=\"right\"><font $header_font>$proj_unitname</font></td>
	   <td><font $header_font>&nbsp;</td>
	  </tr>";

 $result = sybase_query($qs);
 $rows = sybase_num_rows($result);
 $maxwork_units = (double) 0;
 for ($i = 0; $i<$rows; $i++) {
   sybase_data_seek($result,$i);
   $par = sybase_fetch_object($result);
   $work_units = (double) $par->WORK_UNITS;
   if($work_units > $maxwork_units) {
     $maxwork_units = $work_units;
   }
   debug_text("<!-- work_units: $work_units, maxwork_units: $maxwork_units -->\n",$debug);
 }
 
 for ($i = 0; $i<$rows; $i++) {
   sybase_data_seek($result,$i);
   $par = sybase_fetch_object($result);
   $work_units = (double) $par->WORK_UNITS;
   $work_units_fmt = number_format($work_units,0);
   $date_fmt = sybase_date_format_long($par->DATE);
   $width = (int) (($work_units / $maxwork_units) * 200)+1;

   debug_text("<!-- work_units: $work_units, maxwork_units: $maxwork_units -->\n",$debug);

   print "
	<tr bgcolor=" . row_background_color($i) . ">
	   <td>$date_fmt</td>
	   <td align=\"right\">$work_units_fmt</td>
	   <td align=\"left\"><img src=\"/images/bar.jpg\" height=\"8\" width=\"$width\"></td>
	</tr>\n";
 }
 print "
	 </table>";

?>
<?include "templates/footer.inc";?>
