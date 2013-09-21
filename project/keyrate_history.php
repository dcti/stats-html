<?
include "../etc/global.inc";
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
    <img src="http://bovine.statsdev.distributed.net/rc572-rate-10y.png"><br>
    <img src="http://bovine.statsdev.distributed.net/rc572-rate-3y.png"><br>
    <img src="http://bovine.statsdev.distributed.net/rc572-rate-1y.png"><br>
    <img src="http://bovine.statsdev.distributed.net/rc572-rate-1m.png"><br>
    <img src="http://bovine.statsdev.distributed.net/rc572-rate-1w.png"><br>
    </div>
    <?
}
if ($gproj -> get_type() == 'OGR' )
{
    $found = true;
    ?>
    <div align="center">
    <img src="http://bovine.statsdev.distributed.net/ogrng-rate-10y.png"><br>
    <img src="http://bovine.statsdev.distributed.net/ogrng-rate-3y.png"><br>
    <img src="http://bovine.statsdev.distributed.net/ogrng-rate-1y.png"><br>
    </div>
    <?
}

if (! isset($found) ) {
    Trigger_error("Sorry - There are no keyrate graphs available for this project",E_USER_WARNING);
}

include "../templates/footer.inc";
?>
