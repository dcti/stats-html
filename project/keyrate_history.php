<?
include "../etc/config.inc";
include "../etc/modules.inc";
include "../etc/project.inc";

$title = "Project Keyrate History";

include "../templates/header.inc";
display_last_update();

if ($project_id == 8 )
{
    $found = true;
    ?>
    <div align="center">
    <img src="http://www1.distributed.net/~pstadt/rc5-72/rc5-72-complete.png"><br>
    <img src="http://www1.distributed.net/~pstadt/rc5-72/rc5-72-daily.png"><br>
    <img src="http://www1.distributed.net/~pstadt/rc5-72/rc5-72-weekly.png"><br>
    <img src="http://www1.distributed.net/~pstadt/rc5-72/rc5-72-monthly.png"><br>
    <img src="http://www1.distributed.net/~pstadt/rc5-72/rc5-72-quarterly.png"><br>
    <img src="http://www1.distributed.net/~pstadt/rc5-72/rc5-72-yearly.png"><br>
    </div>
    <?
}
if ($gproj -> get_type() == 'OGR' )
{
    $found = true;
    ?>
    <div align="center">
    <img src="http://www1.distributed.net/~pstadt/ogr/ogr-daily.png"><br>
    <img src="http://www1.distributed.net/~pstadt/ogr/ogr-weekly.png"><br>
    <img src="http://www1.distributed.net/~pstadt/ogr/ogr-monthly.png"><br>
    <img src="http://www1.distributed.net/~pstadt/ogr/ogr-quarterly.png"><br>
    <img src="http://www1.distributed.net/~pstadt/ogr/ogr-yearly.png"><br>
    <img src="http://www1.distributed.net/~pstadt/ogr/ogr-2years.png"><br>
    </div>
    <?
}

if (! isset($found) ) {
    Trigger_error("Sorry - There are no keyrate graphs available for this project",E_USER_WARNING);
}

include "../templates/footer.inc";
?>