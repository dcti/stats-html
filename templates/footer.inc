<? 
$query_string = str_replace ("project_id=$project_id","",$_SERVER['QUERY_STRING']);
if (!isset($proj_list)) { ?>
<center> This page in other projects: <br>
<?if ($project_id!=5)  {?> <a href="<?=$myname?>?project_id=5<?=$query_string?>">RC5-64</a>  <? } ?>
<?if ($project_id!=24) {?>  <a href="<?=$myname?>?project_id=24<?=$query_string?>">OGR-24</a> <? }?>
<?if ($project_id!=25) {?>  <a href="<?=$myname?>?project_id=25<?=$query_string?>">OGR-25</a> <? }?>
</center>
<?
} 
?>


  <center>
   <p>
    <a href="http://www.php.net"><img src="/images/php.gif" alt="PHP3" border="0"></a>
    <a href="http://www.sybase.com/"><img src="/images/sybase.gif" alt="Sybase" border="0"></a>
   </p>
<? if ($_SERVER["SERVER_NAME"]!="stats.distributed.net"){ ?>
   <p> Page generated in <?=timer_current()?> </p>
<? } ?>
  </center>
 </body>
</html>
