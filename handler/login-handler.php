<?php
	require_once '../include/config.php';
	require_once '../include/functions.php';

	$conn = connect();

	if (exist(trim($_POST['login']))) {
		$login = trim($_POST['login']);
		$password = trim($_POST['password']);
		$query = mysqli_query($conn, "SELECT * FROM `users` WHERE `login` = '$login'");
		if ($query == FALSE) {
			setcookie("DB_err", 1, time()+30, "/");
			header("Location: /login.php");
			exit;
		} elseif (mysqli_num_rows($query) == 0) {
			setcookie("not_registred", 1, time()+30, "/");
			header("Location: /login.php");
			exit;
		} else $user_data = mysqli_fetch_assoc($query);

		if (FALSE == password_verify($password, $user_data['password'])) {
			setcookie("wrong_password", 1, time()+30, "/");
			header("Location: /login.php");
			exit;
		} else {
			$hash = generate_hash();
			$hash_update = mysqli_query($conn, "UPDATE `users` SET `hash` = '$hash' WHERE `id`=".$user_data['id']);
			setcookie("user_hash", $hash, time()+30*24*60*60, "/", NULL, NULL, TRUE);
			setcookie("user_id", $user_data['id'], time()+30*24*60*60, "/");
			header("Location: /");
			close($conn);
			exit;
		}
	}
?>