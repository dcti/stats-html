<?php
  include "security.inc";

  include "../etc/config.inc";

  SetCookie("ss_login",$ss_login,time()+3600,"/secure");
  SetCookie("ss_passwd",$ss_passwd,time()+3600,"/secure");

  echo "<html><head><title>stats.distributed.net - secure</title></head>\n";
  echo "<body bgcolor=\"#440000\" text=\"#000000\" link=\"#007700\">\n";
  echo "<center><table cellpadding=\"4\" cellspacing=\"0\" width=\"100%\"><tr bgcolor=\"#000000\">\n";
  echo "<td><font size=\"+4\" color=\"#ffffff\"><strong>stats</strong>.distributed.net secure site</font></td>\n";
  echo "<td valign=\"top\" align=\"right\"><font color=\"#ffffff\"><strong>$ss_login</strong></font></td>\n";
  echo "</tr></table>\n";
 
  echo "<br>\n";

  echo "<font face=\"courier, courier new\" color=\"#ffffff\">\n";
  phpinfo();
  echo "</font>\n";

?>
</html>
