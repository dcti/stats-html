<html>
<head>
<meta http-equiv="content-type" content="text/html;charset=ISO-8859-1">
<link rel="stylesheet" type="text/css" href="http://stats.distributed.net/css/default.css" />
<title>Stats Administration</title>
</head>
<body>
<table width="100%" border="0" cellpadding="1" cellspacing="0">
  <colgroup><col><col width="33%"><col width="33%"><col width="33%"></colgroup>
   <tr>
    <td colspan="4" align="center" class="htitle">Stats Administration</td>
   </tr>
   <tr valign="top">
    <td align="center" valign="top" class="hcontent">
     <a href="/"><img src="http://stats.distributed.net/images/dcti128.jpg" alt="stats.distributed.net" height="96" width="128"></a>
    </td>
    <td class="hcontent">
      <span class="hsubtitle">General</span><br>
        Apache Configuration<br>
	Apache Status<br>
	PHP Configuration<br>
	<a href="/secure/project_logs.php?project_id=8">RC5-72 Log Tail</a><br>
	<a href="/secure/project_logs.php?project_id=27">OGR-NG Log Tail</a><br>
    </td>
    <td class="hcontent">
        <form method="GET" name="par-search" id="par-search" action="psearch.php">
        <div>
        <span class="hsubtitle">Participant Search</span><br>
        <input style="margin-top: 5px;" type="text" name="st" size="16" maxlength="42">
        <input style="margin-top: 5px;" type="submit" value="Search"><br>
        </div>
        </form>
    </td>
    <td class="hcontent">
        <form id="team-search" name="team-search" method="GET" action="tsearch.php">
	<div>
        <span class="hsubtitle">Team Search</span><br>
          <input style="margin-top: 5px;" type="text" name="st" size="16" maxlength="32">
          <input style="margin-top: 5px;" type="submit" value="Search"><br>
        </div>
        </form>
    </td>
   </tr>
   <tr>
    <td colspan="4" align="center" class="lastupdate">
      You are logged in as: <?php echo($_SERVER['REMOTE_USER']); ?>
    </td>
   </tr>
 </table>
