<?php
  include "security.inc";
  require ('../etc/config.inc');

  sybase_connect($interface, $ss_login, $ss_passwd);
  $qs = "select @@version as version";
  $result = sybase_query($qs);
  $rows = sybase_num_rows($result);

  if( $rows == 0 ) {
    echo "ack! denied!<p>\n";
    exit;
  }

  sybase_data_seek($result,0);
  $par = sybase_fetch_object($result);
   
  SetCookie("ss_login",$ss_login,time()+3600,"/secure");
  SetCookie("ss_passwd",$ss_passwd,time()+3600,"/secure");

  echo "<html><head><title>stats.distributed.net - secure</title></head>\n";
  echo "<body bgcolor=\"#440000\" text=\"#000000\" link=\"#007700\">\n";
  echo "<center><table cellpadding=\"4\" cellspacing=\"0\" width=\"100%\"><tr bgcolor=\"#000000\">\n";
  echo "<td><font size=\"+4\" color=\"#ffffff\"><strong>stats</strong>.distributed.net secure site</font></td>\n";
  echo "<td valign=\"top\" align=\"right\"><font color=\"#ffffff\"><strong>$ss_login</strong></font></td>\n";
  echo "</tr></table>\n";
 
  echo "<br>\n";

  echo "<table border=\"1\" bgcolor=\"#ffffff\" cellpadding=\"4\" cellspacing=\"0\">\n";
  echo "<tr align=\"center\"><td bgcolor=\"#777777\"><font size=\"+3\">Tally</font></td></tr>\n";
  echo "<tr align=\"center\"><td><a href=\"sp_who.php3\">List Active Connections</a></td></tr>\n";
  echo "<tr align=\"center\"><td><a href=\"server-info\">Apache Configuration</a></td></tr>\n";
  echo "<tr align=\"center\"><td><a href=\"server-status\">Apache Status</a></td></tr>\n";
  echo "<tr align=\"center\"><td><a href=\"phpinfo.php3\">PHP Configuration</a></td></tr>\n";
  echo "<tr align=\"center\"><td><form action=\"psearch.php3\" method=\"get\">Participant: <input type=\"text\" name=\"st\" size=\"10\" maxlength=\"60\"><input type=\"submit\" value=\"go!\"></form><br>NOTE! This is a 'begins-with' search!</td></tr>\n";
  echo "<tr align=\"center\"><td><form action=\"tmsearch.php3\" method=\"get\">Team: <input type=\"text\" name=\"st\" size=\"10\" maxlength=\"60\"><input type=\"submit\" value=\"go!\"></form></td></tr>\n";
  echo "<tr align=\"center\"><td bgcolor=\"#777777\"><font size=\"+3\">RC5-64</font></td></tr>\n";
  echo "<tr align=\"center\"><td><a href=\"rc5log.cgi\">Last 50 Log Entries</a></td></tr>\n";
  echo "<tr align=\"center\"><td bgcolor=\"#777777\"><font size=\"+3\">CSC</font></td></tr>\n";
  echo "<tr align=\"center\"><td><a href=\"csclog.cgi\">Last 50 Log Entries</a></td></tr>\n";
  echo "</table>\n";
?>
</html>
