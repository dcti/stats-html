<?php
  // $Id: pjointeam.php,v 1.12 2000/08/23 21:49:42 decibel Exp $

  // psecure.inc will obtain $id and $pass from the user.
  // Input may come from the url, http headers, or a client cookie

  include "etc/config.inc";
  include "etc/modules.inc";
  include "etc/project.inc";
  include "etc/psecure.inc";

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
        "http://www.w3.org/TR/REC-html40/loose.dtd">
<?

  $id = 0+$par->id;
  $oldteam = 0+$par->team;
  $team = $team+0;

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
    include "templates/header.inc";
    print "<center>
          <h2>This team has been revoked</h2>
          <p>Team #$team ($teaminfo->name) is no longer valid.</p>
          <p><a href=\"http://stats.distributed.net/\">Oh well, I'll find another team...</a></p>
         </center>
        </body>";
    exit;
  }

  if ( $team != $oldteam ) {
/*
	<OLD CODE>

    $date = getdate();
    $TJqs = "select * from Team_Joins where JOIN_DATE=\"$date[month] $date[mday] $date[year]\" and ID = $id";
    $result = sybase_query($TJqs);
    $TJrows = sybase_num_rows($result);
    if( $TJrows >= 1 ) {
      $qs = "update stats.dbo.Team_Joins set TEAM_ID = $team where JOIN_DATE=\"$date[month] $date[mday] $date[year]\" and ID = $id";
      $result = sybase_query($qs);
    } else {
      $qs = "insert into stats.dbo.Team_Joins(JOIN_DATE, ID, TEAM_ID) select \"$date[month] $date[mday] $date[year]\", $id, $team";
      $result = sybase_query($qs);
    }
    $qs = "update stats.dbo.STATS_participant set team = $team where id = $id";
    $result = sybase_query($qs);
    $qs = "update stats.dbo.STATS_participant set team = $team where retire_to = $id";
    $result = sybase_query($qs);

	</OLD CODE>
*/
    $qs = "p_teamjoin @id=$id, @team=$team";
    $result = sybase_query($qs);
  }

  $title = "$par->email has joined $newteamname";

  include "templates/header.inc";
  debug_text("<!-- TJqs: $TJqs, TJrows: $TJrows. -->\n",$debug);

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
	   You have until the next stats run to change your mind without any problem.<br>
	   Nothing <i>really</i> gets changed until the daily update takes place.
	  </p>
	  <p>
	   Stats runs typically occur daily at 00:00 UTC.
	  </p>
	  <p><a href=\"/\">Cool!  It's about damn time</a></p>
	 </center>
	</body>";
?>
</html>
