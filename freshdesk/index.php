<?php
require_once("lib/freshdesk.class.php");
require_once("pass.php");

//get data
$bot=new freshdesk($loginData);
$bot->login();
$tickets_count = $bot->getTicketsCount();
$groups = $bot->getGroupsData(["Flow","Flow EN"]);
echo $bot->saveToDB($tickets_count,$groups);

//echo results
echo date("d.m.Y")."\n";
echo "Opened tickets count: ".$tickets_count."\n";
echo "Tickets resolved:\n";
foreach($groups as $key=>$value) {
	echo $key.": ".$value."\n";
}
?>
