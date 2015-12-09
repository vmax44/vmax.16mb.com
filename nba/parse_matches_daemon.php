<?php
error_reporting(E_ALL);
include_once("espn_nba.php");
$parser=new nba_match_details_parser();
//$parser->goRandomUrls();
if(isset($_GET['matchIds'])) {
	$matchIds=$_GET['matchIds'];
} else {
	$matchIds=$parser->getNotParsedCompetitions(15);
}
if(!$matchIds) {
	die();
}
$log=[];
//print_r($matchIds);
foreach($matchIds as $matchId) {
	$log[]=$matchId.": ".$result=$parser->parse($matchId);
}
print_r($log);
?>