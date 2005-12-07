<?php
  // $Id: tmedit_save.php,v 1.13 2005/12/07 05:44:01 fiddles Exp $

  include "../etc/global.inc";
  include "../etc/modules.inc";
  include "../etc/project.inc";
  include "../etc/tmsecure.inc";

  if(isset($_POST['cookie'])) {
    if( $_POST['cookie'] == "yes" ) {
      SetCookie("sbteam",$tm,time()+3600*24*365,"/");
      SetCookie("sbtpass",$pass,time()+3600*24*365,"/");
    }
  }

  /*
  $gteam = new Team($gdb, $gproj, $team);
  if($gteam->get_password() != $pass)
  {
    trigger_error("Incorrect Password for this team");
    exit;
  }
  */

  $listmode = $gteam->get_listmode();

  if ($listmode == 8 or $listmode == 9 or $listmode == 18 or $listmode == 19) {
    include "../templates/tmlocked.inc";
    exit;
  }
  
  if ($readonly_tmedit != 0) {
    print "<html><head><title>Cannot update team: read-only</title></head><body>";
    include "../templates/readonly.inc";
    print "<a href=\"/\">Alright, I guess I'll check back later</a></body></html>";
    exit;
  }
  
  $gteam->set_name($_POST['name']);
  $gteam->set_url($_POST['url']);
  $gteam->set_contact_name($_POST['contactname']);
  $gteam->set_contact_email($_POST['contactemail']);
  $gteam->set_logo($_POST['logo']);
  $gteam->set_show_members($_POST['showmembers']);
  $gteam->set_show_password($_POST['showpassword']);
  $gteam->set_description($_POST['description']);

  // Save the team information
  $retVal = $gteam->save();
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
		<title>Updating <?= safe_display($gteam->get_name()) ?> data</title>
		<meta http-equiv="refresh" content="4; URL=tmsummary.php?team=<?=$gteam->get_id()?>">
	</head>
	<body>
		<div style="text-align: center">
			<h2>Saving your information...</h2>
		</div>
	</body>
</html>
