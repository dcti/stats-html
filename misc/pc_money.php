<?
# $Id: pc_money.php,v 1.6 2003/08/31 16:11:33 paul Exp $
# vi: ts=2 sw=2 tw=120 syntax=php

$title = "Disposition of Prize Money";

include "../etc/config.inc";
include "../etc/modules.inc";
include "../etc/project.inc";

display_last_update('e');

if ( $gproj->get_prize() == 0 ) {
  print "
    <center>
      <p>
        <font size=\"+2\">
          Sorry, there's no prize for this contest.
        </font>
      </p>
    </center>
  </body>
</html>";
 exit;
}

$fmt_prize = number_style_convert($gproj->get_prize());
$fmt_a = number_style_convert(($gproj->get_prize())*0.6);
$fmt_b = number_style_convert(($gproj->get_prize())*0.1);
$fmt_c = number_style_convert(($gproj->get_prize())*0.1);
$fmt_d = number_style_convert(($gproj->get_prize())*0.2);

$qs = "SELECT m.nonprofit, m.people, m.votes, n.name, n.url, n.comments
FROM (  SELECT p.nonprofit, count(*) AS people, sum(r.work_total * 1) AS votes
	FROM stats_participant p, email_rank r
	WHERE p.id = r.id
	AND r.project_id = 8
	GROUP BY p.nonprofit
	) m, stats_nonprofit n
WHERE m.nonprofit = n.nonprofit
ORDER BY votes desc";

$result = $gdb->query($qs);
$rows = $gdb->num_rows();
$par = $gdb->fetch_object();
$np_winner = $par->nonprofit;
$nm_winner = $par->name;

if( $np_winner <> 1 ) {
 $np_runnerup = 1;
 $nm_runnerup = "distributed.net";
} else {
 $gdb->data_seek(1);
 $par = $gdb->fetch_object($result);
 $np_runnerup = $par->nonprofit;
 $nm_runnerup = $par->name;
}

print "
  <center>
   <br>
   <p>
    <font  size=\"+2\">
     The US\$$fmt_prize prize will be divided as follows:
    </font>
   </p>
   <table border=\"0\">
    <tr>
     <td>$nm_winner</td>
     <td>US\$$fmt_a</td>
    </tr>
    <tr>
     <td>The individual who finds the key</td>
     <td>US\$$fmt_b</td>
    </tr>
    <tr>
     <td>The winning individual's team</td>
     <td>US\$$fmt_c</td>
    </tr>
    <tr>
     <td>$nm_runnerup</td>
     <td>US\$$fmt_d</td>
    </tr>
   </table>
   <hr>
   <p>
    <font size=\"+2\">
     How this is determined:
    </font>
   </p>
   <table width=\"80%\" border=\"0\">
    <tr>
     <td>
      <p>
       Each individual participant involved in the effort is allowed to select
       which non-profit he or she would prefer to send the prize money.  Each
       person will be given one vote per block submitted.  The non-profit that
       receives the most votes will be given 60% of the prize money.
      </p>
      <p>
       Of the remaining 40%, 10% will be given to the individual who finds the
       key and 10% will be given to that person's team.  If the winning individual
       is NOT on a team when they find the key, they will receive the entire 20%.
      </p>
      <p>
       The 20% remaining will be retained by distributed.net to fund additional
       projects.
      </p>
      <p>
       If distributed.net is, however, selected as the recipient non-profit, the
       20% that would have otherwise gone to distributed.net will instead be given
       to the runner-up non-profit.
      </p>
      <p>
       Is everyone confused yet?
      </p>
      <p>
       To choose your non-profit, simply edit your participant information.
       <br>
       If you know your email address and stats password, you can do so
       <a href=\"/pedit.php3\">here and now</a>.
      </p>
     </td>
    </tr>
   </table>
   <hr>
   <p>
    <font size=\"+2\">
     Current Voting Scoreboard
    </font>
   </p>
   <table>
    <tr bgcolor=\"#cccccc\">
     <td>Non-Profit</td>
     <td align=\"right\">Votes</td>
     <td align=\"right\">Supporters</td>
    </tr>";
 for( $i=0; $i<$rows; $i++) {
   $gdb->data_seek($i);
   $par = $gdb->fetch_object();
   $votes = number_style_convert($par->votes);
   $people = number_style_convert($par->people);
   print "
    <tr>
     <td>$par->name</td>
     <td align=\"right\">$votes</td>
     <td align=\"right\">$people</td>
    </tr>";
 }
 print "
   </table>
   <hr>
   <p>
    <font size=\"+2\">
     Non-Profit Information and Links
    </font>
   </p>
   <table width=\"80%\">";
 for( $i=$rows-1; $i>=0; $i--) {
   $gdb->data_seek($i);
   $par = $gdb->fetch_object($result);
   print "
    <tr>
     <td colspan=\"2\" bgcolor=\"#ccccc\">
      <a href=\"$par->url\"><font size=\"+1\" color=\"#000044\">$par->name</font></a>
     </td>
    </tr>
    <tr>
     <td width=\"20\">&nbsp;</td>
     <td>$par->comments</td>
    </tr>";
 }

?>
   </table>
  </center>
 </body>
</html>
