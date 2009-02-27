<?

include "etc/global.inc";
include "etc/modules.inc";
include "etc/project.inc";

$title = "Keyrate History";

include "templates/header.inc";
display_last_update();
echo('<br>');
if ($project_id == 8) { ?>
<img src="http://www1.distributed.net/~pstadt/rc5-72/rc5-72-complete.png" alt="complete"><br><br>
<img src="http://www1.distributed.net/~pstadt/rc5-72/rc5-72-daily.png" alt="daily"><br><br>
<img src="http://www1.distributed.net/~pstadt/rc5-72/rc5-72-weekly.png" alt="weekly"><br><br>
<img src="http://www1.distributed.net/~pstadt/rc5-72/rc5-72-monthly.png" alt="monthly"><br><br>
<img src="http://www1.distributed.net/~pstadt/rc5-72/rc5-72-quarterly.png" alt="quarterly"><br><br>
<img src="http://www1.distributed.net/~pstadt/rc5-72/rc5-72-yearly.png" alt="yearly"><br><br>
<img src="http://www1.distributed.net/~pstadt/rc5-72/rc5-72-2years.png" alt="2years"><br>

<?php } elseif ($project_id == 24 || $project_id == 25) { ?>
<img src="http://www1.distributed.net/~pstadt/ogrp2/ogrp2-daily.png" alt="daily"><br><br>
<img src="http://www1.distributed.net/~pstadt/ogrp2/ogrp2-weekly.png" alt="weekly"><br><br>
<img src="http://www1.distributed.net/~pstadt/ogrp2/ogrp2-monthly.png" alt="monthly"><br><br>
<img src="http://www1.distributed.net/~pstadt/ogrp2/ogrp2-quarterly.png" alt="quarterly"><br><br>
<img src="http://www1.distributed.net/~pstadt/ogrp2/ogrp2-yearly.png" alt="yearly"><br><br>
<!--<img src="http://www1.distributed.net/~pstadt/ogrp2/ogrp2-2years.png" alt="2years"><br>-->

<?php } elseif ($project_id == 26 || $project_id == 27 || $project_id == 28) { ?>
<img src="http://www1.distributed.net/~pstadt/ogrng/ogrng-daily.png" alt="daily"><br><br>
<img src="http://www1.distributed.net/~pstadt/ogrng/ogrng-weekly.png" alt="weekly"><br><br>
<img src="http://www1.distributed.net/~pstadt/ogrng/ogrng-monthly.png" alt="monthly"><br><br>
<img src="http://www1.distributed.net/~pstadt/ogrng/ogrng-quarterly.png" alt="quarterly"><br><br>
<img src="http://www1.distributed.net/~pstadt/ogrng/ogrng-yearly.png" alt="yearly"><br><br>
<!--<img src="http://www1.distributed.net/~pstadt/ogrng/ogrng-2years.png" alt="2years"><br>-->

<?php } else { ?>
<p>There is no keyrate information available for this project</p>
<?php }
echo("<hr>\r\n");
include "templates/footer.inc";
?>
