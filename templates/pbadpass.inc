<?php
  print "
	<center>
	<table width=\"400\" border=\"0\"><tr><td>
	<h2>There was an error processing your request</h2>
	<p>
         You have entered either an invalid id, or an incorrect password.
	</p>
	<p>Note: If you were presented with a login/password box, the server
	   was expecting you to enter your email address and password, not 
	   your participant ID # and password.  That's a bit confusing.<br>
	   If you entered your ID # instead of your email address, close
	   your browser and try again.</p>
	<p>If your ID # was > 500000, then you've been re-numbered.  Due to
	   a bug in the stats server, about 900 participants were assigned 
	   ID numbers that were way too high.  Please search for your email
	   and see what your new ID number is.</p>
	</td></tr></table>";
?>
