<? 
// $Id: phistory_raw.php,v 1.12 2003/09/01 23:16:58 paul Exp $
// Variables Passed in url:
// id == Participant ID
// @todo -c Implement .see phistory and implement during update lock code
// @todo -c Implement .date format
// @todo -c Implement .order by date desc/asc

include "../etc/config.inc";
include "../etc/modules.inc";
include "../etc/project.inc";
include "../etc/participant.php";
include "../etc/participantstats.php";

$gpart = new Participant($gdb, $gproj, $id);
$gpartstats = new ParticipantStats($gdb, $gproj, $id, null);
$history = $gpartstats -> get_stats_history();

if($gpart->get_retire_to() > 0) {
    header("Location: http://stats.distributed.net/generic/phistory_raw.php?project_id=$project_id&id=$retire_to");
    exit();
} 

$lastupdate = last_update('ec');
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
PARTICIPANT=<?=$gpart->get_display_name()?> 
LASTUPDATE=<?=$lastupdate?> 
---BEGIN DATA---
DATE,UNITS
<?

foreach ($history as $histrow)
{
	print $histrow->stats_date . "," . number_format($histrow->work_units, 0) ."\n";
} 
?>
---END DATA---
</pre>
</body> 
</html>
