<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
        "http://www.w3.org/TR/REC-html40/loose.dtd">
<?
  // $Id: ppass.php,v 1.10 2000/01/18 03:51:59 decibel Exp $

  // Variables Passed in url:
  //  id == email id

  $title = "Password request: individual email [$id]";
  
  $myname = "ppass.php3";

  include "etc/config.inc";
  include "etc/modules.inc";
  include "etc/project.inc";
  include "templates/header.inc";

  if (!$id) {
    print "
	<h1>Error: No ID Supplied</h1>
	<h3>
	 This either means that you didn't follow a proper link, you didn't enter data in all the field,
 	 or your browser doesn't support the forms that we are using.  Please attempt to correct the problem, or
	 mail <a href=\"mailto:stats@distributed.net\">stats@distributed.net</a> for help.
	</h3>";
    include "templates/footer.inc";
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

  if (!$par->password) {
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

 http://stats.distributed.net/pedit.php3?id=$id&pass=$pass

If you'd prefer to not have your id/password revealed in the http logs
(this is a concern if you use a proxy), you can simply use the
url:

 http://stats.distributed.net/pedit.php3

You will be prompted for your email address and password.  Note, you
should not use your ID # but your email address when using this
method of authentication.

To see your RC5-64 stats, visit:

 http://stats.distributed.net/rc5-64/psummary.php3?id=$id

Do not reply to this email.  Replies to this email will never be seen
by a real, live person.  If you need further assistance, please mail
help@distributed.net.

Thanks.";

  send_mail($par->email, "passmail@distributed.net", "Your distributed.net stats password", $message);

  $fh = fopen("/var/log/ppass.log","a+");
  $ts = gmdate("M d Y H:i:s",time());
  fputs($fh,"$ts password for id $id ($par->email) requested by $REMOTE_HOST [$REMOTE_ADDR]\n");
  fclose($fh);

?>
