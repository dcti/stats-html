<?php
  # $Id: pbadpass.inc,v 1.6 2000/06/13 20:18:45 decibel Exp $

  print "
	<center>
	<table width=\"400\" border=\"0\"><tr><td>
	<h2>There was an error processing your request</h2>
	<p>
         You have entered either an invalid id, or an incorrect password.
	</p>
	<p>If your ID # was > 500000, then you've been re-numbered.  Due to
	   a bug in the stats server, about 900 participants were assigned 
	   ID numbers that were way too high.  Please search for your email
	   and see what your new ID number is.</p>
	<p>Also, if previous to 3-Feb-2000 your password was all a's,
	   we've told the stats server to generate a new password for you.
	   If this is the first time you've seen this error, your password
	   should be on it's way to you.  As usual, feel free to contact
	   help@distributed.net if you have any further questions
	   regarding this change.</p>
	</td></tr></table>";
?>
