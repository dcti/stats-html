<?php
  // $Id: newteam4.php,v 1.11 2003/05/27 18:38:29 thejet Exp $
  
  include "../etc/config.inc";
  include "../etc/project.inc";
  include "../etc/team.php";
  include "../etc/teamstats.php";
  unset($proj_name);

  $title = "Adding Team data to stats...";
  $lastupdate = last_update('t');
  include "../templates/header.inc";

  // Create the team object to save the information
  $newteam = new Team($gdb, null);
  $newteam->set_name(htmlspecialchars($_GET['name']));
  $newteam->set_contact_name($_GET['contactname']);
  $newteam->set_contact_email($_GET['contactemail']);

  // Build a random password
  $passstring = "0Aa1Bb2Cc3Dd4Ee5Ff6Gg7Hh8Ii9JjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz";
  $password = "";
  for($i = 0; $i < 8; $i++)
  {
    $password .= substr($passstring, rand(0,strlen($passstring)-1), 1);
  }
  $newteam->set_password($password);

  $retVal = $newteam->save();
  if($retVal != "")
  {
    // validation error
    print "<br><h2>Validation Errors occurred:</h2><br>";
    print str_replace("\n", "<br>", $retVal);
    print "<br><a href=\"javascript:history.back()\">Go back and correct the problem</a><br>";
    //include("../templates/footer.inc");
    exit(0);
  }
  else
  {
    // Save was successful
  }

  /*
  //sybase_connect($interface,$username,$password);
  //
  //$name = htmlspecialchars($name);
  //
  $qs = "select * from STATS_team where name like '$name'";
  $result = sybase_query($qs);
  $rows = sybase_num_rows($result);
  if ($rows <> 0) {
    include "../templates/tmdupename.inc";
    include "../templates/footer.inc";
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
    include "../templates/tmerror.inc";
    include "../templates/footer.inc";
    exit;
  }

  $qs = "select * from STATS_team where name like '$name'";
  $result = sybase_query($qs);
  $rows = sybase_num_rows($result);
  if ($rows <> 1) {
    include "../templates/tmerror.inc";
    include "../templates/footer.inc";
    exit;
  }
  sybase_data_seek($result,0);
  $par = sybase_fetch_object($result);
  */
  $password = $newteam->get_password();
  $teamnum = $newteam->get_id();

  
  print "
	  <div style=\"text-align: center\">
	   <h2>Saving your new team...</h2>
	   <h1>Your team number is:</h1>
           <p>
            <h2 style=\"color: #770000\">$teamnum</h2>
	   </p>
	   <h1>Your team configuration password is:</h1>
           <p>
            <h2 style=\"color: #770000\">$password</h2>
	   </p>
	   <p>
	    Your team will <span style=\"font-weight: bold; color: #770000\">not</span> be listed in the stats database  <span style=\"font-weight: bold;\">until you've joined it</span>
	   </p>
	   <p>
	    After you join your team, it will show up after the next stats run.
	   </p>
	   <p>
	    You may edit your team information by using this link:
	    <br>
	    <a href=\"tmedit.php?team=$teamnum&pass=$password\">http://stats.distributed.net/team/tmedit.php?team=$teamnum&pass=$password</a>
	   </p>
	   <p>
	    You should also join your team by using this link:
	    <br>
	    This link will require you to know your email address and your
	    participant password.
	    <br>
	    <a href=\"/participant/pjointeam.php?team=$teamnum\">http://stats.distributed.net/participant/pjointeam.php?team=$teamnum</a>
	   </p>
	  </div>";
include "../templates/footer.inc";
?>
