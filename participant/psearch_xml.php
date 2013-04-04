<? 
// Variables Passed in url:
// st == Search term (required)
//   If numeric (1+ digits), match exact id
//   If alphanumeric (3+ chars), match partial email
// limit == How many results to return (optional, 1+ digits, default = 50)
// Author: Simon Trigona

include "../etc/global.inc";
$random_stats = 0;
include "../etc/modules.inc";
include "../etc/project.inc";
include "../etc/participant.php";


if (isset($_GET['limit'])) {
	$limit = (int)$_GET['limit'];
} else {
	$limit = 50;
}

// output the XML header
header("Content-type: text/xml", true);
print("<"."?xml version=\"1.0\" encoding=\"ISO-8859-1\"?".">\n");
?>
<!-- WARNING: This code is experimental and the schema is subject to change at any time -->
<!--
API Documentation:
// Variables Passed in url:
// st == Search term (required)
//   If numeric (1+ digits), match exact id
//   If alphanumeric (3+ chars), match partial email
// limit == How many results to return (optional, 1+ digits, default = 50)
// Error Codes:
// 1 - Invalid search term, must be at least 3 characters
-->
<?php
if (is_numeric($st)) {
  $onepp = new Participant($gdb, $gproj, (int)$st);
  if ($onepp->get_id() != (int)$st) {
    $result = array();
  } else {
    $result = array($onepp);
  }
} else {
  if (strlen($st) < 3) {
      ?>
      <error code="1">There was an error processing your request. Search Text must be at least 3 characters</error>
      <?
    exit;
  }

  // Execute the procedure to get the results
  $result = Participant::get_search_list_no_stats($st, $limit, $gdb);
}

$rows = count($result);
// Generate XML
print("<search-result>\n");
for ($i = 0; $i < $rows; $i++) {
	$ROWparticipant = $result[$i];
	$id = (int) $ROWparticipant->id;
	$name = safe_display($ROWparticipant->display_name);
?>
        <participant-summary id="<?php echo $id; ?>">
        	<name><![CDATA[<?php echo $name; ?>]]></name>
        </participant-summary>
<?php
}
print("</search-result>");
?>
