<html>
	<head>
		<title>craigslist</title>
	</head>
	<body>
	<form>
		<p>Input Url, for example http://philadelphia.craigslist.org/apa/5299825266.html</p>
		<input type="text" name="url" value="<?=@$_GET['url'] ? @$_GET['url'] : "http://philadelphia.craigslist.org/apa/5299825266.html"?>">
		<input type="submit" name="parse" value="parse">
	</form>
<?php
	if(isset($_GET['url'])) {
		require_once("request.php");
		require_once("simple_html_dom.php");
		
		$url=$_GET['url'];
		$http=new HTTP_Request();
		$html=new simple_html_dom();
		
		$content=$http->request($url);
		$html->load($content);
		$posting=$html->find("section#postingbody")[0]->outertext;
		$contactInfoLink="http://philadelphia.craigslist.org".$html->find('a.showcontact')[0]->href;
		
		$content=$http->request($contactInfoLink);
		print_r($content);
	}

	
	function l($str) {
		echo $str."\n";
	}
?>
	</body>
</html>