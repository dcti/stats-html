<?
if (isset($project_id)) {
    $t_project_id = "project_id=$project_id";
} else {
	$t_project_id = "";
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
        "http://www.w3.org/TR/html4/strict.dtd">

<html>
<!-- vi: ts=2 sw=2 tw=120 syntax=php
-->
 <head>
  <title>stats.distributed.net - <? if (isset($gproj)) { echo $gproj->get_name(); }?> <?=$title?></title>
  <meta http-equiv="content-type" content="text/html;charset=ISO-8859-1">
  <link rel="stylesheet" type="text/css" href="/css/default.css" />
 </head>
 <body>
  <table width="100%" border="0" cellpadding="1" cellspacing="0">
  <colgroup><col><col width="33%"><col width="33%"><col width="33%"></colgroup>
   <tr>
    <td colspan="4" align="center" class="htitle">
      <? if(isset($gproj) && $gproj->get_name() != null) {
    echo $gproj->get_name() . " / ";
} else {echo "&nbsp;";}
?>  <? if (isset($title)) { echo $title;}?>
    </td>
   </tr>
   <tr>
    <td colspan="4" align="center" class="hcontent">
      <a href="http://bugs.distributed.net/buglist.cgi?product=Stats&bug_status=UNCONFIRMED&bug_status=NEW&bug_status=ASSIGNED&bug_status=REOPENED" style="color: red; font-weight:bolder">Bug Database</a>&nbsp;&nbsp;&nbsp;&nbsp;
      <a href="http://n0cgi.distributed.net/cgi/dnet-finger.cgi?user=decibel" style="color:red; font-weight:bolder">Current Progress</a>
    </td>
  </tr>
   <tr valign="top">
    <td align="center" valign="top" class="hcontent">
     <a href="/"><img src="/images/dcti128.jpg" alt="stats.distributed.net" height="96" width="128"></a>
    </td>
    <td class="hcontent">
      <br>
        <span class="hsubtitle">Project Stats</span>
      <br>
        <a href="/projects.php?<?=$t_project_id?>">Overall Project Stats</a><br>
        Keyrate History<br>
        Participation History<br>
        <a href="/misc/platformlist.php?<?=$t_project_id?>&amp;view=tco">CPU/OS Distribution</a><br>
        <a href="/misc/countries.php?<?=$t_project_id?>">Participating Countries</a><br>
        <?
if (isset($gpoj) && $gproj->get_prize() <> 0) {

    ?>
          <a href="/misc/money.php?project_id=<?=$t_project_id?>">Distribution of Prize Money</a><br>
          <?
} 

?>
    </td>
    <td class="hcontent">
        <form method="GET" name="par-search" id="par-search" action="/participant/psearch.php">
        <div>
          <input type="hidden" name="project_id" value="<?=$project_id?>">
      <br>
       <span class="hsubtitle">Participant Stats</span>
      <br>
        <a href="/participant/plist.php?<?=$t_project_id?>&amp;low=1&amp;limit=100">Top 100 Participants Overall</a><br>
        <a href="/participant/plist.php?<?=$t_project_id?>&amp;low=1&amp;limit=100&amp;source=y">Top 100 Participants Yesterday</a><br>
          <input style="margin-top: 5px;" type="text" name="st" size="16" maxlength="42">
          <input style="margin-top: 5px;" type="submit" value="Search"><br>
        <a href="/participant/pedit.php?project_id=<?=$gproj->get_id()?>">Edit your Information</a><br>
        </div>
        </form>
    </td>
    <td class="hcontent">
        <form id="team-search" name="team-search" method="GET" action="/team/tsearch.php">
	<div>
          <input type="hidden" name="project_id" value="<?=$project_id?>">
      <br>
        <span class="hsubtitle">Team Stats</span>
      <br>
        <a href="/team/tlist.php?<?=$t_project_id?>&amp;low=1&amp;limit=100">Top 100 Teams Overall</a><br>
        <a href="/team/tlist.php?<?=$t_project_id?>&amp;low=1&amp;limit=100&amp;source=y">Top 100 Teams Yesterday</a><br>
          <input style="margin-top: 5px;" type="text" name="st" size="16" maxlength="32">
          <input style="margin-top: 5px;" type="submit" value="Search"><br>
          <a href="/team/newteam1.php">Create a new Team</a><br>
        </div>
        </form>
    </td>
   </tr>
<? if(isset($lastupdate)) {
    ?>
   <tr>
    <td colspan="4" align="center" class="lastupdate">
<? if($lastupdate <> "") {
        ?>
      Data shown reflects all blocks received as of <?=$lastupdate?> at 23:59 UTC
<? } else {
        ?>
      Update currently in progress...
<? } 
    ?>
    </td>
   </tr>
 </table>
<? } ?> 

