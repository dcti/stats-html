<?php
include "security.inc";

include "../etc/config.inc";
include "../etc/project.inc";

  sybase_connect($interface, $ss_login, $ss_passwd);
  if(isset($id)) {
    $qs = "select * from stats.dbo.STATS_participant where id = $id";
  } else {
    $qs = "select * from stats.dbo.STATS_participant where email = '$email'";
  }
  $result = sybase_query($qs);
  $rows = sybase_num_rows($result);

  if( $rows <> 1) {
    include "templates/pbadpass.inc";
    exit;
  }
  sybase_data_seek($result,0);
  $par = sybase_fetch_object($result);
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
        "http://www.w3.org/TR/REC-html40/loose.dtd">
<?

  if ($dem_yob == "") {
    $dem_yob = 0;
  }
  $qs = "update stats.dbo.STATS_participant set
	listmode = $listmode,
	dem_yob = $dem_yob,
	dem_heard = $dem_heard,
	dem_gender = '$dem_gender',
	dem_motivation = $dem_motivation,
	contact_name = '$contact_name',
	contact_phone = '$contact_phone',
        motto = '$motto',
        password = '$pword',
	dem_country = '$dem_country'
	where id = $id";


  $result = sybase_query($qs);
  print "
	<html>
	 <head>
	  <title>Updating $par->email data</title>
	 </head>
	 <meta http-equiv=\"refresh\" content=\"4; URL=http://stats.distributed.net/secure/\">
	 <body>
	  <center>
	   <h2>Saving your information...</h2>
	  </center>
	 <!-- qs: $qs, result: $result. -->
	 </body>";
?>
</html>
