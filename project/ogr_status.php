<?php
// $Id: ogr_status.php,v 1.6 2009/02/27 03:45:10 thejet Exp $
// ************ OGR Status                           ***
// ************ Filename: ogr_status.php             ***

// ************ Setup any USE statements and initialize variables
include "../etc/global.inc";
include "../etc/modules.inc";
include "../etc/db_pgsql.php";
include "../etc/project.inc";
include "../etc/ogrstubspace.php";
include "../etc/markup.inc";

// load up the list of OGR Stubspaces
$stubspaceList =& OGRStubspace::get_stubspace_list($gproj, $gdb);
$cnt = count($stubspaceList);
$totalStubs = 0;
$stubsDone = 0;
$stubsVerified = 0;
$stubDelta = 0;
$delta3Day = 0;
$delta7Day = 0;
$delta14Day = 0;
$delta30Day = 0;

for($i = 0; $i < $cnt; $i++)
{
  $tmpStats =& $stubspaceList[$i]->get_current_stats();
  $totalStubs += $stubspaceList[$i]->get_total_stubs();
  $stubsDone += $tmpStats->get_stubs_done();
  $stubsVerified += $tmpStats->get_stubs_verified();
  $stubDelta += $tmpStats->get_stub_delta();
  $delta3Day += $tmpStats->get_3day_avg_delta();
  $delta7Day += $tmpStats->get_7day_avg_delta();
  $delta14Day += $tmpStats->get_14day_avg_delta();
  $delta30Day += $tmpStats->get_30day_avg_delta();
  if($i <= 0)
  {
    $lastupdate = strftime("%d-%b-%Y", strtotime($tmpStats->get_stats_date()));
  }
  unset($tmpStats);
}

$stubsLeft = 2 * $totalStubs - $stubsDone - $stubsVerified;
$totPctComp = round(($stubsDone + $stubsVerified) / (2*$totalStubs), 4) * 100;
if($totPctComp == 100 && $stubsLeft != 0)
{
  $totPctComp = 99.99;
}

$title = "Stubspace Status";
#$lastupdate = last_update('e');

include "../templates/header.inc";

$nextRowClass = "row2";
?>
<h3 align="center">Status Overview</h3>
<table align="center" border="0" cellspacing="0" cellpadding="0">
<tr>
  <td style="width: 150px; font-weight: bold">Total Stubs:</td>
  <td style="width: 150px; text-align: right;"><?=number_style_convert($totalStubs)?></td>
</tr>
<tr>
  <td style="width: 150px; font-weight: bold">Stubs Done:</td>
  <td style="width: 150px; text-align: right;"><?=number_style_convert($stubsDone)?></td>
</tr>
<tr>
  <td style="width: 150px; font-weight: bold">Stubs Verified:</td>
  <td style="width: 150px; text-align: right;"><?=number_style_convert($stubsVerified)?></td>
</tr>
<tr>
  <td style="width: 150px; font-weight: bold">Stubs Yesterday:</td>
  <td style="width: 150px; text-align: right;"><?=number_style_convert($stubDelta)?></td>
</tr>
<tr>
  <td style="width: 150px; font-weight: bold">% Complete:</td>
  <td style="width: 150px; text-align: right;"><?=$totPctComp?>%</td>
</tr>
</table>
<br />
<h3 align="center">Projected Completion Times</h3>
<table align="center" border="1" cellspacing="0" cellpadding="2" class="tborder">
<tr>
  <th class="thead">&nbsp;</th>
  <th class="thead">Daily Stubs</th>
  <th class="thead">Projected End Date</th>
</tr>
<?
if($stubDelta > 0) {
  $endDate = round($stubsLeft / $stubDelta, 0) * 60 * 60 * 24;
  $endDate = strftime("%d-%b-%Y", strtotime($lastupdate) + $endDate);
?>
<tr class="<?=$nextRowClass?>">
  <td class="thead">1 Day Average</td>
  <td align="right"><?=number_style_convert($stubDelta)?></td>
  <td align="center"><?=$endDate?></td>
</tr>
<?
  $nextRowClass = ($nextRowClass == "row1")?"row2":"row1";
}

if($delta3Day > 0) {
?>
<tr class="<?=$nextRowClass?>">
  <td class="thead">3 Day Average</td>
  <td align="right"><?=number_style_convert($delta3Day)?></td>
  <td align="center"><?=strftime("%d-%b-%Y", strtotime($lastupdate) + round($stubsLeft / $delta3Day, 0) * 60 * 60 * 24)?></td>
</tr>
<?
  $nextRowClass = ($nextRowClass == "row1")?"row2":"row1";
}

if($delta7Day > 0) {
?>
<tr class="<?=$nextRowClass?>">
<tr class="row2">
  <td class="thead">7 Day Average</td>
  <td align="right"><?=number_style_convert($delta7Day)?></td>
  <td align="center"><?=strftime("%d-%b-%Y", strtotime($lastupdate) + round($stubsLeft / $delta7Day, 0) * 60 * 60 * 24)?></td>
</tr>
<?
  $nextRowClass = ($nextRowClass == "row1")?"row2":"row1";
}

if($delta14Day > 0) {
?>
<tr class="<?=$nextRowClass?>">
  <td class="thead">14 Day Average</td>
  <td align="right"><?=number_style_convert($delta14Day)?></td>
  <td align="center"><?=strftime("%d-%b-%Y", strtotime($lastupdate) + round($stubsLeft / $delta14Day, 0) * 60 * 60 * 24)?></td>
</tr>
<?
  $nextRowClass = ($nextRowClass == "row1")?"row2":"row1";
}

if($delta30Day > 0) {
?>
<tr class="<?=$nextRowClass?>">
  <td class="thead">30 Day Average</td>
  <td align="right"><?=number_style_convert($delta30Day)?></td>
  <td align="center"><?=strftime("%d-%b-%Y", strtotime($lastupdate) + round($stubsLeft / $delta30Day, 0) * 60 * 60 * 24)?></td>
</tr>
<?
}
?>
</table>
<br />
<h3 align="center">Detailed Stubspace Status</h3>
<table border="1" cellspacing="0" cellpadding="2" width="800" align="center" class="tborder">
<tr>
  <th rowspan="2" class="thead">Name</th>
  <th colspan="5" class="thead">Stubs</th>
  <th rowspan="2" class="thead" colspan="2">% Complete</th>
</tr>
<tr>
  <th class="thead">Total</th>
  <th class="thead">Done</th>
  <th class="thead">Verified</th>
  <th class="thead">Yesterday</th>
  <th class="thead">Remaining</th>
</tr>
<?
for($i = 0; $i < $cnt; $i++)
{
  $statsTmp =& $stubspaceList[$i]->get_current_stats();
  $stubspace =& $stubspaceList[$i];
  $statsClass = ($i % 2 == 0)?"row2":"row1";
  $pctComplete = round(
		       ($statsTmp->get_stubs_done() + $statsTmp->get_stubs_verified()) /
		          (2 * $stubspace->get_total_stubs()),
		       4);
  $barLength = round(150 * $pctComplete, 0);
  $stubsLeft = $stubspace->get_total_stubs() * 2 - $statsTmp->get_stubs_done() - $statsTmp->get_stubs_verified();
  if($pctComplete == 1 && $stubsLeft > 0)
  {
    $pctComplete = .9999;
  }
?>
<tr class="<?=$statsClass?>">
  <td><?=$stubspace->get_name()?></td>
  <td align="right"><?=number_style_convert($stubspace->get_total_stubs())?></td>
  <td align="right"><?=number_style_convert($statsTmp->get_stubs_done())?></td>
  <td align="right"><?=number_style_convert($statsTmp->get_stubs_verified())?></td>
  <td align="right"><?=number_style_convert($statsTmp->get_stub_delta())?></td>
  <td align="right"><?=number_style_convert($stubsLeft)?></td>
  <td align="right"><?=$pctComplete * 100?>%</td>
  <td width="152"><img src="/images/bar.jpg" height="15" width="<?=$barLength?>" alt="<?=$pctComplete?>" /></td>
</tr>
<?
  unset($stubspace);
  unset($statsTmp);
}
echo "</table>\n";
echo "<br /><br />";

$project_filter = array(25, 26, 27, 28);

include "../templates/footer.inc";

?>
