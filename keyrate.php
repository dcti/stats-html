<?

include "etc/global.inc";
include "etc/modules.inc";
include "etc/project.inc";

$title = "Keyrate History";

include "templates/header.inc";
display_last_update();

if ($project_id == 8) { ?>
<div class="row">
  <div class="col-xs-6 col-md-3">
    <a href="http://bovine.statsdev.distributed.net/rc572-rate-10y.png" class="thumbnail">
      <img src="http://bovine.statsdev.distributed.net/rc572-rate-10y.png" alt="...">
    </a>
  </div>
  <div class="col-xs-6 col-md-3">
    <a href="http://bovine.statsdev.distributed.net/rc572-rate-3y.png" class="thumbnail">
      <img src="http://bovine.statsdev.distributed.net/rc572-rate-3y.png" alt="...">
    </a>
  </div>
  <div class="col-xs-6 col-md-3">
    <a href="http://bovine.statsdev.distributed.net/rc572-rate-1y.png" class="thumbnail">
      <img src="http://bovine.statsdev.distributed.net/rc572-rate-1y.png" alt="...">
    </a>
  </div>
  <div class="col-xs-6 col-md-3">
    <a href="http://bovine.statsdev.distributed.net/rc572-rate-1m.png" class="thumbnail">
      <img src="http://bovine.statsdev.distributed.net/rc572-rate-1m.png" alt="...">
    </a>
  </div>
  <div class="col-xs-6 col-md-3">
    <a href="http://bovine.statsdev.distributed.net/rc572-rate-1w.png" class="thumbnail">
      <img src="http://bovine.statsdev.distributed.net/rc572-rate-1w.png" alt="...">
    </a>
  </div>
</div>

<?php } elseif ($project_id == 26 || $project_id == 27 || $project_id == 28) { ?>

<div class="row">
  <div class="col-xs-6 col-md-3">
    <a href="http://bovine.statsdev.distributed.net/ogrng-rate-10y.png" class="thumbnail">
      <img src="http://bovine.statsdev.distributed.net/ogrng-rate-10y.png" alt="...">
    </a>
  </div>
  <div class="col-xs-6 col-md-3">
    <a href="http://bovine.statsdev.distributed.net/ogrng-rate-3y.png" class="thumbnail">
      <img src="http://bovine.statsdev.distributed.net/ogrng-rate-3y.png" alt="...">
    </a>
  </div>
  <div class="col-xs-6 col-md-3">
    <a href="http://bovine.statsdev.distributed.net/ogrng-rate-1y.png" class="thumbnail">
      <img src="http://bovine.statsdev.distributed.net/ogrng-rate-1y.png" alt="...">
    </a>
  </div>
</div>

<?php } else { ?>
<div class="panel panel-warning">
  <div class="panel-heading">Sorry!</div>
  <div class="panel-body">
    There is no keyrate information available for this project 
  </div>
</div>
<?php }
include "templates/footer.inc";
?>
