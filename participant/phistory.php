<? 
// vi: ts=2 sw=2 tw=120
// $Id: phistory.php,v 1.17 2003/08/25 18:17:14 thejet Exp $
// Variables Passed in url:
// id == Participant ID
// @todo -c Implement .check type of unit name
// @todo -c Implement .scale units

include "../etc/config.inc";
include "../etc/modules.inc";
include "../etc/project.inc";
include "../etc/participant.php";
include "../etc/participantstats.php";

if(isset($lockfile)) {
    if(file_exists($lockfile)) {
        $title = "Participant History (Unavailable)";
        include "../templates/header.inc";
        include "../templates/updating.inc";
        exit;
    } 
} 

$gpart = new Participant($gdb, $gproj, $id);
$gpartstats = new ParticipantStats($gdb, $gproj, $id, null);
$history = $gpartstats -> get_stats_history();

if($gpart->get_retire_to() > 0) {
    header("Location: http://stats.distributed.net/participant/phistory.php?project_id=$project_id&amp;id=$retire_to");
    exit();
} 

$lastupdate = last_update('ec');

$title = "Participant History for ".$gpart->get_display_name();

include "../templates/header.inc";

?> 

<!-- IMPORTANT NOTE TO SCRIPTERS!
This page, like many stats pages, has a version which is far more suitable
for machine parsing.  Please try the url:
http://stats.distributed.net/participant/phistory_raw.php?project_id=$project_id&id=$id
-->
    <p align="center"><a href="psummary.php?project_id=<?=$project_id?>&amp;id=<?=$id?>">View <?=$gpart->get_display_name()?>'s Participant Summary</a></p>
      <table align="center" border="1" cellspacing="0" cellpadding="1" >
      <tr>
       <th class="thead">Date</th>
       <th class="thead" align="right"><?=$gproj->get_scaled_unit_name()?></th>
       <th class="thead">&nbsp;</th>
      </tr>
<?

$maxwork_units = (double) 0;
foreach ($history as $histrow)
{
    if($histrow->work_units > $maxwork_units) {
        $maxwork_units = $histrow->work_units;
    } 
}

$i = 0; 
foreach ($history as $histrow)
{
    $work_units_fmt = number_format($histrow->work_units*$gproj->get_scale(), 0);
    $date_fmt = $histrow->stats_date;
    //$date_fmt = sybase_date_format_long($date);
    $width = (int) (((double)$histrow->work_units / $maxwork_units) * 200) + 1;
    ?>
      <tr class=<?=row_background_color($i);?>>
        <td><?=$date_fmt?></td>
        <td align="right"><?=$work_units_fmt?></td>
        <td align="left"><img src="/images/bar.jpg" height="8" width="<?=$width?>" alt=""></td>
      </tr>
<?
	$i++;
	} 
?>
    </table>
    <p align="center"><a href="psummary.php?project_id=<?=$project_id?>&amp;id=<?=$id?>">View <?=$gpart->get_display_name()?>'s Participant Summary</a></p>
<?include "../templates/footer.inc";
?>
