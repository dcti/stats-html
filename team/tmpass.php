<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
        "http://www.w3.org/TR/REC-html40/loose.dtd">
<?
  // $Id: tmpass.php,v 1.12 2003/10/03 18:10:36 thejet Exp $

  // Variables Passed in url:
  //  team = team id

  $title = "Password request: Team $team";
  
  $myname = "tmpass.php";

  include "../etc/config.inc";
  include "../etc/modules.inc";
  include "../etc/project.inc";
  include "../etc/team.php";
  include "../templates/header.inc";

  display_last_update('t');
  $team = 0+$tm;

  if ($team < 1) {
    print "
	<h1>Error: No Team ID Supplied</h1>
	<h3>
	 This either means that you didn't follow a proper link, you didn't enter data in all the field,
 	 or your browser doesn't support the forms that we are using.  Please attempt to correct the problem, or
	 mail <a href=\"mailto:stats@distributed.net\">stats@distributed.net</a> for help.
	</h3>";
    include "../templates/footer.inc";
    print "</html>\n";
    exit();
  }

  $teamPtr =& new Team($gdb, $gproj, $team);
  $teamcontact = $teamPtr->get_contact_name();

  $pass = $teamPtr->get_password();

  print "<h2>Your request has been processed.</h2><br>\n";
  print "<h3>The password will be mailed to " . $teamPtr->get_contact_email() . " and should arrive within 10 minutes.</h3>\n";
  print "</body></html>\n";

  $message = "Greetings, $teamcontact:

You (or \"".$_SERVER['REMOTE_HOST']."\" [".$_SERVER['REMOTE_ADDR']."]) recently
requested the password for your distributed.net team account.  You
should keep this information confidential.  If you did not just request
your password, it just means that some confused person has clicked on
the \"mail the password to the team coordinator\" link on your team stats 
page.  This is no reason to be alarmed, they cannot get to your
password this way.

 Your password: $pass

You may edit your team information by visiting:

 http://stats.distributed.net/team/tmedit.php?team=$team&pass=$pass

If you'd prefer to not have your id/password revealed in the http logs
(this is a concern if you use a proxy), you can simply use the
url:

 http://stats.distributed.net/team/tmedit.php

You will be prompted for your team's ID # and password. 

To see your team stats, visit:

 http://stats.distributed.net/team/tmsummary.php?team=$team

Do not reply to this email.  Replies to this email will never be seen
by a real, live person.  If you need further assistance, please mail
help@distributed.net.

Thanks.";

  send_mail($teamPtr->get_contact_email(), "passmail@distributed.net", "Your distributed.net stats password", $message);

  $fh = fopen("/var/log/tmpass.log","a+");
  $ts = gmdate("M d Y H:i:s",time());
  fputs($fh,"$ts password for team $team requested by $REMOTE_HOST [$REMOTE_ADDR]\n");
  fclose($fh);

  include "../templates/footer.inc";
?>
