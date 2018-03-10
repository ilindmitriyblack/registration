<?php
class Registration extends AController {
	
	public function get_body() {
		
		parent::get_body();
		
		if(isset($_POST['reg'])) {
	
			$msg = $this->db->registration($_POST);
			
			if($msg === TRUE) {
				$_SESSION['msg'] = "Вы успешно зарегистрировались на сайте. Для подтвержения регистрации,Вам на почту было отправлено письмо с инструкциями."."<br/>"."<a href='index.php?option=login'>Войти на сайт</a>";
			}
			else {
				$_SESSION['msg'] = $msg;
			}
			
			header("Location:?option=registration");
			exit();
		}
		
		return $this->render('registration');
		
	}
}
?>