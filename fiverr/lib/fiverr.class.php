<?php
require_once("request.php");
require_once("simple_html_dom.php");

class FiverrBot {
	private $http;
	private $html;
	private $loginData;
	private $baseUrl;
	public $homeScope;
	public $userName;
	
	public function __construct($logData) {
		$this->loginData=$logData;
		$this->http=new HTTP_Request();
		$this->html=new simple_html_dom();
		$this->baseUrl="https://www.fiverr.com";
		$this->homeScope=null;
		$this->userName="";
		//$this->initDB();
	}
	
	public function login() {
		//Login
		$content=$this->http->request($this->baseUrl,'GET');
		$this->html->load($content);
		$form = $this->html->find("#session_form",0);
		$inputs=$form->find("input");
		$inp=[];
		foreach($inputs as $input) {
			switch($input->class) {
				case "js-form-password": 
						$inp[$input->name] = $this->loginData['password'];
						break;
				case "js-form-login": 
						$inp[$input->name] = $this->loginData['login'];
						break;
				default: 
						$inp[$input->name] = $input->value;
						break;
			}
		}
		
		$content = $this->http->request($this->baseUrl.$form->action,'POST', $inp);

		if(!$this->isAutorized()) {
			throw new Exception("Login failed!");
		}
		$this->homeScope = $this->getHomeScope();
		$this->userName=$this->homeScope->currentUser->userName;
	}
	
	private function getHomeScope() {
		$content=$this->http->request($this->baseUrl);
		$scope=$this->getJson("pageScope",$content);
		return $scope;
	}
	
	private function getJson($name, $content) {
		$begin=strpos($content,$name);
		$jsonstr="{}";
		if($begin!==FALSE) {
			$begin=strpos($content,"{",$begin);
			$end=strpos($content,"};",$begin)+1;
			$jsonstr=substr($content,$begin,$end-$begin);
		}
		$json=json_decode(trim($jsonstr));
		return $json;
	}
	
	public function getBalance() {
		$content=$this->http->request($this->baseUrl."/users/".
										$this->userName.
										"/balance/shopping");
		$stats=$this->getJson("viewData",$content);
		return $stats->statsData->stats;
	}
	
	private function isAutorized() {
		return (@$this->http->cookies['was_logged_in']!="");
	}
}
?>