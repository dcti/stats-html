<?php
  // $Id: pjointeam.php,v 1.23 2003/12/16 16:15:00 thejet Exp $

  // psecure.inc will obtain $id and $pass from the user.
  // Input may come from the url, http headers, or a client cookie

  include "../etc/config.inc";
  include "../etc/modules.inc";
  include "../etc/project.inc";
  include "../etc/psecure.inc";
  include "../etc/team.php";

  $id = $gpart->get_id();
  $team = $tm+0;

  $oldteamid = $gpart->get_team_id();
  if($oldteamid <= 0)
      $oldteamname = "No Team";
  else
  {
      $pteam = new Team($gdb, $gproj, $oldteamid);
      if($oldteamid != $pteam->get_id())
          $oldteamname = "Invalid Team";
      else
          $oldteamname = $pteam->get_name();
  }

  if( $team > 0 ) {
      //***BJG[12/16/2003]: Fix to take into account team renumbering
      $pteam = new Team($gdb, $gproj, $team);
      if($team != $pteam->get_id() && !$pteam->get_id_mismatch())
          $newteamname = "Invalid team";
      else
          $newteamname = $pteam->get_name();

      // reset the $team variable based on the newly loaded new team
      $team = $pteam->get_id();
      if( $pteam->get_listmode() > 0 ) {
          $title = "This team has been revoked";
          include "../templates/header.inc";
          display_last_update('t');
          print "<div style=\"text-align:center\">
                     <h2>This team has been revoked</h2>
                     <p>Team #$team ($newteamname) is no longer valid.</p>
                     <p><a href=\"/\">Oh well, I'll find another team...</a></p>
                 </div>
             </body>";
          exit;
      }
  }

  $result = $gpart->join_team($team);
  if(!$result)
  {
      $title = "Error joining team #$team";
      include "../templates/header.inc";
      display_last_update('t');
      print "<div style=\"text-align:center\">
             <!-- $oldteamid, $oldteamname, $team, $newteamname -->
                 <h2>There was an error when joining team</h2>
                 <p><a href=\"/\">Oh well, I'll try again later...</a></p>
             </div>
             </body>";
      exit;
   }

  $title = $gpart->get_email()." has joined $newteamname";

  include "../templates/header.inc";
  display_last_update('t');

  print "<div style=\"text-align: center\">
          <h2>You have joined $newteamname</h2>
	  <table cellpadding=\"1\" cellspacing=\"1\" style=\"background-color: #dddddd; margin: auto;\">
	   <tr>
	    <td>Email Address:</td>
	    <td>" . $gpart->get_email() . "</td>
	   </tr>
	   <tr>
	    <td>Participant Number:</td>
	    <td>$id</td>
	   </tr>
	   <tr>
	    <td>Old Team Affiliation</td>
	    <td>$oldteamid: $oldteamname</td>
	   </tr>
	   <tr>
	    <td>New Team Affiliation</td>
	    <td bgcolor=\"#ffdddd\"><strong>$team: $newteamname</strong></td>
	   </tr>
	  </table>
	  <p>
	   Any submitted blocks that have <strong>not</strong> been allocated to a team<br>
	   will be allocated to the team you have now selected during the next stats run.
	  </p>
	  <p>
	   Any submitted blocks that you have already allocated to a team<br>
	   will not be affected.  Only future blocks will go to the new team.
	  </p>
	  <p>
	   No changes will be visible until the next stats run.
	  </p>
	  <p>
	   You have until midnight UTC to change your mind without any problem.<br>
	   Team joins are tracked on a day-by-day basis, so only the last teamjoin<br>
	   on a given day will take effect. Once midnight rolls around though, your<br>
	   team selection for that day turns into a pumpkin, err, becomes permanent.
	  </p>
	  <p>
	   Stats runs typically occur daily at 00:00 UTC.
	  </p>
	  <p><a href=\"/\">Cool!  It's about damn time</a></p>
	 </div>
	</body>";
?>
</html>
