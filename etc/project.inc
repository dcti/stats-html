<?
# vi: ts=2 sw=2 tw=120 syntax=php
# $Id: project.inc,v 1.18 2003/03/09 11:54:59 paul Exp $

include "limit.inc";
include "debug.inc";

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

sybase_pconnect($interface, $username, $password);
$qs = "select name, project_type, work_unit_qty, work_unit_disp_multiplier, work_unit_disp_divisor,
              scaled_work_unit_name, unscaled_work_unit_name, PRIZE
              -- Deprecated
              , work_unit_scale, work_unit_name
          from Projects
          where PROJECT_ID = $project_id";
sybase_query("set rowcount 1");
$result = sybase_query($qs);

if($result == "") {
  if($debug == "yes") {
    include "templates/debug.inc";
  } else {
    include "templates/error.inc";
  }
    exit();
}

// get project_info object
sybase_data_seek($result,0);
$prj_info = sybase_fetch_object($result);
debug_text("<!-- Project Info -- qs: $qs, result: $result, prj_info: $prj_info -->\n",$debug);

$proj_name = $prj_info->name;
$proj_type = $prj_info->project_type;
$proj_totalunits = (double)$prj_info->work_unit_qty;
$proj_scale = (double)$prj_info->work_unit_disp_multiplier / $prj_info->work_unit_disp_divisor;
$proj_scaled_unit_name = $prj_info->scaled_work_unit_name;
$proj_unscaled_unit_name = $prj_info->unscaled_work_unit_name;
$proj_prize = $prj_info->PRIZE;

# Deprecated
$proj_divider = (double)$prj_info->work_unit_scale;
$proj_unitname = $prj_info->work_unit_name;

function last_update($type)
{
  global $debug,$lastupdate,$project_id;

  debug_text("<!-- last_update: type: $type -->\n",$debug);
  switch ($type) { 
    case 'e':
      $qs = "p_lastupdate_new_e @project_id=$project_id";
      break;
    case 'ec':
      $qs = "p_lastupdate_new_ec @project_id=$project_id";
      break;
    case 't':
      $qs = "p_lastupdate_new_t @project_id=$project_id";
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

  $result = sybase_query($qs);
  debug_text("<!-- last_update query: qs: $qs result: $result -->\n",$debug);

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
     <td colspan="4" align="center" bgcolor="#777777">
       <font face="Arial,Helvetica" color=<?=$colorc?> size="-2">
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
