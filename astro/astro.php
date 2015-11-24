<?php
	require_once('astro_DAL.php');
	require_once('simple_html_dom.php');
	require_once('request.php');
	
	class astro_details_parser {
		private $db;
		private $html;
		private $http;
		private $baseurl;
		
		public function __construct() {
			$this->http=new HTTP_Request();
			$this->html=new simple_html_dom();
			$this->db=new astro_DAL();
			$this->baseurl="http://www.astro.com";
		}

		public function parseDetails($url) {
			$this->html->load($this->http->request($baseurl.$url));
			
		}		
		
	}
	
	class astro_links_parser
	{
		private $db;
		private $html;
		private $http;
		private $baseurl;
		
		public function __construct() {
			$this->http=new HTTP_Request();
			$this->html=new simple_html_dom();
			$this->db=new astro_DAL();
			$this->baseurl="http://www.astro.com";
		}
		
		public function parseUrls() {
			$result=[];
			
			$lastUrlName=$this->db->getLastUrlName();
			if($lastUrlName===FALSE) {
				$lastUrlName="A, Dominique";
			}
			$nextUrl=$this->baseurl."/wiki/astro-databank/index.php?title=Special:AllPages&from=".
				urlencode(str_replace(" ","_",$lastUrlName));
			
			while($nextUrl!="") {
				echo "nextUrl: $nextUrl\n";
				$content=$this->http->request("$nextUrl");
				$this->html->load($content);
				$htmlurls=$this->html->find('ul.mw-allpages-chunk a');
				foreach($htmlurls as $htmlurl) {
					$url=$htmlurl->href;
					$url_name=$htmlurl->title;
					$result[]=[$url, $url_name];
					//echo "url: $url, url_name: {$url_name}\n";
				}
				//die();
				$this->db->saveUrls($result);
				$nextUrl=$this->getNextPage();
				echo "nexturl: $nextUrl\n";
				//die();
			}
		}
		
		private function getNextPage() {
			$htmlNextUrls=$this->html->find('div.mw-allpages-nav a');
			foreach($htmlNextUrls as $htmlNextUrl) {
				if(stripos($htmlNextUrl->innertext,"Next page")!==false) {
					return $this->baseurl.
						html_entity_decode(trim($htmlNextUrl->href));
				}
			}
			return "";
		}
	}
?>