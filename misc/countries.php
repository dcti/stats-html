<?

 include "../etc/config.inc";
 include "../etc/modules.inc";
 include "../etc/project.inc";


 $title = "Participating Countries";

 include "../templates/header.inc";
 if( file_exists("../cache/countries_".$source."_$project_id.inc")) {
	readfile( "../cache/countries_".$source."_$project_id.inc");
 } else {
 	display_last_update();
  ?> 
	<center>	
	Apologies, these pages are currently being built. Please try again in a few minutes
	</center>
  <? 
}
?>
<?include "../templates/footer.inc";?>
