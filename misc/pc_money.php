<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
        "http://www.w3.org/TR/REC-html40/loose.dtd">
<?
 # $Id: pc_money.php,v 1.1 2002/08/12 21:10:15 paul Exp $

 $title = "Disposition of Prize Money";

 include "../etc/config.inc";
 include "../etc/modules.inc";
 include "..etc/project.inc";
 include "..etc/lastupdate.inc";
 include "templates/header.inc";

 $constant_tot_blocks = 68719476736;
 $constant_keys_in_one_block = 268435456;

 $constant_prize = 10000;
 $fmt_prize = number_style_convert($constant_prize);
 $fmt_a = number_style_convert($constant_prize*.6);
 $fmt_b = number_style_convert($constant_prize*.1);
 $fmt_c = number_style_convert($constant_prize*.1);
 $fmt_d = number_style_convert($constant_prize*.2);

 $tot_blocks_to_search = number_style_convert($constant_tot_blocks);

 sybase_pconnect($interface,$username,$password);
/*
 $qs = "select distinct
	 S.nonprofit, count(S.nonprofit) as people, sum(blocks) as votes
	into #MONEYa
	from
	 stats.dbo.STATS_participant S,
	 stats.statproc.CACHE_tm_MEMBERS M
	where
	 (S.nonprofit <> 0 and S.nonprofit <> NULL) and
	 (S.id = M.id) 
	group by
	 S.nonprofit";
*/

 $qs = "select distinct
         S.nonprofit, count(S.nonprofit) as people, sum(WORK_TOTAL) as votes
        into #MONEYa
        from
         stats.dbo.STATS_participant S,
         email_rank M
        where
         (S.nonprofit <> 0 and S.nonprofit <> NULL) and
         (convert(int,S.id) = M.id) and M.project_id =5 and S.listmode <10
        group by
         S.nonprofit";


 $result = sybase_query("set rowcount 0");
 $result = sybase_query($qs);

 $qs = "select M.nonprofit, M.people, M.votes, S.name, S.url, S.comments
	from #MONEYa M, stats.dbo.STATS_nonprofit S
	where (M.nonprofit = S.nonprofit)
	order by M.votes desc";
 $result = sybase_query($qs);
 $rows = sybase_num_rows($result);

 sybase_data_seek($result,0);
 $par = sybase_fetch_object($result);
 $np_winner = $par->nonprofit;
 $nm_winner = $par->name;

 if( $np_winner <> 1 ) {
   $np_runnerup = 1;
   $nm_runnerup = "distributed.net";
 } else {
   sybase_data_seek($result,1);
   $par = sybase_fetch_object($result);
   $np_runnerup = $par->nonprofit;
   $nm_runnerup = $par->name;
 }

 print "
  <center>
   <br>
   <p>
    <font $fontd size=\"+2\">
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
    <font $fontd size=\"+2\">
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
    <font $fontd size=\"+2\">
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
   sybase_data_seek($result,$i);
   $par = sybase_fetch_object($result);
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
    <font $fontd size=\"+2\">
     Non-Profit Information and Links
    </font>
   </p>
   <table width=\"80%\">";
 for( $i=$rows-1; $i>=0; $i--) {
   sybase_data_seek($result,$i);
   $par = sybase_fetch_object($result);
   print "
    <tr>
     <td colspan=\"2\" bgcolor=\"#ccccc\">
      <a href=\"$par->url\"><font $fontd dize=\"+1\" color=\"#000044\">$par->name</font></a>
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
