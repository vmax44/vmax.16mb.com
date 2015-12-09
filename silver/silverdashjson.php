<?php
header('Content-type: application/json; charset=utf-8');
include_once("silverdash_.php");
$answer=json_encode(["silverprice"=>$price]);
echo $answer;
?>