<?php

abstract class Controller {
	protected $model;
	protected $view;

	public function __construct() {
		$this->model = $this->setModel(get_class($this)."Model");
		$this->view = $this->setView(get_class($this)."View", $this->model, $this);
	}

	protected function setModel($model) {
		require_once '../app/models/'.$model.'.php';
		return new $model();
	}

	protected function setView($view, $model,$controller) {
		require_once '../app/views/'.$view.'.php';
		return new $view($model,$controller);
	}

}

?>