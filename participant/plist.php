<?
# vi: ts=2 sw=2 tw=120 syntax=php
# $Id: plist.php,v 1.20 2003/04/20 21:31:37 paul Exp $

$hour = 3;
$now = getdate();
if ($now['hours'] >= 0 and $now['hours'] < $hour) {
	$now = time();
} else {
	$now = time() + 86400;
}

Header("Cache-Control: must-revalidate");
Header("Expires: " . gmdate("D, d M Y", $now) . " $hour:00 GMT");

// Variables Passed in url:
//   low == lowest rank used
//   limit == how many lines to retuwn
//   source == "y" for yseterday, all other values ignored.

include "../etc/limit.inc";	// Handles low, high, limit calculations

include "../etc/config.inc";
include "../etc/modules.inc";
include "../etc/project.inc";

if ("$source" == "y") {
  $title = "Participant Listing by Yesterday's Rank: $lo to $hi";
  $QSlist = "select r.id, r.first_date as first, r.LAST_DATE as last, r.WORK_TODAY as blocks,
	datediff(day, r.FIRST_DATE, r.LAST_DATE)+1 as Days_Working,
	r.DAY_RANK as rank, r.DAY_RANK_PREVIOUS - r.DAY_RANK as change,
	p.email, p.listmode as listas, p.contact_name
	from Email_Rank r, STATS_Participant p
	where DAY_RANK <= $hi and DAY_RANK >= $lo and r.id = p.id and p.listmode < 10 and r.PROJECT_ID = $project_id
	order by r.DAY_RANK, r.WORK_TODAY desc";
} else {
  $source = "o";
  $title = "Participant Listing by Overall Rank: $lo to $hi";
  $QSlist = "select r.id, r.first_date as first, r.LAST_DATE as last, r.WORK_TOTAL as blocks,
	datediff(day, r.FIRST_DATE, r.LAST_DATE)+1 as Days_Working,
	r.OVERALL_RANK as rank, r.OVERALL_RANK_PREVIOUS - r.OVERALL_RANK as change,
	p.email, p.listmode as listas, p.contact_name
	from Email_Rank r, STATS_Participant p
	where OVERALL_RANK <= $hi and OVERALL_RANK >= $lo and r.id = p.id and p.listmode < 10 and r.PROJECT_ID = $project_id
	order by r.OVERALL_RANK, r.WORK_TOTAL desc";
}
 $lastupdate = last_update('e');
 include "../templates/header.inc";
 sybase_query("set rowcount 100");
 $result = sybase_query($QSlist);

 err_check_query_results($result);

 $rows = sybase_num_rows($result);

?>
     <div><br></div>
      <table border="1" cellspacing="0" cellpadding="1" width="100%" class="tborder">
      <tr>
       <th class="thead">Rank</th>
       <th class="thead">Participant</th>
       <th class="thead" align="right">First Unit</th>
       <th class="thead" align="right">Last Unit</th>
       <th class="thead" align="right">Days</th>
       <th class="thead" align="right"><?=$proj_scaled_unit_name?></th>
      </tr>
<?

 $totalblocks = (double) 0;

 for ($i = 0; $i<$rows; $i++) {

	?>
	<tr class="<?=row_background_color($i)?>">
	<?
	sybase_data_seek($result,$i);
	$par = sybase_fetch_object($result);

participant_listas($par->listas, $par->email,$par->id,$par->contact_name) . " -->\n",$debug);

        $parid = 0+$par->id;
	$totalblocks = $totalblocks + (double) $par->blocks * $proj_scale;
	$decimal_places=0;
	$blocks=number_style_convert( (double) $par->blocks * $proj_scale );
        $firstd = substr($par->first,4,2);
	$firstm = substr($par->first,0,3);
	$firsty = substr($par->first,7,4);
        $lastd = substr($par->last,4,2);
	$lastm = substr($par->last,0,3);
	$lasty = substr($par->last,7,4);

?>
<td><?=$par->rank?>
<?=html_rank_arrow($par->change);?>

<td>
<a href="psummary.php?project_id=<?=$project_id?>&amp;id=<?=$parid?>"><?=participant_listas($par->listas,
			$par->email,$par->id,$par->contact_name)?></a></td>
		<td align="right"><? echo "$firstd-$firstm-$firsty"?></td>
		<td align="right"><? echo "$lastd-$lastm-$lasty"?></td>
		<td align="right"><?=$par->Days_Working?></td>
		<td align="right"><?=$blocks?></td>
		</tr>
 <?
}
 $totalblocks = number_format($totalblocks, 0);
 if ( $lo > $rows ) {
   $btn_back = "<a href=\"$myname?project_id=$project_id&amp;low=$prev_lo&amp;limit=$limit&amp;source=$source\">Back $limit</a>";
 } else {
   $btn_back = "&nbsp;";
 }

 if ( 2 > 1 ) {
   $btn_fwd = "<a href=\"$myname?project_id=$project_id&amp;low=$next_lo&amp;limit=$limit&amp;source=$source\">Next $limit</a>";
 } else {
   $btn_fwd = "&nbsp;";
 }

?>
	 <tr>
	  <td class="tfoot"><? echo "$lo-$hi"?></td>
	  <td class="tfoot" align="right" colspan="4">Total</td>
	  <td class="tfoot" align="right"><?=$totalblocks?></td>
	 </tr>
	 <tr>
	  <td class="tfoot"><?=$btn_back?></td>
	  <td class="tfoot" colspan="4">&nbsp;</td>
	  <td class="tfoot" align="right"><?=$btn_fwd?></td>
	 </tr>
	</table>
<?include "../templates/footer.inc";?>
