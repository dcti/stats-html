<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
        "http://www.w3.org/TR/REC-html40/loose.dtd">
<?
 // $Id: motto.php3,v 1.4 2003/05/20 20:01:54 paul Exp $
 //
 // Not a production script.

 $myname = "demographics.php3";

 include "etc/config.inc";
 include "etc/number_format.inc";

 sybase_pconnect($interface, $username, $password);
 $qs = "select motto,email from stats.dbo.STATS_Participant where motto <> '' and motto <> NULL and motto not like '%%<%%'";
 sybase_query("set rowcount 0");
 $result = sybase_query($qs);

 if ($result == "") {
     include "templates/error.inc";
   }
   exit();
 }
 $rows = sybase_num_rows($result);

 print "
	<html>
	 <head>
	  <title>Mottos</title>
	 </head>
	 <body>
	  <h2>Mottos in stats</h2>";
 for ($i = 0; $i < $rows; $i++) {
   sybase_data_seek($result,$i);
   $par = sybase_fetch_object($result);
   print "$par->motto <i>-$par->email</i><br>\n";
 }
 print "
	 </body>
	</html>";
?>
