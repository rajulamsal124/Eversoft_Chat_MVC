<?php


class Database {

	private $_host = "localhost";
	private $_user = "root";
	private $_password = "";
	private $_name = "eversoft";
	
	//PDO Object
	private $conn = null;

	//Connection status
	private $connected = false;
	
	//PDO Statement Object Query
	private $query = null;
	
	//Affected rows from last query	
	private $affectedRows = 0;

	//Parameters of SQL Query
	private $parameters;
	

	public $id;
	
	/*
	* This is construction method and will be connected
	*/
	public function __construct() {
		$this->connect();
	}
	
	/*
	* This method is used to connect to database.
	*/
	private function connect() {
		try {
			//Connecting using PDO
			$this->conn = new PDO("mysql:host=$this->_host;dbname=$this->_name", $this->_user, $this->_password, array(
			            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
			        ));
			//Enable logging in fatal errors.
			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			//Disable emulation of prepared statements, use real prepared statements instead.
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		}
		catch(PDOException $e)
		{
			die("Connection failed: " . $e->getMessage());
		}
	}

	//Close the connection
	public function close() {
		if(!$this->conn == null) {
			$this->conn = null;
		}
	}

	/**
     *	Every method which needs to execute a SQL query uses this method.
     *	
     *	1. If not connected, connect to the database.
     *	2. Prepare Query.
     *	3. Parameterize Query.
     *	4. Execute Query.	
     *	5. On exception : Write Exception into the log + SQL query.
     *	6. Reset the Parameters.
     */
	private function init($query, $parameters = "")
    {
        if (!$this->conn) {
            $this->connect();
        }
        try {
            // Prepare query
            $this->query = $this->conn->prepare($query);
            
            // Add parameters to the parameter array	
            $this->bindMore($parameters);
            
            // Bind parameters
            if (!empty($this->parameters)) {
                foreach ($this->parameters as $param => $value) {
                    if(is_int($value[1])) {
                        $type = PDO::PARAM_INT;
                    } else if(is_bool($value[1])) {
                        $type = PDO::PARAM_BOOL;
                    } else if(is_null($value[1])) {
                        $type = PDO::PARAM_NULL;
                    } else {
                        $type = PDO::PARAM_STR;
                    }
                    // Add type when binding the values to the column
                    $this->query->bindValue($value[0], $value[1], $type);
                }
            }
            
            // Execute SQL 
            $this->query->execute();
        }
        catch (PDOException $e) {
            die("Error : ". $e->getMessage());
        }
        
        // Reset the parameters
        $this->parameters = array();
    }

    /*	Add the parameter to the parameter array
     *	@param string $para  
     *	@param string $value 
     */
    public function bind($para, $value)
    {
    	if(!empty($this->parameters)) {
        	array_push($this->parameters, [":" . $para , $value]);
    	}
    	else {
    		$this->parameters = array([":" . $para , $value]);
    	}
    }

    /*
     *	Add more parameters to the parameter array
     *	@param array $parray
     */
    public function bindMore($parray)
    {
        if (empty($this->parameters) && is_array($parray)) {
            $columns = array_keys($parray);
            foreach ($columns as $i => &$column) {
                $this->bind($column, $parray[$column]);
            }
        }
    }


    /**
    * Run the query.
    * @return if query contains a SELECT or SHOW statement it returns an array containing all of the result set row
    * @return if the SQL statement is a DELETE, INSERT, or UPDATE statement it returns the number of affected rows
    * @param $query string of query
    * @param params array of parameter
    * @param fetchmode PDO fetchmode (optional)
    */
    public function query($query, $params = null, $fetchmode = PDO::FETCH_ASSOC)
    {
        $query = trim(str_replace("\r", " ", $query));
        
        $this->init($query, $params);
        
        $rawStatement = explode(" ", preg_replace("/\s+|\t+|\n+/", " ", $query));
        
        // Which SQL statement is used 
        $statement = strtolower($rawStatement[0]);
        
        if ($statement === 'select' || $statement === 'show') {
            return $this->query->fetchAll($fetchmode);
        } elseif ($statement === 'insert' || $statement === 'update' || $statement === 'delete') {
            return $this->query->rowCount();
        } else {
            return NULL;
        }
    }


     /**
     *  Returns the last inserted id.
     *  @return string
     */
    public function lastInsertId()
    {
        return $this->conn->lastInsertId();
    }
    
    /**
     * Starts the transaction
     * @return boolean, true on success or false on failure
     */
    public function beginTransaction()
    {
        return $this->conn->beginTransaction();
    }
    
    /**
     *  Execute Transaction
     *  @return boolean, true on success or false on failure
     */
    public function executeTransaction()
    {
        return $this->conn->commit();
    }
    
    /**
     *  Rollback of Transaction
     *  @return boolean, true on success or false on failure
     */
    public function rollBack()
    {
        return $this->conn->rollBack();
    }
    
    /**
     *	Returns an array which represents a column from the result set 
     *
     *	@param  string $query
     *	@param  array  $params
     *	@return array
     */
    public function column($query, $params = null)
    {
        $this->Init($query, $params);
        $Columns = $this->sQuery->fetchAll(PDO::FETCH_NUM);
        
        $column = null;
        
        foreach ($Columns as $cells) {
            $column[] = $cells[0];
        }
        
        return $column;
        
    }

    /**
     *	Returns an array which represents a row from the result set 
     *
     *	@param  string $query
     *	@param  array  $params
     *  @param  int    $fetchmode
     *	@return array
     */
    public function row($query, $params = null, $fetchmode = PDO::FETCH_ASSOC)
    {
        $this->init($query, $params);
        $result = $this->query->fetch($fetchmode);
        $this->query->closeCursor(); // Frees up the connection to the server so that other SQL statements may be issued,
        return $result;
    }

    /**
     *	Returns the value of one single field/column
     *
     *	@param  string $query
     *	@param  array  $params
     *	@return string
     */
    public function single($query, $params = null)
    {
        $this->init($query, $params);
        $result = $this->query->fetchColumn();
        $this->query->closeCursor(); // Frees up the connection to the server so that other SQL statements may be issued
        return $result;
    }




}
?>