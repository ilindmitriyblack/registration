<?php

class Model {
	public $db;
	
	public function __construct($host,$user,$pass,$db) {
		$this->db = mysql_connect($host,$user,$pass);
		if(!$this->db) {
			exit('No connection with database');
		}
		if(!mysql_select_db($db,$this->db)) {
			exit('No table');
		}
		
		mysql_query("SET NAMES utf8");
		
		return $this->db;
	}

    function registration($post) {
		
		$login = $this->clean_data($post['reg_login']);
		$password = trim($post['reg_password']);
		$conf_pass= trim($post['reg_password_confirm']);
		$email = $this->clean_data($post['reg_email']);
		$name = $this->clean_data($post['reg_name']);
		
		$msg = '';
		
		if(empty($login)) {
			$msg .= "Введите логин <br />";
		}
		if(empty($password)) {
			$msg .= "Введите пароль <br />";
		}
		if(empty($email)) {
			$msg .= "Введите адресс почтового ящика <br />";
		}
		if(empty($name)) {
			$msg .= "Введите имя <br />";
		}
		
		if($msg) {
			$_SESSION['reg']['login'] = $login;
			$_SESSION['reg']['email'] = $email;
			$_SESSION['reg']['name'] = $name;
			return $msg;
		}
		
		if($conf_pass == $password) {
			$sql = "SELECT user_id
					FROM users
					WHERE login='%s'";
			$sql = sprintf($sql,mysql_real_escape_string($login));
			
			$result = mysql_query($sql);
			
			if(mysql_num_rows($result) > 0) {
				$_SESSION['reg']['email'] = $email;
				$_SESSION['reg']['name'] = $name;
				
				return "Пользователь с таким логином уже существует";
			}
					
			$password = md5($password);
			$hash = md5(microtime());
			
			$query = "INSERT INTO users (
						name,
						email,
						password,
						login,
						hash
						) 
					VALUES (
						'%s',
						'%s',
						'%s',
						'%s',
						'$hash'
					)";
			$query = sprintf($query,
								mysql_real_escape_string($name),
								mysql_real_escape_string($email),
								$password,
								mysql_real_escape_string($login)
							);
			$result2 = mysql_query($query);
			
			if(!$result2) {
				$_SESSION['reg']['login'] = $login;
				$_SESSION['reg']['email'] = $email;
				$_SESSION['reg']['name'] = $name;
				return "Ошибка при добавлении пользователя в базу данных".mysql_error();
			}
			else {
				$headers = '';
				$headers .= "From: Admin <ilindmitriyblack@gmail.com> \r\n";
				$headers .= "Content-Type: text/plain; charset=utf8";
				
				$theme = "registration";
				
				$mail_body = "Спасибо за регистрацию на сайте. Ваша ссылка для подтверждения  учетной записи: http://registration/controller/?option=confirm&hash=".$hash;
				
				mail($email,$theme,$mail_body,$headers);
				
				return TRUE;
				
			}								
		}
		else {
			$_SESSION['reg']['login'] = $login;
			$_SESSION['reg']['email'] = $email;
			$_SESSION['reg']['name'] = $name;
			return "Вы не правильно подтвердили пароль";
		}
		
	}
	
	
	function confirm() {
		
		$new_hash = $this->clean_data($_GET['hash']);
		
		$query = "UPDATE users
					SET confirm='1'
					WHERE hash = '%s'
					";
		$query = sprintf($query,mysql_real_escape_string($new_hash));	
		
		$result = mysql_query($query);
		
		if(mysql_affected_rows() == 1) {
			return TRUE;
		}
		else {
			return "Не верный код подтверждения регистрации";
		}	
}

	function clean_data($str) {
		return strip_tags(trim($str));
	}

	public function get_user() {
		if(isset($_SESSION['sess'])) {
			$sess = $_SESSION['sess'];
			
			$time_last = time();
			
			$sql = "UPDATE sessions SET time_last='$time_last' WHERE sess='%s'";
			$sql = sprintf($sql,mysql_real_escape_string($sess));
			
			
			$result = mysql_query($sql);
			if(!$result) {
				$_SESSION['msg'] = "ERROR database".mysql_error();
				return FALSE;
			}
			
			if(mysql_affected_rows() !== 1) {
				$query = "SELECT count(*) FROM sessions WHERE sess='%s'";
				$query = sprintf($query,mysql_real_escape_string($sess));
				
				$result1 = mysql_query($query);
					if(!$result1) {
						$_SESSION['msg'] = "ERROR database".mysql_error();
						return FALSE;
					}
				$row = mysql_fetch_row($result1);
				if($row[0] == 0 ) {
					return FALSE;
				}	
			}
			
			$sql2 = "SELECT 
						sessions.user_id,
						users.login,
						users.password,
						users.name
						FROM sessions
					LEFT JOIN users
						ON users.user_id=sessions.user_id
					WHERE sessions.sess = '$sess'			
					";
			
			$result2 = mysql_query($sql2);		
			if(!$result2) {
					$_SESSION['msg'] = "ERROR database".mysql_error();
					return FALSE;
			}
			
			if(mysql_num_rows($result2) == 0) {
				return FALSE;
			}
			
			return mysql_fetch_assoc($result2);		
			
		}
		elseif(isset($_COOKIE['login']) && isset($_COOKIE['password'])) {
			$user = $this->getLogin($_COOKIE['login']);
			
			if(!$user) {
				return FALSE;
			}
			
			if($user['password'] !== $_COOKIE['password']) {
				return FALSE;
			}
			
			$sess = $this->openSession($user['user_id']);
			
			if($sess) {
				return $user;
			}
		}
		else {
			return FALSE;
		}
	}
	
	public function login($login,$password,$member) {
		
		if(empty($login) || empty($password)) {
			$_SESSION['msg'] = "Все поля нужно заполнить";
			return FALSE;
		}
		
		$password = md5($password);
		
		$user = $this->getLogin($login);
		
		if(!$user) {
			return FALSE;
		}
		
		if($user['password'] !== $password) {
			$_SESSION['msg'] = "Не верный пароль";
			return FALSE;
		}
		
		if($member == '1') {
			$expire = time() + 3600*24*30;
			
			setcookie('login',$login,$expire);
			setcookie('password',$password,$expire);
		}
		
		$id_user = $user['user_id'];
		
		$sess = $this->openSession($id_user);
		
		if(!$sess) {
			$_SESSION['msg'] = "Не удалось авторизировать пользователя";
			return FALSE;
		}
		
		return TRUE;
		
	}
	
	public function getLogin($login) {
		$sql = "SELECT user_id,
						login,
						password,
						users.name
						FROM users
						WHERE login='%s'		
						";
		$sql = sprintf($sql,mysql_real_escape_string($login));
		
		$result = mysql_query($sql);
		
		if(!$result) {
			$_SESSION['msg'] = "ERROR database".mysql_error();
			return FALSE;
		}
		
		if(mysql_num_rows($result) !== 1) {
			$_SESSION['msg'] = "Такого пользователя нет";
			return FALSE;
		}
		
		return mysql_fetch_assoc($result);
						
	}
	public function openSession($id) {
		$id = (int)$id;
		
		$sess = $this->generateStr();
		
		$time_start = time();
		$time_last = $time_start;
		
		$sql = "INSERT INTO sessions
				(user_id,sess,time_start,time_last)
				VALUES
				('$id','$sess','$time_start','$time_last')";
		$result = mysql_query($sql);
		
		if(!$result) {
			$_SESSION['msg'] = "ERROR database".mysql_error();
			return FALSE;
		}
		
		$_SESSION['sess'] = $sess;
	
		return $sess;		
	}
	
	public function generateStr() {
		$str = '23456789abcdefghijkmnpqrstuvwxyzABCDEFGHIJKMNPQRSTUVWXYZ';
			$rand = '';
			
			for($i = 0; $i < 15;$i++) {
				$x = mt_rand(0,(strlen($str)-1));
				
				//исключаем сопадение двух одинаковых символов идущих друг за другом
				if($i != 0) {
			
					if($rand[strlen($rand)-1] == $str[$x]) {
					$i--;
					continue;
					}
				}
				$rand .= $str[$x];
			}
		return $rand;	
	}
	
	public function logout() {
		setcookie('login',"",time()-3600);
		setcookie('password',"",time()-3600);
		
		unset($_SESSION['sess']);
	}
	
	public function clearSessions() {
		$time = time() - 20*60;
		
		$sql = "DELETE FROM sessions WHERE time_last < '$time'";
		
		$result = mysql_query($sql);
		
		if(!$result) {
			return FALSE;
		} 
		
		return TRUE;
	}
	
}

?>