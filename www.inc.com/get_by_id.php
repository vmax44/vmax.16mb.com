<?php
header('Content-type: application/javascript; charset=utf-8');
if(!isset($_GET['id'])) {
	echo(json_encode(["error"=>"Id of firm required"]));
	die();
}
$id=$_GET['id'];
$url="http://www.inc.com/rest/inc5000company/".$id."/full_list";
$result=file_get_contents($url);
echo $result;
?>