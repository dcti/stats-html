<?php
  // $Id: tmedit.php3,v 1.3 2000/01/18 03:52:01 decibel Exp $

  // psecure.inc will obtain $id and $pass from the user.
  // Input may come from the url, http headers, or a client cookie
  
  include "security.inc";
  include "../etc/config.inc";
  include "../etc/project.inc";

  sybase_connect($interface,$username,$password);
  $qs = "select * from STATS_team where team = $team";
  $result = sybase_query($qs);
  $rows = sybase_num_rows($result);

  if( $rows <> 1) {
    include "templates/tmbadpass.inc";
    exit;
  }
  sybase_data_seek($result,0);
  $par = sybase_fetch_object($result);

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
        "http://www.w3.org/TR/REC-html40/loose.dtd">
<?

  $team = 0+$par->team;
  $listmode = 0+$par->listmode;

  if($par->listmode <= 2) {
    switch ($par->listmode) {
      case 0:
        $sel_normal = "selected";
        break;
      case 1:
        $sel_restricted = "selected";
        break;
      case 2:
        $sel_closed = "selected";
        break;
    }
    $lmlist = "
          <select name=\"listmode\">
          <option value=\"0\" $sel_normal>Anyone may join this team.</option>
          <option value=\"1\" $sel_obscure>A password is required to join this team.</option>
          <option value=\"2\" $sel_realname>This team has disbanded.</option>
          </select>";
  } else {
    switch ($par->listmode) {
      case 8:
      case 18:
        $lmlist = "This team has a known hacker as a member.";
        break;
      case 9:
      case 19:
        $lmlist = "This team has a known spammer as a member.";
        break;
    }
    if ($par->listmode >= 10) {
      $lmmore = "This team will not be ranked or listed.";
    }
  }

  switch ($par->showmembers) {
    case 'YES':
      $psel_yes = "selected";
      break;
    case 'NO':
      $psel_no = "selected";
      break;
    case 'PAS':
      $psel_pas = "selected";
      break;
  }

  include "templates/header.inc";

  print "
  <form action=\"tmedit_save.php3\" method=\"post\">
   <center>
    <h2>
     Team Configuration for Team #$team
    </h2>
    <table>
     <tr>
      <td>Team Name:</td>
      <td><input name=\"name\" value=\"$par->name\" size=\"50\" maxlength=\"64\"></td>
     </tr>
     <tr>
      <td>&nbsp;</td>
      <td>
       <font size=\"-1\">
        No HTML in Team Names.  If you try, it will look silly.
        <br>
        Changes to Team Names will not fully take effect until the next stats run.
       </font>
      </td>
     </tr>
     <tr>
      <td>Team Web Page:</td>
      <td><input name=\"url\" value=\"$par->url\" size=\"50\" maxlength=\"64\"></td>
     </tr>
     <tr>
      <td>Team Logo url:</td>
      <td><input name=\"logo\" value=\"$par->logo\" size=\"50\" maxlength=\"64\"></td>
     </tr>
     <tr>
      <td>Description:</td>
      <td><textarea name=\"description\" type=\"text\" cols=\"50\" rows=\"5\" wrap=\"virtual\">$par->description</textarea></td>
     </tr>
     <tr>
      <td>&nbsp;</td>
      <td><font size=\"-1\">HTML is permitted in the description.</font></td>
     </tr>
     <tr>
      <td>Coordinator's Name:</td>
      <td><input name=\"contactname\" value=\"$par->contactname\" size=\"50\" maxlength=\"64\"></td>
     </tr> 
     <tr>
      <td>Coordinator's Email:</td>
      <td><input name=\"contactemail\" value=\"$par->contactemail\" size=\"50\" maxlength=\"64\"></td>
     </tr> 
     <tr>
      <td>Privacy:</td>
      <td>
       <select name=\"showmembers\">
        <option value=\"YES\" $psel_yes>Public Members Listing</option>
        <option value=\"NO\" $psel_no>No Members Listing</option>
        <option value=\"PAS\" $psel_pas>Private Members Listing</option>
       </select>
      </td>
     </tr>
     <tr>
      <td>Team Members' Password:</td>
      <td><input name=\"showpassword\" value=\"$par->showpassword\" size=\"16\" maxlength=\"16\"></td>
     </tr> 
     <tr>
      <td colspan=\"2\" align=\"center\">
       <hr>
       Check this box <input name=\"cookie\" type=\"checkbox\" value=\"yes\"> to save your login information in a cookie<br>
       <font color=\"red\">It would be very silly to do this on a machine you share with others<br>
        or on a machine that's not in a secure location.<br>
        This will store your password on the machine.</font>
        <hr>
       <input name=\"team\" type=\"hidden\" value=\"$team\">
       <input name=\"pass\" type=\"hidden\" value=\"$tpass\">
       <input value=\"Update information\" type=\"submit\">
      </td>
     </tr>
    </table>
    <p>
     <i>All, most, or some of the above may or may not work yet.</i>
    </p>
   </center>
  </form>";
?>
</html>
