<html>
	<head>
		<title>Domains check</title>
	</head>
	<body>
<?php
	error_reporting(E_ALL);
	if(isset($_GET['url'])) {
		include_once("domains_check.php");
		$url=$_GET['url'];
		$bot=new domains_check();
		$bot->run($url);
		echo "Data for $url:<br>\n";
		echo "time load: ".iconv_strlen($bot->loadTime)."<br>\n";
		echo "title length: ".iconv_strlen($bot->titleLength)."<br>\n";
		echo "meta description length: ".iconv_strlen($bot->descriptionLength)."<br>\n";
		echo "meta keywords length: ".iconv_strlen($bot->keywordsLength)."<br>\n";
	};
?>
	<form>
		<p>Input Url, for example http://www.yandex.ru</p>
		<input type="text" name="url" value="<?=@$_GET['url']?>">
		<input type="submit" name="submit" value="Check Site">
	</form>
	</body>
</html>