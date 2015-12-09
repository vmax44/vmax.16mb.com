<?php
	error_reporting(E_ALL);
	include_once("vendor/autoload.php");
	include_once("simple_html_dom.php");

	use GuzzleHttp\Pool;
	use GuzzleHttp\Client;
	use GuzzleHttp\Psr7\Request;
	use GuzzleHttp\Promise;
class streeteasy {
	
	
	private $html;
	private $http;
	private $details;
	
	public function __construct() {
		$this->html=new simple_html_dom();
		$this->http=new Client(['base_uri'=>'http://streeteasy.com/',
						'cookies'=>true]);
		$this->details=[];
	}
	
	function run($startPage='http://streeteasy.com/for-sale/downtown/status:closed%7Clisted%3C1500') {
		$nextPage = $startPage;
		$promise= [$this->http->requestAsync('GET',$nextPage)];
		$urls=[];
		$i=0;
		do {
			$result=Promise\unwrap($promise);

			$content=$result[0]->getBody();
			$this->html->load($content);
			$nextPage=$this->parseNextUrl($this->html);

			$promise= [$this->http->requestAsync('GET',$nextPage)];

			$urls=$this->parseUrls($this->html);
			print_r($urls);
			$this->parseDetails($urls);
			echo "nextPage=$nextPage\n";
			if($i++>=1) {
				break;
			}
		} while ($nextPage);
		return $this->details;
	}
	
	function parseUrls(simple_html_dom $html) {
		$tmp=$html->find("div[class*='details-title']");
		$urls=[];
		foreach($tmp as $url) {
			echo "Tag: ".$url->find("a",0)->tag."\n";
			$urls[]=$url->find("a",0)->href;
		}
		return $urls;
	}
	
	function parseNextUrl(simple_html_dom $html) {
		$nextUrlnode=$html->find("span[class='next']",0)->first_child();
		return $nextUrlnode->href;
	}
	
	function parseDetails($urls) {
		
		$requests = function ($arr) {
			for($i=0;$i<count($arr);$i++) {
				yield new Request('GET', $arr[$i]);
			}
		};

		$pool = new Pool($this->http, $requests($urls), [
			'concurrency' => 4,
			'fulfilled' => function ($response, $index) use($urls) {
				echo "$index loaded\n";
				$this->html->load((string) $response->getBody());
				$detail=$this->parseDetails_($this->html);
				echo "Details parsed\n";
				$id=$this->parseId($urls[$index]);
				$this->details[$id]=$detail;
				$this->details[$id]['url']=$urls[$index];
				$this->details[$id]['status']='ok';
				//var_dump($this->details);
			},
			'rejected' => function ($reason, $index) {
				// this is delivered each failed request
				echo "$index rejected\n";
				$id=$this->parseId($urls[$index]);
				$this->details[$id]['url']=$urls[$index];
				$this->details[$id]['status']=$reason;
			},
		]);
		
		// Initiate the transfers and create a promise
		$promise = $pool->promise();
		
		// Force the pool of requests to complete.
		$promise->wait();
		return $this->details;
	}
	
	private function parseId($str) {
		$beginId=strrpos($str,'/')+1;
		$endId=strrpos($str,'?');
		if($endId===false) {
			return substr($str,$beginId);
		} else {
			return substr($str,$beginId,$endId-$beginId);
		}
	}
	
	private function parseDetails_(simple_html_dom $html) {
		$result=[];
		$result['title']=$this->getPlainTextOrEmpty($html,"h1[class*='building-title'] a",0);
		$result['price']=$this->getPlainTextOrEmpty($html,"div[class*='price']",0);
		return $result;
	}
	
	private function getPlainTextOrEmpty(simple_html_dom $html, $selector,$num) {
		$result="";
		$node=$html->find($selector,$num);
		if(!is_null($node)) {
			$result=trim($node->plaintext);
		}
		return $result;
	}
	

}
?>