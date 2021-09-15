<?php


if (isset($project_id)) {
    $t_project_id = "project_id=$project_id";
} else {
	$t_project_id = "";
}
if (!isset($myname)) {
    $myname = "";
}



    $query_string = '';
    foreach ($_GET as $getvar => $getval) {
        if ($getvar != 'project_id') {
            $query_string .= '&amp;' . htmlentities($getvar) . '=' . htmlentities(stripslashes($getval));
        }
    }

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>stats.distributed.net: <? if (isset($gproj) && $gproj->is_project_loaded()) { echo $gproj->get_name(); }?> <?=$title?></title> 
    <!-- Bootstrap -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap theme -->
    <link href="/css/bootstrap-theme.min.css" rel="stylesheet">

    <link href="/css/dcti.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body role="document">
    <!-- Fixed navbar -->
    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="/">distributed.net</a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Project Stats<b class="caret"></b></a> 
              <ul class="dropdown-menu">
                <li><a href="/projects.php?<?=$t_project_id?>">Overall Project Stats</a></li>
                <li><a href="/keyrate.php?<?=$t_project_id?>">Keyrate History</a></li>
                <li><a href="/misc/platformlist.php?<?=$t_project_id?>&amp;view=tco">CPU/OS Distribution</a></li>
                <li><a href="/misc/countries.php?<?=$t_project_id?>">Participating Countries</a></li>
                <? if ( isset( $gproj ) && $gproj->is_project_loaded() && $gproj->get_prize() <> 0 ) { ?>
                <li><a href="/misc/money.php?<?=$t_project_id?>">Distribution of Prize Money</a></li>
                <? } ?>
              </ul>
            </li>

            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Participant Stats<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="/participant/plist.php?<?=$t_project_id?>&amp;low=1&amp;limit=100">Top 100 Participants Overall</a></li>
                <li><a href="/participant/plist.php?<?=$t_project_id?>&amp;low=1&amp;limit=100&amp;source=y">Top 100 Participants Yesterday</a></li> 
              </ul>
            </li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Team Stats<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="/team/tlist.php?<?=$t_project_id?>&amp;low=1&amp;limit=100">Top 100 Teams Overall</a></li>
                <li><a href="/team/tlist.php?<?=$t_project_id?>&amp;low=1&amp;limit=100&amp;source=y">Top 100 Teams Yesterday</a></li>
              </ul>
            </li>

            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Switch Project <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li class="dropdown-header">Active Projects</li>   
                <li><a href="<?=$myname?>?project_id=8<?=$query_string?>">RC5-72 - RSA Labs' 72bit RC5 Encryption Challenge</a></li>
                <li><a href="<?=$myname?>?project_id=28<?=$query_string?>">OGR-28 - Optimal Golomb Rulers</a></li>
                <li class="divider"></li>
                <li class="dropdown-header">Completed Projects</li>
                <li><a href="<?=$myname?>?project_id=24<?=$query_string?>">OGR-24 - Optimal Golomb Rulers</a></li>
                <li><a href="<?=$myname?>?project_id=25<?=$query_string?>">OGR-25 - Optimal Golomb Rulers</a></li>
                <li><a href="<?=$myname?>?project_id=26<?=$query_string?>">OGR-26 - Optimal Golomb Rulers</a></li>
                <li><a href="<?=$myname?>?project_id=27<?=$query_string?>">OGR-27 - Optimal Golomb Rulers</a></li>
                <li><a href="<?=$myname?>?project_id=3<?=$query_string?>">RC5-56 - RSA Labs' 56bit RC5 Encryption Challenge</a></li>
                <li><a href="<?=$myname?>?project_id=5<?=$query_string?>">RC5-64 - RSA Labs' 64bit RC5 Encryption Challenge</a></li>
                <li><a href="<?=$myname?>?project_id=205<?=$query_string?>">RC5-64(all) - RSA Labs' 64bit RC5 Encryption Challenge (plus work after key found)</a></li>
              </ul>
            </li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">My Distributed.net <b class="caret"></b></a>
              <ul class="dropdown-menu">
                 <li><a href="/participant/pedit.php?project_id=24">Edit your Information</a></li>
                 <li><a href="/team/newteam1.php">Create a new Team</a></li> 
              </ul>
          </ul>

        </div><!--/.nav-collapse -->
      </div>
    </div>


  <div class="container">
    <div class="main"> 
    <div class="page-header">
      <h1>
<? if(isset($gproj) && $gproj->is_project_loaded()) {
    echo $gproj->get_name() . " / ";
} else {echo "&nbsp;";}
?>  <? if (isset($title)) { echo $title;}?>
      </h1>
    </div>
   </div>

<?php
if( isset($gproj) && !$gproj->is_project_loaded()) {
?>
<div class="panel panel-warning">
  <div class="panel-heading">Sorry!</div>
  <div class="panel-body">
    The project you requested does not exist.
  </div>
</div>
<?php
  include "templates/footer.inc";
  die;
}
?>
<!--
        <form method="GET" name="par-search" id="par-search" action="/participant/psearch.php">
        <div>
          <input type="hidden" name="project_id" value="<?=$project_id?>">
          <input style="margin-top: 5px;" type="text" name="st" size="16" maxlength="42">
          <input style="margin-top: 5px;" type="submit" value="Search"><br>
        </div>
        </form>
        <form id="team-search" name="team-search" method="GET" action="/team/tsearch.php">
	<div>
          <input type="hidden" name="project_id" value="<?=$project_id?>">
          <input style="margin-top: 5px;" type="text" name="st" size="16" maxlength="32">
          <input style="margin-top: 5px;" type="submit" value="Search"><br>
        </div>
        </form>
-->
