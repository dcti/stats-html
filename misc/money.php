<?php

# $Id: money.php,v 1.6 2004/07/01 10:26:01 fiddles Exp $
# vi: ts=2 sw=2 tw=120

include "../etc/config.inc";
include "../etc/modules.inc";
include "../etc/project.inc";

$title = "Disposition of Prize Money";
$filename = "../cache/money_$project_id.inc";

include "../templates/header.inc";

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
include "../templates/footer.inc";

?>
