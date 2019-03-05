<?php
	
	class HomeView extends View{
		public function __construct($model,$controller) {
			parent::__construct($model,$controller);
		}

		public function render() {
			$template = $this->model->template;
			include_once($template);
		}
	}

?>