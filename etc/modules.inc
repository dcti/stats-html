<?php
  // $Id: modules.inc,v 1.1 1999/07/11 19:21:15 nugget Exp $

  function err_Check_Query_Results($result) {
    if ($result == "") {
	if ($debug=="yes") {
	  include "templates/debug.inc";
	} else {
	  include "templates/error.inc";
	}
	exit();
     }
  }

  function participant_listas($listmode, $email, $id, $name) {
    if ($listmode == 0 || $listmode == 8 || $listmode == 9)
	$listas="$email";
    else if ($listmode == 1)
	$listas="Participant #".number_style_convert($id);
    else if ($listmode == 2)
	if ($name == "")
		$listas="Participant #".number_style_convert($id);
	else
		$listas="$name";
    else
	$listas="Record error for #".number_style_convert($id)."!";
    return $listas;
  }

  function sybase_date_format_long($arg_1) {
    $parts = explode(" ",ltrim($arg_1));
    if($parts[0]=="") {
      $formatted_date="&nbsp;";
    } else {
      if($parts[1]=="") {
        $formatted_date = "$parts[2]-$parts[0]-$parts[3]";
      } else {  
        $formatted_date = "$parts[1]-$parts[0]-$parts[2]";
      }
    }
    return trim($formatted_date);
  }

  function sybase_date_format_short($arg_1) {
    $parts = explode(" ",ltrim($arg_1));
    if($parts[0]=="") {
      $formatted_date="&nbsp;";
    } else {
      if($parts[1]=="") {
        $formatted_date = "$parts[2]-$parts[0]-$parts[3]";
      } else {  
        $formatted_date = "$parts[1]-$parts[0]-$parts[2]";
      }
    }
    return trim($formatted_date);
  }

  function sybase_time_format($arg_1) {
    $timestr = substr($arg_1,12);
    $ap = strtolower(substr($arg_1,24,1));
    $parts = explode(":",$timestr);
    if($parts[0]=="") {
      $formatted_time="an unknown time";
    } else {
      $formatted_time = "$parts[0]:$parts[1]$ap";
    }
    return trim($formatted_time);
  }

  function number_style_convert($number, $decimal_places = 0) {
    $number_style=US;
    if ( $number_style == "US" ) {
      $number = number_format($number, $decimal_places, ".", ",");
     }
    if ( $number_style == "EU" ) {
      $number = number_format($number, $decimal_places, ".", ".");
    }
   return $number;
  }

  function send_mail($to_address, $from_address, $subject, $message) {
    $path_to_sendmail = "/usr/lib/sendmail";
    $fp = popen("$path_to_sendmail -t -f $from_address", "w");
    $num = fputs($fp, "To: $to_address\n");
    $num += fputs($fp, "From: $from_address\n");
    $num += fputs($fp, "X-Errors-To: passmail@distributed.net\n");
    $num += fputs($fp, "X-Distributed: Join the cows!  http://www.distributed.net/ ]:8)\n");
    $num += fputs($fp, "X-Mailer: distributed.net stats password mailer 1.0\n");
    $num += fputs($fp, "Subject: $subject\n\n");
    $num += fputs($fp, "$message");
    pclose($fp);
    if ($num > 0) {
      return 1;
    } else {
     return 0;
    }
  }

?>
