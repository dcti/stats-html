<?php

 $outname = "countries";

 include "../etc/global.inc";
 include "../etc/modules.inc";
 include "../etc/project.inc";

 $qs = "select distinct code, country, count(country) as recs, sum(work_total)* ".$gproj->get_scale()." as units_total,
		sum(work_today)* ".$gproj->get_scale()." as units_today
	from STATS_participant, STATS_country,email_RANK
	where retire_to IS NULL
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

?>
  <div class="table-responsive">
  <table class="table table-bordered table-striped">
<?php
print "
	   <thead>
            <th colspan=2>&nbsp</td>";
 if ($source == 'y') {
   print "
	    <th class=\"thead\" colspan=2><a href=\"$outname.php?project_id=$project_id&source=o\">Overall</a></td>
	    <th class=\"thead\" colspan=2>Yesterday</td>";
 } else {
   print "
	    <th class=\"thead\" colspan=2>Overall</td>
	    <th class=\"thead\" colspan=2><a href=\"$outname.php?project_id=$project_id&source=y\">Yesterday</a></td>";
 }
?> 
           </thead>
	   <thead>
	    <th class="thead">Nationality</th>
	    <th class="thead" align="center">People</th>
	    <th class="thead" align="center"><?=$gproj->get_scaled_unit_name()?></th>
	    <th class="thead" align="center"><?=$gproj->get_scaled_unit_name()?>/Person</th>
	    <th class="thead" align="center"><?=$gproj->get_scaled_unit_name()?></th>
	    <th class="thead" align="center"><?=$gproj->get_scaled_unit_name()?>/Person</th>
	   </thead>
<?

 $country = $gdb->query($qs);
 $countries = $gdb->num_rows();

 for ($i = 0; $i < $countries; $i++) {
   print "<tr>";
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

<div class="well">
               Note: Nationalities listed on this page are only reflective of
               those participants who have designated their nationality when
               <a href="/participant/pedit.php">editing</a> their participant information.
               No attempt has been made to derive nationalities from participant
               email addresses.
</div>

<div class="well">
 <?php display_last_update('e'); ?>
</div>
