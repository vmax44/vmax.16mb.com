<?php
	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	
?>
<html>
	<head>
		<title>astro</title>
	</head>
	<body>
<?php
	
	include_once("astro.php");
	$bot=new astro_links_parser();
	$bot->parseUrls();
	
?>
	</body>
</html>