<?
 # $Id: psearch.php,v 1.17 2003/10/21 17:42:08 thejet Exp $

 // Variables Passed in url:
 //   st == Search Term

 include "../etc/config.inc";
 include "../etc/modules.inc";
 include "../etc/project.inc";
 include "../etc/participant.php";

 $title = "Participant Search: [".safe_display($st)."]";

 $lastupdate = last_update('e');

if (strlen($st) < 3) {
 include "../templates/header.inc";
	?>
    <table align="center" width="400" border="0"><tr><td>
	<h2>There was an error processing your request</h2>
	<p>Search Text must be at least 3 characters</p>
	</p></td></tr></table>
	<?
	include "../templates/footer.inc";
	exit;
}

// Execute the procedure to get the results
$result = Participant::get_search_list($st, 50, $gdb, $gproj);
$rows = count($result);

 if($rows == 1) {
	# Only one hit, let's jump straight to psummary
	$id = (int) $result[0]->get_id();
	header("Location: psummary.php?project_id=".$gproj->get_id()."&id=$id");
	exit;
 }

 include "../templates/header.inc";

 ?> 
  <div style="text-align: center;">
     <br />
     <table border="1" cellspacing="0" style="margin:auto" width="90%">
     <tr>
       <th class="thead">Rank</th>
       <th class="thead">Participant</th>
       <th class="thead" align="right">First Unit</th>
       <th class="thead" align="right">Last Unit</th>
       <th class="thead" align="right">Days</th>
       <th class="thead" align="right"><?=$gproj->get_scaled_unit_name()?></th>
      </tr>
 <?

 $totalblocks = (double) 0;
 if($rows <= 0)
 {
   echo "<tr><td colspan=\"6\">No Matching Records Found</td></tr>\n";
 }
 for ($i = 0; $i<$rows; $i++) {
   $ROWparticipant = $result[$i];
   $ROWstats = $ROWparticipant->get_current_stats();
   $id = (int) $ROWparticipant->get_id();
   $totalblocks += (double) $ROWstats->get_stats_item("work_total") * $gproj->get_scale();

	?>
	<tr class="<?=row_background_color($i)?>">
         <td align="left"><?=$ROWstats->get_stats_item("overall_rank")?><?=html_rank_arrow($ROWstats->get_stats_item("rank_change"))?></td>
         <td align="left"><a href="psummary.php?project_id=<?=$gproj->get_id()?>&amp;id=<?=$id?>"><?=safe_display($ROWparticipant->get_display_name())?></a></td>
         <td align="right"><?=$ROWstats->get_stats_item("first_date")?></td>
         <td align="right"><?=$ROWstats->get_stats_item("last_date")?></td>
         <td align="right"><?=number_style_convert($ROWstats->get_stats_item("days_working"))?></td>
         <td align="right"><?=number_style_convert( (double) $ROWstats->get_stats_item("work_total") * $gproj->get_scale())?></td>
        </tr>
        <?
 }
?>
	 <tr>
	  <td class="tfoot"><?=$i?></td>
	  <td class="tfoot" colspan="4" align="right">Total</td>
	  <td class="tfoot" align="right"><? echo number_format($totalblocks, 0) ?></td>
	 </tr>
	</table>
<?
 if( $rows == 0 ) {
?>   
	<p>
	 <a href="http://www.distributed.net/FAQ/">Confused?  Look here</a>
	</p>
<?
 }
?>
<?include "../templates/footer.inc";?>
