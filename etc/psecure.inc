<?php
  // $Id: psecure.inc,v 1.19 2003/09/23 21:50:59 paul Exp $

include_once "participant.php";

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
    
   	// @todo - header?
   	include "../templates/$why.inc";
   	print 'datestamp: ' . date("M d Y H:i:s");
   	exit;
}

if(isset($id) && isset($pass) && $pass != "") {
    # ID and PASS provided in url, use these over all other options
   	$test_id = $id;
   	$test_pass = $pass;
   	$test_from = "url";
} else {
    if(isset($_COOKIE['sbid'])) {
   		# We have a cookie set, use this
		$test_id = $_COOKIE['sbid'];
    	$test_pass = $_COOKIE['sbpass'];
    	$test_from = "cookie";
    } else {
   		# No choice but to ask...
   		if(!isset($_SERVER['PHP_AUTH_USER'])) {
       		authenticate();
   		} else {
      		$test_id = $_SERVER['PHP_AUTH_USER'];
       		$test_pass = $_SERVER['PHP_AUTH_PW'];
       		$test_from = "auth";
   		}
   	}
}

// @TODO - pass null or gproj here?
$gpart = new Participant($gdb, $gproj, $id);
if ( !$gpart ) {
	authfail("pdbbadobject",$test_from,$test_id,$test_pass);
}

if ( $id != $gpart->get_id() ) {
	authfail("pbaduser",$test_from,$test_id,$test_pass);
}

if ( ! $gpart->check_password($test_pass) ) {
	authfail("pbadpass",$test_from,$test_id,$test_pass);
}

?>

