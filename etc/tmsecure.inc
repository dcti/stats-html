<?php
  // $Id: tmsecure.inc,v 1.6 2003/09/26 03:09:12 thejet Exp $

include_once "team.php";

function authenticate() {
   	Header("WWW-authenticate: basic realm=\"Team Password\"");
   	Header("HTTP/1.0 401 Unauthorized");
   	authfail("tmbadpass","NULL","NULL","NULL");
}

function authfail($why,$tfrom,$tid,$tpass,$desc = "") {
  	if(!($tfrom == "NULL")) {
   		# This might be a bit verbose.  Disabled normally.
   		error_log("$why $tfrom [$tid/$tpass] $desc",0);
   	}
   	
   	if($tfrom == "cookie") {
		# If the cookie failed, that means it's stale.  Let's clear it
      	        # so the luser has a chance at logging in manually...
      	        SetCookie("sbteam","",time()+3600*24*365,"/");
      	        SetCookie("sbtpass","",time()+3600*24*365,"/");
       	        authenticate();
   	}
    	
   	if($tfrom == "auth" and $why == "tmbadpass") {
   		# The user has actually keyed in their ID, let's ask again.
   		authenticate();
   	}
    
   	// @todo - header?
   	include "../templates/$why.inc";
   	print 'datestamp: ' . date("M d Y H:i:s");
   	exit;
}

if(isset($tm) && isset($pass) && $pass != "") {
    # ID and PASS provided in url, use these over all other options
   	$test_id = $tm;
   	$test_pass = $pass;
   	$test_from = "url";
} else {
    if(isset($_COOKIE['sbteam'])) {
   		# We have a cookie set, use this
		$test_id = $_COOKIE['sbteam'];
    	$test_pass = $_COOKIE['sbtpass'];
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
$gteam = new Team($gdb, $gproj, $tm);
if ( !$gteam ) {
	authfail("tmbadobject",$test_from,$test_id,$test_pass);
}

if ( $tm != $gteam->get_id() ) {
	authfail("tmbadteam",$test_from,$test_id,$test_pass);
}

if ( ! $gteam->check_password($test_pass) ) {
	authfail("tmbadpass",$test_from,$test_id,$test_pass);
}

?>

