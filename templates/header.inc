<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
        "http://www.w3.org/TR/html4/strict.dtd">

<html>
<!-- vi: ts=2 sw=2 tw=120 syntax=php
-->
 <head>
  <title>stats.distributed.net - <?=$proj_name?> <?=$title?></title>
  <meta http-equiv="content-type" content="text/html;charset=ISO-8859-1">
  <style type="text/css"><!--
   .row1 {background-color:#dddddd }
   .row2 {background-color:#ffffdd }
   .row3 {background-color:#ffffff }
   .thead {font-family:verdana,helvetica,arial;font-weight:bold;color:#FFFFFF;background-color:#222222;}
   .tfoot {font-family:verdana,helvetica,arial;color:#FFFFFF;background-color:#222222;}
   .tcontent {font-family:verdana,helvetica,arial;color:#000000;font-size:small;}
   .phead {font-family:verdana,helvetica,arial;font-size:x-large;}
   .phead2 {font-family:verdana,helvetica,arial;font-size:medium;font-weight:bold;}
   body { font-family:verdana,helvetica,arial;font-size:small;color:#000000;background-color:#FFFFFF; }
  --></style>
 </head>
 <body>
  <table width="100%" border="0" cellpadding="1" cellspacing="0">
  <colgroup><col><col width="33%"><col width="33%"><col width="33%"></colgroup>
   <tr>
    <td colspan="4" align="center">
      <? if(isset($proj_name)) { echo $proj_name." / ";}?>  <?=$title?>
    </td>
   </tr>
   <tr valign="top">
    <td align="center" valign="top">
     <a href="/"><img src="/images/dcti128.jpg" alt="stats.distributed.net" height="96" width="128" border="0"></a>
    </td>
    <td>
      <br>
        Project Stats
      <br>
        <a href="/projects.php?project_id=<?=$project_id?>">Overall Project Stats</a><br>
        Keyrate History<br>
        Participation History<br>
        <a href="/misc/platformlist.php?project_id=<?=$project_id?>&amp;view=tco">CPU/OS Distribution</a><br>
        <a href="/misc/countries.php?project_id=<?=$project_id?>">Participating Countries</a><br>
        <?
        if ( $proj_prize <> 0 ) {
          ?>
          <a href="/misc/money.php?project_id=<?=$project_id?>">Distribution of Prize Money</a><br>
          <?
        }
        ?>
    </td>
    <td>
      <br>
       Participant Stats
      <br>
        <a href="/participant/plist.php?project_id=<?=$project_id?>&amp;low=1&amp;limit=100">Top 100 Participants Overall</a><br>
        <a href="/participant/plist.php?project_id=<?=$project_id?>&amp;low=1&amp;limit=100&amp;source=y">Top 100 Participants Yesterday</a><br>
        <form method="GET" name="par-search" id="par-search" action="/participant/psearch.php">
          <div><input type="hidden" name="project_id" value="<?=$project_id?>">
          <input type="text" name="st" size="16" maxlength="42">
          <input type="submit" value="Search"></div>
        </form>
        <a href="/participant/pedit.php">Edit your Information</a><br>
    </td>
    <td>
      <br>
        Team Stats
      <br>
        <a href="/team/tlist.php?project_id=<?=$project_id?>&amp;low=1&amp;limit=100">Top 100 Teams Overall</a><br>
        <a href="/team/tlist.php?project_id=<?=$project_id?>&amp;low=1&amp;limit=100&amp;source=y">Top 100 Teams Yesterday</a><br>
        <form id="team-search" name="team-search" method="GET" action="/team/tsearch.php">
          <div><input type="hidden" name="project_id" value="<?=$project_id?>">
          <input type="text" name="st" size="16" maxlength="32">
          <input type="submit" value="Search"></div>
        </form>
        <a href="/team/newteam1.php">Create a new Team</a><br>
    </td>
   </tr>
<? if(isset($lastupdate)) { ?>
   <tr>
    <td colspan="4" align="center">
<? if($lastupdate<>"") { ?>
      Data shown reflects all blocks received as of <?=$lastupdate?> at 23:59 UTC
<? } else { ?>
      Update currently in progress...
<? } ?>
    </td>
   </tr>
  </table>
<? } ?>
