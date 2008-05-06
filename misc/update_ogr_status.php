<?
 include "../etc/global.inc";
 include "../etc/modules.inc";
 include "../etc/db_pgsql.php";
 include "../etc/project.inc";
 include "../etc/ogrstubspace.php";

 class StubspaceStats
 {
   var $project_id;
   var $stubspace_id;
   var $stats_date;
   var $stubs_done;
   var $stubs_verified;
   var $stub_delta;
   var $delta_3;
   var $delta_7;
   var $delta_14;
   var $delta_30;

   function toSql()
   {
     $sql = "INSERT INTO ogr_stubspace_stats ";
     $sql .= " (project_id, stubspace_id, stats_date, stubs_done, ";
     $sql .= "  stubs_verified, stub_delta, delta_3day_average, ";
     $sql .= "  delta_7day_average, delta_14day_average, delta_30day_average) ";
     $sql .= " VALUES ";
     $sql .= " (".$this->project_id.",".$this->stubspace_id.",'".strftime("%d-%b-%Y", $this->stats_date)."',";
     $sql .= $this->stubs_done.",".$this->stubs_verified.",".$this->stub_delta.",";
     $sql .= round($this->delta_3,0).",".round($this->delta_7,0).",";
     $sql .= round($this->delta_14,0).",".round($this->delta_30,0).");";
 
     return $sql;
   }

   function &get_stubspace_history($stubspace_id, $lastX)
   {
     global $gproj, $gdb;
     $history =& OGRStubspaceStats::get_stats_history($gdb, $gproj, $stubspace_id, $lastX);
     $cnt = count($history);
     $result = array();
     for($i = 0; $i < $cnt; $i++)
     {
       $tmp =& new StubspaceStats();
       $tmp->project_id = $history[$i]->get_project_id();
       $tmp->stubspace_id = $history[$i]->get_stubspace_id();
       $tmp->stats_date = $history[$i]->get_stats_date();
       $tmp->stubs_done = $history[$i]->get_stubs_done();
       $tmp->stubs_verified = $history[$i]->get_stubs_verified();
       $tmp->stub_delta = $history[$i]->get_stub_delta();
       $tmp->delta_3 = $history[$i]->get_3day_avg_delta(); 
       $tmp->delta_7 = $history[$i]->get_7day_avg_delta(); 
       $tmp->delta_14 = $history[$i]->get_14day_avg_delta(); 
       $tmp->delta_30 = $history[$i]->get_30day_avg_delta(); 

       $result[] = $tmp;
       unset($tmp);
     }

     $result = array_reverse($result);
     return $result;
   }
 }

 // Download and parse the ogrstats.txt file
 $text = file_get_contents($ogrp2_stats);
 if(!$text)
 {
   echo "Could not download statistics update file.";
   exit(1);
 }

 // Formulate the dummy stats line to parse
 $matches = array();
 if(preg_match('/^\w+\s+?(\w+\s+\d+) \d+:\d+:\d+ UTC (200\d)/m', $text, $matches) > 0)
 {
   if(count($matches) < 3)
   {
     echo "Incorrect date expression match\n";
     exit(1);
   }

   $line = $matches[1] . ", " . $matches[2];

   // loop through getting all matches
   $ssMatches = array();
   $pattern = '/Scanning[^\n]+?ogrp2_25_(\d).*?(\d+) stubs done.*?(\d+) stubs verified.*?Completion :\s+([\d\.]+)\%/s';
   if(preg_match_all($pattern, $text, $ssMatches, PREG_SET_ORDER) <= 0)
   {
     echo "Incorrect stubspace expression match\n";
     exit(1);
   }

   $cnt = count($ssMatches);
   for($i = 0; $i < $cnt; $i++)
   {
     #echo $ssMatches[$i][0] . "\n";
     $line .= ";" . $ssMatches[$i][1] . ":" . $ssMatches[$i][2] . ":" . $ssMatches[$i][3];
   }
 }

 if(!$line)
 {
   echo "Unable to get updated statistics";
   exit(1);
 }

 $statsList["1"] =& StubspaceStats::get_stubspace_history(1, 30);
 $statsList["2"] =& StubspaceStats::get_stubspace_history(2, 30);
 $statsList["3"] =& StubspaceStats::get_stubspace_history(3, 30);
 $statsList["4"] =& StubspaceStats::get_stubspace_history(4, 30);
 $statsList["5"] =& StubspaceStats::get_stubspace_history(5, 30);
 $statsList["6"] =& StubspaceStats::get_stubspace_history(6, 30);
 $statsList["7"] =& StubspaceStats::get_stubspace_history(7, 30);
 $statsList["8"] =& StubspaceStats::get_stubspace_history(8, 30);
 $statsList["9"] =& StubspaceStats::get_stubspace_history(9, 30);

 $lines = array();
 $linedata = explode(";", $line);
 $date = getdate(strtotime($linedata[0]));

 // subtract one day
 if($date["mday"] == 1)
 {
   $date = gmmktime(0, 0, 0, $date["mon"], 0, $date["year"]);
 }
 else
 {
   $date = gmmktime(0, 0, 0, $date["mon"], $date["mday"] -1, $date["year"]);
 }
 
 // build the sql output
 $stubspaces = explode(";", $line);
 for($i = 1; $i < count($stubspaces); $i++)
 {
   $stubspace = explode(":", $stubspaces[$i]);
   $newStats = new StubspaceStats();
   $newStats->project_id = 25;
   $newStats->stubspace_id = $stubspace[0];
   $newStats->stats_date = $date;
   $newStats->stubs_done = $stubspace[1] + $stubspace[2];
   $newStats->stubs_verified = $stubspace[2];

   // check for an existing stats entry for this day
   $strDate = strftime('%d-%b-%Y', $date);
   $curStats = new OGRStubspaceStats($gdb, $gproj, $newStats->stubspace_id, $strDate);
   if($curStats->_state != null)
   {
     echo "Data for $strDate has already been loaded\n\n";
     exit(1);
   }

   $statsList["$i"][] = $newStats;
 }


 foreach($statsList as $key => $value)
 {
   $cnt = count($value);
   for($i = $cnt -1; $i < $cnt; $i++)
   {
     if($i == 0)
     {
       $value[$i]->stub_delta = 0;
     }
     else
     {
       $value[$i]->stub_delta = ($value[$i]->stubs_done - $value[$i-1]->stubs_done) + ($value[$i]->stubs_verified - $value[$i-1]->stubs_verified);
     }
     
     // averages
     $value[$i]->delta_3 = 0;
     $value[$i]->delta_7 = 0;
     $value[$i]->delta_14 = 0;
     $value[$i]->delta_30 = 0;
     for($j = $i; $j >= 0 && $j >= ($i - 30); $j--)
     {
       // 3 day
       if($i >= 2 && ($i - $j) < 3) $value[$i]->delta_3 += $value[$j]->stub_delta;
       // 7 day
       if($i >= 6 && ($i - $j) < 7) $value[$i]->delta_7 += $value[$j]->stub_delta;
       // 14 day
       if($i >= 13 && ($i - $j) < 14) $value[$i]->delta_14 += $value[$j]->stub_delta;
       // 30 day
       if($i >= 29 && ($i - $j) < 30) $value[$i]->delta_30 += $value[$j]->stub_delta;
     }
     $value[$i]->delta_3 /= 3;
     $value[$i]->delta_7 /= 7;
     $value[$i]->delta_14 /= 14;
     $value[$i]->delta_30 /= 30;

     // run the insert
     $gdb->free_result($gdb->query($value[$i]->toSql()));
   }
 }

 echo "Done.\n\n";
?>
