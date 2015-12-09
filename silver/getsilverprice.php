<?php
	//require_once("lib/request.php");
	//$http=new HTTP_Request();
	//$json=$http->request("http://api2.goldprice.org/Service.svc/GetRaw/3");
	$json=file_get_contents("http://api2.goldprice.org/Service.svc/GetRaw/3");
	//echo $json."\n";
	$json=str_replace("([\"","",$json);
	//echo $json."\n";
	$json=explode(",",$json);
	//print_r($json);
	$initialprice=$json[1];
	echo "Silver Price per Ounce:<br><br>\n";
	echo "initial price: ".$initialprice."<br>\n";
	echo "initial price - 1  = ".($initialprice-1)."<br>\n";
	echo "initial price - 0.5 = ".($initialprice-0.5)."<br>\n";
		
?>

