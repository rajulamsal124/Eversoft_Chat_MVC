<?php

class HomeModel extends Model{
	public function __construct() {
		$this->template = 'home/dashboard.php';
		$this->data = array('content' => "Hello from Dashboard", 'link' => 'user');
	}
}
