<?php
// $Id: ogr_status.php,v 1.1 2008/04/24 18:09:23 thejet Exp $
// ************ OGR Status                           ***
// ************ Filename: ogr_status.php             ***

// ************ Setup any USE statements and initialize variables
include "../etc/global.inc";
include "../etc/modules.inc";
include "../etc/db_pgsql.php";
include "../etc/project.inc";
include "../etc/ogrstubspace.php";
include "../etc/markup.inc";

//include "../etc/jpgraph/jpgraph.php";
//include "../etc/jpgraph/jpgraph_line.php";

// ************ Setup the CGI data
//$project = (int)$_GET["project_id"];

// ************ Connect to the database
//$db = new DB($dbDSN);
//if($db == 0)
//{
//  print "Error connecting to database\n";
//  print "Last Error: " + $db->get_last_error();
//  exit();
//}

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
$endDate = round($stubsLeft / $stubDelta, 0) * 60 * 60 * 24;
$endDate = strftime("%d-%b-%Y", strtotime($lastupdate) + $endDate);

$title = "Stubspace Status";
#$lastupdate = last_update('e');

include "../templates/header.inc";

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
  <td style="width: 150px; text-align: right;"><?=round(($stubsDone + $stubsVerified) / (2*$totalStubs), 4) * 100?>%</td>
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
<tr class="row2">
  <td class="thead">1 Day Average</td>
  <td align="right"><?=number_style_convert($stubDelta)?></td>
  <td align="center"><?=$endDate?></td>
</tr>
<tr class="row1">
  <td class="thead">3 Day Average</td>
  <td align="right"><?=number_style_convert($delta3Day)?></td>
  <td align="center"><?=strftime("%d-%b-%Y", strtotime($lastupdate) + round($stubsLeft / $delta3Day, 0) * 60 * 60 * 24)?></td>
</tr>
<tr class="row2">
  <td class="thead">7 Day Average</td>
  <td align="right"><?=number_style_convert($delta7Day)?></td>
  <td align="center"><?=strftime("%d-%b-%Y", strtotime($lastupdate) + round($stubsLeft / $delta7Day, 0) * 60 * 60 * 24)?></td>
</tr>
<tr class="row1">
  <td class="thead">14 Day Average</td>
  <td align="right"><?=number_style_convert($delta14Day)?></td>
  <td align="center"><?=strftime("%d-%b-%Y", strtotime($lastupdate) + round($stubsLeft / $delta14Day, 0) * 60 * 60 * 24)?></td>
</tr>
<tr class="row2">
  <td class="thead">30 Day Average</td>
  <td align="right"><?=number_style_convert($delta30Day)?></td>
  <td align="center"><?=strftime("%d-%b-%Y", strtotime($lastupdate) + round($stubsLeft / $delta30Day, 0) * 60 * 60 * 24)?></td>
</tr>
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

$proj_list = 0;

include "../templates/footer.inc";

exit(0);

// ************ Get the data
$queryData = $db->query("SELECT rundate, count, pass1, pass2 FROM $table WHERE project_id = $project ORDER BY rundate DESC LIMIT $history");

if($queryData == 0)
{
  print "Error performing query";
  exit();
}

while($result = $db->fetch_array($queryData))
{ 
    $xData1[] = (int)$result{'count'}/$numeric_scale;
    $xData2[] = (int)$result{'pass1'}/$numeric_scale;
    $xData3[] = (int)$result{'pass2'}/$numeric_scale;
    $xData4[] = ($result{'pass1'}/$result{'count'} + $result{'pass2'}/$result{'count'}) /2 * 100;
    $xLabel[] = $result{'rundate'};
    
    if ($result{'count'} > $max) { $max = (int)$result{'count'}; }
}

// ************ Change the graph orientation
$xData1 = array_reverse($xData1);
$xData2 = array_reverse($xData2);
$xData3 = array_reverse($xData3);
$xData4 = array_reverse($xData4);
$xLabel = array_reverse($xLabel);

$graph = new Graph(450,300,"auto");
$graph->SetScale("textlin", 0, (int)$max/$numeric_scale);
$graph->SetY2Scale("lin", 0, 100);
$graph->title->Set("OGR-$project Completion Statistics");
$graph->yaxis->SetTitle("Stubs ($scale_text)", "middle");
$graph->yaxis->SetTitleMargin(30);
$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->y2axis->SetTitle("% complete (aggregate)", "middle");
$graph->y2axis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->y2axis->SetTitleMargin(35);

// Use built in font
$graph->title->SetFont(FF_FONT2,FS_BOLD);

// Make the margin around the plot a little bit bigger
// then default
$graph->img->SetMargin(60,60,40,70);
#$graph->img->SetAntiAliasing();

// Slightly adjust the legend from it's default position in the
// top right corner to middle right side
$graph->legend->Pos(0.5,0.90,"center","center");
$graph->legend->SetLayout(LEGEND_HOR);

// Set the x-axis labels
$graph->xaxis->SetTickLabels($xLabel);
#$graph->xaxis->SetTextLabelInterval(5);
$graph->xaxis->SetTextTickInterval(12);

// Create a red line plot
//$p1 = new LinePlot($xData1);
//$p1->SetColor("red");
//$p1->SetLegend("Stub Count");
//$graph->Add($p1);

// Create a red line plot
$p2 = new LinePlot($xData2);
$p2->SetColor("blue");
$p2->SetLegend("Pass1 Stubs Completed");
#$p2->SetWeight(1);
$graph->Add($p2);

// Create a red line plot
$p3 = new LinePlot($xData3);
$p3->SetColor("red");
$p3->SetLegend("Pass2 Stubs Completed");
#$p3->SetWeight(3);
$graph->Add($p3);

// Add the %-complete graph
$p4 = new LinePlot($xData4);
$p4->SetColor("#770077");
$p4->SetLegend("%-complete");
#$p4->SetWeight(1.25);
$graph->AddY2($p4);

// Finally output the  image
$graph->Stroke();
?>
