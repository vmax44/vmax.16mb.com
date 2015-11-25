<?php
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

echo "<br>List fields in table:<br>\n";
$sql="SHOW COLUMNS FROM $tableName";
$fields=$db->getCol($sql);

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