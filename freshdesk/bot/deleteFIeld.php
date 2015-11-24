<?php
require_once("config.php");
require_once("pass.php");
require_once("lib/safemysql.class.php");

$tableName="tickets";

echo "Log in MySQL<br>\n";
$db = new SafeMySQL([
	'host' => $loginData['mysql_host'],
	'user' => $loginData['mysql_username'],
	'pass' => $loginData['mysql_password'],
	'db'   => $loginData['mysql_db'] 
]);

echo "<br>Delete field harlequin_tickets<br>\n";

deleteFieldFromDB("harlequin_tickets",$db,$tableName);

echo "<br>done.<br><br>\n";

function deleteFieldFromDB($field,$db,$tableName) {
	$sql="ALTER TABLE `$tableName` DROP COLUMN `$field`";
	$db->query($sql);
}

?>