<?php

# $Id: money.php,v 1.2 2002/10/31 17:26:26 nugget Exp $

 include "../etc/config.inc";
 include "../etc/modules.inc";
 include "../etc/project.inc";

 if($project_id == 205) {
   $project_id = 5;
 }

 $filename = "money-$project_id.inc";
 $title = "Disposition of Prize Money";

 include "../templates/header.inc";
 print "   </tr>\n  </table>\n";

 if(file_exists($filename)) {
   include "$filename";
 } else {

?>
  <center>
   <p>
    <font  size="+2">
     There is no prize money corresponding to this project.
    </font>
   </p>
  </center>

<?php
 }

 include "../templates/footer.inc";

?>
