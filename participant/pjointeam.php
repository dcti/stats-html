<?php
  // $Id: pjointeam.php,v 1.21 2003/10/14 19:12:20 paul Exp $

  // psecure.inc will obtain $id and $pass from the user.
  // Input may come from the url, http headers, or a client cookie

  include "../etc/config.inc";
  include "../etc/modules.inc";
  include "../etc/project.inc";
  include "../etc/psecure.inc";

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
        "http://www.w3.org/TR/REC-html40/loose.dtd">
<?

  $id = 0+$par->id;
  $team = $team+0;

  $qs = "select team_id from Team_Joins where id=$id and last_date=null";
  $result = sybase_query($qs);
  $rows = sybase_num_rows($result);
  if( $rows == 1 ) {
    sybase_data_seek($result,0);
    $RS_teamid = sybase_fetch_object($result);
    $oldteam = (int) $RS_teamid->team_id;
  } else {
    $oldteam = 0;
  }

  $oldteamname = "No Team";
  if( $oldteam > 0 ) {
    $oldteamname = "Invalid team";
    $qs = "select * from stats.dbo.STATS_team where team = $oldteam";
    $result = sybase_query($qs);
    $rows = sybase_num_rows($result);
    if( $rows == 1 ) {
      sybase_data_seek($result,0);
      $teaminfo = sybase_fetch_object($result);
      $oldteamname = $teaminfo->name;
    }
  }

  if( $team > 0 ) {
    $newteamname = "Invalid team";
    $qs = "select * from stats.dbo.STATS_team where team = $team";
    $result = sybase_query($qs);
    $rows = sybase_num_rows($result);
    if( $rows == 1 ) {
      sybase_data_seek($result,0);
      $teaminfo = sybase_fetch_object($result);
      $newteamname = $teaminfo->name;
    }
  }

  if( $teaminfo->listmode > 0 ) {
    $title = "This team has been revoked";
    include "../templates/header.inc";
    display_last_update();
    print "<center>
          <h2>This team has been revoked</h2>
          <p>Team #$team ($teaminfo->name) is no longer valid.</p>
          <p><a href=\"/\">Oh well, I'll find another team...</a></p>
         </center>
        </body>";
    exit;
  }

  $qs = "p_teamjoin @id=$id, @team=$team";
  $result = sybase_query($qs);

  $title = "$par->email has joined $newteamname";

  include "../templates/header.inc";
  display_last_update();

  print "<center>
          <h2>You have joined $teamname</h2>
	  <table cellpadding=\"1\" cellspacing=\"1\" bgcolor=\"#dddddd\">
	   <tr>
	    <td>Email Address:</td>
	    <td>$par->email</td>
	   </tr>
	   <tr>
	    <td>Participant Number:</td>
	    <td>$id</td>
	   </tr>
	   <tr>
	    <td>Old Team Affiliation</td>
	    <td>$oldteam: $oldteamname</td>
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
	 </center>
	</body>";
?>
</html>
