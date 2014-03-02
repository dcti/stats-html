<?php

// $Id: pedit.php,v 1.27 2006/11/02 15:41:31 thejet Exp $
//
// psecure.inc will obtain $id and $pass from the user.
// Input may come from the url, http headers, or a client cookie
  
include "../etc/global.inc";
include "../etc/project.inc";
include "../etc/modules.inc";
include "../etc/psecure.inc";
include "../etc/team.php";

$title = 'Participant Edit';  
// @todo - nonprofit + global vars?
$nonprofit = 0+$gpart->get_non_profit();

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

$lmmore = '';
if($gpart->get_list_mode() <= 2) {
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
	}
	$lmlist = "
          <select name=\"listas\">
          <option value=\"0\" $sel_normal>List me as '".$gpart->get_email()."'.</option>
          <option value=\"1\" $sel_obscure>List me as 'Participant $id'.</option>
          <option value=\"2\" $sel_realname>List me using my real name.</option>
          </select>";
} else { // @TODO any code about list_mode from here on should never get reached as psecure should block it...
	switch ($gpart->get_list_mode()) {
		case 8:
		case 18:
			$lmlist = 'This participant is a known hacker.';
			break;
		case 9:
		case 19:
			$lmlist = 'This participant is a known spammer.';
			break;
		case 11:
			$lmlist = 'This participant is a team address.';
			break;
	}
	if ($gpart->get_list_mode() >= 10) {
		$lmmore = "This participant will not be ranked or listed.";
	}
}

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

include "../templates/header.inc";
display_last_update();

if ($readonly_pedit == 0) {
    print "  <form action=\"pedit_save.php\" method=\"post\">";
} else {
    print "<p><b><font colour=red>Read-Only</font></b><br>
The server is currently undergoing maintenance. As a result, you cannot currently edit your details.  Sorry for any inconvenience.</p>";
}
?>

    <h2>
     Participant Configuration for <?=$gpart->get_email()?>
    </h2>
    <form class="form-horizontal" role="form">
      <div class="form-group">       
        Participant: <?=$gpart->get_email()?>
      </div>
      <div class="form-group">
        Team: <?=$teamname?>
      </div>
      <p class="bg-info">
        To join a team, have your email address and password handy and visit that team's stats summary page.
        <?php if ($team != 0) { ?>
        If you do not wish to be on a team, click <a href="pjointeam.php?team=0">here</a>.
        <?php } ?>
      </p>
      <div class="form-group">
        Non-Profit:
        <select name="nonprofit"><?=$npoptions?></select>
      </div>
      <div class="form-group">
        List Mode:
        <?php echo "$lmlist<br>$lmmore";?>
      </div>
      <div class="form-group">
        <?php include "../etc/markuplegend.inc";?>
      </div>
      <div class="form-group">
        Motto:
        <textarea name="motto" cols="50" rows="5"><?=safe_display($gpart->get_motto())?></textarea>
      </div>
      <div class="form-group">
        Friend #1:
        <input name="friend_a" value="<?=get_friend_id($gpart,0)?>" size="7">
      </div>
      <div class="form-group">
        Friend #2:
        <input name="friend_b" value="<?=get_friend_id($gpart,1)?>" size="7">
      </div>
      <div class="form-group">
        Friend #3:
        <input name="friend_c" value="<?=get_friend_id($gpart,2)?>" size="7">
      </div>
      <div class="form-group">
        Friend #4:
        <input name="friend_d" value="<?=get_friend_id($gpart,3)?>" size="7">
      </div>
      <div class="form-group">
        Friend #5:
        <input name="friend_e" value="<?=get_friend_id($gpart,4)?>" size="7">
      </div>
      <div class="form-group">
        Real Name:
        <input name="contact_name" value="<?=safe_display($gpart->get_contact_name())?>" size="30">
      </div>
      <div class="form-group">
        Phone Number:
        <input name="contact_phone" value="<?=safe_display($gpart->get_contact_phone())?>" size="20">
      </div>
      <div class="form-group">
        Year you were born:
        <input name="dem_yob" value="<?=$gpart->get_dem_yob()?>" size="4"> 
      </div>
      <div class="form-group">
        Gender:
       <select name="dem_gender">
        <option value="">Private</option>
        <option value="M" <?=$gsel_male?>>Male</option>
        <option value="F" <?=$gsel_female?>>Female</option>
       </select>
      </div>
      <div class="form-group">
        Country Code:
       <select name="dem_country">
        <?=$countryoptions?>
       </select>
      </div>
      <div class="form-group">
        How did you hear<br>about distributed.net?
       <select name="dem_heard">
        <option value="0" $hsel_dunno>Who knows?  I've slept since then.</option>
        <option value="1" $hsel_friend>A friend told me about it.</option>
        <option value="2" $hsel_banner>I clicked on a banner.</option>
        <option value="3" $hsel_link>I followed a link from someone's page.</option>
        <option value="4" $hsel_sig>Saw it in someone's .signature file.</option>
        <option value="5" $hsel_press>Read an article about it.</option>
        <option value="99" $hsel_promo>None of the above, You need more options!</option>
       </select>
      </div>
      <div class="form-group">
        Why?
       <select name="dem_motivation">
        <option value="0" $msel_dunno>Why not?</option>
        <option value="1" $msel_cool>It's really cool!</option>
        <option value="2" $msel_politic>To fight the man!  It's all about politics.</option>
        <option value="3" $msel_cash>I need the money</option>
        <option value="4" $msel_stats>I love stats.</option>
        <option value="5" $msel_cow>The cow is soooooo cute.</option>
        <option value="6" $msel_sex>To attract the opposite sex.</option>
       </select>
      </div>
      <div class="form-group">
       Check this box <input name="cookie" type="checkbox" value="yes"> to save your login information in a cookie<br>
       <font color="red">It would be very silly to do this on a machine you share with others<br>
        or on a machine that's not in a secure location.<br>
        This will store your password on the machine.</font>
      </div>
      <div class="form-group">
<?php if ($readonly_pedit == 0) { ?>
       <input name="id" type="hidden" value="<?=$gpart->get_id()?>">
       <input name="pass" type="hidden" value="<?=$pass?>">
       <input name="project_id" type="hidden" value="<?=$gproj->get_id()?>">
       <input value="Update my information" type="submit">
<?php } ?>
      </div>
    </form>
    <div class="well">
     If this address is no longer current/valid, you may "retire" its blocks into another email address.
     <br>
     Before you can do this, you should update all of your clients to your new email address and wait
     for that new address to appear in the stats database.

     <?php if ($readonly_pretire == 0) { ?>
       Once you've done that, you may then <a href="pretire.php?id=$id&pass=$test_pass">retire this email address permanently</a>
     <?php } else { ?>
       Once you've done that, you may then retire this email address permanently
     <?php } ?>

     <br>
     All information is *completely* confidential.

     Name and Phone number to ensure we can reach you if your client
     finds the winning block.<br>The other stuff is just so we can understand
     better who is running the client and how we can best attract new people.
    </p>
    <p>
     <i>All, most, or some of the above may or may not work yet.</i>
    </p>
    </div>


<?
include "templates/footer.inc";

function get_friend_id(&$par, $index)
{
  $friend = $par->get_friends($index);
  if($friend == null)
    return "";
  else
    return $friend->get_id();
}
?>
