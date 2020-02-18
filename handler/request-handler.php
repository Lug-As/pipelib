<?php
	require_once '../include/config.php';
	require_once '../include/functions.php';
	$conn = connect();

	if (exist($_POST['public_id'])) {
		$id = $_POST['public_id'];
		$query = mysqli_query($conn, "SELECT `category_id` FROM `maps` WHERE `id` = ".$id);
		$result = mysqli_fetch_assoc($query);
		$cat_id = $result['category_id'];
		$time = time();

		$update = mysqli_query($conn, "UPDATE `maps` SET `time` = '$time', `published` = '1', `checked` = '1' WHERE `maps`.`id` = ".$id);
		$update_cat = mysqli_query($conn, "UPDATE `categories` SET `checked` = '1' WHERE `categories`.`id` = ".$cat_id);

		header("Location: /admin.php");
		close($conn);
		exit;
	}

	if (exist($_POST['reject_id'])) {
		$id = $_POST['reject_id'];
		$comm = $_POST['admincomm'];

		$update = mysqli_query($conn, "UPDATE `maps` SET `published` = '0', `checked` = '1', `admin_comm` = '$comm' WHERE `maps`.`id` = ".$id);

		header("Location: /admin.php");
		close($conn);
		exit;
	}
	close($conn);
?>