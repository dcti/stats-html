<?
 # $Id: psearch.php,v 1.1 2005/01/05 10:29:03 fiddles Exp $

 // Variables Passed in url:
 //   st == Search Term

 include "../etc/global.inc";
 include "../etc/modules.inc";
 include "../etc/project.inc";
 include "../etc/participant.php";

 $title = "Participant Search: [".safe_display($st)."]";

 $lastupdate = last_update('e');

if (is_numeric($st)) {
  $onepp = new Participant($gdb, $gproj, (int)$st);
  if ($onepp->get_id() != (int)$st) {
    $result = array();
  } else {
    $result = array($onepp);
  }
} else {
  if (strlen($st) < 3) {
    include "etc/header.inc";
      ?>
      <table align="center" width="400" border="0"><tr><td>
         <h2>There was an error processing your request</h2>
         <p>Search Text must be at least 3 characters</p>
         </p></td></tr></table>
         <?
         include "etc/footer.inc";
    exit;
  }

  // Execute the procedure to get the results
  $result = Participant::get_search_list_no_stats_all($st, 50, $gdb, $gproj);
}
$rows = count($result);

 if($rows == 1) {
	# Only one hit, let's jump straight to psummary
	$id = (int) $result[0]->id;
	header("Location: pedit.php?id=$id");
	exit;
 }

 include "etc/header.inc";

 ?> 
  <div style="text-align: center;">
     <br />
     <table border="1" cellspacing="0" style="margin:auto" width="90%">
     <tr>
       <th class="thead">Participant ID</th>
       <th class="thead">Email Address</th>
      </tr>
 <?

 if($rows <= 0)
 {
   echo "<tr><td colspan=\"2\">No Matching Records Found</td></tr>\n";
 }
 for ($i = 0; $i<$rows; $i++) {
   $ROWparticipant = $result[$i];
   $id = (int) $ROWparticipant->id;
	?>
	<tr class="<?=row_background_color($i)?>">
         <td align="left"><a href="pedit.php?id=<?=$id?>"><?=$id?></a></td>
         <td align="left"><a href="pedit.php?id=<?=$id?>"><?=safe_display($ROWparticipant->email)?></a></td>
        </tr>
        <?
 }
?>
	</table>

	<p>
	 All participants were searched, regardless of listmode, only the first 50 results have been returned.
	</p>

<?include "etc/footer.inc";?>
