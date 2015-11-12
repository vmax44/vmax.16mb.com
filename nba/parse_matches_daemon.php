<?php
include_once("espn_nba.php");
$parser=new nba_match_details_parser();
if(isset($_GET['matchIds'])) {
	$matchIds=$_GET['matchIds'];
} else {
	$matchIds=$parser->getNotParsedCompetitions(1);
}
if(!$matchIds) {
	die();
}
$log=[];
print_r($matchIds);
foreach($matchIds as $matchId) {
	echo $matchId.": ".$parser->parse($matchId);
}
print_r($log);
?>