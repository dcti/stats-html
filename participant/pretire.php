<?php
  // $Id: pretire.php,v 1.20 2003/12/31 16:30:19 decibel Exp $

  // Parameters passed to pretire.php
  // id = id to be retired
  // pass = password of id to be retired
  //
  // ems = email search string  \
  //                            - either em or destid may be passed
  // destid = id to retire to  /

  include "../etc/config.inc";
  include "../etc/project.inc";
  include "../etc/psecure.inc";
  include "../etc/team.php";
  
  $destid = $_REQUEST['destid'];
  $ems = $_REQUEST['ems'];

  $title = "Retiring " . $gpart->get_email();

  include "../templates/header.inc";
  display_last_update('t');

  if ($destid == "" and $ems == "") {
  ?>
	  <h2>You are about to permanently retire the address <?=$gpart->get_email()?></h2>
	  <p>
	   You are about to completely and permanently remove this email from the stats database.
	   All past, current, and future work submitted by this email will be attributed to a new
	   email address.  This procedure is irreversable.  It is permanent.  Once you do this, we
	   cannot restore it.
	  </p>
	  <p>
	   You should <strong>only</strong> be doing this if you will no longer be using the old
	   email address.  This feature has been designed to allow participants to gracefully
	   migrate from one email address to another without losing their longevity or standing
	   in the stats database.
	  </p>
	  <p>
	   In order to retire this email address, you must first pick a destination email address.
	   All past blocks, and any future blocks that are submitted from <?=$par->EMAIL?> will be
	   treated as if they were submitted by the new email address you are about to choose.
	  </p>
	  <p>
	   Before you can retire this email address, you must have first changed over your clients
	   to your new email address, and you must have flushed blocks from the new address and have
	   those blocks visible in stats.
	  </p>
	  <p>
	   Please enter your new email address below:
	   <br>
	   <form action="pretire.php" method="post">
	    <input type="text" name="ems" size="64" maxlength="64">
	    <input type="hidden" name="id" value="<?=$id?>">
	    <input type="hidden" name="pass" value="<?=$pass?>">
	    <input type="submit" value="Search for this email">
	   </form>
	  </p>
  <?
  } else {
    if ($ems <> "") {
      $result = Participant::get_search_list($ems, 50, $gdb, $gproj);
      $rows = count($result);
      if ($rows == 0)
      {
        print "
           <h2>No Matches</H2>
           <p>
            No participants were found matching \"" . $ems . "\". For more 
            information, <A HREF=\"http://www.distributed.net/faqs\">look here</A>
           </p>";
      }
      else
      {
      print "
	  <h2>Please choose your new email address</h2>
	  <p>
	   Below are all the email addresses in stats that match what you just typed in.
	   Please choose the appropriate email address by clicking on it.
	  </p>
	  <p>
	   This <strong>will retire</strong> " . $gpart->get_email() . ".
	  </p>
	  <p>
	   Clicking an email below will result in a permanent change to the stats database.
	  </p>
	  <table border=\"1\">
	   <tr bgcolor=\"#00aaaa\">
	    <td align=\"right\">ID #</td>
	    <td>Email Address</td>
	   </tr>";
      for ($i=0;$i<$rows;$i++) {
        $ROWparticipant = $result[$i];

        $tmpid = 0 + $ROWparticipant->get_id();
        if ($tmpid != $id) {
          print "
	     <tr><td align=\"right\">" . $tmpid . "</td>
	         <td><a href=\"pretire.php?id=" . $id . "&pass=" . $pass . "&destid=" . $tmpid . "\">" . $ROWparticipant->get_email() . "</a></td>
	     </tr>";
        }
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
      $result = $gpart->retire($destid);
      if ($result == FALSE) {
        print "
          <h2>Retire Procedure Failed</h2>
          <p>Ahh well, i'll try again later...</p>";
      } else {
      $destpart = new Participant($gdb, $gproj, $destid);
      $destteamid = $destpart->get_team_id();
      //if ($destteamid > 0) {
      //  $destteam = new Team($gdb, $gproj, $destteamid);
      //  $destteamname = destteam->get_name();
      //}
      print "
	<h2>Retire Procedure successful</h2>
	<p>
	 You have successfully retired the email address " . $gpart->get_email() . ".
	</p>
	<p>
	 This will take effect during the next stats run.
	</p>
	<p>
	 All past blocks, and any future blocks submitted from " . $gpart->get_email() . " will be allocated to the stats for " . $destpart->get_email() . " instead.
	</p>
	<p>
	 All future blocks from this address will be attributed to team $destteamid, which is your current team.
	 If, in the future, you change teams, it will affect all retired emails as you'd expect it to.
	</p>";
      print '<p><a href="/">Great, that rocks!</a></p>';
      }
     }
    }
  }
?>
 </body>
</html>
