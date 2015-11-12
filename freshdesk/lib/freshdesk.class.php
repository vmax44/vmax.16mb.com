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
	
	public function getTicketsCount() {
		if(!$this->isAutorized()) {
			throw new Exception("Not autorized!");
		}
		$tickets_count=0;
		//Getting tickets count
		$content=$this->http->request($this->baseUrl."/helpdesk/tickets/view/".$this->loginData['viewnumber']);
		$content=$this->http->request($this->baseUrl."/helpdesk/tickets/full_paginate?tickets_in_current_page=1");
		if(preg_match("/html\(([0-9]+)\)/",$content,$matches)) {
			$tickets_count=$matches[1];
		} else {
			throw new Exception("Tickets count not found!");
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
		//date_default_timezone_set('UTC');
		//format current date
		$date=date("j+M+Y",strtotime("today"));
		//assemble url for report page
		$url=$this->baseUrl."/reports/2?date_range=".$date;
		//download report page
		$content=$this->http->request($url);
		$this->html->load($content);
		//parse report page
		$rows=$this->html->find('#agent_ticket_summary tr');
		foreach($rows as $row) {
			$cols=$row->find("td");
			$group=trim($cols[0]->innertext);
			if(($group!="") && in_array($group,$groups)) {
				$result[$group]=trim($cols[1]->innertext);
			}
		}
		//end parse report page. Result in $result
		return $result;
	}
	
	private function isAutorized() {
		return (@$this->http->cookies['user_credentials']!="");
	}
	
	public function saveToDB($tickets,$groups) {
		$sql="INSERT INTO tickets SET dtime=NOW(),tickets=?i,flow=?i,flowen=?i";
		$this->db->query($sql,$tickets,$groups['Flow'],$groups['Flow EN']);
		return $this->db->mysqlInfo();
	}

	private function initDB() {
		$this->db = new SafeMySQL([
			'host' => $this->loginData['mysql_host'],
			'user' => $this->loginData['mysql_username'],
			'pass' => $this->loginData['mysql_password'],
			'db'   => $this->loginData['mysql_db'] 
		]);
		
		$sql="CREATE TABLE IF NOT EXISTS tickets (".
  				"dtime datetime NOT NULL,".
  				"tickets int(11) NOT NULL,".
  				"flow int(11) NOT NULL,".
  				"flowen int(11) NOT NULL,".
  				"PRIMARY KEY (`dtime`)".
			  ") ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
		$this->db->query($sql);
	}
}
?>