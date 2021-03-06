<?php
error_reporting(E_ALL);
include_once("vendor/autoload.php");
include_once("simple_html_dom.php");

use GuzzleHttp\Pool;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

$html=new simple_html_dom();

$urls = [
	'http://www.rennline.com/Porsche/departments/1/',
	//'http://www.rennline.com/BMW/departments/5/',
	//'http://www.rennline.com/Audi/departments/4/',
	//'http://www.rennline.com/Universal/departments/880/'
];

$client=new Client(['base_uri'=>'http://www.rennline.com/',
					'cookies'=>true]);

$requests = function ($arr) {
    for($i=0;$i<1;$i++) { //$i<count($arr);$i++) {
        yield new Request('GET', $arr[$i]);
    }
};

$pool = new Pool($client, $requests($urls), [
    'concurrency' => 2,
    'fulfilled' => function ($response, $index) {
		echo "$index loaded\n";
		parseModels((string) $response->getBody());
    },
    'rejected' => function ($reason, $index) {
        // this is delivered each failed request
    	echo "$index rejected\n";
	},
]);

function parseModels($text) {
	//echo $text;
	global $html;
	global $modelsLinks;
	$html->load($text);
	$res=$html->find('a[class=secondnav]');
	foreach($res as $model) {
		$modelsLinks[]=$model->href;
	}
}

$modelsLinks=[];
// Initiate the transfers and create a promise
$promise = $pool->promise();

// Force the pool of requests to complete.
$promise->wait();
print_r($modelsLinks);


$pool = new Pool($client, $requests($modelsLinks), [
    'concurrency' => 4,
    'fulfilled' => function ($response, $index) {
		global $modelsLinks;
		echo "$index loaded\n";
		parseProducts((string) $response->getBody(),$modelsLinks[$index]);
    },
    'rejected' => function ($reason, $index) {
        // this is delivered each failed request
    	echo "$index rejected\n";
	},
]);

function parseProducts($text,$url) {
	//echo $text;
	global $html;
	global $productUrls;
	$html->load($text);
	$productsUrls[]=$url;
	foreach($html->find('a.sidenav') as $sidenav) {
		if((!isset($sidenav->onClick)) 
				&& ($sidenav->href!="#")
		) {
			$productUrls[] = $sidenav->href;
		}
	}
	foreach($html->find('a.sidenav3') as $sidenav3) {
		if($sidenav3->href!='javascript:void(0)') {
			$productUrls[] = $sidenav3->href;
		}
	
	}
	
	//die();
}

$productUrls=[];
// Initiate the transfers and create a promise
$promise = $pool->promise();

// Force the pool of requests to complete.
$promise->wait();
print_r($productUrls);


$pool = new Pool($client, $requests($productUrls), [
    'concurrency' => 4,
    'fulfilled' => function ($response, $index) {
		echo "$index loaded\n";
		parseProductsLinks((string) $response->getBody());
    },
    'rejected' => function ($reason, $index) {
        // this is delivered each failed request
    	echo "$index rejected\n";
	}
]);

function parseProductsLinks($text) {
	//echo $text;
	global $html;
	global $productsLinks;
	$html->load($text);
	$table=$html->find('body>center>table>tr',2);
	echo $table->outertext;
	$table1=$table->find('table td',4);
	//echo $table1->outertext;
	//$table2=$table1->find('table td',1);
	if($table1==null) {
		echo "Warning: format error!\n";
		return;
	}
	$res=$table1->find('td a img');
	foreach($res as $img) {
		$a=$img->parent();
		if(isset($a->href)) {
			$productsLinks[]=$a->href;
		} else {
			echo "Warning: no href!\n";
		}
	}
	print_r($productsLinks);
	die();
}

$productsLinks=[];
// Initiate the transfers and create a promise
$promise = $pool->promise();

// Force the pool of requests to complete.
$promise->wait();
print_r($productsLinks);
?>