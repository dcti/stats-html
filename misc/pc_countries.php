<?

 // $Id: pc_countries.php,v 1.14 2003/08/31 22:48:07 paul Exp $

 $outname = "countries";

 include "../etc/config.inc";
 include "../etc/modules.inc";
 include "../etc/project.inc";

 $qs = "select distinct code, country, count(country) as recs, sum(work_total)* ".$gproj->get_scale()." as units_total,
		sum(work_today)* ".$gproj->get_scale()." as units_today
	from STATS_participant, STATS_country,email_RANK
	where retire_to = 0
		and dem_country IS NOT NULL
		and dem_country = code
		and email_RANK.id = STATS_participant.id
		and email_RANK.project_id = $project_id
	group by code, country ";
 if ("$source" == "y") {
   $qs .= "	order by sum(work_today)* ".$gproj->get_scale()." desc";
 } else {
   $qs .= "	order by sum(work_total)* ".$gproj->get_scale()." desc";
 };

 display_last_update('e');
 print "
	 <center>
	  <table border=\"1\" cellspacing=\"0\" >
	   <tr>
            <td colspan=2>&nbsp</td>";
 if ($source == 'y') {
   print "
	    <td align=\"center\" colspan=2><a href=\"$outname.php?project_id=$project_id&source=o\">Overall</a></td>
	    <td align=\"center\" colspan=2>Yesterday</td>";
 } else {
   print "
	    <td align=\"center\" colspan=2>Overall</td>
	    <td align=\"center\" colspan=2><a href=\"$outname.php?project_id=$project_id&source=y\">Yesterday</a></td>";
 }
?> 
           </tr>
	   <tr>
	    <th>Nationality</th>
	    <th align="center">People</th>
	    <th align="center"><?=$gproj->get_scaled_unit_name()?></th>
	    <th align="center"><?=$gproj->get_scaled_unit_name()?>/Person</th>
	    <th align="center"><?=$gproj->get_scaled_unit_name()?></th>
	    <th align="center"><?=$gproj->get_scaled_unit_name()?>/Person</th>
	   </tr>
<?

 $country = $gdb->query($qs);
 $countries = $gdb->num_rows();

 for ($i = 0; $i < $countries; $i++) {
   print "<tr class=" . row_background_color($i) . ">";
   $gdb->data_seek($i);
   $par = $gdb->fetch_object($country);
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

