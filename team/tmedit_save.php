<?php
  // $Id: tmedit_save.php,v 1.6 2003/09/23 20:40:02 paul Exp $

  // tmsecure.inc will obtain $team and $tpass from the user.
  // Input may come from the url, http headers, or a client cookie

  if(isset($_POST['cookie'])) {
    if( $_POST['cookie'] == "yes" ) {
      SetCookie("sbteam",$team,time()+3600*24*365,"/");
      SetCookie("sbtpass",$pass,time()+3600*24*365,"/");
    }
  }
  
  include "../etc/tmsecure.inc";
  include "../etc/config.inc";
  include "../etc/project.inc";
  include "../etc/team.php";

  $tmptr = new Team($gdb, $gproj, $team);
  if($tmptr->get_password() != $pass)
  {
    include "../templates/tmbadpass.inc";
    exit;
  }

  $listmode = $tmptr->get_listmode();

  if ($listmode == 8 or $listmode == 9 or $listmode == 18 or $listmode == 19) {
    include "../templates/tmlocked.inc";
    exit;
  }

  $name = htmlspecialchars($name);

  $tmptr->set_name($_POST['name']);
  $tmptr->set_url($_POST['url']);
  $tmptr->set_contact_name($_POST['contactname']);
  $tmptr->set_contact_email($_POST['contactemail']);
  $tmptr->set_logo($_POST['logo']);
  $tmptr->set_show_members($_POST['showmembers']);
  $tmptr->set_show_password($_POST['showpassword']);
  $tmptr->set_description($_POST['description']);

  // Save the team information
  $retVal = $tmptr->save();
  if($retVal != "")
  {
    print("<h2>Validation Errors:</h2>\n");
    print str_replace("\n", "<br>", $retVal);
    print("<br><a href=\"javascript:history.back();\">Go back and fix these errors</a><br>\n");
    print("</body></html>");
    exit;
  }
  
?>
<html>
	<head>
		<title>Updating <?=$tmptr->get_name()?> data</title>
		<meta http-equiv="refresh" content="4; URL=tmsummary.php?team=<?=$tmptr->get_id()?>">
	</head>
	<body>
		<div style="text-align: center">
			<h2>Saving your information...</h2>
		</div>
	</body>
</html>
