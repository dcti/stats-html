<?
 # $Id: phistory_raw.php,v 1.10 2002/12/16 20:00:31 decibel Exp $

 // Variables Passed in url:
 //   id == Participant ID


 include "../etc/config.inc";
 include "../etc/modules.inc";
 include "../etc/project.inc";

 $qs = "p_participant_all $id";
 sybase_query("set rowcount 0");
 $result = sybase_query($qs);

 err_check_query_results($result);

 sybase_data_seek($result,0);
 $person = sybase_fetch_object($result);
 $participant = participant_listas($person->listmode,$person->email,$id,$person->contact_name);

 $retire_to = (int) $person->retire_to;
 if( $retire_to > 0 ) {
   header("Location: http://stats.distributed.net/generic/phistory_raw.php?project_id=$project_id&id=$retire_to");
   exit();
 }

 $lastupdate = last_update('ec');

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
