<? include "inc/header.php";?>
	<div id="content">
		<div id="main">
		<?if($user):?>
			<h2>Добро пожаловать - <?=$user['name'];?></h2>
             	<? endif?>
            <p><a href="index.php?option=login">Войти</a></p>
		</div>
<? include "inc/sidebar.php";?>

<? include "inc/footer.php";?>