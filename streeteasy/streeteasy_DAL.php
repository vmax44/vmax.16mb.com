<?PHP
require_once("safemysql.class.php");
class streeteasy_DAL {
	
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
	
	public function addDate($urls, $parsed=FALSE) {
		$data=array('date'=>$date, 'parsed'=>$parsed);
		$sql="INSERT INTO streeteasy SET ?u";
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
									"status='STATUS_FINAL' ORDER BY `date` DESC LIMIT ?i", $limit);
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
	
	public function saveMatchDetails($matchId, $details) {
		$this->db->ping();
		// Save details to players table
		$sql="INSERT INTO players (competition_id,player,`team`,opponent,start_bench,".
			"home_away,position,params) values $details";
		$this->db->query($sql);
		// Update competitions table - set parsed
		$sql="UPDATE competitions SET parsed=TRUE WHERE id='$matchId'";
		$this->db->query($sql);
		
		return true; 
	}
	
	public function setErrorWhileParseMatchDetails($matchId) {
		$this->db->ping();
		$sql="UPDATE competitions SET parsed=2 WHERE id='$matchId'";
		$this->db->query($sql);
	}
	
}