<?php
	require_once '../include/config.php';
	require_once '../include/functions.php';

	$conn = connect();

	if (exist(trim($_POST['mapname']))) {
		$id = $_POST['map_id'];
		$original = get_article_assoc($conn, $id);

		$name = trim($_POST['mapname']);
		$description = trim($_POST['description']);
		$link = trim($_POST['link']);
		$time = time();

		if (exist(trim($_POST['newcategory']))) {
			$cat_name = trim($_POST['newcategory']);
			$insert =  mysqli_query($conn, "INSERT INTO `categories` (`title`, `checked`) VALUES ('$cat_name', 0)");
			$select = mysqli_query($conn, "SELECT `id` FROM `categories` ORDER BY `id` DESC LIMIT 1");
			$result = mysqli_fetch_assoc($select);
			$category_id = $result['id'];
		} else $category_id = $_POST['category'];

		$sql = "UPDATE `maps` SET `time` = '$time', `published` = 0, `checked` = 0, `admin_comm` = NULL";

		if ($original['name'] !== $name) {
			$sql .= ", `name` = '$name'";
		}
		if ($original['description'] !== $description) {
			$sql .= ", `description` = '$description'";
		}
		if ($original['link'] !== $link) {
			$sql .= ", `link` = '$link'";
		}
		if ($original['category_id'] !== $category_id) {
			$sql .= ", `category_id` = '$category_id'";

			$select = mysqli_query($conn, "SELECT * FROM `categories` WHERE `id` = ".$original['category_id']);
			$result = mysqli_fetch_assoc($select);
			$cat_check = $result['checked'];
			# Удаление прошлой предложенной категории
			if ($cat_check == 0) {
				$delete = mysqli_query($conn, "DELETE FROM `categories` WHERE `categories`.`id` = ".$original['category_id']);
			}
		}

		$sql .= " WHERE `maps`.`id` = ".$id;

		# Обновление записи в БД
		$update = mysqli_query($conn, $sql);

		header('Location: /');
		exit;
	}
?>