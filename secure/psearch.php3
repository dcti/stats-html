<?php
include "security.inc";

include "../etc/config.inc";
include "../misc/number_format.inc";

sybase_connect($interface, $ss_login, $ss_passwd);
$qs = "select * from stats.dbo.STATS_participant where email like \"%%$st%%\" order by id";
$result = sybase_query($qs);
$rows = sybase_num_rows($result);

echo "<html><head><title>stats.distributed.net - secure</title></head>\n";
echo "<body bgcolor=\"#440000\" text=\"#000000\" link=\"#007700\">\n";
echo "<center><table cellpadding=\"4\" cellspacing=\"0\" width=\"100%\"><tr bgcolor=\"#000000\">\n";
echo "<td><font size=\"+4\" color=\"#ffffff\"><strong>stats</strong>.distributed.net secure site</font></td>\n";
echo "<td valign=\"top\" align=\"right\"><font color=\"#ffffff\"><strong>$ss_login</strong></font></td>\n";
echo "</tr></table>\n";

echo "<br>\n";

echo " <h3><font color=\"#ffffff\">Participant Search [$st]</font></h3>\n";

echo " <table border=\"1\" cellspacing=\"0\">\n";
echo "  <tr bgcolor=\"#ffffff\">\n";
echo "   <td>ID</td>\n";
echo "   <td>Email</td>\n";
echo "   <td>Name</td>\n";
echo "  </tr>\n";

for ($i = 0; $i<$rows; $i++) {
	if( ($i/2) == (round($i/2)) ) {
	  echo "  <tr bgcolor=\"$bar_color_a\">\n";
	} else {
	  echo "  <tr bgcolor=\"$bar_color_b\">\n";
	}
	sybase_data_seek($result,$i);
	$par = sybase_fetch_object($result);
	$totalblocks = $totalblocks + $par->blocks;
	$decimal_places=0;
	$blocks=number_style_convert( $par->blocks );

	$id = 0+$par->id;

	echo "   <TD>$id</TR>\n";
	echo "   <TD><a href=\"pedit.php3?id=$id\">$par->email</a></TR>\n";
	echo "   <TD align=\"right\">$par->contact_name&nbsp;</td></TR>\n";
}
	$totalblocks = number_format($totalblocks, 0, ".", ",");
	echo "<TR BGCOLOR=\"#ffffff\"><TD>&nbsp;<TD align=\"right\"><strong>Total</strong></TR><TD>$totalblocks</TR>\n";

echo " </table>\n";

?>
</html>
