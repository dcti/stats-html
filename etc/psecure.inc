<?php
  // $Id: psecure.inc,v 1.14 2002/06/05 23:42:58 paul Exp $

  function authenticate() {
    Header("WWW-authenticate: basic realm=\"Participant Password\"");
    Header("HTTP/1.0 401 Unauthorized");
    authfail("pbadpass","NULL","NULL","NULL");
  }

  function authfail($why,$tfrom,$tid,$tpass,$desc = "") {
   global $interface,$username,$password;
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
//    include "../etc/project.inc";
//    include "../templates/header.inc"; 
    include "../templates/$why.inc";
    print 'datestamp: ' . date("M d Y H:i:s");
//    include "../templates/footer.inc";
    exit;
  }

  # Although we don't advertise this, it's possible that someone will
  # Try to use EM=email@domain in the url since the old statsbox used
  # that format.  We'll cover them, just in case.
  if(isset($em)) {
    $id = $em;
  }

  /* changed to and due to $id being set. This needs to be changed pre-production, else a different $id could be passed to $sbid/$sbpass combination */
  if(isset($id) and isset($pass)) {
    # ID and PASS provided in url, use these over all other options

    $test_id = $id;
    $test_pass = $pass;
    $test_from = "url";

  } else {
    if(isset($sbid)) {
      # We have a cookie set, use this

      $test_id = $sbid;
      $test_pass = $sbpass;
      $test_from = "cookie";
    } else {
      # No choice but to ask...

      if(!isset($PHP_AUTH_USER)) {
        authenticate();
      } else {
        $test_id = $PHP_AUTH_USER;
        $test_pass = $PHP_AUTH_PW;
        $test_from = "auth";
      }
    }
  }

  # Let's try to connect to the database...
  if(!sybase_connect($interface, $username, $password)) {
    authfail("dberror",$test_from,$test_id,$test_pass);
    error_log("Unable to connect to sybase",0);
  }

  # Let's see what the luser has provided for us...
  $ta = $test_id;
  $tb = (int) $test_id;
  if("$ta" <> "$tb") {
    # $test_id is not an integer, assume that it is a full email address.
    $qs = "select id, email, password from STATS_Participant where email = '$test_id'";
    $result = sybase_query($qs);
    if (!$result) {
      authfail("dberror",$test_from,$test_id,$test_pass,"No result returned");
    }
    $rows = sybase_num_rows($result);
    if ($rows <> 1) {
      authfail("pbademail",$test_from,$test_id,$test_pass);
    }
    # OK, We've retrieved the id for this email address.
    $par = sybase_fetch_object($result);
    $test_id = (int) $par->id;
  } else {
    # $test_id is an integer, lets see if we can retrive a record for the user
    $qs = "p_participant_all $test_id";
    $result = sybase_query($qs);
    if(!$result) {
      authfail("dberror",$test_from,$test_id,$test_pass,"Unable to retrieve user's record.");
    }
    $rows = sybase_num_rows($result);
    $par = sybase_fetch_object($result);
  }

  # Some routines (pedit, for instance) need a full record returned, so...
  $qs = "select * from STATS_Participant where id = $par->id";
  $result = sybase_query($qs);
  $par = sybase_fetch_object($result);

  # OK, let's do some sanity checking
  $test_pass = substr($test_pass,0,8);

  # Did we sucessfully grab a single record?
  if ($rows > 1) {
    authfail("dberror",$test_from,$test_id,$test_pass,"Unable to retrieve COMPLETE record for user.");
  }

  # Did we find a record for this ID? (This should return a different error message,
  # such as invalid user or user not found
  if ($rows == 0) {
    authfail("pbadpass",$test_from,$test_id,$test_pass);
  }

  # Does the user have a password set?? (This should eventually return a different
  # error message, as well as emailing the password out
  if ($par->password == "") {
    authfail("pbadpass",$test_from,$test_id,$test_pass);
  }

  # Finally, make sure that we've got the right password (I think that we don't
  # actually want the first condition in here as it breaks when the user submits
  # an email address.)
  if (!($test_id == (int) $par->id) or !($test_pass == $par->password)) {
    authfail("pbadpass",$test_from,$test_id,$test_pass);
  }

  # At this point, we know we've got the right record, and the password is
  # correct.  However, there are still issues which can make us fail the
  # authorization.

  $retire_to = (int) $par->retire_to;
  if ($retire_to > 0) {
    # Cannot edit a participant if it has been retired.
    authfail("pretired",$test_from,$test_id,$test_pass);
  }

  $listmode = (int) $par->listmode;
  if ($listmode > 7) {
    # Cannot edit a participant that has been locked by the stats admins.
    authfail("plocked",$test_from,$test_id,$test_pass);
  }

  # OK, We made it through without a problem.  

  # Now back to our regularly scheduled programming...

?>

