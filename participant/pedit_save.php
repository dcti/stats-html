<?php
  if(isset($cookie)) {
    if( $cookie == "yes" ) {
      SetCookie("sbid",$id,time()+3600*24*365,"/");
      SetCookie("sbpass",$pass,time()+3600*24*365,"/");
    }
  }
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
        "http://www.w3.org/TR/REC-html40/loose.dtd">
<?php
  // $Id: pedit_save.php,v 1.2 1999/07/19 17:38:22 nugget Exp $

  // psecure.inc will obtain $id and $pass from the user.
  // Input may come from the url, http headers, or a client cookie
  
  include "etc/config.inc";
  include "etc/project.inc";
  include "etc/psecure.inc";

  if ($dem_yob == "") {
    $dem_yob = 0;
  }

  $contact_name = htmlspecialchars($contact_name);

  $qs = "update STATS_participant set
	listmode = $listas,
	nonprofit = $nonprofit,
	dem_yob = $dem_yob,
	dem_heard = $dem_heard,
	dem_gender = '$dem_gender',
	dem_motivation = $dem_motivation,
	contact_name = '$contact_name',
	contact_phone = '$contact_phone',
        motto = '$motto',
	dem_country = '$dem_country'
	where id = $id and password = '$pass'";

if ($debug == yes) print $qs;

  $result = sybase_query($qs);
  print "
	<html>
	 <head>
	  <title>Updating $par->email data</title>
	 </head>";
if ($debug != yes) print "	 <meta http-equiv=\"refresh\" content=\"4; URL=http://stats.distributed.net/\">";
print "
	 <body>
	  <center>
	   <h2>Saving your information...</h2>
	  </center>
	 </body>";
?>
</html>
