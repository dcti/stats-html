<?
// vi: ts=2 sw=2 tw=120
// $Id: tmember_xml.php,v 1.6 2004/04/21 15:49:04 thejet Exp $

// 1/17/2003 - Ben Gavin
// Adapted to output XML in the proposed format

// Variables Passed in url:
//  team == team id to display
//  low == ID to start at, assume 0 if not specified
//  limit == Number of records to display at once - default 100
//  pass == Password for viewing member listing
//  source == y for yesterday's contributors only
//error_reporting(0);
include "../etc/config.inc";
include "../etc/modules.inc";
include "../etc/project.inc";
include "../etc/team.php";
include "../etc/teamstats.php";
include "../etc/participant.php";


// output the XML header
header("Content-type: text/xml", true);
print("<"."?xml version=\"1.0\" encoding=\"US-ASCII\"?".">\n");

//debug_text("<!-- team: $team, low: $low, limit: $limit, source: $source, pass: $pass. -->\n",$debug);

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

//debug_text("<!-- lastupdate pre call: lastupdate: $lastupdate -->\n",$debug);
$lastupdate = last_update('m');
//debug_text("<!-- lastupdate post call: lastupdate: $lastupdate -->\n",$debug);

// Verify the team exists
$team = new Team($gdb, $gproj, $tm);
if($team->get_id() == 0)
{
  // This blew up!!!
  print('<error>Bah!</error>');
  exit(1);
}
// support team renumbering
$tm = $team->get_id();

$title = "Team Members Listing - Team #".$tm;

// Do the password checking
if ($team->get_show_members() == "NO") {  
  ?>
    <error>
      Hey, you ain't supposed to be here!  This team does not want to
      list it's members, so you might as well quit asking for them!
    </error>
  <?
  exit;
}

if ($team->get_show_members() == "PAS") {
  if ($pass != $team->get_show_password() ) {  
    if ($pass == "") {
      ?>
        <error>
           Password required
           Not so fast!  You need a password to view this page!
        </error>
      <?
    } else {
      ?>
        <error>
           Invalid password
           Sorry, but the password you entered doesn't match the one I'm
           looking for... please try again.
        </error>
      <?
    }
    //***BJG[1/17/2003] - Removed form since XML is designed for automated processing
    exit;
  }
}


// See how many blocks this team did
/** Don't do this anymore because we don't need pcts
$teamStats =& $team->get_current_stats();
if ($teamStats == null) {
  $totblocks = FALSE;
} else {
  $totblocks = TRUE;
  $yblocks = (double) $teamStats->get_stats_item("work_today") * $gproj->get_scale();
  if ( $yblocks == 0 ) $yblocks = 1;
  $oblocks = (double) $teamStats->get_stats_item("work_total") * $gproj->get_scale();
}
**/

//debug_text("<!-- TOTAL BLOCKS -- qs: $qs, totblocks: $totblocks, yblocks: ${yblocks}, oblocks: ${oblocks}. -->\n",$debug);

// Query server for member listing
if ($source == 'y') {
  $source = "y";
} else {
  $source = "*";
}

$teamMembers =& Participant::get_team_list($tm, $source, $lo, $limit, $rows, $gdb, $gproj);

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

//debug_text("<!-- Query: \"$qs\" Rows: \"$rows\" -->\n",$debug);
// Start the output
?>
<team-summary id="<?= $tm ?>" project="<?= $gproj->get_name() ?>" project-id="<?= $gproj->get_id()?>" date="<?= $lastupdate ?>">
  <members start="<?= $low ?>" limit="<?= $limit ?>" total="<?= $rows ?>"  date="<?= $lastupdate ?>">
  <?
    // Generate the listing here.
    $cnt = count($teamMembers);
    for ($i = 0; $i < $cnt; $i++) {
      $statsTmp =& $teamMembers[$i]->get_current_stats();
      $rnk = number_style_convert($lo + $i);
      $prnk = $statsTmp->get_stats_item("rank");
      $prnkchg = $statsTmp->get_stats_item("rank_change");
      $n_yesterday = (double) $statsTmp->get_stats_item("work_today") * $gproj->get_scale();
      $yesterday = round($n_yesterday,0);
      $n_blocks = (double) $statsTmp->get_stats_item("work_total") * $gproj->get_scale();
      $blocks = round($n_blocks,0);
      $first = $statsTmp->get_stats_item("first_date");
      $last = $statsTmp->get_stats_item("last_date");
      $linkid = (int) $teamMembers[$i]->get_id();
      $fmtid = number_style_convert($linkid);

      $listas = $teamMembers[$i]->get_display_name();

      //debug_text("<!--- y:$n_yesterday o:$n_blocks yt:$yblocks ot:$oblocks y%:$n_yesterday/$yblocks --->\n",$debug);
    ?>
    <participant-summary id="<?= $linkid ?>">
      <name><![CDATA[<?= $listas ?>]]></name>
      <stats>
        <stat name="rank-overall" unit="" value="<?= $rnk ?>" change=""/>
        <stat name="rank-project" unit="" value="<?= $prnk ?>" change="<?= $prnkchg ?>"/>
        <stat name="work-overall" unit="<?= $gproj->get_scaled_unit_name() ?>" value="<?= $blocks ?>"/>
        <stat name="work-day" unit="<?= $gproj->get_scaled_unit_name() ?>" value="<?= $yesterday ?>"/>
        <stat name="work-first" unit="" value="<?= $first ?>"/>
        <stat name="work-last" unit="" value="<?= $last ?>"/>
      </stats>
    </participant-summary>
    <?
      unset($statsTmp);
    }
    ?>
  </members>
</team-summary>
