<?php

abstract class Model {
	public $template;
	protected $db;
	protected $table;
	protected $fields = array();

	public function __construct() {
		$this->db = new Database;
	}
	
}

?>