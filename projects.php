<?

include "etc/global.inc";
include "etc/modules.inc";
include "etc/project.inc";

$title = "Overall Project Stats";

include "templates/header.inc";
if( file_exists("cache/index_$project_id.inc")) {
    readfile( "cache/index_$project_id.inc");
} else {
    display_last_update();
    trigger_error("Apologies, these pages are currently being built. Please try again in a few minutes",E_USER_WARNING);
}

include "templates/footer.inc";
?>
