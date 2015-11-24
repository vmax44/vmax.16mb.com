<?php
	header('Content-type: application/json; charset=utf-8');
	
	require_once("lib/safemysql.class.php");
	require_once("pass.php");
	
	$errormsg="";
	$response=[];
	try {
		$db = new SafeMySQL([
			'host' => $loginData['mysql_host'],
			'user' => $loginData['mysql_username'],
			'pass' => $loginData['mysql_password'],
			'db'   => $loginData['mysql_db'] 
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