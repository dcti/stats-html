<?
# $Id: countries.php,v 1.6 2004/07/16 20:45:27 decibel Exp $
# vi: ts=2 sw=2 tw=120

include "../etc/global.inc";
include "../etc/modules.inc";
include "../etc/project.inc";

$title = "Participating Countries";
$filename = "../cache/countries_" . $source . "_" . $project_id . ".inc";

include "../templates/header.inc";

if( file_exists($filename)) {
  readfile($filename);
} else {
  display_last_update();
  trigger_error("Apologies, these pages are currently being built. Please try again in a few minutes",E_USER_ERROR);
}
?>
<?include "../templates/footer.inc";?>
