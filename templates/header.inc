<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
        "http://www.w3.org/TR/REC-html40/loose.dtd">

<html>
 <head>
  <title>stats.distributed.net - <?=$proj_name?> <?=$title?></title>
  <style type="text/css"><!--
   .row1 {background-color:#dddddd }
   .row2 {background-color:#ffffdd }
   .row3 {background-color:#ffffff }
    TH     {font-family:verdana,helvetica,arial;
	    color:white;
	 }
  --></style>
 </head>
 <body bgcolor=<?=$bgcolor?> text=<?=$text?> link=<?=$link?> vlink=<?=$link?>>
  <table width="100%" border="0" cellpadding="1" cellspacing="0">
   <tr>
    <td colspan="4" align="center" bgcolor="#777777">
     <font <?=$fontt?> color=<?=$colorc?>>
      <?=$proj_name?> / <?=$title?>
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
       <a href="/projects.php?project_id=<?=$project_id?>">Overall Project Stats</a><br>
       Keyrate History<br>
       Participation History<br>
       <a href="/misc/platformlist.php?project_id=<?=$project_id?>&view=tco">CPU/OS Distribution</a><br>
       <a href="/misc/countries.php?project_id=<?=$project_id?>">Participating Countries</a><br>
       <a href="/participant/pedit.php">Edit your Information</a><br>
       <a href="/team/newteam1.php">Create a new Team</a><br>
      </font>
    </td>
    <td width="33%">
      <br>
      <font <?=$fonth?> color=<?=$colora?>>
       Participant Stats
      </font>
      <br>
      <font <?=$fontd?> size="-1" color=<?=$colorb?>>
       <a href="/participant/plist.php?project_id=<?=$project_id?>&low=1&limit=100">Top 100 Participants Overall</a><br>
       <a href="/participant/plist.php?project_id=<?=$project_id?>&low=1&limit=100&source=y">Top 100 Participants Yesterday</a><br>
       <form action="/participant/psearch.php"><input type="hidden" name="project_id" value="<?=$project_id?>"><input type="text" name="st" size="16" maxlength="42">
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
       <a href="/team/tlist.php?project_id=<?=$project_id?>&low=1&limit=100">Top 100 Teams Overall</a><br>
       <a href="/team/tlist.php?project_id=<?=$project_id?>&low=1&limit=100&source=y">Top 100 Teams Yesterday</a><br>
       <form action="/team/tsearch.php"><input type="hidden" name="project_id" value="<?=$project_id?>"><input type="text" name="st" size="16" maxlength="32">
             <input type="submit" value="Search"></form>
      </font>
    </td>
   </tr>
<? if(isset($lastupdate)) { ?>
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
<? } ?>
