<?

# $Id: platformlist.php,v 1.15 2002/06/05 20:23:36 paul Exp $

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

 if (!$view) {
   $view = "c";
 }

 include "../etc/config.inc";
 include "../etc/modules.inc";
 include "../etc/project.inc";

 $lastupdate = last_update('e');
 $title = "CPU Participation";

 include "../templates/header.inc";
 
 $selstr = "select";
 $frostr = "from Platform_Summary p,";
 $whestr = "where p.PROJECT_ID = $project_id";
 $grostr = "group by";
 $ordstr = "order by";
 $show_yesterday = 0;
 $show_total = 0;
 if("$source" == "y") {
   $whestr .= " and WORK_YESTERDAY > 0";
 }

 for($i=0; $i < strlen($view); $i++) {
   $ch = substr($view,$i,1);
   if($ch == 'c') $fieldname = "p.CPU";
   if($ch == 'o') $fieldname = "p.OS";
   if($ch == 'v') $fieldname = "p.VER";
   if($ch == 'y') {
     $fieldname = "sum(p.WORK_YESTERDAY)/$proj_divider as yesterday";
     $show_yesterday = 1;
   }
   if($ch == 't') {
     $fieldname = "sum(p.WORK_TOTAL)/$proj_divider as total";
     $show_total = 1;
   }

   $selstr .= " $fieldname,";
   if($ch != 'y' and $ch != 't') $grostr .= " $fieldname,";
 }


 $selstr .= " min(FIRST_DATE) as first, max(LAST_DATE) as last,";

 for($i=0; $i < strlen($view); $i++) {
   $ch = substr($view,$i,1);
   if($ch == 'c') {
     $selstr = "$selstr min(c.name) as cpuname, min(c.image) as cpuimage,";
     $frostr = "$frostr STATS_cpu c,";
     $whestr = "$whestr and c.cpu = p.CPU";
     $ordstr = "$ordstr p.CPU,";
   }
   if($ch == 'o') {
     $selstr = "$selstr min(o.name) as osname, min(o.image) as osimage,";
     $frostr = "$frostr STATS_os o,";
     $whestr = "$whestr and o.os = p.OS";
     $ordstr = "$ordstr p.OS,";
   }
   if($ch == 'v') {
     $ordstr = "$ordstr p.VER,";
   }
   if($ch == 'y') {
     $ordstr = "$ordstr yesterday desc,";
   }
   if($ch == 't') {
     $ordstr = "$ordstr total desc,";
   }
 }

 $selstr = substr($selstr,0,strlen($selstr)-1);
 $frostr = substr($frostr,0,strlen($frostr)-1);
 $grostr = substr($grostr,0,strlen($grostr)-1);
 $ordstr = substr($ordstr,0,strlen($ordstr)-1);

 $QSlist = "$selstr $frostr $whestr $grostr $ordstr";
 $result = sybase_query($qs);
 $par = sybase_fetch_object($result);
 sybase_query("set rowcount 0");
 $result = sybase_query($QSlist);

 debug_text("<!-- QSlist: $QSlist, result: $result -->", $debug);
 err_check_query_results($result);
 
 $rows = sybase_num_rows($result);

 # Total number of columns in table, not counting yesterday or total columns. Start at 2 to account for first and last.
 $cols = 2;
 print "
    <center>
     <br>
     <table border=\"1\" cellspacing=\"0\" cellpadding\"0\" bgcolor=$header_bg>
      <tr>";
 for($i=0; $i < strlen($view); $i++) {
   $ch = substr($view,$i,1);
   if($ch == 'c') {
     print "<th>CPU</th>";
     $cols++;
   }
   if($ch == 'o') {
     print "<th>OS</th>";
     $cols++;
   }
   if($ch == 'v') {
     print "<th>Version</th>";
     $cols++;
   }
 }
?> 
       <th align="right">First Unit</th>
       <th align="right">Last Unit</th>
<?
 if($show_yesterday){ print "<th>Yesterday</th>";}
 if($show_total) { print "<th>Total $proj_unitname</th>";}
 print '</tr>';
 $total_yesterday = 0;
 $total_overall = 0;
 for ($i = 0; $i<$rows; $i++) {

?>
<tr class="<? echo row_background_color($i)?>">
<?
 sybase_data_seek($result,$i);
 $par = sybase_fetch_object($result);

 $decimal_places=0;
 $firstd = sybase_date_format_long($par->first);
 $lastd = sybase_date_format_long($par->last);

 for($j=0; $j < strlen($view); $j++) {
   $ch = substr($view,$j,1);
   if($ch == 'c') print "<td><img alt=\"\" height=\"14\" width=\"14\" src=\"/images/icons/cpu/$par->cpuimage\"> $par->cpuname</td>\n";
   if($ch == 'o') print "<td><img alt=\"\" height=\"14\" width=\"14\" src=\"/images/icons/os/$par->osimage\"> $par->osname</td>\n";
   if($ch == 'v') print "<td>$par->VER</td>\n";
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
   $totalblocks = number_format($totalblocks, 0);
  
   $padding = (int) $cols - 1;
   print "
   <tr bgcolor=$footer_bg>
    <td align=\"right\" colspan=\"$padding\"><font $footer_font>Total</font></td>";

   if ($show_yesterday) {
     print "
    <td align=\"right\"><font $footer_font>" . number_style_convert($total_yesterday, 0) . "</font></td>";
   }
   if ($show_total) {
     print "
    <td align=\"right\"><font $footer_font>" . number_style_convert($total_overall, 0) . "</font></td>";
   }
 }

   print "
   </tr>
  </table>
";
   include "../templates/footer.inc";
?>
