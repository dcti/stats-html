<?php

# $Id: money.php,v 1.1 2002/10/31 17:14:19 nugget Exp $

 include "../etc/config.inc";
 include "../etc/modules.inc";
 include "../etc/project.inc";

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
