<?
// vi: ts=2 sw=2 tw=120
// $Id: tmsummary_xml.php,v 1.8 2004/04/21 15:49:04 thejet Exp $

// Variables Passed in url:
//  team == team id to display
//error_reporting(0);
include "../etc/config.inc";
include "../etc/modules.inc";
include "../etc/markup.inc";
include "../etc/project.inc";
include "../etc/team.php";
include "../etc/teamstats.php";

header("Content-type: text/xml", true);
print("<"."?xml version=\"1.0\" encoding=\"US-ASCII\"?".">\n");
print("<!-- WARNING: This code is experimental and the schema is subject to change at any time -->\n");

// Create the team and team stats objects
$team = new Team($gdb, $gproj, $tm);

// To support team renumbering, reset $tm based on retrieved id
$tm = $team->get_id();

$title = "Team #$tm Summary";
$lastupdate = last_update('t');

$stats = $team->get_current_stats();

if ($team->get_id() == 0) {
	echo "<error>That team is not known.</error>";
	exit;
}

$neighbors = $team->get_neighbors();
$numneighbors = count($neighbors);
?>
<team-summary id="<?= $team->get_id() ?>" project="<?= $gproj->get_name() ?>" project-id="<?= $gproj->get_id() ?>" date="<?= $lastupdate ?>">
  <name><?= safe_display($team->get_name()) ?></name>
  <logo><?= safe_display($team->get_logo()) ?></logo>
  <description><![CDATA[<?= markup_to_html($team->get_description()) ?>]]></description>
  <contact><?= safe_display($team->get_contact_email()) ?></contact>
  <membership><?if ($team->get_show_members()=="NO") {?>private<?} else { if ($team->get_show_members()=="PAS") {?>password<?} else {?>public<?} }?></membership>
  <stats date="<?= safe_display($stats->get_stats_item("last_date")) ?>">
    <stat name="overall-rank" unit="" value="<?= $stats->get_stats_item("overall_rank") ?>" change="<?= $stats->get_stats_item('overall_rank_previous') - $stats->get_stats_item("overall_rank") ?>"/>
    <stat name="overall-work" unit="<?= $gproj->get_scaled_unit_name() ?>" value="<?= round($stats->get_stats_item("work_total") * $gproj->get_scale(),0) ?>"/>

    <? if ($stats->get_stats_item("work_today") > 0) { ?>
    <stat name="day-rank" unit="" value="<?= $stats->get_stats_item("day_rank") ?>" change="<?= $stats->get_stats_item('day_rank_previous') - $stats->get_stats_item("day_rank") ?>"/>
    <stat name="day-work" unit="<?= $gproj->get_scaled_unit_name() ?>" value="<?= round($stats->get_stats_item("work_today") * $gproj->get_scale(),0) ?>"/>
    <? } ?>

    <stat name="time-working" unit="days" value="<?= round($stats->get_stats_item("days_working"),0) ?>"/>
    <? if ($gproj->get_total_units() > 0) { ?>
    <stat name="odds" unit="" value="<?= round($yest_totals->WORK_UNITS / $par->WORK_TODAY,0) ?>"/>
    <? } ?>

    <stat name="members-overall" unit="" value="<?=round($stats->get_stats_item("members_overall"),0)?>"/>
    <stat name="members-current" unit="" value="<?=round($stats->get_stats_item("members_current"),0)?>"/>
    <stat name="members-day" unit="" value="<?=round($stats->get_stats_item("members_today"),0)?>"/>
  </stats>
  <nearby-teams>
    <?
      for ($i = 0; $i < $numneighbors; $i++) {    
        $tmpTeam =& $neighbors[$i];
        $tmpStats =& $tmpTeam->get_current_stats();
    ?>
    <team-summary id="<?= $tmpTeam->get_id() ?>">
      <name><?= safe_display($tmpTeam->get_name()) ?></name>
      <membership><?if ($tmpTeam->get_show_members()=="NO") {?>private<?} else { if ($tmpTeam->get_show_members()=="PAS") {?>password<?} else {?>public<?} }?></membership>
      <stats date="<?= safe_display($tmpStats->get_stats_item("last_date")) ?>">
        <stat name="overall-rank" unit="" value="<?=$tmpStats->get_stats_item("overall_rank") ?>" change="<?= $tmpStats->get_stats_item('overall_rank_previous') - $tmpStats->get_stats_item("overall_rank") ?>"/>
        <stat name="overall-work" unit="<?= $gproj->get_scaled_unit_name() ?>" value="<?= round($tmpStats->get_stats_item("work_total") * $gproj->get_scale(),0) ?>"/>

        <? if ($tmpStats->get_stats_item("work_today") > 0) { ?>
        <stat name="day-rank" unit="" value="<?= $tmpStats->get_stats_item("day_rank") ?>" change="<?= $tmpStats->get_stats_item('day_rank_previous') - $tmpStats->get_stats_item("day_rank") ?>"/>
        <stat name="day-work" unit="<?= $gproj->get_scaled_unit_name() ?>" value="<?= round($tmpStats->get_stats_item("work_today") * $gproj->get_scale(),0) ?>"/>
        <? } ?>

        <stat name="time-working" unit="days" value="<?= round($tmpStats->get_stats_item("days_working"),0) ?>"/>

        <stat name="members-overall" unit="" value="<?=round($tmpStats->get_stats_item("members_overall"),0)?>"/>
        <stat name="members-current" unit="" value="<?=round($tmpStats->get_stats_item("members_current"),0)?>"/>
        <stat name="members-day" unit="" value="<?=round($tmpStats->get_stats_item("members_today"),0)?>"/>
      </stats>
    </team-summary>
    <?
        unset($tmpTeam);
        unset($tmpStats);
      }
    ?>
  </nearby-teams>
</team-summary>
