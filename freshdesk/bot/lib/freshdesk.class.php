<?php
require_once("request.php");
require_once("simple_html_dom.php");
require_once("safemysql.class.php");

class freshdesk {
	private $http;
	private $html;
	private $db;
	private $loginData;
	private $baseUrl;
	
	public function __construct($logData) {
		$this->loginData=$logData;
		$this->http=new HTTP_Request();
		$this->html=new simple_html_dom();
		$this->baseUrl="https://".$this->loginData['account'].".freshdesk.com";
		$this->initDB();
	}
	
	public function login() {
		//Login
		
		$content=$this->http->request($this->baseUrl."/support/login",'GET');
		$this->isAutorized();
		$this->html->load($content);
		$token=$this->html->find('input[name=authenticity_token]')[0]->value;
		$content1=$this->http->request($this->baseUrl."/support/login",
			'POST',	array(
			'utf8'=>'✓',
			'authenticity_token'=>$token,
			'user_session[email]'=>$this->loginData['login'],
			'user_session[password]'=>$this->loginData['password'],
			'user_session[remember_me]'=>'0'));
		//Login done!
		if(!$this->isAutorized()) {
			throw new Exception("Login failed!");
		}
	}
	
	public function getTicketsCount($vnumbers) {
		if(!$this->isAutorized()) {
			throw new Exception("Not autorized!");
		}
		$result=[];
		foreach($vnumbers as $vnumber=>$field) {
			$result[$field]=$this->getTicketsCountForOneView($vnumber);
		}
		return $result;
	}
	
	private function getTicketsCountForOneView($viewnumber) {
		$tickets_count=0;
		//Getting tickets count
		$content=$this->http->request($this->baseUrl."/helpdesk/tickets/view/".$viewnumber,"HEAD");
		$content=$this->http->request($this->baseUrl."/helpdesk/tickets/full_paginate?tickets_in_current_page=1");
		if(preg_match("/html\(([0-9]+)\)/",$content,$matches)) {
			$tickets_count=$matches[1];
		} else {
			//throw new Exception("Tickets count not found for View $viewnumber!");
		}
		//getting tickets count done!
		return $tickets_count;
	}
	
	//Grab data from report
	public function getGroupsData($groups) {
		if(!$this->isAutorized()) {
			throw new Exception("Not autorized!");
		}
		$result=array();
		
		// Set begin range date and end range date
		// date format: begin of month - "first day of",
		//				monday of current week - "monday this week",
		//				7 days ago - "7 days ago" or "-7 day"
		// 				
		$dateBegin = "7 days ago"; 
		$dateEnd = "today";
		
		//assemble url for report page
		$dateRange=date("j+M+Y",strtotime($dateEnd));
		if(strtotime($dateBegin)<strtotime($dateEnd)) {
			$dateRange=date("j+M+Y",strtotime($dateBegin))."+-+".$dateRange;
		}
		$url=$this->baseUrl."/reports/2?date_range=".$dateRange;
		//download report page
		$content=$this->http->request($url);
		$this->html->load($content);
		//parse report page
		$rows=$this->html->find('#agent_ticket_summary tr');
		foreach($rows as $row) {
			$cols=$row->find("td");
			$group=trim($cols[0]->innertext);
			if(($group!="") && array_key_exists($group,$groups)) {
				$fieldInDb=$groups[$group];
				$result[$fieldInDb]=trim($cols[1]->innertext);
			}
		}
		//end parse report page. Result in $result
		return $result;
	}
	
	private function isAutorized() {
		return (@$this->http->cookies['user_credentials']!="");
	}
	
	public function saveToDB($tickets,$groups) {
		$sql="INSERT INTO tickets SET dtime=NOW(),?u,?u";
		$this->db->query($sql,$tickets,$groups);
		return $this->db->mysqlInfo();
	}

	private function initDB() {
		$this->db = new SafeMySQL([
			'host' => $this->loginData['mysql_host'],
			'user' => $this->loginData['mysql_username'],
			'pass' => $this->loginData['mysql_password'],
			'db'   => $this->loginData['mysql_db'] 
		]);
	}
}
?>