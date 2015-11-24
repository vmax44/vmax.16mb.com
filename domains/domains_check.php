<?php
	require_once("request.php");
	require_once("simple_html_dom.php");
	class domains_check{
		public $loadTime;
		public $titleLength;
		public $descriptionLength;
		public $keywordsLength;
		private $html;
		private $http;
		
		public function __construct() {
			$this->http=new HTTP_Request();
			$this->html=new simple_html_dom();
			
		}
		
		public function run($url) {
			$timestart=$this->microtime_float();
			$content=$this->http->request($url);
			$timeend=$this->microtime_float();
			$this->loadTime=$timeend-$timestart;
			$this->html->load($content);
			$this->titleLength=$this->html->find("head title")->innertext;
			$this->descriptionLength=$this->html->
				find("meta[name=description]")->content;
			$this->keywordsLength=$this->html->
				find("meta[name=keywords]")->content;
		}
	
		private function microtime_float()
		{
			list($usec, $sec) = explode(" ", microtime());
			return ((float)$usec + (float)$sec);
		}	
	}
?>