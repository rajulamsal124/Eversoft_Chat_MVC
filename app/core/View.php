<?php

abstract class View {
	protected $model;
	protected $controller;

	public function __construct($model,$controller) {
		$this->model = $model;
		$this->controller = $controller;
	}

	public function __get($name) {
		if(!empty($name)) {
			return $this->model->data[$name];
		} else {
			return null;
		}
	}
}

?>