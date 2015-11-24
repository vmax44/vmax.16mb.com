<?php
require_once("lib/request.php");
$http=new HTTP_Request();
$json=$http->request("http://api2.goldprice.org/Service.svc/GetRaw/3");
//echo $json."\n";
$json=str_replace("([\"","",$json);
//echo $json."\n";
$json=explode(",",$json);
//print_r($json);
echo "Silver Price per Ounce:<br><br>\n";
echo "initial price: ".$json[1]."<br>\n";
echo "timed price: ".($json[1]*0.99)."<br>\n";
?>