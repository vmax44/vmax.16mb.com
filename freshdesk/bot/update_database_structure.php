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

echo "<br>Create table if not exists<br>\n";
$sql="CREATE TABLE IF NOT EXISTS $tableName (".
		"dtime datetime NOT NULL,".
		"PRIMARY KEY (`dtime`)".
		") ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
$db->query($sql);

echo "<br>List fields in table:<br>\n";
$sql="SHOW COLUMNS FROM $tableName";
$fields=$db->getCol($sql);
print_array($fields);

// create array of needed fields
$needfields=array_merge($config["viewNumbers"],$config["groupsInReport"]);
echo "<br>Fields must be in database:<br>\n";
print_array($needfields);
foreach($needfields as $needfield) {
	if(!in_array($needfield,$fields)) {
		echo "  adding field $needfield<br>\n";
		addFieldToDB($needfield,$db,$tableName);
	}
}
echo "<br>done.<br><br>\n";

function addFieldToDB($field,$db,$tableName) {
	$sql="ALTER TABLE `$tableName` ADD COLUMN `$field` INT(11) NOT NULL";
	$db->query($sql);
}

function print_array($arr) {
	foreach($arr as $a) {
		echo "   $a<br>\n";
	}
}

//Print table to HTML
?>
<table border="1">
	<thead>
		<tr>
<?
foreach($fields as $fieldname) {
	echo "<td>$fieldname</td>\n";
}
echo "</tr></thead><tbody>";

$data=$db->getAll("SELECT * FROM $tableName ORDER BY dtime DESC LIMIT 100");
foreach($data as $row=>$cols) {
	echo"<tr>";
	foreach($cols as $col) {
		echo "   <td>$col</td>\n";
	}
	echo "</tr>\n";
}
echo "</tbody></table>";
?>