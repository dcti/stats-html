<? 
// $Id: psummary_xml.php,v 1.2 2008/04/15 11:55:54 thejet Exp $
// Variables Passed in url:
// id == Participant ID
// project_id == Project ID
// show_friends == Show Friend Data
// show_neighbors == Show Neighbor Data
// Author: Simon Trigona

include "../etc/global.inc";
$random_stats = 0;
include "../etc/modules.inc";
include "../etc/project.inc";
include "../etc/participant.php";


// output the XML header
header("Content-type: text/xml", true);
print("<"."?xml version=\"1.0\" encoding=\"ISO-8859-1\"?".">\n");
print("<!-- WARNING: This code is experimental and the schema is subject to change at any time -->\n");

// Get the participant's record from STATS_Participant and store it in $gpart
$gpart = new Participant($gdb, $gproj, $id);
if($gpart->get_id() == 0) {
  // This blew up!!!
  print('<error>Bah!</error>');
  exit(1);
}

$lastupdate = last_update('ec');

// Is this person retired?
if($gpart -> get_retire_to() > 0) {
    header("Location: psummary.php?project_id=$project_id&id=".$gpart -> get_retire_to());
    exit();
}

$gpartstats = new ParticipantStats($gdb, $gproj, $id, null);

// Process data and store in variables
$name = $gpart->get_display_name();
$motto = $gpart->get_motto();
$prnk = $gpartstats->get_stats_item("overall_rank");
$prnkchg = $gpartstats->get_stats_item("overall_change");
$drank = $gpartstats->get_stats_item("day_rank");
$drankchg = $gpartstats->get_stats_item("day_change");
$n_yesterday = (double) $gpartstats->get_stats_item("work_today") * $gproj->get_scale();
$yesterday = round($n_yesterday,0);
$n_blocks = (double) $gpartstats->get_stats_item("work_total") * $gproj->get_scale();
$blocks = round($n_blocks,0);
$first = $gpartstats->get_stats_item("first_date");
$last = $gpartstats->get_stats_item("last_date");
$days_working = $gpartstats->get_stats_item("days_working");
?>
<participant-summary id="<?= $id ?>" project="<?= $gproj->get_name() ?>" project-id="<?= $gproj->get_id() ?>" last-update="<?= $lastupdate ?>" >
    <name><![CDATA[<?= safe_display($name) ?>]]></name>
    <motto><![CDATA[<?= safe_display($motto) ?>]]></motto>
    <stats>
        <stat name="rank-project" unit="" value="<?= $prnk ?>" change="<?= $prnkchg ?>"/>
	<stat name="rank-project-day" unit="" value="<?= $drank ?>" change="<?= $drankchg ?>" />
        <stat name="work-overall" unit="<?= $gproj->get_scaled_unit_name() ?>" value="<?= $blocks ?>"/>
        <stat name="work-day" unit="<?= $gproj->get_scaled_unit_name() ?>" value="<?= $yesterday ?>"/>
        <stat name="work-first" unit="" value="<?= $first ?>"/>
        <stat name="work-last" unit="" value="<?= $last ?>"/>
        <stat name="days-working" unit="" value="<?= $days_working ?>"/>
    </stats>
<?
if(array_key_exists("show_friends", $_GET))
{
  $friends = $gpart->get_friends();
  $friendCnt = count($friends);
  echo("    <friends>\n");
  for($i = 0; $i < $friendCnt; $i++)
  {
    $curPar = $friends[$i];
    $curStats = $curPar->get_current_stats();
?>
        <participant id="<?= $curPar->get_id() ?>" >
            <name><![CDATA[<?= safe_display($curPar->get_display_name()) ?>]]></name>
            <stats>
                <stat name="rank-project" unit="" value="<?= $curStats->get_stats_item("overall_rank") ?>" change="<?= $curStats->get_stats_item("overall_change") ?>"/>
	        <stat name="rank-project-day" unit="" value="<?= $curStats->get_stats_item("day_rank") ?>" change="<?= $curStats->get_stats_item("day_change") ?>" />
                <stat name="work-overall" unit="<?= $gproj->get_scaled_unit_name() ?>" value="<?= round($curStats->get_stats_item("work_total") * $gproj->get_scale(), 0) ?>"/>
                <stat name="work-day" unit="<?= $gproj->get_scaled_unit_name() ?>" value="<?= round($curStats->get_stats_item("work_today") * $gproj->get_Scale(), 0) ?>"/>
                <stat name="work-first" unit="" value="<?= $curStats->get_stats_item("first_date"); ?>"/>
                <stat name="work-last" unit="" value="<?= $curStats->get_stats_item("last_date") ?>"/>
                <stat name="days-working" unit="" value="<?= $curStats->get_stats_item("days_working") ?>"/>
            </stats>
        </participant>
<?
    unset($curPar);
    unset($curStats);
  } 
  echo("    </friends>\n");
}

if(array_key_exists("show_neighbors", $_GET))
{
  $neighbors = $gpart->get_neighbors();
  $neighborCnt = count($neighbors);
  echo("    <neighbors>\n");
  for($i = 0; $i < $neighborCnt; $i++)
  {
    $curPar = $neighbors[$i];
    $curStats = $curPar->get_current_stats();
?>
        <participant id="<?= $curPar->get_id() ?>" >
            <name><![CDATA[<?= safe_display($curPar->get_display_name()) ?>]]></name>
            <stats>
                <stat name="rank-project" unit="" value="<?= $curStats->get_stats_item("overall_rank") ?>" change="<?= $curStats->get_stats_item("overall_change") ?>"/>
	        <stat name="rank-project-day" unit="" value="<?= $curStats->get_stats_item("day_rank") ?>" change="<?= $curStats->get_stats_item("day_change") ?>" />
                <stat name="work-overall" unit="<?= $gproj->get_scaled_unit_name() ?>" value="<?= round($curStats->get_stats_item("work_total") * $gproj->get_scale(), 0) ?>"/>
                <stat name="work-day" unit="<?= $gproj->get_scaled_unit_name() ?>" value="<?= round($curStats->get_stats_item("work_today") * $gproj->get_Scale(), 0) ?>"/>
                <stat name="work-first" unit="" value="<?= $curStats->get_stats_item("first_date"); ?>"/>
                <stat name="work-last" unit="" value="<?= $curStats->get_stats_item("last_date") ?>"/>
                <stat name="days-working" unit="" value="<?= $curStats->get_stats_item("days_working") ?>"/>
            </stats>
        </participant>
<?
    unset($curPar);
    unset($curStats);
  } 
  echo("    </neighbors>\n");
}
?>
</participant-summary>
