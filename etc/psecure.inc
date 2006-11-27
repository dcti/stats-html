<?php
  // $Id: psecure.inc,v 1.26 2006/11/27 11:55:10 fiddles Exp $

include_once "participant.php";

$id = $_REQUEST['id'];
$pass = $_REQUEST['pass'];

function authenticate() {
   	Header("WWW-authenticate: basic realm=\"Participant Password\"");
   	Header("HTTP/1.0 401 Unauthorized");
   	authfail("pbadpass","NULL","NULL","NULL");
}

function authfail($why,$tfrom,$tid,$tpass,$desc = "") {
  	if(!($tfrom == "NULL")) {
   		# This might be a bit verbose.  Disabled normally.
   		error_log("$why $tfrom [$tid/$tpass] $desc",0);
   	}
   	
   	if($tfrom == "cookie") {
		# If the cookie failed, that means it's stale.  Let's clear it
      	# so the luser has a chance at logging in manually...
      	SetCookie("sbid","",time()+3600*24*365,"/");
      	SetCookie("sbpass","",time()+3600*24*365,"/");
       	authenticate();
   	}
    	
   	if($tfrom == "auth" and $why == "pbadpass") {
   		# The user has actually keyed in their ID, let's ask again.
   		authenticate();
   	}
    
	trigger_error("You have entered either an invalid id, or an incorrect password. As usual, feel free to contact help@distributed.net if you require help",E_USER_ERROR);
   	exit;
}

if(isset($id) && isset($pass) && $pass != "") {
   if($debug > 0) trigger_error("Authenticating through URL.",E_USER_NOTICE);
    # ID and PASS provided in url, use these over all other options
   	$test_id = $id;
   	$test_pass = $pass;
   	$test_from = "url";
} else {
    if(isset($_COOKIE['sbid'])) {
      if($debug > 0) trigger_error("Authenticating through Cookie.",E_USER_NOTICE);
   		# We have a cookie set, use this
		$test_id = $_COOKIE['sbid'];
    	$test_pass = $_COOKIE['sbpass'];
    	$test_from = "cookie";
    } else {
   		# No choice but to ask...
   		if(!isset($_SERVER['PHP_AUTH_USER'])) {
       		authenticate();
   		} else {
                if($debug > 0) trigger_error("Authenticating through PHP headers.",E_USER_NOTICE);
      		$test_id = $_SERVER['PHP_AUTH_USER'];
       		$test_pass = $_SERVER['PHP_AUTH_PW'];
       		$test_from = "auth";
   		}
   	}
}
if($debug > 0) print "<!-- Username: $test_id, Password: $test_pass -->\n";

// @TODO - pass null or gproj here?
$gpart = new Participant($gdb, $gproj, $test_id);
if ( !$gpart ) {
	authfail("pdbbadobject",$test_from,$test_id,$test_pass);
}

if ( (is_numeric($test_id) && $test_id != $gpart->get_id()) || (!is_numeric($test_id) && strtolower($test_id) != $gpart->get_email()) ) {
	authfail("pbaduser",$test_from,$test_id,$test_pass);
}

if ( ! $gpart->check_password($test_pass) ) {
	authfail("pbadpass",$test_from,$test_id,$test_pass);
}

// The authentication passed, so let's set the page variables
$id = $test_id;
$pass = $test_pass;
?>

