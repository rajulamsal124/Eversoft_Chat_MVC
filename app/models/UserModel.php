<?php

class UserModel extends model{

	public function __construct() {
		$this->table = 'userDetails';
		$this->pk = 'id';

		$this->template = 'user/index.php';

		parent::__construct();

		var_dump($this->login($data = array('uname' => "dhpradip", 'pwd' => 'myPass2')));
	}

	public function registerUser($data) {
		/*$data = array('id'=> NULL, 'uname' => "saroj22322", 'pwd' => "myPass", 'fname' => "Saroj",
				'lname' => "Tripathi", 'dob' => NULL, 'email'=>"sar.vhanta@gmail.com", 'phno' => "980225637",
				'age'=> 21, 'ubio' => "I am a student.");
		$data2 = array('id'=> NULL, 'uname' => "dhpradip", 'pwd' => "myPass2", 'fname' => 'Pradip',
				'lname' => "Dhakal", 'dob' => NULL, 'email'=>"dhpradip@gmail.com", 'phno' => "9856678594",
				'age'=> 21, 'ubio' => "I am a programmer.");*/
		$this->setData($data);
		return $this->create();
	}

	public function searchUser($data, $sort = null) {
		//$data = array('uname' => "dhpradip");
		return $this->search($data, $sort);
	}

	public function getUserID($data = null) {
		//$data = array('uname' => "dhpradip");
		return $this->getPK($data);
	}

	public function deleteUser($id) {
		//$id = 1;
		 return $this->delete($id);
	}

	public function login($data) {
		//$data = array('uname' => "dhpradip", 'pwd' => 'myPass2');
		if((isset($data['uname']) || isset($data['email'])) && isset($data['pwd'])) {
			return $this->getPK($data);
		}		
	}
}

?>