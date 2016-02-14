<? if( isset( $lastupdate ) ) { ?>
 <div class="well">
<? if( $lastupdate <> "" ) { ?>
      Data shown reflects all blocks received as of <?=$lastupdate?> at 23:59 UTC. Current time is <?=gmstrftime('%d-%b-%Y %T')?>.
<? } else { ?>
      Update currently in progress...
<? } ?>
 </div>
<? } ?>
  <footer>
  </footer>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="/js/bootstrap.min.js"></script>

<!-- Google Analytics -->
    <script type="text/javascript">
    var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
    document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
    </script>
    <script type="text/javascript">
    try {
      var pageTracker = _gat._getTracker("UA-327688-4");
      pageTracker._trackPageview();
    } catch(err) {}
    </script>

 </body>
</html>
