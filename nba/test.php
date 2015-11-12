<?PHP
header('Content-type: application/json; charset=utf-8');
$req=$_GET['req'];
$json=file_get_contents($req);
echo $json;
?>