<?
# vi: ts=2 sw=2 tw=120 syntax=php
# $Id: project.inc,v 1.22 2003/05/24 01:26:49 thejet Exp $

include "limit.inc";
include "db_pgsql.php";
include "project.php";

# Formatting options
$bgcolor = "\"#ffffff\"";
$text = "\"#000000\"";
$link = "\"#777777\"";

$fonth = "face=\"verdana,helvetica,arial\"";
$fontd = "face=\"verdana,helvetica,arial\"";
$fontf = "face=\"lucida console,courier,courier new\"";
$fontt = "face=\"verdana,helvetica,arial\" size=\"+2\"";

$colora = "\"#0077aa\"";
$colorb = "\"#aaaaaa\"";
$colorc = "\"#ffff77\"";

$header_bg = "\"#222222\"";
$header_font = "face=\"verdana,helvetica,arial\" size=\"+1\" color=\"#ffffff\"";
$footer_bg = "\"#222222\"";
$footer_font = "face=\"verdana,helvetica,arial\" color=\"#ffffff\"";
$bar_color_a = "row1";
$bar_color_b = "row2";


if ($_SERVER["SERVER_NAME"]!="stats.distributed.net"){ 
	dl('../xdebug.so');
	xdebug_start_trace();
	xdebug_start_profiling();
 	include "timing.inc";
  	timer_start();
}

$myname = $_SERVER['PHP_SELF'];

if (isset($_GET['project_id'])) {
  $project_id = (int)$_GET['project_id'];
} else {
  $project_id = 25;
}


// $debug = (int) 0 + $_GET["debug"];

if (isset($_GET['source'])){
  $temp = $_GET['source'];
  switch ($temp) {
    case 'o':
      $source = 'o';
      break;
    case 'y':
      $source = 'y';
      break;
    default:
      $source = 'o';
      break;
  }
} else {
  $source ='o';
}

if (isset($_GET['debug'])) {
  $debug = 'yes';
} else { 
  $debug = 'no';
} 

if (isset($_REQUEST['id'])) {
  $id = (int)$_REQUEST['id'];
} else {
  $id = 1;
}

if (isset($_REQUEST['st'])) {
  $st = $_REQUEST['st'];
} else {
  $st = "";
}

if (isset($_GET['team'])) {
  $tm = (int)$_GET['team'];
} else {
  $tm = 1;
}

$gdb = new DB('dbname=stats');
$gproj = new Project($gdb,$project_id);

function last_update($type)
{
  global $lastupdate,$project_id;

  switch ($type) { 
    case 'e':
      $qs = "p_lastupdate_new_e @project_id=$project_id";
      break;
    case 'ec':
      $qs = "p_lastupdate_new_ec @project_id=$project_id";
      break;
    case 't':
      $qs = "SELECT to_char(MAX(last_date), 'dd-Mon-YYYY') AS lastupdate FROM team_rank WHERE project_id = $project_id";
      break;
    case 'm':
      $qs = "p_lastupdate_new_m @project_id=$project_id";
      break;
    case 'pc':
      $qs = "p_lastupdate_new_pc @project_id=$project_id";
      break;
    case 'i';
      return $lastupdate;
  }

  //$result = sybase_query($qs);
	$result = false;
  if($result) {
    $par = sybase_fetch_object($result);
    $lastupdate = sybase_date_format_long($par->lastupdate);
  } else {
    $lastupdate = "some day, not too long ago";
  }
  return $lastupdate;
}

function display_last_update($type) {
  global $lastupdate;
  global $colorc;
  if(isset($type)) {$lastupdate = last_update($type);
}
?>
   <tr>
     <td colspan="4" align="center" class="lastupdate"> 
<? if(isset($type)) { ?>
  <? if($lastupdate<>"") { ?>
        Data shown reflects all blocks received as of <?=$lastupdate?> at 23:59 UTC
  <? } else { ?>
    Update currently in progress...
  <? } ?>
<? } else { ?>
  &nbsp;
<? } ?>
      </font>
    </td>
  </tr>
</table>
<?
}

?>
