<?php
  // $Id: tmedit.php,v 1.8 2003/05/27 18:38:29 thejet Exp $

  // psecure.inc will obtain $id and $pass from the user.
  // Input may come from the url, http headers, or a client cookie
  
  include "../etc/tmsecure.inc";
  include "../etc/config.inc";
  include "../etc/project.inc";
  include "../etc/team.php";

  $tmptr = new Team($gdb, $gproj, $team);
  if($tmptr->get_password() != $tpass)
  {
    include "../templates/tmbadpass.inc";
    exit;
  }
  /*
  sybase_connect($interface,$username,$password);
  $qs = "select * from STATS_team where team = $team and password = '$tpass'";
  $result = sybase_query($qs);
  $rows = sybase_num_rows($result);

  if( $rows <> 1) {
    include "../templates/tmbadpass.inc";
    exit;
  }
  sybase_data_seek($result,0);
  $par = sybase_fetch_object($result);
  */

  if($tmptr->get_listmode() <= 2) {
    switch ($tmptr->get_listmode()) {
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
    switch ($tmptr->get_listmode()) {
      case 8:
      case 18:
        $lmlist = "This team has a known hacker as a member.";
        break;
      case 9:
      case 19:
        $lmlist = "This team has a known spammer as a member.";
        break;
    }
    if ($tmptr->get_listmode() >= 10) {
      $lmmore = "This team will not be ranked or listed.";
    }
  }

  switch ($tmptr->get_show_members()) {
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

  $lastupdate = last_update('t');
  include "../templates/header.inc";
?>
  <div style="text-align: center;">
  <form action="tmedit_save.php" method="post">
    <h2>
     Team Configuration for Team #<?=$team?>
    </h2>
    <table style="margin: auto; text-align: left;" width="75%">
     <tr>
      <td>Team Name:</td>
      <td><input name="name" value="<?=$tmptr->get_name()?>" size="50" maxlength="64"></td>
     </tr>
     <tr>
      <td>&nbsp;</td>
      <td>
        <span style="font-size: smaller">No HTML in Team Names.  If you try, it will look silly.
        <br>
        Changes to Team Names will not fully take effect until the next stats run.
        </span>
      </td>
     </tr>
     <tr>
      <td>Team Web Page:</td>
      <td><input name="url" value="<?=$tmptr->get_url()?>" size="50" maxlength="64"></td>
     </tr>
     <tr>
      <td>Team Logo url:</td>
      <td><input name="logo" value="<?=$tmptr->get_logo()?>" size="50" maxlength="64"></td>
     </tr>
<?include "../etc/markuplegend.inc";?>
     <tr>
      <td>Description:</td>
      <td><textarea name="description" cols="50" rows="5"><?=$tmptr->get_description()?></textarea></td>
     </tr>
     <tr>
      <td>&nbsp;</td>
      <td><span style="font-size: smaller">HTML is permitted in the description.</span></td>
     </tr>
     <tr>
      <td>Coordinator's Name:</td>
      <td><input name="contactname" value="<?=$tmptr->get_contact_name()?>" size="50" maxlength="64"></td>
     </tr> 
     <tr>
      <td>Coordinator's Email:</td>
      <td><input name="contactemail" value="<?=$tmptr->get_contact_email()?>" size="50" maxlength="64"></td>
     </tr> 
     <tr>
      <td>Privacy:</td>
      <td>
       <select name="showmembers">
        <option value="YES" <?=$psel_yes?>>Public Members Listing</option>
        <option value="NO" <?=$psel_no?>>No Members Listing</option>
        <option value="PAS" <?=$psel_pas?>>Private Members Listing</option>
       </select>
      </td>
     </tr>
     <tr>
      <td>Team Members' Password:</td>
      <td><input name="showpassword" value="<?=$tmptr->get_show_password()?>" size="16" maxlength="16"></td>
     </tr> 
     <tr>
      <td colspan="2" align="center">
       <hr>
       Check this box <input name="cookie" type="checkbox" value="yes"> to save your login information in a cookie<br>
       <span style="color: red">It would be very silly to do this on a machine you share with others<br>
        or on a machine that's not in a secure location.<br>
        This will store your password on the machine.</span>
        <hr>
       <input name="team" type="hidden" value="<?=$team?>">
       <input name="pass" type="hidden" value="<?=$tpass?>">
       <input value="Update information" type="submit">
      </td>
     </tr>
    </table>
    <p>
     <i>All, most, or some of the above may or may not work yet.</i>
    </p>
  </form>
  </div>
</body>
</html>
