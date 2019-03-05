<?php

class User extends Controller {

	public function __construct() {		
		parent::__construct();
	}

	public function index($name = '') {
		$this->login();
	}

	public function login($name = '') {
		//do logic check and change template of model
		// or redirect to dashboard

		$this->model->template = 'user/login.php';
		$this->model->data = array('content' => 'Hello from login', 'link' => 'home');
		$this->view->render();
	}

	public function register() {
		//do logic check and change template of model
		// or redirect to login
		$error = array();

		$validator = new Validator();
		$validation = $validator->check($_GET, array(
			'email'=> array(
				'required'=> true,
				'min' => 5,
				'max' => 7,
				'type' => 'PHNumber' #type => email/number/PHNumber/date
			)
		));
		if ($validator->passed()) {
			echo $validator->passed();
		} else {
			foreach ($validator->errors() as $err) {
		 		array_push($error, $err);
		 	}
		 }
		 var_dump($error);

		$this->model->template = 'user/register.php';
		$this->model->data = array('content' => 'Hello from register', 'link' => 'home');
		$this->view->render();
	}

}

?>