<?PHP
require_once("safemysql.class.php");
class astro_DAL {
	
	private $db=null;
	
	public function __construct(){
		$options=array(
			'host' => 'mysql.hostinger.ru',
			'user' => 'u434585109_nba',
			'pass' => 'nba11111',
			'db'   => 'u434585109_nba'	
		);
		$this->db=new SafeMySQL($options);
	}
	
	public function getLastUrlName() {
		$sql="SELECT `url_name` FROM `astro` ORDER BY id DESC LIMIT 1";
		return $this->db->getOne($sql);
	}
	
	public function saveUrls($data) {
		$val=[];
		foreach($data as $d) {
			$val[]=$this->db->parse("(?s,?s,FALSE)",$d[0],$d[1]);
		}
		//print_r($val);
		$values=implode(",",$val);
		//echo "values: $values\n";
		$sql="INSERT IGNORE INTO astro (url, url_name, parsed) VALUES ?p";
		$this->db->query($sql,$values);
		return $this->db->mysqlInfo();
	}
	
	
	
	
	public function addDate($date, $parsed=FALSE) {
		$data=array('date'=>$date, 'parsed'=>$parsed);
		$sql="INSERT INTO calendar SET ?u";
		$this->db->query($sql,$data);
		return $this->db->mysqlInfo();
	}
	
	public function addDates($dates) {
		$rows=array();
		foreach($dates as $d) {
			$rows[]="('$d')";
		};
		$values=implode(",",$rows);
		$sql="INSERT IGNORE INTO calendar (date) VALUES $values";
		$this->db->query($sql);
		return $this->db->mysqlInfo();
	}
	
	public function getNotParsedDates($limit=100) {
		$result=$this->db->getCol("SELECT date FROM calendar where parsed=0 AND date<NOW() LIMIT ?i",
			$limit);
		return $result;
	}
	
	public function getNotParsedCompetitions($limit=10) {
		$result=$this->db->getCol("SELECT id FROM competitions where parsed=false AND ".
									"status='STATUS_FINAL' LIMIT ?i", $limit);
		return $result;
	}
	
	public function saveCompetitions($comps) {
		$rows=array();
		foreach($comps as $comp) {
			$rows[]="(".implode(",",$comp).")";
		}
		$values=implode(",",$rows);
		$sql="INSERT IGNORE INTO competitions values $values";
		$this->db->query($sql);
		return $this->db->mysqlInfo();
	}
	
	public function setDateParsed($date) {
		$sql="UPDATE calendar SET parsed=TRUE WHERE date='$date'";
		$this->db->query($sql);
		return "setDateParsed: ".$this->db->mysqlInfo();
	}
	
	public function saveMatchDetails($details) {
		$sql="INSERT INTO players (competition_id,player,team,opponent,start_bench,home_away,position,params) ".
			"values $details";
		$this->db->query($sql);
		return false; 
	}
	
}