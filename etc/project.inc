<?
 # $Id: project.inc,v 1.4 2002/03/09 18:31:29 paul Exp $

 $myname = $PHP_SELF;

 $project_id = (int) 0 + $_GET["project_id"];
 if($project_id == "" or $project_id == 0) {
  $project_id = 25;
 }

 
 $id = (int) 0+$_GET["id"];
 if ( $id == "" or $id == 0) {
  $id = 1;
 }

 sybase_pconnect($interface, $username, $password);
 $qs = "select name, dist_unit_qty, work_unit_scale, work_unit_name from
        projects where project_id = $project_id";
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

 $proj_name= $prj_info->name;
 $proj_totalunits = (int)$prj_info->dist_unit_qty;
 $proj_divider = (int)$prj_info->work_unit_scale;
 $proj_unitname = $prj_info->work_unit_name;



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
 $bar_color_a = "\"#dddddd\"";
 $bar_color_b = "\"#ffffdd\"";

?>
