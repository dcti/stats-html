<?
 # $Id: phistory_raw.php,v 1.6 2002/03/16 15:47:26 paul Exp $

 // Variables Passed in url:
 //   id == Participant ID


 include "../etc/config.inc";
 include "../etc/modules.inc";
 include "etc/project.inc";

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
   header("Location: http://stats.distributed.net/generic/phistory_raw.php?project_id=$project_id&id=$retire_to");
   exit();
 }

/*
*************************
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
*************************
*/

 $qz = "select max(date)
		from email_contrib
		where date>dateadd(day,-10,getdate())
			and project_id=$project_id";
 $result = sybase_query($qs);
 if($result) {
   $par = sybase_fetch_object($result);
   $lastupdate = sybase_date_format_long($par->lastupdate);
 }

 $qs = "p_phistory @project_id = $project_id, @id = $id";

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
        "http://www.w3.org/TR/REC-html40/loose.dtd">
<html>
<head>
<title>Raw Participant History for ID <?=$id?></title>
</head>
<body>
<pre>

NOTE: Please make your scripts tolerant of additional values
in this report.  Future improvements may be implemented which
result in additional fields added to each line.

---BEGIN HEADER---
ID=<?=$id?>
PARTICIPANT=<?=$participant?>
LASTUPDATE=<?=$lastupdate?>
---BEGIN DATA---
DATE,UNITS
<?
 $result = sybase_query($qs);
 $rows = sybase_num_rows($result);
 for ($i = 0; $i<$rows; $i++) {
   sybase_data_seek($result,$i);
   $par = sybase_fetch_object($result);
   $work_units = (double) $par->WORK_UNITS;
   $date = strtotime( substr($par->DATE,0,11) );
   $YYY = substr($par->DATE,7,11);
   print date("m/d",$date) . "/$YYY,$work_units\n";
 }
?>
---END DATA---
</pre>
</body> 
</html>
