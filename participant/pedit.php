<?php
  // $Id: pedit.php,v 1.3 1999/07/20 04:20:12 nugget Exp $
  //
  // psecure.inc will obtain $id and $pass from the user.
  // Input may come from the url, http headers, or a client cookie
  
  include "etc/config.inc";
  include "etc/project.inc";
  include "etc/modules.inc";
  include "etc/psecure.inc";

  # psecure.inc leaves us with $result containing * from STATS_Participant
  # and $par being the fetched object.

  $id = 0+$par->id;
  $team = 0+$par->team;
  $nonprofit = 0+$par->nonprofit;

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

  $qs = "select * from stats.dbo.STATS_nonprofit order by nonprofit";
  sybase_query("set rowcount 0");
  $npresult = sybase_query($qs);
  $nonprofits = sybase_num_rows($npresult);
  
  $npoptions = "<option value=\"0\">None Selected</option>";
  for( $i = 0; $i < $nonprofits; $i++) {
    sybase_data_seek($npresult,$i);
    $npdata = sybase_fetch_object($npresult);
    $npbuf = 0+$npdata->nonprofit;
    if( $npbuf == $nonprofit) {
      $selstring = "selected";
    } else {
      $selstring = "";
    }
    $npoptions = "$npoptions
	<option value=\"$npbuf\" $selstring>$npdata->name</option>";
  }
  
  $qs = "select * from stats.dbo.STATS_country order by country";
  $countryresult = sybase_query($qs);
  $countries = sybase_num_rows($countryresult);

  $countryoptions = "<option value=\"\">None Selected</option>";
  for( $i = 0; $i < $countries; $i++) {
    sybase_data_seek($countryresult,$i);
    $country = sybase_fetch_object($countryresult);
    if( $country->code == $par->dem_country ) {
      $selstring = "selected";
    } else {
      $selstring = "";
    }
    $countryoptions = "$countryoptions
       <option value=\"$country->code\" $selstring>$country->country</option>";
  }
  
  if($par->listmode <= 2) {
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
    }
    $lmlist = "
          <select name=\"listas\">
          <option value=\"0\" $sel_normal>List me as '$par->email'.</option>
          <option value=\"1\" $sel_obscure>List me as 'Participant $id'.</option>
          <option value=\"2\" $sel_realname>List me using my real name.</option>
          </select>";
  } else {
    switch ($par->listmode) {
      case 8:
      case 18:
        $lmlist = "This participant is a known hacker.";
        break;
      case 9:
      case 19:
        $lmlist = "This participant is a known spammer.";
        break;
      case 11:
        $lmlist = "This participant is a team address.";
        break;
    }
    if ($par->listmode >= 10) {
      $lmmore = "This participant will not be ranked or listed.";
    }
  }

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
    case 6:
      $msel_sex="selected";
      break;
  }
 
  include "templates/header.inc";

  if ($debug == yes) print "  <form action=\"pedit_save.php3?debug=yes\" method=\"post\">";
	else print "  <form action=\"pedit_save.php3\" method=\"post\">";
  print "
   <center>
    <h2>
     Participant Configuration for $par->email
    </h2>
    <table>
     <tr>
      <td>Participant:</td>
      <td><strong>$par->email</strong></td>
     </tr>
     <tr>
      <td align=\"top\">Team:</td>
      <td>$team: $teamname</td>
     </tr>
     <tr>
      <td>&nbsp;</td>
      <td>
       <font size=\"-1\">
        <i>To join a team, have your email address and password handy<br>
           and visit that team's stats summary page.</i>
        <p>
        If you do not wish to be on a team, click <a href=\"/pjointeam.php3?team=0\">here</a>.
       </font>
      </td>
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
      <td colspan=\"2\"><hr></td>
     </tr>
     <tr>
      <td>Motto:</td>
      <td><input name=\"motto\" value=\"$par->motto\" size=\"50\" maxlength=\"128\"></td>
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
       <hr>
       Check this box <input name=\"cookie\" type=\"checkbox\" value=\"yes\"> to save your login information in a cookie<br>
       <font color=\"red\">It would be very silly to do this on a machine you share with others<br>
        or on a machine that's not in a secure location.<br>
        This will store your password on the machine.</font>
        <hr>
       <input name=\"id\" type=\"hidden\" value=\"$id\">
       <input name=\"pass\" type=\"hidden\" value=\"$test_pass\">
       <input value=\"Update my information\" type=\"submit\">
      </td>
     </tr>
    </table>
    <hr>
    <p>
     If this address is no longer current/valid, you may \"retire\" its blocks into another email address.
     <br>
     Before you can do this, you should update all of your clients to your new email address and wait
     for that new address to appear in the stats database.
     <br>
     Once you've done that, you may then <a href=\"/pretire.php3?id=$id&pass=$test_pass\">retire this email address permanently</a>.
    </p>
    <hr>
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
