<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
        "http://www.w3.org/TR/REC-html40/loose.dtd">
<?php
  // $Id: pedit_save.php,v 1.10 2003/11/25 18:21:10 thejet Exp $

  // psecure.inc will obtain $id and $pass from the user.
  // Input may come from the url, http headers, or a client cookie
  
  include "../etc/config.inc";
  include "../etc/project.inc";
  include "../etc/psecure.inc";

  if(isset($_POST['cookie'])) {
    if($cookie == "yes" ) {
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

  $contact_name = htmlspecialchars($_POST['contact_name']);
  $contact_phone = $_POST['contact_phone'];
  $motto = $_POST['motto'];

  $listas = (int) $_POST['listas'];

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
    $title = "Error Saving Participant Data #$id";
    include "../templates/header.inc";
    display_last_update();
    print "
       <h1>Error occurred</h2>
       <h3>There was an error saving your participant information, the error message(s)
           is below:</h3>
       " . str_replace("\n", "<br>", $result) . "<br><br>
       <a href=\"javascript:history.back()\">Correct the error</a><br><br>";
    // include "../templates/footer.inc";
    exit(0);
  }
?>
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
