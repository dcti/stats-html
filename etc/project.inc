<?
 # $Id: project.inc,v 1.3 2002/03/08 23:29:15 paul Exp $

 $myname = $PHP_SELF;

 $project = "OGR-25";
 $project_id = (int) 0 + $_GET["project_id"];
 if($project_id == "" or $project_id == 0) {
  $project_id = 25;
 }

 
 $id = (int) 0+$_GET["id"];
 if ( $id == "" or $id == 0) {
  $id = 1;
 }




 $divider = 1000000000;

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
