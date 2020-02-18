<?php
	require_once '../include/config.php';
	require_once '../include/functions.php';

	$conn = connect();

	if (exist($_POST['username'])) {
		$username = $_POST['username'];
		$login = $_POST['login'];
		$raw_password = $_POST['password'];

		# Шифрование пароля
		$password = password_hash($raw_password, PASSWORD_DEFAULT);
		$hash = generate_hash();

		# Проверка логина на совпадение
		$read = mysqli_query($conn, "SELECT * FROM `users` WHERE `login` = '$login'");
		if (mysqli_num_rows($read) > 0) {
			setcookie('not', TRUE, time()+100, "/");
			header('Location: /signup.php');
			close($conn);
			exit;
		}

		# Регистрация нового пользователя
		$insert = mysqli_query($conn, "INSERT INTO `users` (`username`, `login`, `password`, `hash`) VALUES ('$username', '$login', '$password', '$hash')");

		$last_id = mysqli_query($conn, "SELECT `id` FROM `users` ORDER BY `id` DESC LIMIT 1");
		$user_id = mysqli_fetch_assoc($last_id)['id'];

		if ($insert == FALSE) {
			setcookie('err', TRUE, time()+100);
			header('Location: /signup.php');
			close($conn);
			exit;
		} else {
			setcookie("user_hash", $hash, time()+30*24*60*60, "/", NULL, NULL, TRUE);
			setcookie("user_id", $user_id, time()+30*24*60*60, "/");
			header('Location: /');
			close($conn);
			exit;
		}
	}
?>