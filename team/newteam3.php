<?
 // $Id: newteam3.php,v 1.9 2003/05/27 18:38:29 thejet Exp $

 $title = "New Team Creation - Information";

 include "../etc/config.inc";
 include "../etc/modules.inc";
 include "../etc/project.inc";
 unset($proj_name);

 $lastupdate = last_update('t');
 include "../templates/header.inc";
?>
<div style="text-align: center">
   <p>
    <i>What else to I need to know?</i>
   </p>
   <table border="0" width="60%" style="margin:auto;text-align:left">
   <tr>
     <td>
       <ul>
       <li>Below is a very condensed version of the information that you may configure for your team.
           This is the minimum you must provide to register your team.<br><br></li>
       <li>Your team will NOT be listed until after the next database update takes place.  This could
           be anywhere from one minute to one day from now.  Please be patient.<br><br></li>
       <li>Oh yeah. It'd be sorta nice if you like, recruited folks and stuff.<br><br></li>
       </ul>
     </td>
   </tr>
   </table>
   <h2>Team Information</h2>
   <form action="newteam4.php" method="get">
   <table border="0" style="margin:auto;text-align:left">
   <tr>
      <td>Full Team Name:</td>
      <td><input name="name" type="text" value="" size="50" maxlength="64"></td>
   </tr>
   <tr>
      <td>Coordinator Name (You):</td>
      <td><input name="contactname" type="text" value="" size="50" maxlength="64"></td>
   </tr>
   <tr>
      <td>Coordinator Email Address (Yours):</td>
      <td><input name="contactemail" type="text" value="" size="50" maxlength="64"></td>
   </tr>
   <tr>
      <td align="center" colspan="2"><input type="submit" value="This is exciting!  When do I get my password?"></td>
   </tr>
   </table>
   </form>
</div>
</body>
</html>
