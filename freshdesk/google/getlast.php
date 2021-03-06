<?php
	$timestart=microtime();
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
		
		$startCurrentWeek=date("Y-m-d H:i:s", strtotime("last Monday"));
		
		$sql="SELECT *,DAYNAME(dtime) as day FROM $tableName WHERE dtime IN ".
			"(SELECT MAX(dtime) FROM $tableName ".
			"WHERE dtime>='$startCurrentWeek' ".
			"GROUP BY DAYNAME(dtime))";
		$values=$db->getInd("day",$sql);
	
		$response["data"]=$values;
		
		$sql="SELECT * FROM $tableName ORDER BY dtime DESC LIMIT 1";
		$values=$db->getRow($sql);
		
		$response["latest"]=$values;

	} catch(Exception $e) {
		$errormsg=$e->getMessage();
	}
	
	if($errormsg=="") {
		$response["status"]="ok";	
	} else {
		$response["status"]="error";
		$response["message"]=$errormsg;
	}
	$response["elapsed"]=(microtime()-$timestart);
	
	echo json_encode($response);
	
?>