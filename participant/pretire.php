<?php

// $Id: pretire.php,v 1.30 2007/11/10 02:57:58 snikkel Exp $

include "../etc/global.inc";
include "../etc/project.inc";
include "../etc/psecure.inc";
include "../etc/team.php";

$title = "Retiring " . $gpart->get_email();

include "../templates/header.inc";
display_last_update('t');

if ($readonly_pretire == 1) {
    include "../templates/readonly.inc";
    print "<a href=\"/\">I'll go find something else to do then</a>
</body>
</html>";
    exit;
}

if ( !isset($_REQUEST['destid']) && !isset($_REQUEST['ems']) )
{
	?>
	  <h2>You are about to permanently retire the address <?=$gpart->get_email()?></h2>
	  <p>
	   You are about to completely and permanently remove this email from the stats database.
	   All past, current, and future work submitted by this email will be attributed to a new
	   email address.  This procedure is irreversible.  It is permanent.  Once you do this, we
	   cannot restore it.
	  </p>
	  <p>
	   You should <strong>only</strong> be doing this if you will no longer be using the old
	   email address.  This feature has been designed to allow participants to gracefully
	   migrate from one email address to another without losing their longevity or standing
	   in the stats database.
	  </p>
	  <p>
	   In order to retire this email address, you must first pick a destination email address.
	   All past blocks, and any future blocks that are submitted from <?=$gpart->get_email()?> will be
	   treated as if they were submitted by the new email address you are about to choose.
	  </p>
	  <p>
	   Before you can retire this email address, you must have first changed over your clients
	   to your new email address, and you must have flushed blocks from the new address and have
	   those blocks visible in stats.
	  </p>
	  <p>
	   Please enter your new email address below:
	   <br>
	   <form action="pretire.php" method="post">
	    <input type="text" name="ems" size="64" maxlength="64">
	    <input type="hidden" name="id" value="<?=$id?>">
	    <input type="hidden" name="pass" value="<?=$pass?>">
	    <input type="submit" value="Search for this email">
	   </form>
	  </p>
  	<?
} else {
	if (isset($_REQUEST['ems']) && $_REQUEST['ems'] <> "") {
		$ems = $_REQUEST['ems'];
      	$result = Participant::get_search_list_no_stats($ems, 50, $gdb);
      	$rows = count($result);
      	if ($rows == 0)
      	{
	  		trigger_error("No participants were found matching $ems. For more information, http://www.distributed.net/faqs",E_USER_ERROR);
      	} else {
      		?>
	  		<h2>Please choose your new email address</h2>
	  		<p>
	  		Below are all the email addresses in stats that match what you just typed in. Please choose the appropriate email address by clicking on it.
	  		</p>
	  		<p>This <strong>will retire</strong> <?=$gpart->get_email()?>.</p>
	  		<p>Clicking an email below will result in a permanent change to the stats database.</p>
	  		<table border="1">
	  		 <tr bgcolor="#00aaaa">
	  		  <td align="right">ID #</td>
	  		  <td>Email Address</td>
	  		 </tr>
	  		<?
      		for ($i=0;$i<$rows;$i++) {
      			$ROWparticipant = $result[$i];

      	  		$tmpid = 0 + $ROWparticipant->id;
      	  		if ($tmpid != $id) {
      	  			echo '<tr><td align="right">' . $tmpid . '</td>
	  	       		<td><a href="pretire.php?id=' . $id . '&pass=' . $pass . '&destid=' . $tmpid . '">' . $ROWparticipant->email . '</a></td></tr>';
      	  		}
		}
      		echo '</table>';
      	}
	}
    if (isset($_REQUEST['destid']) && $_REQUEST['destid'] <> '' ) {
                $destid = $_REQUEST['destid'];
                if ($retired = $gpart->retire($destid)) {
			$destpart = new Participant($gdb, $gproj, $destid);
			?>
			<h2>Retire Procedure successful</h2>
			<p>
	 		You have successfully retired the email address <?=$gpart->get_email()?>.
			</p>
			<p>
	 		This will take effect during the next stats run.
			</p>
			<p>
			All past blocks, and any future blocks submitted from <?=$gpart->get_email()?> will be allocated to the stats for <?=$destpart->get_email()?> instead.
			</p>
			<p>
	 		All future blocks from this address will be attributed to team <?=$destpart->get_team_id()?>, which is your current team.
	 		If, in the future, you change teams, it will affect all retired emails as you'd expect it to.
			</p>
	 		<p><a href="/">Great, that rocks!</a></p>
	 		<?
	 	} else {
	 		trigger_error("Retired Procedure Failed");
	 	}
    }
}
?>
 </body>
</html>

