<?php

// $Id: pedit.php,v 1.1 2005/01/08 01:30:13 fiddles Exp $
//
// psecure.inc will obtain $id and $pass from the user.
// Input may come from the url, http headers, or a client cookie
  
include "../etc/global.inc";
include "../etc/project.inc";
include "../etc/modules.inc";
include "../etc/team.php";
include "../etc/participant.php";

if (is_int($_REQUEST['id']))
	$id = $_REQUEST['id'];

$gpart = new Participant($gdb, $gproj, $id, true);
if (!$gpart) {
	trigger_error('No participant found with that ID!');
}

$title = 'Participant Edit';  
// @todo - nonprofit + global vars?
$nonprofit = 0+$gpart->get_non_profit();
$password = $gpart->get_password();

$team = $gpart -> get_team_id();
// get a team object or is that pointless for id/name?
$teamname = "Not a team member";
if( $team > 0 ) {
	$teamname = "Invalid team";
        $teamptr = new Team($gdb, $gproj, $team);
        if($teamptr->get_id() > 0)
          $teamname = $teamptr->get_name();
        $teamptr = null;
}

$qs = "select * from STATS_nonprofit order by nonprofit";
$npresult =  $gdb->query($qs);
$nonprofits = $gdb->num_rows($npresult);
  
$npoptions = "<option value=\"0\">None Selected</option>";
for( $i = 0; $i < $nonprofits; $i++) {
	$gdb->data_seek($i,$npresult);
	$npdata = $gdb->fetch_object($npresult);
	$npbuf = 0+$npdata->nonprofit;
	if( $npbuf == $nonprofit) {
		$selstring = "selected";
	} else {
		$selstring = "";
	}
	$npoptions .= "
	<option value=\"$npbuf\" $selstring>$npdata->name</option>";
}
  
$qs = "select * from STATS_country order by country";
$countryresult = $gdb->query($qs);
$countries = $gdb->num_rows($countryresult);

$countryoptions = "<option value=\"\">None Selected</option>";
for( $i = 0; $i < $countries; $i++) {
	$gdb->data_seek($i,$countryresult);
	$country = $gdb->fetch_object($countryresult);
	if( $country->code == $gpart->get_dem_country() ) {
		$selstring = "selected";
	} else {
		$selstring = "";
	}
	$countryoptions .= "
		<option value=\"$country->code\" $selstring>$country->country</option>";
}

$sel_normal = '';
$sel_obscure = '';
$sel_realname = '';
$sel_hackershow = '';
$sel_spammershow = '';
$sel_invisible = '';
$sel_team = '';
$sel_hacker = '';
$sel_spammer = '';

$lmmore = '';
switch ($gpart->get_list_mode()) {
	case 0:
		$sel_normal = 'selected';
		break;
	case 1:
		$sel_obscure = 'selected';
		break;
	case 2:
		$sel_realname = 'selected';
		break;
	case 8:
		$sel_hackershow = 'selected';
		break;
	case 9:
		$sel_spammershow = 'selected';
		break;
	case 10:
		$sel_invisible = 'selected';
		break;
	case 11:
		$sel_team = 'selected';
		break;
	case 18:
		$sel_hacker = 'selected';
		break;
	case 19:
		$sel_spammer = 'selected';
		break;
}
$lmlist = "
         <select name=\"listas\">
         <option value=\"0\" $sel_normal>List me as '".$gpart->get_email()."'.</option>
         <option value=\"1\" $sel_obscure>List me as 'Participant $id'.</option>
         <option value=\"2\" $sel_realname>List me using my real name.</option>
	 <option value=\"8\" $sel_hackershow>HACKER, But show them.</option>
         <option value=\"9\" $sel_spammershow>SPAMMER, But show them.</option>
         <option value=\"10\" $sel_invisible>Invisible, do not show.</option>
         <option value=\"11\" $sel_team>Team address, do not rank.</option>
         <option value=\"18\" $sel_hacker>HACKER, Do not show.</option>
         <option value=\"19\" $sel_spammer>SPAMMER, Do not show.</option>
         </select>";

$hsel_dunno = '';
$hsel_friend = '';
$hsel_banner = '';
$hsel_link = '';
$hsel_sig = '';
$hsel_press = '';
$hsel_promo = '';

switch ($gpart->get_dem_heard()) {
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

$gsel_male = '';
$gsel_female = '';
switch ($gpart->get_dem_gender()) {
	case "M":
		$gsel_male="selected";
		break;
	case "F":
		$gsel_female="selected";
		break;
}

$msel_dunno='';
$msel_cool='';
$msel_politic='';
$msel_cash='';
$msel_cow='';
$msel_sex='';
$msel_stats='';

switch ($gpart->get_dem_motivation()) {
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
	case 6:
		$msel_sex="selected";
		break;
}

include "etc/header.inc";
display_last_update();

print "  <form action=\"pedit_save.php\" method=\"post\">
   <center>
    <h2>
     Participant Configuration for ". $gpart->get_email()."
    </h2>
   <table>
    <tr>
      <td>Participant:</td>
      <td><strong>". $gpart->get_email()."</strong></td>
     </tr>
     <tr>
      <td align=\"top\">Team:</td>
      <td>$team: $teamname</td>
     </tr>
     <tr>
      <td>Non-Profit:</td>
      <td>
       <select name=\"nonprofit\">
        $npoptions
       </select>
     </tr>
     <tr>
      <td>List Mode:</td>
      <td>$lmlist<br>$lmmore</td>
     </tr>
     <tr>
      <td>Password:</td>
      <td><input type=\"text\" name=\"password\" value=\"$password\"></td>
     </tr>
     <tr>
      <td colspan=\"2\"><hr></td>
     </tr>
 "; 
 include "../etc/markuplegend.inc";
  print " 
    <tr>
      <td>Motto:</td>
      <td>
       <textarea name=\"motto\" cols=\"50\" rows=\"5\">".safe_display($gpart->get_motto())."</textarea>
     </tr>
     <tr>
      <td colspan=\"2\"><hr></td>
     </tr>
     <tr>
      <td>Friend #1:</td>
      <td><input name=\"friend_a\" value=\"".get_friend_id($gpart,0)."\" size=\"7\"></td>
     </tr>
     <tr>
      <td>Friend #2:</td>
      <td><input name=\"friend_b\" value=\"".get_friend_id($gpart,1)."\" size=\"7\"></td>
     </tr>
     <tr>
      <td>Friend #3:</td>
      <td><input name=\"friend_c\" value=\"".get_friend_id($gpart,2)."\" size=\"7\"></td>
     </tr>
     <tr>
      <td>Friend #4:</td>
      <td><input name=\"friend_d\" value=\"".get_friend_id($gpart,3)."\" size=\"7\"></td>
     </tr>
     <tr>
      <td>Friend #5:</td>
      <td><input name=\"friend_e\" value=\"".get_friend_id($gpart,4)."\" size=\"7\"></td>
     </tr>
     <tr>
      <td colspan=\"2\"><hr></td>
     </tr>
     <tr>
      <td>Real Name:</td>
      <td><input name=\"contact_name\" value=\"".safe_display($gpart->get_contact_name())."\" size=\"30\"></td>
     </tr>
     <tr>
      <td>Phone Number:</td>
      <td><input name=\"contact_phone\" value=\"".safe_display($gpart->get_contact_phone())."\" size=\"20\"></td>
     </tr>
     <tr>
      <td colspan=\"2\"><hr></td>
     </tr>
     <tr>
      <td>Year you were born:</td>
      <td><input name=\"dem_yob\" value=\"".$gpart->get_dem_yob()."\" size=\"4\"></td>
     </tr>
     <tr>
      <td>Gender:</td>
      <td>
       <select name=\"dem_gender\">
        <option value=\"\">Private</option>
        <option value=\"M\" $gsel_male>Male</option>
        <option value=\"F\" $gsel_female>Female</option>
       </select>
      </td>
     </tr>
     <tr>
      <td>Country Code:</td>
      <td>
       <select name=\"dem_country\">
        $countryoptions
       </select>
     </tr>
     <tr>
      <td>How did you hear<br>about distributed.net?</td>
      <td>
       <select name=\"dem_heard\">
        <option value=\"0\" $hsel_dunno>Who knows?  I've slept since then.</option>
        <option value=\"1\" $hsel_friend>A friend told me about it.</option>
        <option value=\"2\" $hsel_banner>I clicked on a banner.</option>
        <option value=\"3\" $hsel_link>I followed a link from someone's page.</option>
        <option value=\"4\" $hsel_sig>Saw it in someone's .signature file.</option>
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
        <option value=\"6\" $msel_sex>To attract the opposite sex.</option>
       </select>
      </td>
     </tr>
     <tr>
      <td colspan=\"2\" align=\"center\">
       <input name=\"id\" type=\"hidden\" value=\"" . $gpart->get_id() . "\">
       <input name=\"project_id\" type=\"hidden\" value=\"". $gproj->get_id() . "\">
       <input value=\"Update Information\" type=\"submit\">
      </td>
     </tr>
    </table>
    <hr>
    <h2>
     All information is *completely* confidential.
    </h2>
    <p>
     <i>All, most, or some of the above may or may not work yet.</i>
    </p>
   </center>
  </form>";

include('etc/footer.inc');

function get_friend_id(&$par, $index)
{
  $friend =& $par->get_friends($index);
  if($friend == null)
    return "";
  else
    return $friend->get_id();
}
?>
