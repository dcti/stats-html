<?php
// ************ OGR Status Graphing Module           ***
// ************ Filename: ogr_graph.pl               ***
// ************ Author: TheJet, May 2003            ***
// ************ Based on code by: Dyyryath, Jun 2001 ***
// ************                                      ***
// ************ Version: 1.0a                        ***

// ************ Setup any USE statements and initialize variables
include "../etc/global.inc";
include "../etc/db_pgsql.php";
include "../etc/jpgraph/jpgraph.php";
include "../etc/jpgraph/jpgraph_line.php";

$history                 = "60";
$dbDSN                   = "dbname=ogr";
$table                   = "ogr_complete";
$numeric_scale           = 1000000;
$scale_text              = "millions";


// ************ Setup the CGI data
$project = (int)$_GET["project_id"];

// ************ Connect to the database
$db = new DB($dbDSN);
if($db == 0)
{
  print "Error connecting to database\n";
  print "Last Error: " + $db->get_last_error();
  exit();
}

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
