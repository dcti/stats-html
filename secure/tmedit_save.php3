<?php
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
        "http://www.w3.org/TR/REC-html40/loose.dtd">
<?php
  // $Id: tmedit_save.php3,v 1.2 1999/10/31 18:02:03 nugget Exp $

  // tmsecure.inc will obtain $team and $tpass from the user.
  // Input may come from the url, http headers, or a client cookie
  
  include "security.inc";
  include "../etc/config.inc";
  include "../etc/project.inc";

  sybase_connect($interface,$username,$password);
  $qs = "select * from STATS_team where team = $team";
  $result = sybase_query($qs);
  $rows = sybase_num_rows($result);

  if( $rows <> 1) {
    include "templates/tmbadpass.inc";
    exit;
  }
  sybase_data_seek($result,0);
  $par = sybase_fetch_object($result);

  $listmode = 0+$par->listmode;
  if ($listmode == 8 or $listmode == 9 or $listmode == 18 or $listmode == 19) {
    include "templates/tmlocked.inc";
    exit;
  }

  $name = htmlspecialchars($name);

  $qs = "update STATS_team set
	listmode = $listmode,
	name = '$name',
	url = '$url',
	contactname = '$contactname',
	contactemail = '$contactemail',
	logo = '$logo',
	showmembers = '$showmembers',
	showpassword = '$showpassword',
	description = '$description'
	where team = $team";

  $result = sybase_query($qs);
  print "
	<html>
	 <head>
	  <title>Updating $par->name data</title>
	 </head>
	 <meta http-equiv=\"refresh\" content=\"4; URL=http://stats.distributed.net/secure/\">
	 <body>
	  <center>
	   <h2>Saving your information...</h2>
	  </center>
	 </body>";
?>
</html>
