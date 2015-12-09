<?php
include_once("rennline_bot.php");
$parser=new nba_competitions_parser();
if(isset($_GET['dates'])) {
	$dates=$_GET['dates'];
} else {
	$dates=$parser->getNotParsedDates(15);
}
if(!$dates) {
	die();
}
$log=[];
foreach($dates as $date) {
	$log[]=$date.": ".$parser->competitionsParse($date);
}
print_r($log);
?>