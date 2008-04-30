<?php
// $Id: ogr_status_history.php,v 1.2 2008/04/30 10:28:50 thejet Exp $
// ************ OGR Status History                   ***
// * start_date    - The date to start on
// * end_date      - The date to end on
// * stubspace_id  - The Stubspace # to report on [default: all]
// * output_format - The format to output using, default CSV

// ************ Setup any USE statements and initialize variables
include "../etc/global.inc";
include "../etc/modules.inc";
include "../etc/db_pgsql.php";
include "../etc/project.inc";
include "../etc/ogrstubspace.php";
include "../etc/markup.inc";

function print_usage_message($error, $addHeader = false)
{
  if($addHeader)
  {
    header("Content-Type: text/plain");
  }
  echo $error . "\n\n";
  echo "Usage:\n"
     . "  project_id    == The project to do stubspace reporting on\n"
     . "  stubspace_id  == [optional] The specific stubspace to report on\n"
     . "  start_date    == [optional, default: 30 days ago, format example: 01-Apr-2008]\n"
     . "                   The date to start pulling data\n"
     . "  end_date      == [optional, default: today, format example: 01-Apr-2008]\n"
     . "                   The last date to pull data for\n"
     . "  output_format == The output format you'd prefer [xml, csv]\n";

  echo "\nUsage Example:\n"
     . "  ogr_status_history.php?project_id=25&output_format=xml\n\n";
}

// load up the list of OGR Stubspaces
if(isset($_GET['stubspace_id']))
{
  $stubspace_id = (int)$_GET['stubspace_id'];
  $stubspaceList = array();
  $stubspaceList[] = new OGRStubspace($gdb, $gproj, $stubspace_id);
}
else
{
  $stubspace_id = 0;
  $stubspaceList =& OGRStubspace::get_stubspace_list($gproj, $gdb);
}

if(isset($_GET['output_format']))
{
  $output_format = strtolower($_GET['output_format']);
}
else
{
  print_usage_message("You must specify an output format", true);
  exit(1);
}

$end_date = time();
if(isset($_GET['end_date']))
{
  $end_date = strtotime($_GET['end_date']);
}
if(!$end_date)
{
  print_usage_message("Invalid End Date Specified", true);
  exit(1);
}

$start_date = time() - 30 * 86400; // last 30 days
if(isset($_GET['start_date']))
{
  $start_date = strtotime($_GET['start_date']);
}
if(!$start_date)
{
  print_usage_message("Invalid Start Date Specified", true);
  exit(1);
}

$qs = "SELECT os.project_id, os.stubspace_id, os.total_stubs, "
    . "       oss.stats_date, oss.stubs_done, oss.stubs_verified, "
    . "       oss.stub_delta, oss.delta_3day_average, oss.delta_7day_average, "
    . "       oss.delta_14day_average, oss.delta_30day_average "
    . "  FROM ogr_stubspace os INNER JOIN ogr_stubspace_stats oss "
    . "       ON os.project_id = oss.project_id AND os.stubspace_id = oss.stubspace_id "
    . " WHERE os.project_id = $1 ";

$cnt = count($stubspaceList); 
if($cnt == 1)
{
    $qs .= " AND os.stubspace_id = $2 ";
}
else
{
    $qs .= " AND os.stubspace_id IN (0";
    for($i = 0; $i < $cnt; $i++)
    {
        $qs .= "," . $stubspaceList[$i]->get_stubspace_id();
    }
    $qs .= ") ";
}
    $qs .= "   AND stats_date BETWEEN $3 AND $4 "
    . " ORDER BY stats_date, os.stubspace_id; ";
$query = $gdb->query_bound($qs, array(
				  $gproj->get_id(),
				  $stubspace_id,
				  strftime('%d-%b-%Y', $start_date),
				  strftime('%d-%b-%Y', $end_date)
				));
if(!$query)
{
  print_usage_message("Database Query Failed.  Please review your URL reference.", true);
  exit(1);
}

$results = $gdb->fetch_paged_result($query);
$cnt = count($results);

if($output_format == 'csv')
{
  header("Content-Type: text/csv");
  header("Content-Disposition: download;filename=stubspace_stats.csv");

  // the header information
  echo "Date,Stubspace,PctComp,Total Stubs,Stubs Done,Stubs Verified,Change,3DAvg Change,7DAvg Change,14DAvg Change,30DAvg Change\n";

  for($i = 0; $i < $cnt; $i++)
  {
    echo strftime('%d-%b-%Y', strtotime($results[$i]->stats_date)) . "," .
	 $results[$i]->stubspace_id . "," .
	 (round(($results[$i]->stubs_done + $results[$i]->stubs_verified) / ($results[$i]->total_stubs * 2), 4) * 100) . "," .
	 $results[$i]->total_stubs . "," .
	 $results[$i]->stubs_done . "," .
	 $results[$i]->stubs_verified . "," .
	 $results[$i]->stub_delta . "," .
	 $results[$i]->delta_3day_average . "," .
	 $results[$i]->delta_7day_average . "," .
	 $results[$i]->delta_14day_average . "," .
	 $results[$i]->delta_30day_average .
	 "\n";
  } 
}
elseif ($output_format == 'xml')
{
  header("Content-Type: text/xml");
  header("Content-Disposition: download;filename=stubspace_stats.xml");

  echo "<"."?xml version='1.0' standalone='yes'?".">\n";
  echo "<!-- \n";
  print_usage_message("API Documentation:");
  echo "  -->\n";
  echo "<stubspace-stats>\n";
  for($i = 0; $i < $cnt; $i++)
  {
    echo "  <stubspace-stat" .
         " date='".strftime('%d-%b-%Y', strtotime($results[$i]->stats_date)) . "' " .
         " stubspace-id='" . $results[$i]->stubspace_id . "' " .
         " percent-complete='" . (round(($results[$i]->stubs_done + $results[$i]->stubs_verified) / ($results[$i]->total_stubs * 2), 4) * 100) . "' " .
         " total-stubs='".$results[$i]->total_stubs . "' " .
         " stubs-done='".$results[$i]->stubs_done . "' " .
         " stubs-verified='".$results[$i]->stubs_verified . "' " .
         " stub-delta='".$results[$i]->stub_delta . "' " .
         " delta-3day-average='".$results[$i]->delta_3day_average . "' " .
         " delta-7day-average='".$results[$i]->delta_7day_average . "' " .
         " delta-14day-average='".$results[$i]->delta_14day_average . "' " .
         " delta-30day-average='".$results[$i]->delta_30day_average . "' " .
         " />\n";
  }

  echo "</stubspace-stats>\n";
}
else
{
  print_usage_message("Invalid Output Format Specified", true);
  exit(1);
}

?>
