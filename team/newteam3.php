<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
        "http://www.w3.org/TR/REC-html40/loose.dtd">
<?
 // $Id: newteam3.php,v 1.5 2002/04/09 22:48:58 jlawson Exp $

 $title = "New Team Creation - Information";

 include "../etc/config.inc";
 include "../etc/modules.inc";
 include "../etc/project.inc";
 include "../templates/header.inc";
?>
  <center>
   <p>
    <i>What else to I need to know?</i>
   </p>
   <table border="0" width="60%">
    <tr>
     <td>
      <ul>
       <li>Below is a very condensed version of the information that you may configure for your team.
           This is the minimum you must provide to register your team.</li>
       <p>
       <li>Your team will NOT be listed until after the next database update takes place.  This could
           be anywhere from one minute to one day from now.  Please be patient.</li>
       <p>
       <li>Oh yeah. It'd be sorta nice if you like, recruited folks and stuff.</li>
       <p>
      </ul>
     </td>
    </tr>
   </table>
   <h2>Team Information</h2>
   <form action="newteam4.php3" method="get">
    <table border="0">
     <tr>
      <td>Full Team Name:</td>
      <td><input name="name" type="text" value="" size="50" maxsize="64"></td>
     </tr>
     <tr>
      <td>Coordinator Name (You):</td>
      <td><input name="contactname" type="text" value="" size="50" maxsize="64"></td>
     </tr>
     <tr>
      <td>Coordinator Email Address (Yours):</td>
      <td><input name="contactemail" type="text" value="" size="50" maxsize="64"></td>
     </tr>
     <tr>
      <td align="center" colspan="2"><input type="submit" value="This is exciting!  When do I get my password?"></td>
     </tr>
    </table>
   </form>
  </center>
 </body>
</html>
