<?php
include "etc/header.inc";
$project_id = $_REQUEST['project_id'];
if ($project_id == 8) {
    $log = shell_exec('tail -50 ~statproc/log/r72.log');
}
if ($project_id == 24) {
    $log = shell_exec('tail -50 ~statproc/log/ogrp2.log');
}
if ($project_id == 25) {
    $log = shell_exec('tail -50 ~statproc/log/ogrp2.log');
}
echo nl2br($log);
include "etc/footer.inc";
?>
