<?php
include "etc/header.inc";
$project_id = $_REQUEST['project_id'];
switch ($project_id) {
    case 8:
        $log = shell_exec('tail -50 ~statproc/log/r72.log');
	break;
    case 24:
    case 25:
        $log = shell_exec('tail -50 ~statproc/log/ogrp2.log');
	break;
    case 26:
    case 27:
        $log = shell_exec('tail -50 ~statproc/log/ogrng.log');
	break;
}
echo nl2br($log);
include "etc/footer.inc";
?>
