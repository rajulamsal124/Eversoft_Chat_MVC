<?php

class Home extends Controller {

	public function __construct() {
		parent::__construct();
	}

	public function index($name = '') {
		//do logic check and change template of model
		// or redirect to login

		$this->model->template = 'home/dashboard.php';
		$this->model->data = array('content' => "Hello from Dashboard", 'link' => 'user');
		$this->view->render();
	}

}

?>