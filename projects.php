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
    include("templates/stale.inc");
}

include "templates/footer.inc";
?>
