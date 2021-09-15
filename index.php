<?php
  include "etc/global.inc";
  include "etc/modules.inc";

  $title = "Welcome";
  include "templates/header.inc";
?>
    <div class="jumbotron">
        <h1>Welcome to stats.distributed.net!</h1>
        <p>Welcome to distributed.net's statistics server.</p>
    <p>
    This server hosts the database which keeps track of statistics for the
    ongoing distributed.net projects.
    </p>
    <p>
    Running progress and development announcements can be found in
    <a href="https://blogs.distributed.net/">staff .plans/blogs</a>
    and the announcements <a href="https://www.distributed.net/Discussion#lists">mailing list</a>.
    </p>
    <p>
    For those of you that have a morbid fascination about the statsbox
    itself, you can see a <a href="http://faq.distributed.net/?file=63">some details</a>
    of the actual box.
    </p>
    </div>

    <div class="container">
      <div class="row">
        <div class="col-md-4">
          <h3>Active Projects</h3>
          <p><img src="/images/cowhead.gif" alt="[Cow]"><a href="/projects.php?project_id=8">RC5-72 - RSA Labs' 72bit RC5 Encryption Challenge</a></p>
          <p><img src="/images/roohead.gif" alt="[Roo]"><a href="/projects.php?project_id=28">OGR-28 - Optimal Golomb Rulers</a></p>
        </div>
        <div class="col-md-4">
          <h3>Completed Projects</h3>
          <p><img src="/images/roohead.gif" alt="[Roo]"><a href="/projects.php?project_id=24">OGR-24 - Optimal Golomb Rulers</a></p>
          <p><img src="/images/roohead.gif" alt="[Roo]"><a href="/projects.php?project_id=25">OGR-25 - Optimal Golomb Rulers</a></p>
          <p><img src="/images/roohead.gif" alt="[Roo]"><a href="/projects.php?project_id=26">OGR-26 - Optimal Golomb Rulers</a></p>
          <p><img src="/images/roohead.gif" alt="[Roo]"><a href="/projects.php?project_id=27">OGR-27 - Optimal Golomb Rulers</a></p>
          <p><img src="/images/cowhead.gif" alt="[Cow]"><a href="/projects.php?project_id=3">RC5-56 - RSA Labs' 56bit RC5 Encryption Challenge</a></p>      
          <p><img src="/images/cowhead.gif" alt="[Cow]"><a href="/projects.php?project_id=5">RC5-64 - RSA Labs' 64bit RC5 Encryption Challenge</a></p>
          <p><img src="/images/cowhead.gif" alt="[Cow]"><a href="/projects.php?project_id=205">RC5-64(all) - RSA Labs' 64bit RC5 Encryption Challenge (plus work after key found)</a></p>
        </div>
        <div class="col-md-4"> 
          <h3>Cross-Project Statistics</h3>
          <p><a href="https://cgi.distributed.net/speed/">Client speed comparison database (live)</a></p>
        </div>
      </div>
   </div>
<?php
  include "templates/footer.inc";
