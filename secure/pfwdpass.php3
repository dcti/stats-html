<?php
include "security.inc";

include "../etc/config.inc";
include "../etc/project.inc";
include "../etc/modules.inc";

  sybase_connect($interface, $ss_login, $ss_passwd);

if($id == 0 ) { die; } //don't send out password for id = 0 

  if(isset($id)) {
    $qs = "select * from stats.dbo.STATS_participant where id = $id";
  } else {
    die; //$id gets set in config.inc... 
    $qs = "select * from stats.dbo.STATS_participant where email = '$email'";
  }
  $result = sybase_query($qs);
  $rows = sybase_num_rows($result);

  sybase_data_seek($result,0);
  $par = sybase_fetch_object($result);

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

  $ltr_body = "Hello!

Thanks for your participation in distributed.net.  We're sorry that you
have had difficulties obtaining the password for your email.  Sadly, 
there's no safe way for us to handle the mailing of passwords for dead
or mispelled email addresses automatically.  This mailing was handled 
by one of our support representatives, and we apologize if it has
taken longer than you'd hoped.

   The password for $par->email is '$pass'
   (without the quotes, of course)

If you are wanting to retire this email address into a more current
address, then you can use this url, and then follow the link at
the bottom that says \"retire this email address permanently.\"
(It's way down at the bottom)

   http://stats.distributed.net/pedit.php3?id=$id&pass=$pass

We hope that this solves your problem and hope that you don't 
hesitate to contact us in the future if you have additional problems.

Thanks again.

distributed.net technical support/$ss_login
";

  $warn_body = "Hello!

This email is to warn you that your distributed.net stats password has
been compromised.  Someone (obviously not you) has mailed the help team
at distributed.net claiming to be you.  For whatever reason, their story
was plausable enough that our staff believed them and they have now been
given your stats password.

Assuming this is in error, please let us know at help@distributed.net
as soon as possible so we may correct the damage.

$ss_login@distributed.net received an email recently from the address
'$addr' requesting the password to your distributed.net account.
(id #$id).  We believed them, and they now have your password.

Note: please do not reply to this email as it will not reach a human.
Rather, you should mail $ss_login@distributed.net and let them know
that there's been an error.";
?>
<html>
<?
  $id = 0+$par->id;
  $team = 0+$par->team;
  $retire_to = 0+$par->retire_to;

  if (!$addr) {
    print "
	<center>
	<h3>Manual Password Mailer</h3>
	<p>
	 This utility will mail the selected password to a different email
	 address,<br>typically used to give participants the password to dead
	 or mispeled email addresses.
	</p>
	<p>
	 Note that is normally our policy to refuse to mail passwords to @hotmail.com or
	 equivalent addresses which would remove our ability to adequately track down
	 the true recipient of the password in the event that the request was not 
	 legitimate.
	</p>
	<p>
	 The decision to mail passwords like this is left to the discretion of the
	 support personnel.  Use your best judgement.
	</p>
	<form action=\"pfwdpass.php3\" method=\"get\">
	Destination Email Address:
	<input type=\"text\" size=\"50\" name=\"addr\">
	<input type=\"hidden\" name=\"id\" value=\"$id\">
	<input type=\"submit\" value=\"Go!\">
	</form>
	<p>
	 The following email will be sent to the address you specify above:
	</p>
	<table border=\"1\"><tr><td bgcolor=\"#cccccc\"><pre>$ltr_body</pre></td></tr></table>
	<p>
	 The following email will be sent to the bad address ($par->email)
	 <br>
	 <i>(Note, the requesting address will be properly inserted in the actual email)</i>
	</p>
	<table border=\"1\"><tr><td bgcolor=\"#eecccc\"><pre>$warn_body</pre></td></tr></table>";
  } else {
    print "
	<center>
	<h3>Mailing...</h3>
	<p>
	 Sending an email to $addr containing the password for $par->email
	</p>
	<form action=\"psearch.php3\" method=\"get\">
	 Search for another email:
	 <input type=\"text\" name=\"st\" width=\"64\">
	 <input type=\"submit\" value=\"search\">
	</form>";
    send_mail($addr, "help@distributed.net", "The password you requested", $ltr_body);
    send_mail($par->email, "passmail@distributed.net", "WARNING!  Password compromise", $warn_body);
    $fh = fopen("/var/log/passmail.log","a+");
    $ts = gmdate("M d Y H:i:s",time());
    fputs($fh,"$ts $ss_login sent password for id $id ($par->email) sent to $addr\n");
    fclose($fh);
  }

?>
 </body>
</html>
