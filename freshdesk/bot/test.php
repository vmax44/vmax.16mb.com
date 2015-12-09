<?php
require_once("lib/freshdesk.class.php");
require_once("pass.php");
require_once("config.php");

//$timezone='Europe/Amsterdam1';
//set time zone
//date_default_timezone_set($timezone);

try {
	//get data
	$bot=new freshdesk($loginData);
	$bot->login();
	$tickets_count = $bot->getTicketsCount($config["viewNumbers"]);
	$groups = $bot->getGroupsData($config["groupsInReport"]);
	//echo $bot->saveToDB($tickets_count,$groups);

	//echo results
	echo date("d.m.Y H:i")."\n";
	echo "Opened tickets count:\n";
	foreach($tickets_count as $field=>$count) {
		echo $field.": ".$count."\n";
	}
	echo "Tickets resolved:\n";
	foreach($groups as $field=>$count) {
		echo $field.": ".$count."\n";
	}
} catch (Exception $e) {
	echo "Error: ".$e->getMessage(); 	
}
?>
