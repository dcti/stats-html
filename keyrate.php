<?

include "etc/global.inc";
include "etc/modules.inc";
include "etc/project.inc";

$title = "Keyrate History";

include "templates/header.inc";
display_last_update();
echo('<br>');
if ($project_id == 8) { ?>
    <img src="http://bovine.statsdev.distributed.net/rc572-rate-10y.png"><br>
    <img src="http://bovine.statsdev.distributed.net/rc572-rate-3y.png"><br>
    <img src="http://bovine.statsdev.distributed.net/rc572-rate-1y.png"><br>
    <img src="http://bovine.statsdev.distributed.net/rc572-rate-1m.png"><br>
    <img src="http://bovine.statsdev.distributed.net/rc572-rate-1w.png"><br>

<?php } elseif ($project_id == 26 || $project_id == 27 || $project_id == 28) { ?>
    <img src="http://bovine.statsdev.distributed.net/ogrng-rate-10y.png"><br>
    <img src="http://bovine.statsdev.distributed.net/ogrng-rate-3y.png"><br>
    <img src="http://bovine.statsdev.distributed.net/ogrng-rate-1y.png"><br>

<?php } else { ?>
<p>There is no keyrate information available for this project</p>
<?php }
echo("<hr>\r\n");
include "templates/footer.inc";
?>
