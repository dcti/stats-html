<?

 // $Id: pc_countries.php,v 1.3 2002/03/09 12:49:32 paul Exp $

 $outname = "countries";

 include "../etc/config.inc";
 include "../etc/modules.inc";
 include "etc/project.inc";

 sybase_pconnect($interface, $username, $password);

 sybase_query("set rowcount 0");

 $title = "Participating Countries";

 $qs = "p_lastupdate e, @contest='new', @project_id=$project_id";
 $result = sybase_query($qs);
 $par = sybase_fetch_object($result);
 $lastupdate = sybase_date_format_long($par->lastupdate);
 include "templates/header.inc";

 $qs = "select distinct code, country, count(country) as recs, sum(work_total)/$divider as units_total,
		sum(work_today)/$divider as units_today
	from STATS_participant, STATS_country,email_RANK
	where retire_to = 0
		and dem_country <> NULL
		and dem_country = code
		and email_RANK.id = STATS_participant.id
		and email_RANK.project_id = $project_id
	group by code, country ";
 if ("$source" == "y") {
   $qs .= "	order by sum(work_today) desc";
 } else {
   $qs .= "	order by sum(work_total) desc";
 };

 $country = sybase_query($qs);
 $countries = sybase_num_rows($country);

 print "
	 <center>
	  <table border=\"1\" cellspacing=\"0\" bgcolor=$header_bg>
	   <tr>
            <td colspan=2><font $header_font>&nbsp</font></td>";
 if ($source == y) {
   print "
	    <td align=\"center\" colspan=2><a href=\"" . $outname . ".html\">
              <font $header_font>Overall</font>
	    </a></td>
	    <td align=\"center\" colspan=2><font $header_font>Yesterday</font></td>";
 } else {
   print "
	    <td align=\"center\" colspan=2><font $header_font>Overall</font></td>
	    <td align=\"center\" colspan=2><a href=\"" . $outname . "-y.html\">
              <font $header_font>Yesterday</font>
	    </a></td>";
 }
 print "
           </tr>
	   <tr>
	    <td><font $header_font>Nationality</font></font></td>
	    <td align=\"center\"><font $header_font>People</font></td>
	    <td align=\"center\"><font $header_font>Gnodes</font></td>
	    <td align=\"center\"><font $header_font>Gnodes/Person</font></td>
	    <td align=\"center\"><font $header_font>Gnodes</font></td>
	    <td align=\"center\"><font $header_font>Gnodes/Person</font></td>
	   </tr>";

 for ($i = 0; $i < $countries; $i++) {
   print "<tr bgcolor=" . row_background_color($i) . ">";
   sybase_data_seek($country,$i);
   $par = sybase_fetch_object($country);
   $recs = (int) $par->recs;
   $units_total = (double) $par->units_total;
   $units_today = (double) $par->units_today;
   $f_recs = number_style_convert($recs);
   $f_units_total = number_style_convert($units_total);
   $f_blockavg_total = number_style_convert($units_total/$recs);
   $f_units_today = number_style_convert($units_today);
   $f_blockavg_today = number_style_convert($units_today/$recs);
   $icofn = "/images/icons/flags/$par->code.gif";
   if(!file_exists("..$icofn")) {
     $icofn = "/images/icons/flags/unknown.gif";
   }
   print "
	    <td><img src=\"$icofn\" alt=\"$par->code\" height=14 width=14> $par->country</td>
	    <td align=\"right\">$f_recs</td>
	    <td align=\"right\">$f_units_total</td>
	    <td align=\"right\">$f_blockavg_total</td>
	    <td align=\"right\">$f_units_today</td>
	    <td align=\"right\">$f_blockavg_today</td>
	   </tr>";
 } 


 print "
	   </table>
	   <table width=\"60%\">
	    <tr>
	     <td>
	      <font size=\"-1\">
	       Note: Nationalities listed on this page are only reflective of
	       those participants who have designated their nationality when
	       <a href=\"/pedit.php\">editing</a> their participant information.
	       No attempt has been made to derive nationalities from participant
	       email addresses.
	      </font>
	     </td>
	    </tr>
	   </table>
 	  </center>
	 </body>
	</html>";
?>
