<?php
include_once("streeteasy.class.php");
$bot=new streeteasy();
$timestart=time();
$details=$bot->run();
print_r($details);
echo "Parsed: ".count($details)."\n";
echo "Elapsed: ".(time()-$timestart)."\n";
?>