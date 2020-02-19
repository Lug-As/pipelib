<?php
	require_once 'include/functions.php';

	$DB_err = good_cookie("DB_err");
	$not_registred = good_cookie("not_registred");
	$wrong_password = good_cookie("wrong_password");
?>
<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Вход | Pipe Library</title>
	<link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
	<link rel="stylesheet" href="css/style.css">
</head>
<body>

	<div class="container">
		<div class="row">
			<div class="col-md-4 offset-md-4">
				<div class="center-block">
					<div class="form form_center main-content">
						<form action="handler/login-handler.php" method="POST">
							<h2 class="form-header">Вход</h2>
							<input class="form-input" name="login" type="text" placeholder="Ваш логин" required>
							<input class="form-input" name="password" type="password" placeholder="Ваш пароль" required>
							<button class="btn btn-primary form-btn form-input">Войти в аккаунт</button>
						</form>
						<?php
						if ($DB_err) {
							echo "<pre>Произошла ошибка. Попробуйте ещё раз</pre>";
						}
						if ($not_registred) {
							echo "<pre>Вы не зарегистрированы</pre>";
						}
						if ($wrong_password) {
							echo "<pre>Неправильный пароль</pre>";
						}
						?>
						<a href="/signup.php">Зарегестрироваться</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
	<script src="js/script.min.js"></script>
</body>
</html>