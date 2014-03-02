<? 
// $Id: phistory_raw.php,v 1.17 2005/04/01 16:58:42 decibel Exp $
// Variables Passed in url:
// id == Participant ID
// @todo -c Implement .see phistory and implement during update lock code
// @todo -c Implement .date format
// @todo -c Implement .order by date desc/asc

include "../etc/global.inc";
include "../etc/modules.inc";
include "../etc/project.inc";
include "../etc/participant.php";

$gpart = new Participant($gdb, $gproj, $id);

if($gpart->get_retire_to() > 0) {
    header("Location: http://stats.distributed.net/participant/phistory_raw.php?project_id=".$gproj->get_id()."&id=".$gpart->get_retire_to());
    exit();
} 
$gpartstats = new ParticipantStats($gdb, $gproj, $id, null);
$history = $gpartstats -> get_stats_history();

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
    $work_units_fmt = round($histrow->work_units*$gproj->get_scale(), 0);
 
    print "$histrow->stats_date,$work_units_fmt\n";
} 
?>
---END DATA---
</pre>
</body> 
</html>
