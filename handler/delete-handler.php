<?php
	require_once '../include/config.php';
	require_once '../include/functions.php';
	$conn = connect();

	if (exist($_POST['del_id'])) {
		$id = $_POST['del_id'];

		$select = mysqli_query($conn, "SELECT `category_id` FROM `maps` WHERE `id` = ".$id);
		$result = mysqli_fetch_assoc($select);
		$select_cat = mysqli_query($conn, "SELECT * FROM `categories` WHERE `id` = ".$result['category_id']);
		$result_cat = mysqli_fetch_assoc($select_cat);
		$cat_check = $result_cat['checked'];
		# Удаление прошлой предложенной категории
		if ($cat_check == 0) {
			$delete = mysqli_query($conn, "DELETE FROM `categories` WHERE `id` = ".$result['category_id']);
		}

		$query = mysqli_query($conn, "DELETE FROM `maps` WHERE `id` = ".$id);

		header("Location: /notices.php");
		close($conn);
		exit;
	}
	close($conn);
?>