<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
        "http://www.w3.org/TR/REC-html40/loose.dtd">
<html>
 <head>
  <title>stats.distributed.net - <?=$project?> <?=$title?></title>
 </head>
 <body bgcolor=<?=$bgcolor?> text=<?=$text?> link=<?=$link?> vlink=<?=$link?>>
  <table width="100%" border="0" cellpadding="1" cellspacing="0">
   <tr>
    <td colspan="4" align="center" bgcolor="#777777">
     <font <?=$fontt?> color=<?=$colorc?>>
      <?=$project?> / <?=$title?>
     </font>
    </td>
   </tr>
   <tr bgcolor="#000000" valign="top">
    <td align="center" valign="top">
     <a href="/"><img src="/images/dcti128.jpg" alt="stats.distributed.net" height=96 width=128 border="0"></a>
    </td>
    <td width="33%">
      <br>
      <font <?=$fonth?> color=<?=$colora?>>
       Project Stats
      </font>
      <br>
      <font <?=$fontd?> size="-1" color=<?=$colorb?>>
       <a href="/<?=strtolower($project)?>/">Overall Project Stats</a><br>
       Keyrate History<br>
       Participation History<br>
       <a href="platformlist.php?project_id=<?=$project_id?>&view=tco">CPU/OS Distribution</a><br>
       <a href="countries.html">Participating Countries</a><br>
       <a href="/pedit.php">Edit your Information</a><br>
       <a href="/newteam1.php">Create a new Team</a><br>
      </font>
    </td>
    <td width="33%">
      <br>
      <font <?=$fonth?> color=<?=$colora?>>
       Participant Stats
      </font>
      <br>
      <font <?=$fontd?> size="-1" color=<?=$colorb?>>
       <a href="plist.php?project_id=<?=$project_id?>&low=1&limit=100">Top 100 Participants Overall</a><br>
       <a href="plist.php?project_id=<?=$project_id?>&low=1&limit=100&source=y">Top 100 Participants Yesterday</a><br>
       <form action="psearch.php?project_id=<?=$project_id?>"><input type="text" name="st" size="16" maxlength="42">
             <input type="submit" value="Search"></form>
      </font>
    </td>
    <td width="33%">
      <br>
      <font <?=$fonth?> color=<?=$colora?>>
       Team Stats
      </font>
      <br>
      <font <?=$fontd?> size="-1" color=<?=$colorb?>>
       <a href="tlist.php?project_id=<?=$project_id?>&low=1&limit=100">Top 100 Teams Overall</a><br>
       <a href="tlist.php?project_id=<?=$project_id?>&low=1&limit=100&source=y">Top 100 Teams Yesterday</a><br>
       <form action="tsearch.php?project_id=<?=$project_id?>"><input type="text" name="st" size="16" maxlength="32">
             <input type="submit" value="Search"></form>
      </font>
    </td>
   </tr>
   <tr>
    <td colspan="4" align="center" bgcolor="#777777">
     <font face="Arial,Helvetica" color=<?=$colorc?> size="-2">
<? if($lastupdate<>"") { ?>
      Data shown reflects all blocks received as of <?=$lastupdate?> at 23:59 UTC
<? } else { ?>
      Update currently in progress...
<? } ?>
     </font>
    </td>
   </tr>
  </table>
