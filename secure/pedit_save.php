<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
        "http://www.w3.org/TR/REC-html40/loose.dtd">
<?php
  // $Id: pedit_save.php,v 1.2 2005/12/07 05:44:01 fiddles Exp $

  // psecure.inc will obtain $id and $pass from the user.
  // Input may come from the url, http headers, or a client cookie

  include "../etc/global.inc";
  include "../etc/project.inc";
  include "../etc/participant.php";

if (is_int($_REQUEST['id']))
        $id = $_REQUEST['id'];

$gpart = new Participant($gdb, $gproj, $id, true);
if (!$gpart) {
        trigger_error('No participant found with that ID!');
}
  $gpart->_authed = true; // UGLY HACK: We shouldn't be setting it like this!
  $dem_yob = (int) $_POST['dem_yob'];
  $dem_heard = (int) $_POST['dem_heard'];
  $dem_motivation = (int) $_POST['dem_motivation'];
  $dem_gender = $_POST['dem_gender'];
  $dem_country = $_POST['dem_country'];

  $friend_a = (int) $_POST['friend_a'];
  $friend_b = (int) $_POST['friend_b'];
  $friend_c = (int) $_POST['friend_c'];
  $friend_d = (int) $_POST['friend_d'];
  $friend_e = (int) $_POST['friend_e'];

  $nonprofit = (int) $_POST['nonprofit'];

  $contact_name = $_POST['contact_name'];
  $contact_phone = $_POST['contact_phone'];
  $motto = $_POST['motto'];

  $listas = (int) $_POST['listas'];
  
  $password = $_POST['password'];
  
if ($readonly_secure == 0) {
  // Update main participant info
  $gpart->set_list_mode($listas);
  $gpart->set_non_profit($nonprofit);
  $gpart->set_dem_yob($dem_yob);
  $gpart->set_dem_heard($dem_heard);
  $gpart->set_dem_gender($dem_gender);
  $gpart->set_dem_motivation($dem_motivation);
  $gpart->set_dem_country($dem_country);
  $gpart->set_contact_name($contact_name);
  $gpart->set_contact_phone($contact_phone);
  $gpart->set_motto($motto);
  $gpart->set_password($password);

  // Update friend info
  $friend_list = "$friend_a,$friend_b,$friend_c,$friend_d,$friend_e";
  $gpart->set_friends($friend_list);

  // Save the object
  $result = $gpart->save();
  if($result != "")
  {
	trigger_error("There was an error saving participant information. <a href=\"javascript:history.back()\">Correct the error</a><br><br>");
    exit(0);
  }
?>
<html>
 <head>
  <title>Updating <?=$gpart->get_email()?> data</title>
 <?if($debug <= 0){?><meta http-equiv="refresh" content="4; URL=http://stats.distributed.net/secure"><?}?>
 </head>
 <body>
  <div style="text-align: center">
   <h2>Saving...</h2>
  </div>
 </body>
</html>
<?php } else {
 print "
<html>
<head>
<title>Cannot update ".$gpart->get_email().": site is read-only</title>
</head>
<body>
";
include(../templates/readonly.inc);
print "</body>
</html>";
}
?>

