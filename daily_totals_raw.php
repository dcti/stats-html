<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
        "http://www.w3.org/TR/REC-html40/loose.dtd">
<?
# $Id: daily_totals_raw.php,v 1.5 2002/05/14 22:18:54 paul Exp $

 include "etc/config.inc";
 include "etc/project.inc";

 $qs = "SELECT	convert(char(10),DATE,101) as datefmt, *
	FROM	Daily_Summary
	WHERE	PROJECT_ID = $project_id
	ORDER BY	DATE";
 sybase_query("set rowcount 0");
 $result = sybase_query($qs);
 $rows = sybase_num_rows($result);

?>
<html>
<head>
<title>Daily Counts RAW</title> 
</head>
<body>
<pre>
NOTE: Please make your scripts tolerant of additional values
in this report.  Future improvements may be implemented which
result in additional fields added to each line.


--BEGIN DATA--
DATE,UNITS,PARTICIPANTS,TEAMS
<?

for ($i = 0; $i<$rows; $i++) {
	sybase_data_seek($result,$i);
	$par = sybase_fetch_object($result);
	print "$par->datefmt," . (double) $par->WORK_UNITS . "," . (int) $par->PARTICIPANTS . ","
		. (int) $par->TEAMS. "\n";
}
?>
--END DATA--
</pre>
</body> 
</html>
