<?
// vi: ts=2 sw=2 tw=120 syntax=php
// $Id: project.inc,v 1.27 2003/09/01 16:22:21 thejet Exp $
include "limit.inc";
include "db_pgsql.php";
include "project.php";

$debug = 0;
if ($_SERVER["SERVER_NAME"] != "stats.distributed.net") {
    if (isset($_GET['debug'])) {
        $debug = (int)$_REQUEST['debug'];
    } 
    if($debug >= 1)
    {
      dl('/xdebug.so');
      if($debug >= 2) {
        xdebug_start_trace();
      }
      xdebug_start_profiling();
      include "timing.inc";
      timer_start();
    }
} 

$myname = $_SERVER['PHP_SELF'];

if (isset($_GET['project_id'])) {
    $project_id = (int)$_GET['project_id'];
} else {
    $project_id = 25;
} 

if (isset($_GET['source'])) {
    $temp = $_GET['source'];
    switch ($temp) {
    case 'o':
        $source = 'o';
        break;
    case 'y':
        $source = 'y';
        break;
    default:
        $source = 'o';
        break;
    } 
} else {
    $source = 'o';
} 

if (isset($_REQUEST['id'])) {
    $id = (int)$_REQUEST['id'];
} else {
    $id = 1;
} 

if (isset($_REQUEST['st'])) {
    $st = $_REQUEST['st'];
} else {
    $st = "";
} 

if (isset($_GET['team'])) {
    $tm = (int)$_GET['team'];
} else {
    $tm = 1;
} 

$gdb = new DB('dbname=stats');
$gproj = new Project($gdb, $project_id);

function last_update($type)
{
    global $lastupdate, $project_id, $gdb;

    switch ($type) {
    case 'e':
        $qs = "select to_char(max(LAST_DATE), 'dd-Mon-YYYY') as lastupdate from Email_Rank_Last_Update where PROJECT_ID = $project_id";
        break;
    case 'ec':
        $qs = "select to_char(max(LAST_DATE), 'dd-Mon-YYYY') as lastupdate from Email_Contrib_Last_Update where PROJECT_ID = $project_id";
        break;
    case 't':
        $qs = "SELECT to_char(MAX(last_date), 'dd-Mon-YYYY') AS lastupdate FROM team_rank WHERE project_id = $project_id";
        break;
    case 'm':
        $qs = "select to_char(max(LAST_DATE), 'dd-Mon-YYYY') as lastupdate from Email_Contrib_Last_Update where PROJECT_ID = $project_id";
        break;
    case 'pc':
        $qs = "select to_char(max(LAST_DATE), 'dd-Mon-YYYY') as lastupdate from Platform_Contrib_Last_Update where project_id = $project_id";
        break;
    case 'i';
        return $lastupdate;
    } 
    $par = $gdb -> query_first ($qs);
    if($par) {
        $lastupdate = $par -> lastupdate;
    } else {
        $lastupdate = "some day, not too long ago";
    } 
    return $lastupdate;
} 

function display_last_update($type = null)
{
    global $lastupdate;
    global $colorc;
    if(isset($type)) {
        $lastupdate = last_update($type);
    } 

    ?>
   <tr>
     <td colspan="4" align="center" class="lastupdate"> 
<? if(isset($type)) {

        ?>
  <? if($lastupdate <> "") {

            ?>
        Data shown reflects all blocks received as of <?=$lastupdate?> at 23:59 UTC
  <? } else {

            ?>
    Update currently in progress...
  <? } 

        ?>
<? } else {

        ?>
  &nbsp;
<? } 

    ?>
      </font>
    </td>
  </tr>
</table>
<?
} 

?>
