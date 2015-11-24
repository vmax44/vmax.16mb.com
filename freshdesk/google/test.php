<?php
	require_once("lib/safemysql.class.php");
		
	$errormsg="";
	$response=[];
	try {
		$db = new SafeMySQL([
			'host' => "s214.webhostingserver.nl",
			'user' => "deb12215n7_curl3@s214.webhostingserver.nl",
			'pass' => "tofreshdesk3",
			'db'   => "deb12215n7_curl3" 
		]);
		
		$tableName="tickets";
		
		$sql="SELECT * FROM $tableName ORDER BY dtime DESC LIMIT 1";
		$values=$db->getRow($sql);
	
		$response["data"]=$values;

	} catch(Exception $e) {
		$errormsg=$e->getMessage();
	}
	
	if($errormsg=="") {
		$response["status"]="ok";	
	} else {
		$response["status"]="error";
		$response["message"]=$errormsg;
	}
	
	echo json_encode($response);
	
?>