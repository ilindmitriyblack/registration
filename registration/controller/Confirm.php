<?php
class Confirm extends AController {
	
	public function get_body() {
		
		parent::get_body();
		
		if($_GET['hash']) {
	
			$confirm = $this->db->confirm();
			
			if($confirm === TRUE) {
				$confirm = "Ваша учетная запись активирована. Можете авторизироваться нга сайте.";
			}
			}
			else {
				$error = "Неверная ссылка";
		}
		
		
		return $this->render('confirm',array('confirm' => $confirm,'error' => $error));
		
	}
}
?>