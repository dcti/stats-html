<?php
include "security.inc";
include "../etc/config.inc";
include "../etc/project.inc";

  sybase_connect($interface, $ss_login, $ss_passwd);
  if(isset($id)) {
    $qs = "select * from stats.dbo.STATS_participant where id = $id";
  } else {
    $qs = "select * from stats.dbo.STATS_participant where email = '$email'";
  }
  $result = sybase_query($qs);
  $rows = sybase_num_rows($result);

  if( $rows <> 1) {
    include "templates/pbadpass.inc";
    exit;
  }
  sybase_data_seek($result,0);
  $par = sybase_fetch_object($result);
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
        "http://www.w3.org/TR/REC-html40/loose.dtd">
<?

  $title = "Participant configuration for $par->email (#$id)";
  include "../templates/header.inc";

  $id = (int) $par->id;
  $team = (int) $par->team;
  $retire_to = (int) $par->retire_to;

  $teamname = "Not a team member";
  if( $team > 0 ) {
    $teamname = "Invalid team";
    $qs = "select * from stats.dbo.STATS_team where team = $team";
    $result = sybase_query($qs);
    $rows = sybase_num_rows($result);
    if( $rows == 1 ) {
      sybase_data_seek($result,0);
      $teaminfo = sybase_fetch_object($result);
      $teamname = $teaminfo->name;
    }
  }

  switch ($par->listmode) {
    case 0:
      $sel_normal = "selected";
      break;
    case 1:
      $sel_obscure = "selected";
      break;
    case 2:
      $sel_realname = "selected";
      break;
    case 8:
      $sel_hackshow = "selected";
      break;
    case 9:
      $sel_spamshow = "selected";
      break;
    case 10:
      $sel_invisible = "selected";
      break;
    case 11:
      $sel_team = "selected";
      break;
    case 18:
      $sel_hackhide = "selected";
      break;
    case 19:
      $sel_spamhide = "selected";
      break;
  }
  $lmlist = "
        <select name=\"listmode\">
        <option value=\"0\" $sel_normal>List as '$par->email'.</option>
        <option value=\"1\" $sel_obscure>List as 'Participant $id'.</option>
        <option value=\"2\" $sel_realname>List using real name.</option>
        <option value=\"8\" $sel_hackshow>HACKER, But show them.</option>
        <option value=\"9\" $sel_spamshow>SPAMMER, But show them.</option>
        <option value=\"10\" $sel_invisible>Invisible, do not show.</option>
        <option value=\"11\" $sel_team>Team address, do not rank.</option>
        <option value=\"18\" $sel_hackhide>HACKER, Do not show.</option>
        <option value=\"19\" $sel_spamhide>SPAMMER, Do not show.</option>
        </select>";

  switch ($par->dem_heard) {
    case 0:
      $hsel_dunno="selected";
      break;
    case 1:
      $hsel_friend="selected";
      break;
    case 2:
      $hsel_banner="selected";
      break;
    case 3:
      $hsel_link="selected";
      break;
    case 4:
      $hsel_sig="selected";
      break;
    case 5:
      $hsel_press="selected";
      break;
    case 99:
      $hsel_promo="selected";
      break;
  }

  switch ($par->dem_gender) {
    case "M":
      $gsel_male="selected";
      break;
    case "F":
      $gsel_female="selected";
      break;
  }

  switch ($par->dem_motivation) {
    case 0:
      $msel_dunno="selected";
      break;
    case 1:
      $msel_cool="selected";
      break;
    case 2:
      $msel_politic="selected";
      break;
    case 3:
      $msel_cash="selected";
      break;
    case 4:
      $msel_stats="selected";
      break;
    case 5:
      $msel_cow="selected";
      break;
  }
 
  print "
  <form action=\"pedit_save.php3\" method=\"post\">
   <center>
    <h2>$title</h2>
    <table>
     <tr>
      <td>Participant:</td>
      <td>
       <strong>$par->email</strong> ($id)
       <a href=\"/rc5-64/psummary.php3?id=$id\">[RC5-64]</a>
      </td>
     </tr>
     <tr>
      <td>Team:</td>
      <td>
       $team: $teamname
       <a href=\"/rc5-64/tmsummary.php3?team=$team\">[RC5-64]</a>
      </td>
     </tr>
     <tr>
      <td>List Mode:</td>
      <td>$lmlist<br>$lmmore</td>
     </tr>
     <tr>
      <td colspan=\"2\"><hr></td>
     </tr>
     <tr>
      <td>Password:</td>
      <td>
       <input name=\"pword\" value=\"$par->password\" size=\"8\" maxlength=\"8\">
       <a href=\"pfwdpass.php3?id=$id\">
        [<font color=\"Red\">
         Mail this password to someone
        </font>
       </a>]
      </td>
     </tr>
     <tr>
      <td>Motto:</td>
      <td><input name=\"motto\" value=\"$par->motto\" size=\"30\" maxlength=\"128\"></td>
     </tr>
     <tr>
      <td>Team:</td>
      <td><input name=\"team\" value=\"$team\" size=\"8\" maxlength=\"8\"></td>
     </tr>
     <tr>
      <td>retire_to:</td>
      <td><input name=\"retire_to\" value=\"$retire_to\" size=\"8\" maxlength=\"8\"></td>
";
  if ( $retire_to<>0 ) {
	print "<td><a href=\"pedit.php3?id=$retire_to\">Edit participant #$retire_to</a></td>";
  }
  print "
     </tr>
     <tr>
      <td colspan=\"2\"><hr></td>
     </tr>
     <tr>
      <td>Real Name:</td>
      <td><input name=\"contact_name\" value=\"$par->contact_name\" size=\"30\"></td>
     </tr>
     <tr>
      <td>Phone Number:</td>
      <td><input name=\"contact_phone\" value=\"$par->contact_phone\" size=\"20\"></td>
     </tr>
     <tr>
      <td colspan=\"2\"><hr></td>
     </tr>
     <tr>
      <td>Year you were born:</td>
      <td><input name=\"dem_yob\" value=\"$par->dem_yob\" size=\"4\"></td>
     </tr>
     <tr>
      <td>Gender:</td>
      <td>
       <select name=\"dem_gender\">
        <option value=\"-\">Private</option>
        <option value=\"M\" $gsel_male>Male</option>
        <option value=\"F\" $gsel_female>Female</option>
       </select>
      </td>
     </tr>
     <tr>
      <td>Country Code:</td>
      <td><input name=\"dem_country\" value=\"$par->dem_country\" size=\"4\"> [Click for list]</td>
     </tr>
     <tr>
      <td>How did you hear<br>about distributed.net?</td>
      <td>
       <select name=\"dem_heard\">
        <option value=\"0\" $hsel_dunno>Who knows?  I've slept since then.</option>
        <option value=\"1\" $hsel_friend>A friend told me about it.</option>
        <option value=\"2\" $hsel_banner>I clicked on a banner.</option>
        <option value=\"3\" $hsel_link>I followed a link from someone's page.</option>
        <option value=\"4\" $hsel_sig>Saw it is someone's .signature file.</option>
        <option value=\"5\" $hsel_press>Read an article about it.</option>
        <option value=\"99\" $hsel_promo>None of the above, You need more options!</option>
       </select>
      </td>
     </tr>
     <tr>
      <td>Why?</td>
      <td>
       <select name=\"dem_motivation\">
        <option value=\"0\" $msel_dunno>Why not?</option>
        <option value=\"1\" $msel_cool>It's really cool!</option>
        <option value=\"2\" $msel_politic>To fight the man!  It's all about politics.</option>
        <option value=\"3\" $msel_cash>I need the money</option>
        <option value=\"4\" $msel_stats>I love stats.</option>
        <option value=\"5\" $msel_cow>The cow is soooooo cute.</option>
       </select>
      </td>
     </tr>
     <tr>
      <td colspan=\"2\" align=\"center\">
       <hr>
       <input name=\"id\" type=\"hidden\" value=\"$id\">
       <input name=\"pass\" type=\"hidden\" value=\"$pass\">
       <input value=\"Update my information\" type=\"submit\">
      </td>
     </tr>
    </table>
    <h2>
     All information is *completely* confidential.
    </h2>
    <p>
     Name and Phone number to ensure we can reach you if your client
     finds the winning block.<br>The other stuff is just so we can understand
     better who is running the client and how we can best attract new people.
    </p>
    <p>
     <i>All, most, or some of the above may or may not work yet.</i>
    </p>
   </center>
  </form>";
?>
</html>
