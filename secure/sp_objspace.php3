<?php
  include "security.inc";
  include "../etc/config.inc";

  sybase_connect($interface, $ss_login, $ss_passwd);
  $qs = "sp__objnug";
  $result = sybase_query($qs);
  $rows = sybase_num_rows($result);

  if( $rows == 0 ) {
    echo "denied!<p>\n";
    exit;
  }
 
  SetCookie("ss_login",$ss_login,time()+3600,"/secure");
  SetCookie("ss_passwd",$ss_passwd,time()+3600,"/secure");

  echo "<html><head><title>stats.distributed.net - secure</title></head>\n";
  echo "<body bgcolor=\"#440000\" text=\"#000000\" link=\"#007700\">\n";
  echo "<center><table cellpadding=\"4\" cellspacing=\"0\" width=\"100%\"><tr bgcolor=\"#000000\">\n";
  echo "<td><font size=\"+4\" color=\"#ffffff\"><strong>stats</strong>.distributed.net secure site</font></td>\n";
  echo "<td valign=\"top\" align=\"right\"><font color=\"#ffffff\"><strong>$ss_login</strong></font></td>\n";
  echo "</tr></table>\n";
 
  echo "<br>\n";

  echo "<h1>$rows</h1>";  
  echo "<table border=\"1\" bgcolor=\"#ffffff\" cellpadding=\"4\" cellspacing=\"0\">\n";
  echo "<tr align=\"center\"><td colspan=\"10\"><font size=\"+3\">Current Connections Listing</font></td></tr>\n";
  echo "<tr><td align=\"right\">pid</td><td align=\"right\">blk</td><td>db</td><td>login</td>\n";
  echo "<td>hostname</td><td>client</td><td>cmd</td><td>eng</td><td align=\"right\">cpu</td>\n";
  echo "<td align=\"right\">i/o</td></tr>\n";

for ($i = 0; $i<$rows; $i++) {
  sybase_data_seek($result,$i);
  $par = sybase_fetch_object($result);

  $pnfmt = strtolower($par->progname);
  $cmdfmt = strtolower($par->cmd);
  $ln = strtolower($par->login);

  echo "<tr";
  if ($ln == 'luser') {
    echo " bgcolor=\"#ffdddd\"";
  }
  if ($ln == 'nugget' or $ln == 'dbaker' or $ln == 'daa') {
    echo " bgcolor=\"#ffffdd\"";
  }
  if ($ln == 'moonwick' or $ln == 'decibel' or $ln == 'alde' or $ln == 'peter') {
    echo " bgcolor=\"#ddffdd\"";
  }
  echo ">";
  echo "<td align=\"right\">$par->pid</td><td align=\"right\">$par->blk</td><td>$par->db</td><td>$par->login</td>\n";
  echo "<td>$par->Table_Name &nbsp;</td><td>$pnfmt</td><td>\n";
  if ($cmdfmt == 'awaiting command') {
    echo "<font color=\"#777777\">$cmdfmt</font>";
  } else {
    echo "$cmdfmt";
  }
  echo "</td><td align=\"right\">$par->eng</td><td align=\"right\">$par->cpu</td>\n";
  echo "<td align=\"right\">$par->io</td></tr>\n";
}
echo "</table>\n";

?>
</html>



?>
</html>
