<?
// vi: ts=2 sw=2 tw=120 syntax=php
// $Id: project.inc,v 1.40 2004/07/19 06:12:23 jlawson Exp $

include "limit.inc";
include "db_pgsql.php";
include "project.php";

ob_start();

$myname = $_SERVER['PHP_SELF'];

if (isset($_REQUEST['project_id'])) {
    $project_id = (int)$_REQUEST['project_id'];
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

if (isset($_REQUEST['view'])) {
    $view = $_REQUEST['view'];
} else {
    $view = "c";
}

if (isset($_REQUEST['pass'])) {
    $pass = $_REQUEST['pass'];
} else {
    $pass = "";
}

if (isset($_REQUEST['team'])) {
    $tm = (int)$_REQUEST['team'];
} else {
    $tm = 1;
}

$gdb = new DB($conn_string);
$gproj = new Project($gdb, $project_id);

function last_update($type)
{
    global $lastupdate, $project_id, $gdb;

    switch ($type) {
    case 'e':
        $qs = "SELECT TO_CHAR(MAX(last_date), 'dd-Mon-YYYY') AS lastupdate FROM Email_Rank_Last_Update WHERE project_id = $1";
        break;
    case 'ec':
        $qs = "SELECT to_char(MAX(last_date), 'dd-Mon-YYYY') AS lastupdate from Email_Contrib_Last_Update WHERE project_id = $1";
        break;
    case 't':
        $qs = "SELECT to_char(MAX(last_date), 'dd-Mon-YYYY') AS lastupdate FROM team_rank WHERE project_id = $1";
        break;
    case 'm':
        $qs = "SELECT to_char(MAX(last_date), 'dd-Mon-YYYY') AS lastupdate FROM Email_Contrib_Last_Update WHERE project_id = $1";
        break;
    case 'pc':
        $qs = "SELECT to_char(MAX(last_date), 'dd-Mon-YYYY') AS lastupdate FROM Platform_Contrib_Last_Update WHERE project_id = $1";
        break;
    case 'i';
        return $lastupdate;
    }
    $par = $gdb -> query_bound_first ($qs, array( (int)$project_id ));
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
    if(isset($type)) {
        $lastupdate = last_update($type);
    }

    ?>
    <tr>
        <td colspan="4" align="center" class="lastupdate">
        <? if(isset($type)) { ?>
            <? if($lastupdate <> "") { ?>
                Data shown reflects all blocks received as of <?=$lastupdate?> at 23:59 UTC
            <? } else { ?>
                Update currently in progress...
            <? } ?>
        <? } else { ?>
            &nbsp;
        <? } ?>
        </td>
    </tr>
</table>
<?
}
?>
