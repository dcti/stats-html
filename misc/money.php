<?php
    // $Id: money.php,v 1.8 2004/07/29 10:13:51 paul Exp $
    // vi: ts=4 sw=4 tw=120

    include '../etc/global.inc';
    include '../etc/modules.inc';
    include '../etc/project.inc';

    $title = 'Disposition of Prize Money';
    $filename = '../cache/money_' . $project_id . '.inc';

    include '../templates/header.inc';

    if(file_exists($filename)) {
        include "$filename";
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

    include '../templates/footer.inc';

?>