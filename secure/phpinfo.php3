<?php
  include "security.inc";

  include "../etc/config.inc";

  SetCookie("ss_login",$ss_login,time()+3600,"/secure");
  SetCookie("ss_passwd",$ss_passwd,time()+3600,"/secure");
?>

<html><head><title>stats.distributed.net - secure</title></head>
<body bgcolor="#440000" text="#000000" link="#007700">
<center><table cellpadding="4" cellspacing="0" width="100%"><tr bgcolor="#000000">
<td><font size="+4" color="#ffffff"><strong>stats</strong>.distributed.net secure site</font></td>
<td valign="top" align="right"><font color="#ffffff"><strong><?=$ss_login?></strong></font></td>
</tr></table>
 
<br>

<?  phpinfo();?>
</html>
