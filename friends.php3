<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
        "http://www.w3.org/TR/REC-html40/loose.dtd">
<?
# $Id: friends.php3,v 1.1 1999/11/03 16:07:22 nugget Exp $

 $myname = "friends.php3";

 include "etc/config.inc";
 include "etc/project.inc";
 include "etc/modules.inc";

 sybase_pconnect($interface, $username, $password);
 $qs = "create table #friendtally ( id numeric(10), friend int )";
 $result = sybase_query($qs);
 $qs = "insert into #friendtally (id, friend) select id, friend_a from STATS_Participant where friend_a > 0";
 $result = sybase_query($qs);
 $qs = "insert into #friendtally (id, friend) select id, friend_b from STATS_Participant where friend_b > 0";
 $result = sybase_query($qs);
 $qs = "insert into #friendtally (id, friend) select id, friend_c from STATS_Participant where friend_c > 0";
 $result = sybase_query($qs);
 $qs = "insert into #friendtally (id, friend) select id, friend_d from STATS_Participant where friend_d > 0";
 $result = sybase_query($qs);
 $qs = "insert into #friendtally (id, friend) select id, friend_e from STATS_Participant where friend_e > 0";
 $result = sybase_query($qs);
 $qs = "select friend, count(friend) as links from #friendtally group by friend order by count(friend) desc";
 $result = sybase_query($qs);
 $rows = sybase_num_rows($result);
 $qs = "drop table #friendtally";
 sybase_query($qs);

 for ($i = 0; $i<$rows; $i++) {
   sybase_data_seek($result,$i);
   $par = sybase_fetch_object($result);
   $id = (int) $par->friend;
   $links = (int) $par->links;
   $qs = "select email, listmode, contact_name from STATS_Participant where id = $id";
   $parresult = sybase_query($qs);
   $par = sybase_fetch_object($parresult);
   $person = participant_listas($par->listmode,$par->email,$id,$par->contact_name);
   print "$person has $links links<br>\n";
 }
?>
</pre>
</body> 
</html>
