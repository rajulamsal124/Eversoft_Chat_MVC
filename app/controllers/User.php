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
		$this->model->template = 'user/register.php';
		$this->model->data = array('content' => 'Hello from register', 'link' => 'home');
		$this->view->render();
	}

}

?>