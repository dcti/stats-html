<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
        "http://www.w3.org/TR/REC-html40/loose.dtd">
<?php
  // $Id: newteam4.php,v 1.3 2000/01/18 03:49:15 decibel Exp $
  
  include "etc/config.inc";
  include "etc/project.inc";

  sybase_connect($interface,$username,$password);

  $name = htmlspecialchars($name);

  $qs = "select * from STATS_team where name like '$name'";
  $result = sybase_query($qs);
  $rows = sybase_num_rows($result);
  if ($rows <> 0) {
    include "templates/tmdupename.inc";
    exit;
  }
  $qs = "select char(convert(int,rand(datepart(mi,getdate())*datepart(ss,getdate())*datepart(ms,getdate()))*25)+97) +
      char(convert(int,rand()*25)+97) +
      char(convert(int,rand()*25)+97) +
      char(convert(int,rand()*25)+97) +
      char(convert(int,rand()*25)+97) +
      char(convert(int,rand()*25)+97) +
      char(convert(int,rand()*25)+97) +
      char(convert(int,rand()*25)+97) as password";
  $result = sybase_query($qs);
  sybase_data_seek($result,0);
  $result = sybase_fetch_object($result);
  $pass = $result->password;

  $qs = "insert into STATS_team
	(name, contactname, contactemail,password)
	select
	'$name' as name,
	'$contactname' as contactname,
	'$contactemail' as contactemail,
	'$pass' as password";
  $result = sybase_query($qs);
  if ($result == "") {
    include "templates/tmerror.inc";
    exit;
  }

  $qs = "select * from STATS_team where name like '$name'";
  $result = sybase_query($qs);
  $rows = sybase_num_rows($result);
  if ($rows <> 1) {
    include "templates/tmerror.inc";
    exit;
  }
  sybase_data_seek($result,0);
  $par = sybase_fetch_object($result);
  $password = $par->password;
  $teamnum = 0+$par->team;
 
  print "
	<html>
	 <head>
	  <title>Adding Team data to stats...</title>
	 </head>
	 <body>
	  <center>
	   <h2>Saving your new team...</h2>
	   <h1>Your team number is:</h1>
           <p>
            <font color=\"#770000\" size=\"+2\">$teamnum</font>
	   </p>
	   <h1>Your team configuration password is:</h1>
           <p>
            <font color=\"#770000\" size=\"+2\">$pass</font>
	   </p>
	   <p>
	    Your team will <strong><font color=\"#770000\">not</font></strong> be listed in the stats database <strong>until you've joined it</strong>
	   </p>
	   <p>
	    After you join your team, it will show up after the next stats run.
	   </p>
	   <p>
	    You may edit your team information by using this link:
	    <br>
	    <a href=\"/tmedit.php3?team=$teamnum&pass=$pass\">http://stats.distributed.net/tmedit.php3?team=$teamnum&pass=$pass</a>
	   </p>
	   <p>
	    You should also join your team by using this link:
	    <br>
	    This link will require you to know your email address and your
	    participant password.
	    <br>
	    <a href=\"/pjointeam.php3?team=$teamnum\">http://stats.distributed.net/pjointeam.php3?team=$teamnum</a>
	   </p>
	  </center>
	 </body>";
?>
</html>
