<?
header("Content-Type: text/html;charset=utf-8");
include "inc/header.php";
;?>
	<div id="content">
		<div id="main">
			<h1>Авторизируйтесь</h1>
			<?=$_SESSION['msg'];?>
			<? unset($_SESSION['msg'])?>
				<form method='POST' action="?option=login">
				<label>
				login<br>
					<input type='text' name='login'>
				</label><br>
				Password<br>
				<label>
					<input type='password' name='password'>
				</label><br>
				<label>Member
					<input type="checkbox" name='member' value="1">
				</label><br>
				<input style="float:left" type='submit' value='Вход'>
			</form>
			<p style="clear:both">
				<a href="?option=registration">Регистрация</a>
			</p>		
		</div>
	<? include "inc/sidebar.php";?>		
	
<? include "inc/footer.php";?>	
