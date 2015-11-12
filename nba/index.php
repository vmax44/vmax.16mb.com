<?php
	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	if(isset($_GET['url'])) {
		include_once("espn_nba.php");
		include_once("simple_html_dom.php");
		$url=$_GET['url'];
		$html=new simple_html_dom();
		try {
			if(isset($_GET["tocsv"])) {
				$html->load_file($url);
				$nba=new nba_parser($html);
				$nba->parse();
				header("Content-type: text/csv");
				header("Content-Disposition: attachment; filename=result.csv");
				header("Pragma: no-cache");
				header("Expires: 0");
				echo $nba->toCSV();
				die();
			}
			if(isset($_GET["totable"])) {
				$html->load_file($url);
				$nba=new nba_parser($html);
				$nba->parse();
				echo $nba->toTable();
			};
			if(isset($_GET["parseDates"])) {
				$calendar=new nba_calendar_parser();
				$log=$calendar->parseFromDate("2010-01-01");
				print_r($log);
			}
		} catch(Exception $e) {
			echo "<b>Error - ".$e->getMessage();			
		}
	};
?>
<html>
	<head>
		<title>espn.go.com</title>
	</head>
	<body>
	<form>
		<p>Input Url of boxscore page, for example http://espn.go.com/nba/boxscore?gameId=400559376</p>
		<input type="text" name="url" value="<?=@$_GET['url']?>">
		<input type="submit" name="totable" value="Show table">
		<input type="submit" name="tocsv" value="Export to .CSV">
		<input type="submit" name="parseDates" value="Parse Dates (test)">
	</form>
<?php
	

	
	function l($str) {
		echo $str."\n";
	}
?>
	</body>
</html>