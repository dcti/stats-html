<?

 // $Id: pc_countries.php,v 1.12 2002/06/19 02:26:58 decibel Exp $

 $outname = "countries";

 include "../etc/config.inc";
 include "../etc/modules.inc";
 include "../etc/project.inc";

 sybase_query("set rowcount 0");

 $qs = "select distinct code, country, count(country) as recs, sum(work_total)/$proj_divider as units_total,
		sum(work_today)/$proj_divider as units_today
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

	display_last_update('e');
 print "
	 <center>
	  <table border=\"1\" cellspacing=\"0\" bgcolor=$header_bg>
	   <tr>
            <td colspan=2><font $header_font>&nbsp</font></td>";
 if ($source == 'y') {
   print "
	    <td align=\"center\" colspan=2><a href=\"$outname.php?project_id=$project_id&source=o\">
              <font $header_font>Overall</font>
	    </a></td>
	    <td align=\"center\" colspan=2><font $header_font>Yesterday</font></td>";
 } else {
   print "
	    <td align=\"center\" colspan=2><font $header_font>Overall</font></td>
	    <td align=\"center\" colspan=2><a href=\"$outname.php?project_id=$project_id&source=y\">
              <font $header_font>Yesterday</font>
	    </a></td>";
 }
?> 
           </tr>
	   <tr>
	    <th>Nationality</th>
	    <th align="center">People</th>
	    <th align="center"><?=$proj_unitname?></th>
	    <th align="center"><?=$proj_unitname?>/Person</th>
	    <th align="center"><?=$proj_unitname?></th>
	    <th align="center"><?=$proj_unitname?>/Person</th>
	   </tr>
<?
 for ($i = 0; $i < $countries; $i++) {
   print "<tr class=" . row_background_color($i) . ">";
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
?>   
	    <td><img src="<?=$icofn?>" alt="<?=$par->code?>" height=14 width=14> <?=$par->country?></td>
	    <td align="right"><?=$f_recs?></td>
	    <td align="right"><?=$f_units_total?></td>
	    <td align="right"><?=$f_blockavg_total?></td>
	    <td align="right"><?=$f_units_today?></td>
	    <td align="right"><?=$f_blockavg_today?></td>
	   </tr>
<?
 } 
?>
	</table>
           <table width="60%">
            <tr>
             <td>
              <font size="-1">
               Note: Nationalities listed on this page are only reflective of
               those participants who have designated their nationality when
               <a href="/participant/pedit.php">editing</a> their participant information.
               No attempt has been made to derive nationalities from participant
               email addresses.
              </font>
             </td>
            </tr>
           </table>
          </center>

