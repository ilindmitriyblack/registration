<?php
abstract class AController {
	
	protected $db;
	protected $auth = FALSE;
	protected $user;

	function get_body() {
		$this->db = new Model(HOST,USER,PASS,DB);
		
		$this->db->clearSessions();
		
		$this->user = $this->db->get_user();

		if($this->auth) {
			try {
				if(!$this->user) {
					throw new Exception();
				}
			}
			catch(Exception $e) {
				header("Location:index.php?option=login");
				exit();
			}
			
		}
	}
	
	protected function render($file,$params = array()) {
		extract($params);

		ob_start();

		include('view'.DIRECTORY_SEPARATOR.$file.'.php');

		return ob_get_clean();
	}
	//Функция проверки отправлены ли данные методом GET
	protected function isGet() {
		return $_SERVER['REQUEST_METHOD'] == 'GET';
	}
	
	//Функция проверки отправлены ли данные методом POST
	protected function isPost() {
		return $_SERVER['REQUEST_METHOD'] == 'POST';
	}
}
?>