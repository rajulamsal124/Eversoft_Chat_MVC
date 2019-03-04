<?php

abstract class Model {
	public $template;
	private $db;
	protected $variables;
	protected $table;
	protected $pk;


	/**
	* Initialize the connection
	*/
	public function __construct() {
		$this->db = new Database;
	}

	/**
	* Set data in the variables[$keys], $keys equal to field name in DB
	* @param $data array eg. array("fname" = "Example", "lname" = "Example")
	*/
	protected function setData($data = null) {
		if(!empty($data)) {
			$keys = array_keys($data);
			foreach ($keys as $key) {
				if(strtolower($key) === $this->pk) {
					$this->variables[$this->pk] = $data[$key];
				}
				else {
					$this->variables[$key] = $data[$key];
				}
			}
		}
	}


	public function __get($name) {
		if(is_array($this->variables)) {
			if(array_key_exists($name,$this->variables)) {
				return $this->variables[$name];
			}
		}
		return null;
	}

	public function create() { 
		$bindings = $this->variables;
		if(!empty($bindings)) {
			$fields =  array_keys($bindings);
			$fieldsvals =  array(implode(",",$fields),":" . implode(",:",$fields));
			$sql = "INSERT INTO ".$this->table." (".$fieldsvals[0].") VALUES (".$fieldsvals[1].")";
		}
		else {
			$sql = "INSERT INTO ".$this->table." () VALUES ()";
		}
		return $this->exec($sql);
	}

	public function update($id = "0") {
		$this->variables[$this->pk] = (empty($this->variables[$this->pk])) ? $id : $this->variables[$this->pk];
		$fieldsvals = '';
		$columns = array_keys($this->variables);
		foreach($columns as $column)
		{
			if($column !== $this->pk)
			$fieldsvals .= $column . " = :". $column . ",";
		}
		$fieldsvals = substr_replace($fieldsvals , '', -1);
		if(count($columns) > 1 ) {
			$sql = "UPDATE " . $this->table .  " SET " . $fieldsvals . " WHERE " . $this->pk . "= :" . $this->pk;
			if($id === "0" && $this->variables[$this->pk] === "0") { 
				unset($this->variables[$this->pk]);
				return null;
			}
			return $this->exec($sql);
		}
		return null;
	}

	public function delete($id = "") {
		$id = (empty($this->variables[$this->pk])) ? $id : $this->variables[$this->pk];
		if(!empty($id)) {
			$sql = "DELETE FROM " . $this->table . " WHERE " . $this->pk . "= :" . $this->pk. " LIMIT 1" ;
		}
		return $this->exec($sql, array($this->pk=>$id));
	}

	public function load($id = "") {
		$id = (empty($this->variables[$this->pk])) ? $id : $this->variables[$this->pk];
		if(!empty($id)) {
			$sql = "SELECT * FROM " . $this->table ." WHERE " . $this->pk . "= :" . $this->pk . " LIMIT 1";	
			
			$result = $this->db->row($sql, array($this->pk=>$id));
			$this->variables = ($result != false) ? $result : null;
		}
	}

	public function getPK($data = array()) {
		if(empty($data) && (empty($this->variables) || $this->variables[$this->pk] == null)) {
			return -1;
		}
		if(!empty($data)) {
			$result = $this->search($data);
			if(!empty($result)) {
				return $result[0][$this->pk];
			}else {
				return -1;
			}
		} else {
			return $this->variables[$this->pk];
		}
	}

	/**
	* @param array $fields.
	* @param array $sort.
	* @return array of Collection.
	* Example: $user = new User;
	* $found_user_array = $user->search(array('sex' => 'Male', 'age' => '18'), array('dob' => 'DESC'));
	* // Will produce: SELECT * FROM {$this->table_name} WHERE sex = :sex AND age = :age ORDER BY dob DESC;
	* // And rest is binding those params with the Query. Which will return an array.
	* // Now we can use for each on $found_user_array.
	*/

	public function search($fields = null, $sort = null) {
		$bindings = empty($fields) ? $this->variables : $fields;
		$sql = "SELECT * FROM " . $this->table;
		if (!empty($bindings)) {
			$fieldsvals = array();
			$columns = array_keys($bindings);
			foreach($columns as $column) {
				array_push($fieldsvals, $column . " = :". $column);
			}
			$sql .= " WHERE " . implode(" AND ", $fieldsvals);

		}
		
		if (!empty($sort)) {
			$sortvals = array();
			foreach ($sort as $key => $value) {
				array_push($sortvals, $key . " " . $value);
			}
			$sql .= " ORDER BY " . implode(", ", $sortvals);
		}
		return $this->exec($sql, $bindings);
	}

	public function all(){
		return $this->db->query("SELECT * FROM " . $this->table);
	}

	public function count($field)  {
		if($field)
		return $this->db->single("SELECT count(" . $field . ")" . " FROM " . $this->table);
	}	
	
	private function exec($sql, $array = null) {
		
		if($array !== null) {
			$result =  $this->db->query($sql, $array);	
		}
		else {
			$result =  $this->db->query($sql, $this->variables);	
		}
		$this->variables = array();
		return $result;
	}
	
}

?>