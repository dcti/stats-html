<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
        "http://www.w3.org/TR/REC-html40/loose.dtd">
<?
  // $Id: ppass.php,v 1.13 2002/04/09 23:18:49 jlawson Exp $

  // Variables Passed in url:
  //  id == email id

  $title = "Password request: individual email [$id]";
  
  $myname = "ppass.php";

  include "../etc/config.inc";
  include "../etc/modules.inc";
  include "../etc/project.inc";
  include "../templates/header.inc";

  if (!$id) {
    print "
	<h1>Error: No ID Supplied</h1>
	<h3>
	 This either means that you didn't follow a proper link, you didn't enter data in all the field,
 	 or your browser doesn't support the forms that we are using.  Please attempt to correct the problem, or
	 mail <a href=\"mailto:stats@distributed.net\">stats@distributed.net</a> for help.
	</h3>";
    include "../templates/footer.inc";
    print "</html>\n";
    exit();
  }

  sybase_pconnect($interface, $username, $password);
  sybase_query("set rowcount 50");
  $qs = "select * from stats_PARTICIPANT where id = $id";

  $result = sybase_query($qs);
  sybase_data_seek($result,0);
  $par = sybase_fetch_object($result);
  $id = 0+$par->id;

  // If we don't have a password assigned yet, then generate one
  if (!$par->password) {
    mt_srand((double)microtime()*1000000);
    $pass = "";
    for ($i = 1; $i <= 8; $i++) {
      // Generate a random number and convert it to the ASCII value for 0-9, A-Z, or a-z
      $rand = mt_rand(0, 61);	// 10 ('0'-'9') + 26 ('A' - 'Z') + 26 ('a' - 'z') = 62 - 1 (we start at 0) = 61
      if ($rand > 35) {		// 10           + 26             - 1 = 35
        $rand += 61; 		// 97 (asc('a')) - 36 ( 35 + 1 ) = 61
      } elseif ($rand > 9) {	// 10           - 1 = 9
        $rand += 55;		// 65 (asc('A')) - 10 ( 9 + 1 ) = 55
      } else {
	$rand += 48;
      }

      $pass .= chr($rand);	// Append the resulting ASCII code to the password string
    }
    sybase_query("update STATS_participant set password = \"$pass\" where id = $id");
    $result = sybase_query($query);
  } else {
   $pass = $par->password;
  }

  print "<h2>Your request has been processed.</h2><br>\n";
  print "<h3>The password will be mailed and should arrive within 10 minutes.</h3>\n";
  print "</body></html>\n";

  $message = "Greetings, $par->email.

You (or \"$REMOTE_HOST\" [$REMOTE_ADDR]) recently
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
should not use your ID # but your email address when using this
method of authentication.

To see your RC5-64 stats, visit:

 http://stats.distributed.net/participant/psummary.php?project_id=6&id=$id

Do not reply to this email.  Replies to this email will never be seen
by a real, live person.  If you need further assistance, please mail
help@distributed.net.

Thanks.";

  send_mail($par->email, "statspass@distributed.net", "Your distributed.net stats password", $message);

  $fh = fopen("/var/log/ppass.log","a+");
  $ts = gmdate("M d Y H:i:s",time());
  fputs($fh,"$ts password for id $id ($par->email) requested by $REMOTE_HOST [$REMOTE_ADDR]\n");
  fclose($fh);

?>
