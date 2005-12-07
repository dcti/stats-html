<?
 // $Id: newteam2.php,v 1.12 2005/12/07 05:44:01 fiddles Exp $

 $title = "New Team Creation - Information";

 include "../etc/global.inc";
 include "../etc/modules.inc";
 include "../etc/project.inc";
 unset($proj_name);

 $lastupdate = last_update('t');
 if ($readonly_tmedit != 0) {
     $title = "Team Creation Disabled";
     include "../templates/header.inc";
     include "../templates/readonly.inc";
     print "</body></html>";
     exit;
 }

 include "../templates/header.inc";
?>
<div style="text-align: center">
   <p>
    <i>How does all this work?</i>
   </p>
   <table border="0" width="60%" style="margin:auto;text-align:left">
   <tr>
     <td>
       <ul>
       <li>The first step to creating a team is for you (the team coordinator) to register the
           team with the stats server.  It is at this point that you will enter a brief description
           of the team, set the pointer to your team logo, enter your own personal email, and make
           any team configuration decisions as allowed.  Congratulations!  You are exactly where you
           need to be to do this.<br><br></li>
       <li>After your team is registered, your team will show up on the team selection lists
           based on the team category you have chosen.<br><br></li>
       <li>A password will be given to you for use in maintaining your team and updating your
           configuration.<br><br></li>
       <li>Each of the members of your team will need to have their own password assigned.  They
           will do this by viewing their own personal statistics page
           (<a href="/participant/psummary.php?id=1">example</a>) and choosing the link at the
           bottom of the page.  With this password, they will be able to choose which team they are 
           a member of.<br><br></li>
       <li>As each member "joins" your team, your team will be credited for any blocks they have
           completed which are not credited to any other team.  This means that participants who
           are already members of a team who switch to your team will NOT bring their blocks 
           with them.<br><br></li>
       <li>Again, only "virgin" blocks from a new participant who is not a member of a team will
           be credited to your team.  And, of course, any blocks completed in the future, for so
           long as that member is configured as a member of your team.<br><br></li>
       </ul>
     </td>
   </tr>
   </table>
   <p>
    <a href="newteam3.php">OK, Already!  I've been waiting <i>months</i>.  Let's get on with it!</a>
   </p>
</div>
</body>
</html>
