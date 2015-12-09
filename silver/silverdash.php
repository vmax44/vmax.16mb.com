<?php
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
<html>
	<head>
<?
	if(getNumericArg('r')>0) {
		echo "<meta http-equiv=\"refresh\" content=\"".getNumericArg('r')."\">\n";
	}
?>
	</head>
	<body>
		<center>
			<h1 id="silverprice">$<?=$price?></h1>
		</center>
	</body>
</html>