<?

 include "etc/config.inc";
 include "etc/modules.inc";
 include "etc/project.inc";


 $title = "Overall Project Stats";

 include "templates/header.inc";
 if( file_exists("cache/index_$project_id.inc")) {
	readfile( "cache/index_$project_id.inc");
 } else {
  display_last_update();
  ?> 
	<center>
	Apologies, these pages are currently being built. Please try again in a few minutes
	</center>
  <? 
}
?>
<?include "templates/footer.inc";?>
