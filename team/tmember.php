<?
// vi: ts=2 sw=2 tw=120
// $Id: tmember.php,v 1.24 2003/03/12 21:37:22 thejet Exp $

// Variables Passed in url:
//  team == team id to display
//  low == ID to start at, assume 0 if not specified
//  limit == Number of records to display at once - default 100
//  pass == Password for viewing member listing
//  source == y for yesterday's contributors only

include "../etc/config.inc";
include "../etc/modules.inc";
include "../etc/project.inc";

debug_text("<!-- team: $team, low: $low, limit: $limit, source: $source, pass: $pass. -->\n",$debug);

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

debug_text("<!-- lastupdate pre call: lastupdate: $lastupdate -->\n",$debug);
$lastupdate = last_update('m');
debug_text("<!-- lastupdate post call: lastupdate: $lastupdate -->\n",$debug);

// Query server for basic team information
$qs = "select name, showpassword, showmembers
        from STATS_team
        where team = $tm";
$result = sybase_query($qs);
$info = sybase_fetch_object($result);
$rows = sybase_num_rows($result);

// Verify the team exists
$title = "Team Members Listing - Team #".$tm;

// Do the password checking
if ($info->showmembers == "NO") {
  
  include "../templates/header.inc";
  ?>
    <h1>Hey, you ain't supposed to be here!</h1>
    <p>This team does not want to list it's members, so you might as well
    quit asking for them!</p>
  <?
  include "../templates/footer.inc";
  exit;
}

if ($info->showmembers == "PAS") {
  if ($pass != $info->showpassword ) {
  
    include "../templates/header.inc";
    if ($pass == "") {
      ?>
        <center><h1>Password required</h1>
        <p>Not so fast!  You need a password to view this page!</p>
      <?
    } else {
      ?>
        <center><h1>Invalid password</h1>
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
      </form></center>
      <?
    include "../templates/footer.inc";
    exit;
  }
}


// See how many blocks this team did
$qs = "SELECT  WORK_TOTAL as WORK_TOTAL, WORK_TODAY as WORK_TODAY
        FROM Team_Rank
        WHERE TEAM_ID = $tm
          and PROJECT_ID = $project_id";
$result = sybase_query("set rowcount 0");
$result = sybase_query($qs);
if ($result == "") {
  $totblocks = FALSE;
} else {
  $totblocks = TRUE;
  $blocksresult = sybase_fetch_object($result);
  $yblocks = (double) $blocksresult->WORK_TODAY * $proj_scale;
  if ( $yblocks == 0 ) $yblocks = 1;
  $oblocks = (double) $blocksresult->WORK_TOTAL * $proj_scale;
}

debug_text("<!-- TOTAL BLOCKS -- qs: $qs, totblocks: $totblocks, yblocks: ${yblocks}, oblocks: ${oblocks}. -->\n",$debug);

// Query server for member listing
if ($source == 'y') {    // $qs_source is an easy way around re-doing $qs based on $source
  $qs_source = "";
} else {
  $qs_source = "*";
}

$qs = "SELECT  tm.WORK_TOTAL as WORK_TOTAL, tm.FIRST_DATE, tm.LAST_DATE,
          p.id, p.listmode, p.contact_name, p.email, p.team,
          tm.WORK_TODAY as WORK_TODAY,";
if ($source == 'y') {
  $qs .= "
          er.DAY_RANK as eRANK, (er.DAY_RANK_PREVIOUS - er.DAY_RANK) as eRANK_CHANGE";
  } else {
  $qs .= "
          er.OVERALL_RANK as eRANK, (er.OVERALL_RANK_PREVIOUS - er.OVERALL_RANK) as eRANK_CHANGE";
  }
$qs .= "
        FROM  Team_Members tm, STATS_Participant p, Email_Rank er
        WHERE  tm.PROJECT_ID = $project_id
          and er.PROJECT_ID = $project_id
          and tm.PROJECT_ID = er.PROJECT_ID
          and tm.TEAM_ID = $tm";
if ($source =='y') {
  $qs.= "
          and tm.WORK_TODAY >0";
}
$qs.= "
          and p.id = tm.ID
          and er.ID = tm.ID
          and p.id = er.ID";
if ($source == 'y') {
  $qs .= "
        ORDER BY  er.WORK_TODAY desc, tm.WORK_TOTAL desc";
} else {
  $qs .= "
        ORDER BY  tm.WORK_TOTAL desc, er.WORK_TODAY desc";
}

$result = sybase_query("set rowcount 0");
$result = sybase_query($qs);
$rows = sybase_num_rows($result);

debug_text("<!-- Query: \"$qs\" Rows: \"$rows\" -->\n",$debug);

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
$title = "$info->name Members ";
if ($source == 'y') {
  $title = $title . "Yesterday ";
} else {
  $title = $title . "Overall ";
}
$title = $title . "$lo - $hi";

include "../templates/header.inc";

// Display how many members
print "
<table border=\"0\"><tr>
  <td align=left>Total Members:</td>
  <td align=right>$rows</td>
  </tr></table><p>";

  // Provide a link back to tmsummary.php
  print "<center>Return to the <a href=\"tmsummary.php?project_id=$project_id&amp;team=$tm\">team summary page</a>.</center>";

  // Start the table
  ?>
  </p>
  <center>
      <table border="1" cellspacing="0">
        <tr>
          <th>Team Rank</th>
          <th>Participant</th>
          <th>Project Rank</th>
          <th>First</th>
          <th>Last</th>
          <th>Yesterday</th>
          <?
          if ($totblocks) { ?>
            <th>%</th>
          <? } ?>
          <th>Total</th>
          <?
          if ($totblocks) { ?>
            <th>%</th>
          <? } ?>
        </tr>

        <?
        // Generate the listing here.
        for ($i = $low; $i < $low + $limit; $i++) {
          sybase_data_seek($result, $i);
          $member = sybase_fetch_object($result);
          $rnk = number_style_convert($i+1);
          $prnk = $member->eRANK;
          $prnkchg = $member->eRANK_CHANGE;
          $n_yesterday = (double) $member->WORK_TODAY * $proj_scale;
          $yesterday = number_style_convert($n_yesterday);
          $n_blocks = (double) $member->WORK_TOTAL * $proj_scale;
          $blocks = number_style_convert($n_blocks);
          $first = sybase_date_format_long($member->FIRST_DATE);
          $last = sybase_date_format_long($member->LAST_DATE);
          $linkid = (int) $member->id;
          $fmtid = number_style_convert($linkid);

          $listas = participant_listas($member->listmode, $member->email, $member->id, $member->contact_name);
          debug_text("<!--- y:$n_yesterday o:$n_blocks yt:$yblocks ot:$oblocks y%:$n_yesterday/$yblocks --->\n",$debug);

          print "
          <tr class=" . row_background_color($i, $color_a, $color_b) . ">
            <td>$rnk</td>
            <td>
              <a href=\"/participant/psummary.php?project_id=$project_id&amp;id=$linkid\">$listas</a>
            </td>";
          if ($n_yesterday < 1 and $source == y) {
            print "
            <td align=\"center\">--</td>";
          } else {
            print "
            <td>$prnk " . html_rank_arrow($prnkchg) . "</td>";
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
  </center>
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
    <center>Return to the <a href="tmsummary.php?project_id=$project_id&amp;team=$tm\">team summary page</a>.</center>
  <?
}

include "../templates/footer.inc";
?>
