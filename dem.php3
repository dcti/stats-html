<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
        "http://www.w3.org/TR/REC-html40/loose.dtd">
<?

 // $Id: dem.php3,v 1.3 1999/08/29 20:50:03 nugget Exp $

 $myname = "demographics.php3";

 include "etc/config.inc";

 sybase_pconnect($interface, $username, $password);
 $qs = "select count(*) as totp, max(id) as maxid from STATS_Participant where retire_to = 0 or retire_to = NULL";
 sybase_query("set rowcount 0");
 $result = sybase_query($qs);

 if ($result == "") {
   if ($debug=="yes") {
     include "templates/debug.inc";
   } else {
     include "templates/error.inc";
   }
   exit();
 }
 sybase_data_seek($result,0);
 $par = sybase_fetch_object($result);

 $total_participants = 0+$par->totp;
 $maxid = 0+$par->maxid;

 $qs = "select distinct dem_gender, count(dem_gender) as recs
	from STATS_participant
	where (retire_to = 0 or retire_to = NULL) and dem_gender <> NULL
	group by dem_gender
	order by count(dem_gender) desc";
 $gender = sybase_query($qs);
 $gender_rows = sybase_num_rows($gender);

 $hearddesc[0] = "Who knows?";
 $hearddesc[1] = "A friend";
 $hearddesc[2] = "Banner Ad";
 $hearddesc[3] = "Link on a Page";
 $hearddesc[4] = ".sig file";
 $hearddesc[5] = "Read an Article";
 $hearddesc[99] = "None of the above";

 $qs = "select distinct dem_heard, count(dem_heard) as recs
	from STATS_participant
	where (retire_to = 0 or retire_to = NULL) and dem_heard <> NULL
	group by dem_heard
	order by count(dem_heard) desc";
 $heard = sybase_query($qs);
 $heard_rows = sybase_num_rows($heard);

 $reasondesc[0] = "Why not?";
 $reasondesc[1] = "It's cool!";
 $reasondesc[2] = "To fight the man!";
 $reasondesc[3] = "I need the money";
 $reasondesc[4] = "I love stats";
 $reasondesc[5] = "The cow is cute";
 $reasondesc[6] = "To attract the opposite sex";

 $qs = "select distinct dem_motivation, count(dem_motivation) as recs
	from STATS_participant
	where (retire_to = 0 or retire_to = NULL) and dem_motivation <> NULL
	group by dem_motivation
	order by count(dem_motivation) desc";
 $reason = sybase_query($qs);
 $reason_rows = sybase_num_rows($reason);

 $qs = "select distinct STATS_participant.dem_country, 
	       count(STATS_participant.dem_country) as recs,
	       sum(stats.statproc.CACHE_em_RANK.blocks) as blocks
	from STATS_participant, stats.statproc.CACHE_em_RANK
	where stats.statproc.CACHE_em_RANK.id = STATS_participant.id
	group by dem_country
	order by blocks desc";

 $qs = "select distinct code, country, count(country) as recs
	from STATS_participant, STATS_country
	where ((retire_to = 0 or retire_to = NULL) and dem_country <> NULL) and
	      (dem_country = code)
	group by code, country
	order by count(country) desc";
 $country = sybase_query($qs);
 $countries = sybase_num_rows($country);

 print "
	<html>
	 <head>
	  <title>Demographics</title>
	 </head>
	 <body>
	  <center>
	  <table>
	   <tr>
	    <td colspan=\"2\" align=\"center\"><hr><strong>Totals</strong><hr></td>
	   </tr>
	   <tr>
	    <td>Total Participants:</td>
	    <td align=\"right\">$total_participants</td>
	   </tr>
	   <tr>
	    <td>Max ID # in Table:</td>
	    <td align=\"right\">$maxid</td>
	   </tr>
	   <tr>
	    <td colspan=\"2\" align=\"center\"><hr><strong>Gender</strong><hr></td>
	   </tr>";
 for ($i = 0; $i < $gender_rows; $i++) {
   sybase_data_seek($gender,$i);
   $par = sybase_fetch_object($gender);
   $recs = 0+$par->recs;
   print "<tr><td>$par->dem_gender</td><td align=\"right\">$recs</td></tr>\n";
 }
 print "
	   <tr>
	    <td colspan=\"2\" align=\"center\"><hr><strong>How did you hear about dnet?</strong><hr></td>
	   </tr>";
 for ($i = 0; $i < $heard_rows; $i++) {
   sybase_data_seek($heard,$i);
   $par = sybase_fetch_object($heard);
   $recs = 0+$par->recs;
   $text = $hearddesc[$par->dem_heard];
   print "<tr><td>$text</td><td align=\"right\">$recs</td></tr>\n";
 } 

 print "
	   <tr>
	    <td colspan=\"2\" align=\"center\"><hr><strong>Why are you involved?</strong><hr></td>
	   </tr>";
 for ($i = 0; $i < $reason_rows; $i++) {
   sybase_data_seek($reason,$i);
   $par = sybase_fetch_object($reason);
   $recs = 0+$par->recs;
   $text = $reasondesc[$par->dem_motivation];
   print "<tr><td>$text</td><td align=\"right\">$recs</td></tr>\n";
 } 
 print "
	   <tr>
	    <td colspan=\"2\" align=\"center\"><hr><strong>Nationality ($countries)</strong><hr></td>
	   </tr>";
 for ($i = 0; $i < $countries; $i++) {
   sybase_data_seek($country,$i);
   $par = sybase_fetch_object($country);
   $recs = 0+$par->recs;
   $icofn = "/images/icons/flags/$par->code.gif";
   if(!file_exists(".$icofn")) {
     $icofn = "/images/icons/flags/unknown.gif";
   }
   print "<tr><td><img src=\"$icofn\" alt=\"$par->code\"> $par->country</td><td align=\"right\">$recs</td></tr>\n";
 } 


 print "
	  </table>
 	  </center>
	 </body>
	</html>";
?>
