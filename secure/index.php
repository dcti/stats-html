<?php
// @todo drop unauthenticated users
require "security.inc";


?>
<div style="text-align:center">
<table>
<td valign="top" align="right">
<strong><?=$ss_login?></strong></td>
</tr></table>

<br>
<table border="1" cellpadding="4" cellspacing="0">
<tr align="center"><td><a href="sp_who.php">List Active Connections</a></td></tr>
<tr align="center"><td><a href="server-info">Apache Configuration</a></td></tr>
<tr align="center"><td><a href="server-status">Apache Status</a></td></tr>
<tr align="center"><td><a href="phpinfo.php">PHP Configuration</a></td></tr>
<tr align="center"><td><form action="psearch.php" method="get">Participant: <input type="text" name="st" size="10" maxlength="60"><input type="submit" value="go!"></form><br>NOTE! This is a 'begins-with' search!</td></tr>
<tr align="center"><td><form action="tmsearch.php" method="get">Team: <input type="text" name="st" size="10" maxlength="60"><input type="submit" value="go!"></form></td></tr>
<tr align="center"><td><font size="+3">RC5-64</font></td></tr>
<tr align="center"><td><a href="rc5log.cgi">Last 50 Log Entries</a></td></tr>
<tr align="center"><td><font size="+3">OGR</font></td></tr>
<tr align="center"><td><a href="csclog.cgi">OGR-24 50 Log Entries</a></td></tr>
<tr align="center"><td><a href="csclog.cgi">OGR-25 Last 50 Log Entries</a></td></tr>
</table>
</div>
<?
	include "footer.inc";
?>
