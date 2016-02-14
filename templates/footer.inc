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
 </body>
</html>
