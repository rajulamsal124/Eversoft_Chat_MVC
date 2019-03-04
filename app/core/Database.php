<?php
class Database {

	private $_host = "localhost";
	private $_user = "root";
	private $_password = "";
	private $_name = "eversoft";
	
	private $conn = null;
	public $lastQuery = null;
	public $affectedRows = 0;
	
	public $insertKeys = array();
	public $insertValues = array();
	public $updateSets = array();
	
	public $id;
	
	
	public function __construct() {
		$this->connect();
	}
	
	private function connect() {
		try {
			$this->conn = new PDO("mysql:host=$_host;dbname=$_name", $_user, $_password);
			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch(PDOException $e)
		{
			die("Connection failed: " . $e->getMessage());
		}
	}

	public function close() {
		if(!$this->conn == null) {
			$this->conn = null;
		}
	}
}
?>