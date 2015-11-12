<?php
header('Content-type: application/javascript; charset=utf-8');
$url="http://www.inc.com/inc5000list/json/inc5000_2015.json";
$result=file_get_contents($url);

if(!$result) {
	$result=json_encode(["error"=>"Error while loading fulllist"]);
};
echo($result);
?>