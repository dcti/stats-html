<?php
  // $Id: pretire.php,v 1.9 2000/04/13 15:06:39 bwilson Exp $

  // Parameters passed to pretire.php3
  // id = id to be retired
  // pass = password of id to be retired
  //
  // em = EMAIL search string  \
  //                            - either em or destid may be passed
  // destid = id to retire to  /

  include "etc/config.inc";
  include "etc/project.inc";
  include "etc/psecure.inc";

  $title = "Retiring $par->EMAIL";

  include "templates/header.inc";

  if ($destid == "" and $ems == "") {
    print "
	  <h2>You are about to permanently retire the address $par->EMAIL</h2>
	  <p>
	   You are about to completely and permanently remove this EMAIL from the stats database.
	   All past, current, and future work submitted by this EMAIL will be attributed to a new
	   EMAIL address.  This procedure is irreversable.  It is permanent.  Once you do this, we
	   cannot restore it.
	  </p>
	  <p>
	   You should <strong>only</strong> be doing this if you will no longer be using the old
	   EMAIL address.  This feature has been designed to allow participants to gracefully
	   migrate from one EMAIL address to another without losing their longevity or standing
	   in the stats database.
	  </p>
	  <p>
	   In order to retire this EMAIL address, you must first pick a destination EMAIL address.
	   All past blocks, and any future blocks that are submitted from $par->EMAIL will be
	   treated as if they were submitted by the new EMAIL address you are about to choose.
	  </p>
	  <p>
	   Before you can retire this EMAIL address, you must have first changed over your clients
	   to your new EMAIL address, and you must have flushed blocks from the new address and have
	   those blocks visible in stats.
	  </p>
	  <p>
	   Please enter your new EMAIL address below:
	   <br>
	   <form action=\"/pretire.php3\" method=\"post\">
	    <input type=\"text\" name=\"ems\" size=\"64\" maxlength=\"64\">
	    <input type=\"hidden\" name=\"id\" value=\"$id\">
	    <input type=\"hidden\" name=\"pass\" value=\"$pass\">
	    <input type=\"submit\" value=\"Search for this EMAIL\">
	   </form>
	  </p>";
  } else {
    if ($ems <> "") {
      $qs = "select id,EMAIL from STATS_participant where EMAIL like '%%$ems%%' and id <> $id and (retire_to = 0 or retire_to = NULL)";
      $result = sybase_query($qs);
      $matches = sybase_num_rows($result);

      print "
	  <h2>Please choose your new EMAIL address</h2>
	  <p>
	   Below are all the EMAIL addresses in stats that match what you just typed in.
	   Please choose the appropriate EMAIL address by clicking on it.
	  </p>
	  <p>
	   This <strong>will retire</strong> $par->EMAIL.
	  </p>
	  <p>
	   Clicking an EMAIL below will result in a permanent change to the stats database.
	  </p>
	  <table border=\"1\">
	   <tr bgcolor=\"#00aaaa\">
	    <td align=\"right\">ID #</td>
	    <td>Email Address</td>
	   </tr>";
      for ($i=0;$i<$matches;$i++) {
        sybase_data_seek($result,$i);
        $par = sybase_fetch_object($result);
        $tmpid = 0+$par->id;
        print "
	   <tr><td align=\"right\">$tmpid</td>
	       <td><a href=\"/pretire.php3?id=$id&pass=$pass&destid=$tmpid\">$par->EMAIL</a></td>
	   </tr>";
      }
      print "</table>";
    }
    if ($destid <> "") {
      if ($id == $destid) {
        print "
	  <h2>Error: Retire Loop</h2>
	  <p>That's cute, but it won't work.</p>";
        exit;
      }
      $qs = "select id,EMAIL,team,retire_to from STATS_participant where id = $destid";
      $result = sybase_query($qs);
      if(sybase_num_rows($result)<>1) {
        print "
	  <h2>Error: Destination ID Lookup Failure</h2>
	  <p>I was looking for ID #$destid and something went wrong</p>";
        exit;
      }
      $destpar = sybase_fetch_object($result);
      $dretire_to = 0+$destpar->retire_to;
      $destteam = 0+$destpar->team;
      if( $dretire_to > 0 ) {
        print "
	  <h2>Error: Retire Form</h2>
	  <p>That's cute, but it won't work.</p>";
        exit;
      }
      $qs = "select count(*) as numEMAILs from STATS_participant where retire_to = $destid or retire_to = $id";
      $result = sybase_query($qs);
      $par = sybase_fetch_object($result);
      if ( $par->numEMAILs >= 8 ) {
        print "
	  <h2>Error: Too many retires to that EMAIL</h2>
	  <p>To prevent abuse of the this facility, there is a limit of 8 EMAIL addresses which may be retired by one person.
	     If there is a legitimate need for you to retire more than 8 EMAIL addresses, please contact help@distributed.net</p>";
        exit;
      }
      $qs = "update STATS_participant set retire_to = $destid, team = $destteam where id = $id and password = '$pass'";
      $result = sybase_query($qs);
      $qs = "update STATS_participant set retire_to = $destid, team = $destteam where retire_to = $id";
      $result = sybase_query($qs);
# BW: Prevent the retired e-mail from being ranked
#     $qs = "delete OGR_Email_Rank where id = $id";
#     $result = sybase_query($qs);
      $qs = "select * from STATS_participant where id = $destid";
      $result = sybase_query($qs);
      $destpar = sybase_fetch_object($result);
      $qs = "select * from STATS_participant where id = $id";
      $result = sybase_query($qs);
      $srcpar = sybase_fetch_object($result);
      $qs = "select id, EMAIL, retire_to from STATS_participant where retire_to = $destid";
      $result = sybase_query($qs);
      $rows = sybase_num_rows($result);
      if($rows <> 1) {
        $plural = "es";
      }
      print "
	<h2>Retire Procedure successful</h2>
	<p>
	 You have successfully retired the EMAIL address $srcpar->EMAIL.
	</p>
	<p>
	 This will take effect during the next stats run.
	</p>
	<p>
	 All past blocks, and any future blocks submitted from $srcpar->EMAIL will be allocated to the stats for $destpar->EMAIL instead.
	</p>
	<p>
	 All future blocks from this address will be attributed to team $destteam, which is your current team.
	 If, in the future, you change teams, it will affect all retired EMAILs as you'd expect it to.
	</p>
	<p>The EMAIL address $destpar->EMAIL currently has $rows EMAIL address$plural retired into it:</p>
	<ul>";
      for($i = 0; $i<$rows; $i++) {
        sybase_data_seek($result,$i);
        $par = sybase_fetch_object($result);
        $tmpid = 0+$par->id;
        print "<li>$par->EMAIL</li>";
      }
      print "
	 </ul>
	 <p><a href=\"/\">Great, that rocks!</a></p>";
    }
  }
?>
 </body>
</html>
