#!/usr/bin/perl -Tw

use strict;
use CGI;

$ENV{PATH} = '/usr/bin:/bin';
&CGI::ReadParse;

my $text = "";

print "Content-type: text/html

<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\"
\"http://www.w3.org/TR/REC-html40/loose.dtd\">

<html>
 <meta http-equiv=\"refresh\" content=\"60\">
 <head>
  <title>stats.distributed.net - secure - RC5 Project Log</title>
 </head>
 <body bgcolor=\"#440000\" text=\"#aaaaaa\" link=\"#007777\">
  <center>
   <table cellpadding=\"4\" cellspacing=\"0\" width=\"100%\">
    <tr bgcolor=\"#000000\">
     <td width=\"100%\"><font size=\"+4\" color=\"#ffffff\"><strong>stats</strong>.distributed.net secure site</font></td>
     <td nowrap>RC5 Log<br>Last 50 Lines</td>
    </tr>
   </table>
  </center>
  <font face=\"lucida console,courier new,courier\" size=\"-1\">
   <p>
";
$text = `tail -50 ~statproc/log/rc5.log`;
$text =~ s/\</\&lt;/g;
$text =~ s/\>/\&gt;/g;
$text =~ s/([a-z]\S+\@\S+\.[a-z]{2,3})/<a href="mailto:$1">$1<\/a>/gi;
$text =~ s/\n/<br>\n/g;
print "$text";
print "
   </p>
  </font>
 </body>
</html>
";

