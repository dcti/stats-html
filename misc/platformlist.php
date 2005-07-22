<?

# $Id: platformlist.php,v 1.27 2005/07/22 16:55:04 decibel Exp $

$hour = 3;
$now = getdate();
if ($now['hours'] >= 0 and $now['hours'] < $hour) {
	$now = time();
} else {
	$now = time() + 86400;
}

Header("Cache-Control: must-revalidate");
Header("Expires: " . gmdate("D, d M Y", $now) . " $hour:00 GMT");

/// Variables passed in url
//   source == "y" for yseterday, all other values ignored.
//   view == display and sort order (t = total blocks, c = cpu, o = os, v = version)
//           page will show those columns in view, sorted in view's order
//   		(DEFAULT value is 'c', set in project.inc

 include "../etc/global.inc";
 include "../etc/modules.inc";
 include "../etc/project.inc";

 $lastupdate = last_update('e');
 $title = "CPU Participation";

 include "../templates/header.inc";

 $selstr = "select";
 $frostr = "from Platform_Summary p";
 $whestr = "where p.PROJECT_ID = $project_id";
 $grostr = "group by";
 $ordstr = "order by";
 $show_yesterday = 0;
 $show_total = 0;
 if("$source" == "y") {
   $whestr .= " and WORK_TODAY > 0";
 }

 for($i=0; $i < strlen($view); $i++) {
   $ch = substr($view,$i,1);
   if($ch == 'c') $fieldname = "p.CPU";
   if($ch == 'o') $fieldname = "p.OS";
   if($ch == 'v') $fieldname = "p.ver";
   if($ch == 'y') {
     $fieldname = "sum(p.WORK_TODAY)* ".$gproj->get_scale()." as yesterday";
     $show_yesterday = 1;
   }
   if($ch == 't') {
     $fieldname = "sum(p.WORK_TOTAL)* ".$gproj->get_scale()." as total";
     $show_total = 1;
   }

   $selstr .= " $fieldname,";
   if($ch != 'y' and $ch != 't') $grostr .= " $fieldname,";
 }


 $selstr .= " min(FIRST_DATE) as first, max(LAST_DATE) as last,";

 for($i=0; $i < strlen($view); $i++) {
   $ch = substr($view,$i,1);
   if($ch == 'c') {
     $selstr = "$selstr min(coalesce(c.name, 'Unknown')) AS cpuname, min(coalesce(c.image, 'unknown.gif')) AS cpuimage,";
     $frostr = "$frostr LEFT JOIN STATS_cpu c ON ( c.cpu = p.cpu )";
     $ordstr = "$ordstr cpuname,";
   }
   if($ch == 'o') {
     $selstr = "$selstr min(coalesce(o.name, 'Unknown')) AS osname, min(coalesce(o.image, 'unknown.gif')) AS osimage,";
     $frostr = "$frostr LEFT JOIN STATS_os o ON ( o.os = p.os )";
     $ordstr = "$ordstr osname,";
   }
   if($ch == 'v') {
     $selstr = "$selstr p.ver,";
     $ordstr = "$ordstr p.ver,";
   }
   if($ch == 'y') {
     $ordstr = "$ordstr yesterday desc,";
   }
   if($ch == 't') {
     $ordstr = "$ordstr total desc,";
   }
 }

 $selstr = substr($selstr,0,strlen($selstr)-1);
 $grostr = substr($grostr,0,strlen($grostr)-1);
 $ordstr = substr($ordstr,0,strlen($ordstr)-1);

 $QSlist = "$selstr $frostr $whestr $grostr $ordstr";
 $result = $gdb->query($QSlist);


 $rows = $gdb->num_rows();

 # Total number of columns in table, not counting yesterday or total columns. Start at 2 to account for first and last.
 $cols = 3;
 print "
    <center>
     <br>
     <table border=\"1\" cellspacing=\"0\" cellpadding\"0\" >
      <tr>";
 for($i=0; $i < strlen($view); $i++) {
   $ch = substr($view,$i,1);
   if($ch == 'c') {
     print "<th class=\"thead\">CPU</th>";
     $cols++;
   }
   if($ch == 'o') {
     print "<th class=\"thead\">OS</th>";
     $cols++;
   }
   if($ch == 'v') {
     print "<th class=\"thead\">Version</th>";
     $cols++;
   }
 }
?>
       <th class="thead" align="right">First Unit</th>
       <th class="thead" align="right">Last Unit</th>
<?
 if($show_yesterday){ print "<th class=\"thead\">Yesterday</th>";}
 if($show_total) { print "<th class=\"thead\">Total ".$gproj->get_scaled_unit_name()."</th>";}
 print '</tr>';
 $total_yesterday = 0;
 $total_overall = 0;
 for ($i = 0; $i<$rows; $i++) {

?>
<tr class="<? echo row_background_color($i)?>">
<?
 $gdb->data_seek($i);
 $par = $gdb->fetch_object();

 $decimal_places=0;
 $firstd = $par->first;
 $lastd = $par->last;

 for($j=0; $j < strlen($view); $j++) {
   $ch = substr($view,$j,1);
   if($ch == 'c') print "<td><img alt=\"\" height=\"14\" width=\"14\" src=\"/images/icons/cpu/$par->cpuimage\"> $par->cpuname</td>\n";
   if($ch == 'o') print "<td><img alt=\"\" height=\"14\" width=\"14\" src=\"/images/icons/os/$par->osimage\"> $par->osname</td>\n";
   if($ch == 'v') print "<td>$par->ver</td>\n";
 }

 print "
 	<td align=\"right\">$firstd</td>
 	<td align=\"right\">$lastd</td>
 ";

 if($show_yesterday) {
   print "<td align=\"right\">" . number_style_convert( (double) $par->yesterday ) . "</td>\n";
   $total_yesterday += (double) $par->yesterday ;
 }
 if($show_total) {
   print "<td align=\"right\">" . number_style_convert( (double) $par->total ) . "</td>\n";
   $total_overall += (double) $par->total ;
 }
 print "</tr>";
}

 if($show_yesterday or $show_total) {

   $padding = (int) $cols - 1;
   print "
   <tr>
	<td class= \"tfoot\" align=\"right\" colspan=\"$padding\">Total</td>";

   if ($show_yesterday) {
     print "<td class= \"tfoot\" align=\"right\">" . number_style_convert($total_yesterday, 0) . "</td>\n";
   }
   if ($show_total) {
     print "<td class= \"tfoot\" align=\"right\">" . number_style_convert($total_overall, 0) . "</td>\n";
   }
 }

   print "
   </tr>
  </table>
";
   include "../templates/footer.inc";
?>
