
<? include "inc/header.php";?>
	<div id="content">
		<div id="main">
	<h1>Регистрация</h1>
	<?=$_SESSION['msg'];?>
	<? unset($_SESSION['msg']);?>
		<form method='POST'>
		Логин<br>
			<input type='text' name='reg_login' action="?option=registration" value="<?=$_SESSION['reg']['login'];?>">
		<br>
		Пароль<br>
			<input type='password' name='reg_password'>
		<br>
		Подтвердите пароль<br>
			<input type='password' name='reg_password_confirm'>
		<br>
		Почта<br>
			<input type='text' name='reg_email' value="<?=$_SESSION['reg']['email'];?>">
		<br>
		Имя<br>
			<input type='text' name='reg_name' value="<?=$_SESSION['reg']['name'];?>">
		<br>
		<input style="float:left" type='submit' name='reg' value='Регистрация'>
	</form>
	</div>
<? include "inc/sidebar.php";?>		
	
<? include "inc/footer.php";?>

<? unset($_SESSION['reg']); ?>
