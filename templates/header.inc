<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
        "http://www.w3.org/TR/html4/strict.dtd">

<html>
<!-- vi: ts=2 sw=2 tw=120 syntax=php
-->
 <head>
  <title>stats.distributed.net - <?=$proj_name?> <?=$title?></title>
  <meta http-equiv="content-type" content="text/html;charset=ISO-8859-1">
  <style type="text/css"><!--
   .row1 {border-color:#AAAAAA;background-color:#dddddd }
   .row2 {border-color:#AAAAAA;background-color:#ffffdd }
   .row3 {border-color:#AAAAAA;background-color:#ffffff }
   .row1 a, .row2 a, .row3 a { color: #CC0000; }
   .thead {border-color:#AAAAAA;font-size:medium;font-weight:bold;color:#FFFFFF;background-color:#222222;}
   .tfoot {border-color:#AAAAAA;color:#FFFFFF;background-color:#222222;}
   .tcontent {border-color:#000000;color:#000000;font-size:small;}
   .thead a, .thead a:visited, .thead a:hover {font-weight:bold;color:#AAAAAA;background-color:#222222;}
   .tfoot a, .tfoot a:visited, .tfoot a:hover {font-weight:bold;color:#AAAAAA;background-color:#222222;}
   .tborder {border-color:#000000;border-width:1px;}
   .phead {text-align:center;font-family:verdana,helvetica,arial;font-size:x-large;}
   .phead2 {font-family:verdana,helvetica,arial;font-size:medium;font-weight:bold;}
   .htitle {font-size: x-large; color: #ffff77; background-color: #777777;}
   .hcontent {font-size: small; color: #aaaaaa; background-color: #000000;}
   .hcontent a, .hcontent a:visited, .hcontent a:hover {font-size: small; color: #FFFFFF; background-color: #000000;text-decoration: underline}
   .hsubtitle {font-size: medium; color: #0077aa;}
   .lastupdate {font-size: xx-small; color: #ffff77; background-color: #777777;}
   body { font-family:verdana,helvetica,arial;font-size:small;color:#000000;background-color:#FFFFFF; }
   body a, body a:visited, body a:hover { color: #777777 }
   img { border: 0px; }
  --></style>
 </head>
 <body>
  <table width="100%" border="0" cellpadding="1" cellspacing="0">
  <colgroup><col><col width="33%"><col width="33%"><col width="33%"></colgroup>
   <tr>
    <td colspan="4" align="center" class="htitle">
      <? if(isset($proj_name)) { echo $proj_name." / ";}?>  <?=$title?>
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
        <a href="/projects.php?project_id=<?=$project_id?>">Overall Project Stats</a><br>
        Keyrate History<br>
        Participation History<br>
        <a href="/misc/platformlist.php?project_id=<?=$project_id?>&amp;view=tco">CPU/OS Distribution</a><br>
        <a href="/misc/countries.php?project_id=<?=$project_id?>">Participating Countries</a><br>
        <?
        if ( $gproj->getprize() <> 0 ) {
          ?>
          <a href="/misc/money.php?project_id=<?=$project_id?>">Distribution of Prize Money</a><br>
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
        <a href="/participant/plist.php?project_id=<?=$project_id?>&amp;low=1&amp;limit=100">Top 100 Participants Overall</a><br>
        <a href="/participant/plist.php?project_id=<?=$project_id?>&amp;low=1&amp;limit=100&amp;source=y">Top 100 Participants Yesterday</a><br>
          <input style="margin-top: 5px;" type="text" name="st" size="16" maxlength="42">
          <input style="margin-top: 5px;" type="submit" value="Search"><br>
        <a href="/participant/pedit.php">Edit your Information</a><br>
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
        <a href="/team/tlist.php?project_id=<?=$project_id?>&amp;low=1&amp;limit=100">Top 100 Teams Overall</a><br>
        <a href="/team/tlist.php?project_id=<?=$project_id?>&amp;low=1&amp;limit=100&amp;source=y">Top 100 Teams Yesterday</a><br>
          <input style="margin-top: 5px;" type="text" name="st" size="16" maxlength="32">
          <input style="margin-top: 5px;" type="submit" value="Search"><br>
        <a href="/team/newteam1.php">Create a new Team</a><br>
        </div>
        </form>
    </td>
   </tr>
<? if(isset($lastupdate)) { ?>
   <tr>
    <td colspan="4" align="center" class="lastupdate">
<? if($lastupdate<>"") { ?>
      Data shown reflects all blocks received as of <?=$lastupdate?> at 23:59 UTC
<? } else { ?>
      Update currently in progress...
<? } ?>
    </td>
   </tr>
<? } ?>
  </table>
