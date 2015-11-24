<?php
	require_once('nba_DAL.php');
	class nba_parser
	{
		public $match;
		private $html;
		
		public function __construct($h) {
			$this->html=$h;
			$this->match=new nba_match();
		}
		
		public function parse() {
			$this->parse_match_date();
			$this->parse_team_names();
			$this->parse_players();
		}
		
		private function parse_match_date() {
			$datestr=$this->html->find(".subhead span a",0)->href;
			$this->match->set_date_from_string($datestr);
		}
		
		private function parse_team_names() {
			$teams=$this->html->find('table.linescore td.team a');
			$this->match->team_away=$teams[0]->plaintext;
			$this->match->team_home=$teams[1]->plaintext;
		}
		
		private function parse_players() {
			$theads=$this->html->find('div#my-players-table div table thead');
			// $theads[0] and $theads[1] - table parts for 1 team;
			// $theads[3] and $theads[4] - table parts for 2 team;
			//parse first team
			$this->match->team_away_starters=$this->parse_table_part($theads[0]);
			$this->match->team_away_bench=$this->parse_table_part($theads[1]);
			$this->match->team_home_starters=$this->parse_table_part($theads[3]);
			$this->match->team_home_bench=$this->parse_table_part($theads[4]);
		}
		
		private function parse_table_part($thead) {
			$players=[];
			$header_rows=$thead->find("tr");
			$header_row=$header_rows[count($header_rows)-1];
			$headers=$header_row->find("th");
			$rows=$thead->next_sibling()->find("tr");
			foreach($rows as $row) {
				$player=new nba_player();
				$tds=$row->find("td");
				$name_pos=$tds[0];
				$player->name=$name_pos->find("a",0)->plaintext;
				$pos_txt=$name_pos->plaintext;
				$space_pos=strrpos($pos_txt," ");
				$pos=substr($pos_txt,$space_pos+1,strlen($pos_txt)-$space_pos);
				$player->position=$pos;
				for($i=1;$i<count($headers);$i++) {
					$key=$headers[$i]->plaintext;
					$value=isset($tds[$i]) ? $tds[$i]->plaintext : "";
					$player->params[$key]=$value;
				}
				$players[]=$player;
			}
			return $players;
		}


		public function toCSV() {
			$arr=$this->toArray();
			$csv="";
			foreach($arr as $row) {
				$csv.=$this->arrayToCsv($row)."\n";
			}
			return $csv;
		}

		public function toArray($needHeadersRow=true) {
			$res=[];
			if($needHeadersRow) {
				//first elem - headers
				$res[0]=["Player","Team","Opponent","Start/Bench","Home/Away","Date Played","Position"];
				$params=$this->match->team_away_starters[0]->params;
				foreach($params as $key=>$value) {
					$res[0][]=$key;
				}
			}
			$res=array_merge($res,$this->print_players_to_array($this->match->team_away_starters,$this->match->team_away,$this->match->team_home,"START","AWAY",
				$this->match->date));
			$res=array_merge($res,$this->print_players_to_array($this->match->team_away_bench,$this->match->team_away,$this->match->team_home,"BENCH","AWAY",
				$this->match->date));
			$res=array_merge($res,$this->print_players_to_array($this->match->team_home_starters,$this->match->team_home,$this->match->team_away,"START","HOME",
				$this->match->date));
			$res=array_merge($res,$this->print_players_to_array($this->match->team_home_bench,$this->match->team_home,$this->match->team_away,"BENCH","HOME",
				$this->match->date));
			return $res;
		}
		
		private function print_players_to_array($players,$team,$opponent,$start_bench,$home_away,$date_played) {
			$res=[];
			foreach($players as $player) {
				$arr=[];
				$arr[]=$player->name;
				$arr[]=$team;
				$arr[]=$opponent;
				$arr[]=$start_bench;
				$arr[]=$home_away;
				$arr[]=$date_played;
				$arr[]=$player->position;
				foreach($player->params as $param) {
					$arr[]=$param;
				}
				$res[]=$arr;
			}
			return $res;
		}

		public function toJson() {
			return json_encode($this->match);
		}

		public function toTable() {
			$res="";
			$res="<table><thead><tr>".
				"<th>Player</th><th>Team</th><th>Opponent</th><th>Start/Bench</th><th>Home/Away</th><th>Date Played</th><th>Position</th>";
			$params=$this->match->team_away_starters[0]->params;
			foreach($params as $key=>$value) {
				$res.="<th>".$key."</th>";
			}
			$res.="</tr></thead>\n";
			$res.=$this->print_players($this->match->team_away_starters,$this->match->team_away,$this->match->team_home,"START","AWAY",
				$this->match->date);
			$res.=$this->print_players($this->match->team_away_bench,$this->match->team_away,$this->match->team_home,"BENCH","AWAY",
				$this->match->date);
			$res.=$this->print_players($this->match->team_home_starters,$this->match->team_home,$this->match->team_away,"START","HOME",
				$this->match->date);
			$res.=$this->print_players($this->match->team_home_bench,$this->match->team_home,$this->match->team_away,"BENCH","HOME",
				$this->match->date);
			$res.="</table>";
			return $res;
		}

		private function print_players($players,$team,$opponent,$start_bench,$home_away,$date_played) {
			$res="";
			foreach($players as $player) {
				$res.="<tr>\n";
				$res.="<td>$player->name</td>\n";
				$res.="<td>$team</td>\n";
				$res.="<td>$opponent</td>\n";
				$res.="<td>$start_bench</td>\n";
				$res.="<td>$home_away</td>\n";
				$res.="<td>$date_played</td>\n";
				$res.="<td>$player->position</td>\n";
				foreach($player->params as $param) {
					$res.="<td>$param</td>";
				}
				$res.="</tr>\n";
			}
			return $res;
		}
		
		/**
		* Formats a line (passed as a fields  array) as CSV and returns the CSV as a string.
		* Adapted from http://us3.php.net/manual/en/function.fputcsv.php#87120
		*/
		private function arrayToCsv( array &$fields, $delimiter = ';', $enclosure = '"', $encloseAll = false, $nullToMysqlNull = false ) {
			$delimiter_esc = preg_quote($delimiter, '/');
			$enclosure_esc = preg_quote($enclosure, '/');
		
			$output = array();
			foreach ( $fields as $field ) {
				if ($field === null && $nullToMysqlNull) {
					$output[] = 'NULL';
					continue;
				}
		
				// Enclose fields containing $delimiter, $enclosure or whitespace
				if ( $encloseAll || preg_match( "/(?:${delimiter_esc}|${enclosure_esc}|\s)/", $field ) ) {
					$output[] = $enclosure . str_replace($enclosure, $enclosure . $enclosure, $field) . $enclosure;
				}
				else {
					$output[] = $field;
				}
			}
		
			return implode( $delimiter, $output );
		}

	}
	
	include_once("simple_html_dom.php");
	class nba_match_parser
	{
		public $match;
		private $html; //simple_html_dom
		//private $dal;
		
		public function __construct() {
			$this->html=new simple_html_dom();
			//$this->dal=new nba_DAL();
		}
		
		public function parse($matchId) {
			$url="http://espn.go.com/nba/boxscore?gameId=".$matchId;
			$this->html->load_file($url);
			$this->match=new nba_match();
			$this->match->id=$matchId;
			$this->parse_match_date();
			$this->parse_team_names();
			$this->parse_players();
			return $this;
		}
		
		private function parse_match_date() {
			$datestr=$this->html->find(".subhead span a",0)->href;
			$this->match->set_date_from_string($datestr);
		}
		
		private function parse_team_names() {
			$teams=$this->html->find('table.linescore td.team a');
			$this->match->team_away=$teams[0]->plaintext;
			$this->match->team_home=$teams[1]->plaintext;
		}
		
		private function parse_players() {
			$theads=$this->html->find('div#my-players-table div table thead');
			// $theads[0] and $theads[1] - table parts for 1 team;
			// $theads[3] and $theads[4] - table parts for 2 team;
			//parse first team
			$this->match->team_away_starters=$this->parse_table_part($theads[0]);
			$this->match->team_away_bench=$this->parse_table_part($theads[1]);
			$this->match->team_home_starters=$this->parse_table_part($theads[3]);
			$this->match->team_home_bench=$this->parse_table_part($theads[4]);
		}
		
		private function parse_table_part($thead) {
			$players=[];
			$header_rows=$thead->find("tr");
			$header_row=$header_rows[count($header_rows)-1];
			$headers=$header_row->find("th");
			$rows=$thead->next_sibling()->find("tr");
			foreach($rows as $row) {
				$player=new nba_player();
				$tds=$row->find("td");
				$name_pos=$tds[0];
				$player->name=$name_pos->find("a",0)->plaintext;
				$pos_txt=$name_pos->plaintext;
				$space_pos=strrpos($pos_txt," ");
				$pos=substr($pos_txt,$space_pos+1,strlen($pos_txt)-$space_pos);
				$player->position=$pos;
				for($i=1;$i<count($headers);$i++) {
					$key=$headers[$i]->plaintext;
					$value=isset($tds[$i]) ? $tds[$i]->plaintext : "";
					$player->params[$key]=$value;
				}
				$players[]=$player;
			}
			return $players;
		}

		public function toArray($needHeadersRow=true) {
			$res=[];
			if($needHeadersRow) {
				//first elem - headers
				$res[0]=["Player","Team","Opponent","Start/Bench","Home/Away","Date Played","Position"];
				$params=$this->match->team_away_starters[0]->params;
				foreach($params as $key=>$value) {
					$res[0][]=$key;
				}
			}
			$res=array_merge($res,$this->print_players_to_array($this->match->team_away_starters,$this->match->team_away,$this->match->team_home,"START","AWAY",
				$this->match->date));
			$res=array_merge($res,$this->print_players_to_array($this->match->team_away_bench,$this->match->team_away,$this->match->team_home,"BENCH","AWAY",
				$this->match->date));
			$res=array_merge($res,$this->print_players_to_array($this->match->team_home_starters,$this->match->team_home,$this->match->team_away,"START","HOME",
				$this->match->date));
			$res=array_merge($res,$this->print_players_to_array($this->match->team_home_bench,$this->match->team_home,$this->match->team_away,"BENCH","HOME",
				$this->match->date));
			return $res;
		}
		
		
		private function print_players_to_array($players,$team,$opponent,$start_bench,$home_away,$date_played) {
			$res=[];
			foreach($players as $player) {
				$arr=[];
				$arr[]=$player->name;
				$arr[]=$team;
				$arr[]=$opponent;
				$arr[]=$start_bench;
				$arr[]=$home_away;
				$arr[]=$date_played;
				$arr[]=$player->position;
				foreach($player->params as $param) {
					$arr[]=$param;
				}
				$res[]=$arr;
			}
			return $res;
		}
		
		public function toDBRows() {
			$res=array();
			$res=array_merge($res,$this->players_to_DBRows($this->match->id,$this->match->team_away_starters,$this->match->team_away,$this->match->team_home,"START","AWAY",
				$this->match->date));
			$res=array_merge($res,$this->players_to_DBRows($this->match->id,$this->match->team_away_bench,$this->match->team_away,$this->match->team_home,"BENCH","AWAY",
				$this->match->date));
			$res=array_merge($res,$this->players_to_DBRows($this->match->id,$this->match->team_home_starters,$this->match->team_home,$this->match->team_away,"START","HOME",
				$this->match->date));
			$res=array_merge($res,$this->players_to_DBRows($this->match->id,$this->match->team_home_bench,$this->match->team_home,$this->match->team_away,"BENCH","HOME",
				$this->match->date));
			return implode(",",$res);
		}
		
		private function players_to_DBRows($id,$players,$team,$opponent,$start_bench,$home_away,$date_played) {
			$res=[];
			foreach($players as $player) {
				$arr=[];
				$arr[]=$id;
				$arr[]="'".str_replace("'","\'",$player->name)."'";
				$arr[]="'$team'";
				$arr[]="'$opponent'";
				$arr[]="'$start_bench'";
				$arr[]="'$home_away'";
				//$arr[]="'$date_played'";
				$arr[]="'{$player->position}'";
				$arr[]="'".(str_replace("'","\'",json_encode($player->params)))."'";
				
				$res[]="(".implode(",",$arr).")";
			}
			return $res;
		}

		public function toJson() {
			return json_encode($this->match);
		}

	}
	
	class nba_calendar_parser {
		private $dal=null;
		
		public function __construct() {
			$this->dal=new nba_DAL();
		}
		
		public function parseOneSeasonDates($date="2000-01-01") {
			$url="http://site.api.espn.com/apis/site/v2/sports/basketball/nba/scoreboard?calendartype=whitelist&limit=100&dates=";
			$url.=str_replace("-","",$date);
			$jsonstr=file_get_contents($url);
			$json=json_decode($jsonstr);
			$dateparsed=strtotime($date);
			$datefirst=strtotime($json->leagues[0]->calendar[0]);
			$datelast=strtotime($json->leagues[0]->calendar[count($json->leagues[0]->calendar)-1]);
			if(($dateparsed>=$datefirst) && ($dateparsed<=$datelast)) {
				//parsed dates from requested season
				$log=$this->dal->addDates($json->leagues[0]->calendar);
			}
			return $log;
		}
		
		public function parseFromDate($fromDate="2000-01-01") {
			$d=date_parse($fromDate);
			//print_r($d);
			$now=getdate();
			$log=[];
			while($d['year']<=$now['year']+1) {
				$newDate=mktime(0,0,0,$d['month'],$d['day'],$d['year']++);
				$newDateStr=date("Y-m-d",$newDate);
				print_r($newDateStr);
				echo "\n";
				$log[]=$this->parseOneSeasonDates($newDateStr);
			}
			return($log);
		}
	}
	
	class nba_competitions_parser {
		private $dal=null;
		
		public function __construct() {
			$this->dal=new nba_DAL();
		}
		
		public function getNotParsedDates($limit=100) {
			$dates=$this->dal->getNotParsedDates($limit);
			return $dates;
		}
		
		public function competitionsParse($date) {
			$url="http://site.api.espn.com/apis/site/v2/sports/basketball/nba/scoreboard?calendartype=blacklist&limit=100&dates=";
			$url.=str_replace("-","",$date);
			//print_r($url);
			$jsonstr=file_get_contents($url);
			//echo "Url: $url\n";
			//echo "json: ".$jsonstr."\n";
			if($jsonstr===false) {
				return "error while geting page $url";
			}
			$json=json_decode($jsonstr);
			if($json==null) {
				return "error json decode $url";
			}
			$competitions=array();
			$needSetDateParsed=true;
			
			foreach($json->events as $event) {
				$status=$event->status->type->name;
				if(($status!="STATUS_FINAL") && 
						($status!="STATUS_POSTPONED") &&
						($status!="STATUS_CANCELED")) 
				{
					$needSetDateParsed=false; //if match not ended yet, parse this date next time and
					continue;			  // do not save it to DB
				}
				$winner="";
				if($event->competitions[0]->competitors[0]->winner) {
					$winner=$event->competitions[0]->competitors[0]->team->abbreviation;
				} 
				if($event->competitions[0]->competitors[1]->winner) {
					$winner=$event->competitions[0]->competitors[1]->team->abbreviation;
				}
				$competitions[]=[
					$event->id,
					"'$date'",
					$event->season->year,
					$event->season->type,
					"'{$event->competitions[0]->competitors[0]->team->abbreviation}'",
					"'{$event->competitions[0]->competitors[1]->team->abbreviation}'",
					"'{$winner}'",
					0,
					"'".$status."'"
				];
			}
			if(count($competitions)>0) {
				$log=$this->dal->saveCompetitions($competitions);
				if($needSetDateParsed) {
					$this->dal->setDateParsed($date);
				}
			} else {
				$log="Nothing to save";
			}
			return $log;
		}
	}
	
	class nba_match_details_parser {
		private $dal;
		private $parser;
		
		public function __construct() {
			$this->dal=new nba_DAL();
			$this->parser=new nba_match_parser();
		}
		
		public function getNotParsedCompetitions($limit=10) {
			return $this->dal->getNotParsedCompetitions($limit);
		}
		
		public function parse($matchId) {
			$log="$matchId: Start parsing\n";
			try {
				$parsed=$this->parser->parse($matchId)->toDBRows();
				$log.="$matchId: Parsed\n";
				$allSaved=$this->dal->saveMatchDetails($matchId,$parsed);
				if($allSaved) {
					$log.="$matchId: Saved to DB\n";
				}
			} catch(Exception $e) {
				$log.="$matchId: Exception! ".$e->getMessage()."\n";
			}
			return $log;
		}
	}
	
	class nba_match
	{
		public $id="";
		public $date="";
		public $team_away="";
		public $team_home="";
		
		public $team_away_starters=[];
		public $team_away_bench=[];
		public $team_home_starters=[];
		public $team_home_bench=[];

		public function set_date_from_string($str) {
			// /nba/scoreboard?date=20140610
			if(strlen($str)<=8) {
				return;
			}
			$year=substr($str,strlen($str)-8,4);
			$month=substr($str,strlen($str)-4,2);
			$day=substr($str,strlen($str)-2,2);
			if(!is_numeric($year.$month.$day)) {
				return;
			}
			$this->date = $year."-".$month."-".$day;
		}
	}
	
	class nba_player
	{
		public $name="";
		public $position="";
		public $params=[];
	}
?>