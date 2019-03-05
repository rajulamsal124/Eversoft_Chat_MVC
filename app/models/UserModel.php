<?php

class UserModel extends Model{

	public function __construct() {
		// tablename for the model
		$this->table = 'userDetails';
		// primary key for the table
		$this->pk = 'id';

		$this->template = 'user/login.php';
		$this->data = array('content' => "Hello from User", 'link' => 'home');

		parent::__construct();
		//$this->deleteUser(2);
		/*$data = array('id'=> NULL, 'uname' => "saroj22322", 'pwd' => "myPass", 'fname' => "Saroj",
				'lname' => "Tripathi", 'dob' => NULL, 'email'=>"sar.vhanta@gmail.com", 'phno' => "980225637",
				'age'=> 21, 'ubio' => "I am a student.");*/
		/*$data2 = array('id'=> NULL, 'uname' => "dhpradip", 'pwd' => "myPass2", 'fname' => 'Pradip',
				'lname' => "Dhakal", 'dob' => NULL, 'email'=>"dhpradip@gmail.com", 'phno' => "9856678594",
				'age'=> 21, 'ubio' => "I am a programmer.");*/
		//$this->registerUser($data2);
	}

	/**
	* @param $data : array
	* @return first id with given data.
	* @return 0 if fail
	*/
	public function getUserID($data) {
		return $this->getPk($data);
	}

	/**
	* This method register user with given data
	* @param $data : array
	* @return 1 on success
	* @return 0 on failure
	*/
	public function registerUser($data) {
		$this->setData($data);
		return $this->create();
	}

	/**
	* This method search user with given data
	* @param $data : array
	* @param $sort : array
	* @return array with all matching records
	*/
	public function searchUser($data, $sort = null) {
		return $this->search($data, $sort);
	}

	/**
	* This method delete user with given id
	* @param $id : num
	* @return 1 on success
	* @return 0 on failure
	*/
	public function deleteUser($id) {
		 return $this->delete($id);
	}


	/**
	* This method login user with given data
	* @param $data : array with 'email' or 'usname' and 'pwd'
	* @return id on success
	* @return 0 on failure
	*/
	public function login($data) {
		if((isset($data['uname']) || isset($data['email'])) && isset($data['pwd'])) {
			return $this->getPk($data);
		}		
	}

	/**
	* This method update user with given id
	* @param $id : num
	* @param $data : array -> data to update
	* @return 1 on success
	* @return 0 on failure
	*/
	public function updateUser($id, $data) {
		$this->setData($data);
		return $this->update($id);
	}
}

?>