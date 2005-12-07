<?php
include "etc/header.inc";
?>
<p>
Welcome to stats-slash-secure! Select from the menus above!
</p>
<p>
Note: Only participant management is working at the moment.
</p>
<?php if ($readonly_secure != 0) { ?>
<p>
<b>READ ONLY</b><br>
The site is currently read-only. As a result no changes are possible at the moment.
</p>
<p>Sorry for any inconvenience caused</p>
<?php } ?>
<?
include "etc/footer.inc";
?>
