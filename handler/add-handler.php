<?php
	require_once '../include/config.php';
	require_once '../include/functions.php';

	$conn = connect();

	if (exist(trim($_POST['mapname']))) {
		$author_id = $_POST['author_id'];
		$select = mysqli_query($conn, "SELECT `admin` FROM `users` WHERE `id` = ".$author_id);
		if ($select) {
			$result = mysqli_fetch_assoc($select);
			$admin = $result['admin'];
		}
		if (exist(trim($_POST['newcategory']))) {
			$cat_name = trim($_POST['newcategory']);
			$insert =  mysqli_query($conn, "INSERT INTO `categories` (`title`, `checked`) VALUES ('$cat_name', $admin)");
			$select = mysqli_query($conn, "SELECT `id` FROM `categories` ORDER BY `id` DESC LIMIT 1");
			$result = mysqli_fetch_assoc($select);
			$category_id = $result['id'];
		} else $category_id = $_POST['category'];
		$name = trim($_POST['mapname']);
		$description = trim($_POST['description']);
		$link = trim($_POST['link']);
		$time = time();

		# Добавление записи в БД
		$insert = mysqli_query($conn, "INSERT INTO `maps` (`name`, `description`, `link`, `category_id`, `author_id`, `time`, `published`, `checked`) VALUES ('$name', '$description', '$link', '$category_id', '$author_id', '$time', '$admin', '$admin')");
		
		header('Location: /');
		exit;
	}
?>