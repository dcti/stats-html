<?php
  // $Id: pedit_save.php,v 1.18 2005/12/07 10:22:31 fiddles Exp $

  // psecure.inc will obtain $id and $pass from the user.
  // Input may come from the url, http headers, or a client cookie

  include "../etc/global.inc";
  include "../etc/project.inc";
  include "../etc/psecure.inc";

  if(isset($_POST['cookie'])) {
    if($_POST['cookie'] == "yes" ) {
      SetCookie("sbid",$id,time()+3600*24*365,"/");
      SetCookie("sbpass",$pass,time()+3600*24*365,"/");
    }
  }

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

  // Block all changes if pedit is in read-only mode
  if ($readonly_pedit == 0) {
      // Update main participant info
      if($gpart->get_list_mode() <= 2 && $listas <= 2)
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

      // Update friend info
      $friend_list = "$friend_a,$friend_b,$friend_c,$friend_d,$friend_e";
      $gpart->set_friends($friend_list);

      // Save the object
      $result = $gpart->save();
      if($result != "")
      {
    	    trigger_error("There was an error saving your participant information. <a href=\"javascript:history.back()\">Correct the error</a><br><br>");
            exit(0);
      }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
        "http://www.w3.org/TR/REC-html40/loose.dtd">
<html>
 <head>
  <title>Updating <?=$gpart->get_email()?> data</title>
 <?if($debug <= 0){?><meta http-equiv="refresh" content="4; URL=http://stats.distributed.net/participant/psummary.php?project_id=<?=$gproj->get_id()?>&amp;id=<?=$id?>"><?}?>
 </head>
 <body>
  <div style="text-align: center">
   <h2>Saving your information...</h2>
  </div>
 </body>
</html>
<?php
  } else { // if ($readonly_pedit == 0)
  print "
<html>
<head>
<title>Cannot update ".$gpart->get_email().": site is read-only</title>
</head>
<body>
";
include("../templates/readonly.inc");
print "Click here to <a href=\"http://stats.distributed.net/participant/psummary.php?project_id=".$gproj->get_id()."&id=$id\">return to your participant summary</a>
</body>
</html>
";
} // if ($readonly_pedit == 0)
?>
