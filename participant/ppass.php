<?
  // $Id: ppass.php,v 1.16 2003/10/21 15:42:57 thejet Exp $

  // Variables Passed in url:
  //  id == email id
 
  $myname = "ppass.php";

  include "../etc/config.inc";
  include "../etc/modules.inc";
  include "../etc/project.inc";
  include "../etc/participant.php";

  $title = "Password request: individual email [$id]";

  include "../templates/header.inc";
  display_last_update();

  $id = 0 + $id;
 
  if ($id <= 0) {
    print "
	<h1>Error: No ID Supplied</h1>
	<h3>
	 This either means that you didn't follow a proper link, you didn't enter data in all the field,
 	 or your browser doesn't support the forms that we are using.  Please attempt to correct the problem, or
	 mail <a href=\"mailto:stats@distributed.net\">stats@distributed.net</a> for help.
	</h3>";
    include "../templates/footer.inc";
    exit();
  }

  // Create the new participant object
  $par = new Participant($gdb, $gproj, $id);

  // If we don't have a password assigned yet, then generate one
  if (trim($par->get_password()) == "") {
    print "Generating new password...<br>";
    mt_srand((double)microtime()*1000000);
    $pass = "";
    // Build a random password
    $passstring = "0Aa1Bb2Cc3Dd4Ee5Ff6Gg7Hh8Ii9JjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz";
    for($i = 0; $i < 8; $i++)
    {
      $pass .= substr($passstring, mt_rand(0,strlen($passstring)-1), 1);
    }
    $par->set_password($pass);
    if($par->save() != "")
    {
      print "
        <h1>Error: Unable to save new participant password</h1>
        <h3>
         This probably means that there is a problem with the database server.  Please
         try again later. If you have further questions you can mail
         <a href=\"mailto:stats@distributed.net\">stats@distributed.net</a> for help.
        </h3>";
      include "../templates/footer.inc";
      exit();
    }

  } else {
   $pass = $par->get_password();
  }

  print "<h2>Your request has been processed.</h2><br>\n";
  print "<h3>The password will be mailed and should arrive within 10 minutes.</h3>\n";
  print "</body></html>\n";

  $message = "Greetings, " . $par->get_email() . ".

You (or \"". $_SERVER['REMOTE_HOST'] . "\" [". $_SERVER['REMOTE_ADDR'] . "]) recently
requested the password for your distributed.net stats account.  You
should keep this information confidential.  If you did not just request
your password, it just means that some confused person has clicked on
the \"mail me my password\" link on your personal stats page.  This is
no reason to be alarmed, they cannot get to your password this way.

 Your password: $pass

You may edit your personal information by visiting:

 http://stats.distributed.net/participant/pedit.php?id=$id&pass=$pass

If you'd prefer to not have your id/password revealed in the http logs
(this is a concern if you use a proxy), you can simply use the
url:

 http://stats.distributed.net/participant/pedit.php

You will be prompted for your email address and password.  Note, you
should use your ID # as the 'username' when using this
method of authentication.

To see your participant stats, visit:

 http://stats.distributed.net/participant/psummary.php?id=$id

Do not reply to this email.  Replies to this email will never be seen
by a real, live person.  If you need further assistance, please mail
help@distributed.net.

Thanks.";

  send_mail($par->get_email(), "statspass@distributed.net", "Your distributed.net stats password", $message);

  $fh = fopen("/var/log/ppass.log","a+");
  $ts = gmdate("M d Y H:i:s",time());
  fputs($fh,"$ts password for id $id (" . $par->get_email() . ") requested by " . $_SERVER['REMOTE_HOST'] . " [" . $_SERVER['REMOTE_ADDR'] . "]\n");
  fclose($fh);

?>
