<?
// vi: ts=2 sw=2 tw=120
// $Id: tmsummary_xml.php,v 1.1 2003/09/02 03:41:58 thejet Exp $

// Variables Passed in url:
//  team == team id to display
//error_reporting(0);
include "../etc/config.inc";
include "../etc/modules.inc";
include "../etc/markup.inc";
include "../etc/project.inc";
include "../etc/team.php";
include "../etc/teamstats.php";

$title = "Team #$tm Summary";
$lastupdate = last_update('t');
header("Content-type: text/xml", true);
print("<"."?xml version=\"1.0\" standalone=\"yes\"?".">\n");
print("<!-- WARNING: This code is experimental and the schema is subject to change at any time -->\n");

// Create the team and team stats objects
$team = new Team($gdb, $gproj, $tm);
$stats = $team->get_current_stats();

if ($team->get_id() == 0) {
	echo "<error>That team is not known.</error>";
	exit;
}

$neighbors = $team->get_neighbors();
$numneighbors = count($neighbors);

/*
$qs = "select *
        from Daily_Summary nolock
        where PROJECT_ID = $project_id
          and DATE = (select max(DATE) from Daily_Summary where project_id=$project_id)";
sybase_query("set rowcount 0");
$result = sybase_query($qs);
$yest_totals = sybase_fetch_object($result);
*/
?>
<team-summary id="<?= $tm ?>" project="<?= $gproj->get_name() ?>" project-id="<?= $gproj->get_id() ?>" date="<?= $lastupdate ?>">
  <name><?= safe_display($team->get_name()) ?></name>
  <logo><?= safe_display($team->get_logo()) ?></logo>
  <description><![CDATA[<?= markup_to_html($team->get_description()) ?>]]></description>
  <contact><?= safe_display($team->get_contact_email()) ?></contact>
  <membership><?if ($team->get_show_members()=="NO") {?>private<?} else { if ($team->get_show_members()=="PAS") {?>password<?} else {?>public<?} }?></membership>
  <stats date="<?= safe_display($stats->get_stats_item("last_date")) ?>">
    <stat name="overall-rank" unit="" value="<?= $stats->get_stats_item("overall_rank") ?>" change="<?= $stats->get_stats_item('overall_rank_previous') - $stats->get_stats_item("overall_rank") ?>"/>
    <stat name="overall-work" unit="<?= $gproj->get_scaled_unit_name() ?>" value="<?= number_style_convert($stats->get_stats_item("work_total") * $gproj->get_scale()) ?>"/>
    <stat name="overall-work-persec" unit="<?= $gproj->get_scaled_unit_name() ?>/sec" value="<?= number_style_convert($stats->get_stats_item("work_total") * $gproj->get_scale() / (86400 * $stats->get_stats_item("days_working")), 3) ?>"/>
    <stat name="overall-work-permember" unit="<?= $gproj->get_scaled_unit_name() ?>/member" value="<?= number_style_convert($stats->get_stats_item("work_total") * $gproj->get_scale() / $stats->get_stats_item("members_overall")) ?>"/>
    <!-- Note, you can insert unscaled information here, if you're interested -->

    <? if ($stats->get_stats_item("work_today") > 0) { ?>
    <stat name="day-rank" unit="" value="<?= $stats->get_stats_item("day_rank") ?>" change="<?= $stats->get_stats_item('day_rank_previous') - $stats->get_stats_item("day_rank") ?>"/>
    <stat name="day-work" unit="<?= $gproj->get_scaled_unit_name() ?>" value="<?= number_style_convert($stats->get_stats_item("work_today") * $gproj->get_scale()) ?>"/>
    <stat name="day-work-persec" unit="<?= $gproj->get_scaled_unit_name() ?>" value="<?= number_style_convert($stats->get_stats_item("work_today") * $gproj->get_scale() / 86400, 3) ?>"/>
    <stat name="day-work-permember" unit="<?= $gproj->get_scaled_unit_name() ?>" value="<?= number_style_convert($stats->get_stats_item("work_today") * $gproj->get_scale() / $stats->get_stats_item("members_today")) ?>"/>
    <!-- Note, you can insert unscaled information here, if you're interested -->
    <? } ?>

    <stat name="time-working" unit="days" value="<?= number_style_convert($stats->get_stats_item("days_working")) ?>"/>
    <? if ($gproj->get_total_units() > 0) { ?>
    <stat name="odds" unit="" value="<?= number_style_convert($yest_totals->WORK_UNITS / $par->WORK_TODAY) ?>"/>
    <? } ?>

    <stat name="members-overall" unit="" value="<?=number_style_convert($stats->get_stats_item("members_overall"))?>"/>
    <stat name="members-current" unit="" value="<?=number_style_convert($stats->get_stats_item("members_current"))?>"/>
    <stat name="members-day" unit="" value="<?=number_style_convert($stats->get_stats_item("members_today"))?>"/>
  </stats>
  <nearby-teams>
    <?
      for ($i = 0; $i < $numneighbors; $i++) {    
        $tmpTeam =& $neighbors[$i];
        $tmpStats =& $tmpTeam->get_current_stats();
    ?>
    <team-summary id="<?= $tmpTeam->get_id() ?>">
      <name><?= safe_display($tmpTeam->get_name()) ?></name>
      <logo><?= safe_display($tmpTeam->get_logo()) ?></logo>
      <description><![CDATA[<?= markup_to_html($tmpTeam->get_description()) ?>]]></description>
      <contact><?= safe_display($tmpTeam->get_contact_email()) ?></contact>
      <membership><?if ($tmpTeam->get_show_members()=="NO") {?>private<?} else { if ($tmpTeam->get_show_members()=="PAS") {?>password<?} else {?>public<?} }?></membership>
      <stats date="<?= safe_display($tmpStats->get_stats_item("last_date")) ?>">
        <stat name="overall-rank" unit="" value="<?=$tmpStats->get_stats_item("overall_rank") ?>" change="<?= $tmpStats->get_stats_item('overall_rank_previous') - $tmpStats->get_stats_item("overall_rank") ?>"/>
        <stat name="overall-work" unit="<?= $gproj->get_scaled_unit_name() ?>" value="<?= number_style_convert($tmpStats->get_stats_item("work_total") * $gproj->get_scale()) ?>"/>
        <stat name="overall-work-persec" unit="<?= $gproj->get_scaled_unit_name() ?>/sec" value="<?= number_style_convert($tmpStats->get_stats_item("work_total") * $gproj->get_scale() / (86400 * $tmpStats->get_stats_item("days_working")), 3) ?>"/>
        <stat name="overall-work-permember" unit="<?= $gproj->get_scaled_unit_name() ?>/member" value="<?= number_style_convert($tmpStats->get_stats_item("work_total") * $gproj->get_scale() / $tmpStats->get_stats_item("members_overall")) ?>"/>
        <!-- Note, you can insert unscaled information here, if you're interested -->

        <? if ($tmpStats->get_stats_item("work_today") > 0) { ?>
        <stat name="day-rank" unit="" value="<?= $tmpStats->get_stats_item("day_rank") ?>" change="<?= $tmpStats->get_stats_item('day_rank_previous') - $tmpStats->get_stats_item("day_rank") ?>"/>
        <stat name="day-work" unit="<?= $gproj->get_scaled_unit_name() ?>" value="<?= number_style_convert($tmpStats->get_stats_item("work_today") * $gproj->get_scale()) ?>"/>
        <stat name="day-work-persec" unit="<?= $gproj->get_scaled_unit_name() ?>" value="<?= number_style_convert($tmpStats->get_stats_item("work_today") * $gproj->get_scale() / 86400, 3) ?>"/>
        <stat name="day-work-permember" unit="<?= $gproj->get_scaled_unit_name() ?>" value="<?= number_style_convert($tmpStats->get_stats_item("work_today") * $gproj->get_scale() / $tmpStats->get_stats_item("members_today")) ?>"/>
        <!-- Note, you can insert unscaled information here, if you're interested -->
        <? } ?>

        <stat name="time-working" unit="days" value="<?= number_style_convert($tmpStats->get_stats_item("days_working")) ?>"/>
        <? if ($gproj->get_total_units() > 0) { ?>
        <stat name="odds" unit="" value="<?= number_style_convert($yest_totals->WORK_UNITS / $teamrec->WORK_TODAY) ?>"/>
        <? } ?>

        <stat name="members-overall" unit="" value="<?=number_style_convert($tmpStats->get_stats_item("members_overall"))?>"/>
        <stat name="members-current" unit="" value="<?=number_style_convert($tmpStats->get_stats_item("members_current"))?>"/>
        <stat name="members-day" unit="" value="<?=number_style_convert($tmpStats->get_stats_item("members_today"))?>"/>
      </stats>
    </team-summary>
    <?
        unset($tmpTeam);
        unset($tmpStats);
      }
    ?>
  </nearby-teams>
</team-summary>
