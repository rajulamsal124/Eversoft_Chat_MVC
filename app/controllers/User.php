<?php

class User extends Controller {

	public function __construct() {		
		parent::__construct();
	}

	public function index($name = '') {
		$this->view->render();
	}

}

?>