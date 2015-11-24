<html>
	<head>
		<title>Fiverr.com Bot</title>
	</head>
	<body>
<?php
require_once("lib/fiverr.class.php");

$login=@$_POST['login'];
$pass=@$_POST['pass'];

if($login!="" && $pass!="") {
	try {
		//get data
		$bot=new FiverrBot(['login'=>$_POST['login'],
							'password'=>$_POST['pass']]);
		$bot->login();
		$stats=$bot->getBalance();

		//echo results
?>
<div>
	<h2>Stats for user <?=$bot->userName?>:</h2>
	<ul>
<?
foreach($stats as $stat) {
?>
		<li><?=$stat->text?>: <?=$stat->count?></li>
<?
}
?>
	</ul>
</div>
<?		
	} catch (Exception $e) {
		echo "Error: ".$e->getMessage(); 	
	};
} else {
	echo "<H3>Enter your login data for site http://www.fiverr.com:</H3>\n";
	echo "<H4>Or enter login: vmax44, password: vmax1111</H4>\n";
}

?>

	<form method="POST">
		<input type="TEXT" name="login" value="<?=@$_POST['login']?>" placeholder="Enter your login on www.fiverr.com"><br>
		<input type="PASSWORD" name="pass" value="<?=@$_POST['pass']?>" placeholder="Enter password"><br>
		<input type="SUBMIT" name="submit" value="Get Balance">
	</form>
</body>
</html>
