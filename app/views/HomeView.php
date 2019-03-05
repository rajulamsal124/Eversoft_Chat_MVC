<?php
	
	class HomeView extends View{
		public function __construct($model,$controller) {
			parent::__construct($model,$controller);
		}

		public function render() {
			$template = $this->model->template; 
			$link = $this->model->data['link'];
			$content = $this->model->data['content'];
			echo include_once($template);
		}
	}

?>