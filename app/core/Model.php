<?php
/*
* Model class
*/
abstract class Model {
	//template for the view
	public $template;
	// data for view
	public $data;

	private $db;
	// array to hold information for SQL
	protected $variables;
	
	protected $table;
	protected $pk;


	/**
	* Initialize the connection
	*/
	public function __construct() {
		// connection with database
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

	/**
	* This method help to avoid error and get the value of field with variable name equals fieldname
	* @param $name : string 
	* eg. in childModel : $this->$id;
	*/
	public function __get($name) {
		if(is_array($this->variables)) {
			if(array_key_exists($name,$this->variables)) {
				return $this->variables[$name];
			}
		}
		return null;
	}

	/**
	* This method insert the data set from $variables to table in $table.
	* @return last inserted id on success or 0 on failure.
	*/
	public function create() { 
		$bindings = $this->variables;
		if(!empty($bindings)) {
			$fields =  array_keys($bindings);
			$fieldsvals =  array(implode(",",$fields),":" . implode(",:",$fields));
			$sql = "INSERT INTO ".$this->table." (".$fieldsvals[0].") VALUES (".$fieldsvals[1].")";
			if($this->exec($sql) == 1) {
				return $this->db->lastInsertId();
			}
		}
		return 0;
	}

	/**
	* This method update the data from $variables with help of pk (id)
	* @param $id 
	* @return number of affected rows
	*/
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

	/**
	* This method delete the data from $variables with help of pk (id)
	* @param $id 
	* @return number of affected rows
	*/
	public function delete($id = "") {
		$id = (empty($this->variables[$this->pk])) ? $id : $this->variables[$this->pk];
		if(!empty($id)) {
			$sql = "DELETE FROM " . $this->table . " WHERE " . $this->pk . "= :" . $this->pk. " LIMIT 1" ;
		}
		return $this->exec($sql, array($this->pk=>$id));
	}

	/**
	* This method load the data to $variables with help of pk (id)
	* @param $id 
	* @return array of data
	*/
	public function load($id = "") {
		$id = (empty($this->variables[$this->pk])) ? $id : $this->variables[$this->pk];
		if(!empty($id)) {
			$sql = "SELECT * FROM " . $this->table ." WHERE " . $this->pk . "= :" . $this->pk . " LIMIT 1";	
			
			$result = $this->db->row($sql, array($this->pk=>$id));
			$this->variables = ($result != false) ? $result : null;
		}
	}


	/**
	* This method return the first Primary key with given conditions($data)
	* @param $data
	* @return pk
	*/
	public function getPk($data = array()) {
		if(empty($data) && (empty($this->variables) || $this->variables[$this->pk] == null)) {
			return 0;
		}
		if(!empty($data)) {
			$result = $this->search($data);
			if(!empty($result)) {
				return $result[0][$this->pk];
			}else {
				return 0;
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
	* $found_user_array = $user->search(array('uname' => 'Male', 'age' => '18'), array('dob' => 'DESC'));
	* Will produce: SELECT * FROM {$this->table_name} WHERE sex = :sex AND age = :age ORDER BY dob DESC;
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

	/**
	* This method return whole table as array.
	* @return whole table as array.  
	*/
	private function all(){
		return $this->db->query("SELECT * FROM " . $this->table);
	}

	/**
	* This method count number of datas in the field.
	* @param $field : string eg. 'id'
	* @return if field found number of data else 0;
	*/
	public function count($field)  {
		if($field)
			return $this->db->single("SELECT count(" . $field . ")" . " FROM " . $this->table);
		else 
			return 0;
	}	
	
	/**
	* This method is used in this class to execute query.
	* @param $sql : string -> SQL command
	* @param $array : array -> Array with variables to bind 
	* @return returns number of affected rows for UPDATE, INSERT and DELETE
	* @return returns array (mode : ASSOC) for SELECT and SHOW
	*/
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