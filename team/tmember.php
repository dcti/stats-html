<?
// vi: ts=2 sw=2 tw=120
// $Id: tmember.php,v 1.40 2005/04/01 16:58:42 decibel Exp $

// Variables Passed in url:
//  team == team id to display
//  low == ID to start at, assume 0 if not specified
//  limit == Number of records to display at once - default 100
//  pass == Password for viewing member listing
//  source == y for yesterday's contributors only

include "../etc/global.inc";
include "../etc/modules.inc";
include "../etc/project.inc";
include "../etc/team.php";
include "../etc/teamstats.php";
include "../etc/participant.php";

if ($low == ""){
  $low = 0;
} else {
  // Make external interface 1 based, internal 0 based
  $low = $low - 1;
  if ($low < 0){
    $low = 0;
  }
}

if ($limit == "") {
  $limit = 100;
}
$lim = $limit;

$lastupdate = last_update('m');

// Query server for basic team information
$team = new Team($gdb, $gproj, $tm);
if($team->get_id() == 0)
{
  // This blew up!!!
  print('Bah!');
  exit(1);
}
// Support team renumbering
$tm = $team->get_id();

// Verify the team exists
$title = "Team Members Listing - Team #".$tm;

// Do the password checking
if ($team->get_show_members() == "NO") {

  include "../templates/header.inc";
  ?>
    <h1>Hey, you ain't supposed to be here!</h1>
    <p>This team does not want to list it's members, so you might as well
    quit asking for them!</p>
  <?
  include "../templates/footer.inc";
  exit;
}

if ($team->get_show_members() == "PAS") {
  if ($pass != $team->get_show_password() ) {

    include "../templates/header.inc";
    if ($pass == "") {
      ?>
        <div style="text-align: center">
        <h1>Password required</h1>
        <p>Not so fast!  You need a password to view this page!</p>
      <?
    } else {
      ?>
        <div style="text-align: center">
        <h1>Invalid password</h1>
        <p>Sorry, but the password you entered doesn't match the one I'm
        looking for... please try again.</p>
      <?
    }
      ?>
      <form action="<?=$myname?>" method="get">
      <p>
      Password: <input type="password" length="16" name="pass">
      <input type="hidden" name="team" value="<?=$tm?>">
      <input type="hidden" name="source" value="<?=$source?>">
      <input type="hidden" name="project_id" value="<?=$project_id?>">
      <input type="submit" value="Go">
      </p>
      </form>
      </div>
      <?
    include "../templates/footer.inc";
    exit;
  }
}


// See how many blocks this team did
$teamStats =& $team->get_current_stats();
if ($teamStats == null) {
  $totblocks = FALSE;
} else {
  $totblocks = TRUE;
  $yblocks = (double) $teamStats->get_stats_item("work_today") * $gproj->get_scale();
  if ( $yblocks == 0 ) $yblocks = 1;
  $oblocks = (double) $teamStats->get_stats_item("work_total") * $gproj->get_scale();
}

// The source of the data...
$title = safe_display($team->get_name()) . " Members ";
if ($source == 'y') {
  $title = $title . "Yesterday ";
  $source = 'y';
} else {
  $title = $title . "Overall ";
  $source = 'o';
}
$title = $title . "$lo - $hi";

$teamMembers =& Participant::get_team_list($team->get_id(), $source, $lo, $limit, $rows, $gdb, $gproj);

// Sanity check $low and $limit
if ($low > $rows) {
  $low = $rows - 1;
  $limit = 1;
}

if ($low + $limit > $rows)
{
  $limit = $rows - $low;
  if ($limit > $rows) $limit = $rows;
  $low = $rows - $limit;
}

$hi = $low + $limit;
$lo = $low + 1;

include "../templates/header.inc";

if($team->get_id_mismatch() == true) {
  print "<h2 class=\"phead2\" style=\"color: red\">NOTICE: This team has been renumbered, the new
  team ID is " . $team->get_id() . ".</h2>";
}

// Display how many members
print "
<table border=\"0\"><tr>
  <td align=left>Total Members:</td>
  <td align=right>$rows</td>
  </tr></table>";

  // Provide a link back to tmsummary.php
  print "<p style=\"text-align:center\">Return to the <a href=\"tmsummary.php?project_id=$project_id&amp;team=$tm\">team summary page</a>.</p>";

  // Start the table
  ?>
      <table border="1" cellspacing="0" width="100%">
        <tr>
          <th class="thead">Team Rank</th>
          <th class="thead">Participant</th>
          <th class="thead">Project Rank</th>
          <th class="thead">First</th>
          <th class="thead">Last</th>
          <th class="thead"><?=$gproj->get_scaled_unit_name()?> Yesterday</th>
          <?
          if ($totblocks) { ?>
            <th class="thead">%</th>
          <? } ?>
          <th class="thead"><?=$gproj->get_scaled_unit_name()?> Total</th>
          <?
          if ($totblocks) { ?>
            <th class="thead">%</th>
          <? } ?>
        </tr>

        <?
        // Generate the listing here.
        $cnt = count($teamMembers);
        for ($i = 0; $i < $cnt; $i++) {
          $statsTmp =& $teamMembers[$i]->get_current_stats();
          $rnk = number_style_convert($lo + $i);
          $prnk = $statsTmp->get_stats_item("rank");
          $prnkchg = $statsTmp->get_stats_item("rank_change");
          $n_yesterday = (double) $statsTmp->get_stats_item("work_today") * $gproj->get_scale();
          $yesterday = number_style_convert($n_yesterday);
          $n_blocks = (double) $statsTmp->get_stats_item("work_total") * $gproj->get_scale();
          $blocks = number_style_convert($n_blocks);
          $first = $statsTmp->get_stats_item("first_date");
          $last = $statsTmp->get_stats_item("last_date");
          $linkid = (int) $teamMembers[$i]->get_id();
          $fmtid = number_style_convert($linkid);

          $listas = safe_display($teamMembers[$i]->get_display_name());

          print "
          <tr class=" . row_background_color($i) . ">
            <td>$rnk</td>
            <td>
              <a href=\"/participant/psummary.php?project_id=$project_id&amp;id=$linkid\">$listas</a>
            </td>";
          if ($n_yesterday < 1 and $source == 'y') {
            print "
            <td align=\"center\">--</td>";
          } else {
            print "
            <td>$prnk " . html_rank_arrow($prnkchg) . "</td>";
          }
          if ( $random_stats == 1 ) {
            print "
            <!-- Random goodness! -->";
          }
          print "
            <td align=\"right\">$first</td>
            <td align=\"right\">$last</td>
            <td align=\"right\">$yesterday</td>";
          if ($totblocks) print "
            <td align=\"right\">" . number_style_convert($n_yesterday/$yblocks*100 ,2) . "</td>";
          print "
            <td align=\"right\">$blocks</td>";
          if ($totblocks) print "
            <td align=\"right\">" . number_style_convert($n_blocks/$oblocks*100, 2) . "</td>";
          print "
          </tr>";
        }
        ?>

    </table>
  <!-- Navigation Buttons here -->
  <table border="0" width="100%">
    <tr>
      <?
      if ($low > 0) {
        ?>
        <td align = left>
        <?
        $newlow = $low - $lim + 1;
        $newlimit=$lim;
        if ($newlow < 1)
        {
          $newlow=1;
        }
        print "
          <a href=\"$myname?project_id=$project_id&amp;pass=" .
            urlencode($pass) .
            "&amp;team=$tm&amp;source=$source&amp;low=$newlow&amp;limit=$newlimit\">$newlow - $low</a>
        </td>";
      } else {
        // needed for alignment
        ?>
        <td></td>
        <?
      }
      if ($low + $lim < $rows) {
        ?>
        <td align = right>
        <?
        $newlow = $low + $lim + 1;
        $newlimit = $lim;
        if ($newlow + $newlimit > $rows)
        {
          $newlimit = $rows - $newlow + 1;
        }
        $high = $newlow + $newlimit - 1;
        print "
          <a href=\"$myname?project_id=$project_id&amp;pass=" .
            urlencode($pass) .
            "&amp;team=$tm&amp;source=$source&amp;low=$newlow&amp;limit=$lim\">$newlow - $high</a>
        </td>";
      } else {
        // Needed for alignment
        ?>
        <td></td>
        <?
      }
      ?>
    </tr>
  </table>
  <?
  // Provide a link back to tmsummary.php
  if ($rows > 25) {
    ?>
    <p style="text-align:center">Return to the <a href="tmsummary.php?project_id=<?=$project_id?>&amp;team=<?=$tm?>">team summary page</a>.</p>
  <?
}

include "../templates/footer.inc";
?>
