<?php
	//require_once("lib/request.php");
	//$http=new HTTP_Request();
	//$json=$http->request("http://api2.goldprice.org/Service.svc/GetRaw/3");
	$json=file_get_contents("http://api2.goldprice.org/Service.svc/GetRaw/3");
	$json=str_replace("([\"","",$json);
	$json=explode(",",$json);
	$initialprice=$json[1];
	$price = number_format($initialprice-getNumericArg('s')/100,2,"."," ");
	
	function getNumericArg($v) {
		if(isset($_GET[$v]) && is_numeric($_GET[$v])+0) {
			return $_GET[$v];
		} else {
			return 0;
		}
		
	}
?>
