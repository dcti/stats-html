<?
# $Id: countries.php,v 1.4 2002/12/17 04:49:27 decibel Exp $
# vi: ts=2 sw=2 tw=120

include "../etc/config.inc";
include "../etc/modules.inc";
include "../etc/project.inc";

$title = "Participating Countries";
$filename = "../cache/countries_" . $source . "_" . $project_id . ".inc";

include "../templates/header.inc";

if( file_exists($filename)) {
  readfile($filename);
} else {
  display_last_update();
  ?> 
  <center>	
    <p>
      Apologies, these pages are currently being built. Please try again in a few minutes.
    </p>
  </center>
  <? 
}
?>
<?include "../templates/footer.inc";?>
