<?php
	require_once '../include/config.php';
	require_once '../include/functions.php';

	$conn = connect();
	$userdata = userdata($conn);
	$line = $_SERVER['HTTP_REFERER'];
	$line = explode("?", $line);
	$line = $line[1];
	$line = explode("=", $line);
	$map_id = $line[1];

	if (exist(trim($_POST['comment']))) {
		$text = trim($_POST['comment']);
		$text = nl2br($text);
		$author_id = userdata($conn)['id'];
		$time = time();

		# Добавление записи в БД
		$insert = mysqli_query($conn, "INSERT INTO `comments` (`text`, `time`, `author_id`, `map_id`) VALUES ('$text', $time, $author_id, $map_id)");
		$comments = select_comments($conn, $map_id);
		?><div class="comment-output" id="comment-output">
			<?php
			if (mysqli_num_rows($comments) > 0) {
				while ($result = mysqli_fetch_assoc($comments)) {
			?>
			<div class="comment">
				<div class="row">
					<div class="col-md-8">
						<p class="comment-author"><b><?php echo get_user_name($conn, $result['author_id']); ?></b></p>
					</div>
					<div class="col-md-4">
						<p class="comment-time"><?php echo get_date($result['time']); ?></p>
					</div>
				</div>
				<div class="alert alert-secondary comment-block">
					<p class="comment-text"><?php echo $result['text']; ?></p>
					<?php 
						if ($result['author_id'] == $userdata['id']) {
					?>
					<form action="handler/comment-handler.php" method="POST" class="comment-delete-form">
						<input type="hidden" name="delete_id" value="<?php echo $result['id']; ?>">
						<button class="comment-delete"><img class="comment-delete-img" src="img/basket.png" alt="Удалить"></button>
					</form>
					<?php
						}
					?>
				</div>
			</div>
			<?php
				}
			} else {
			?>
				<p class="comment-no">Пока что нет комментариев</p>
			<?php
			}
		?></div><?php
		close($conn);
		exit;
	}

	if (exist($_POST['delete_id'])) {
		$delete_id = $_POST['delete_id'];
		$select = mysqli_query($conn, "SELECT * FROM `comments` WHERE `id` = ".$delete_id);
		if ($select) {
			$result = mysqli_fetch_assoc($select);
		} else echo "<pre>Error</pre>";
		if ($result['author_id'] == $userdata['id']) {
			$delete = mysqli_query($conn, "DELETE FROM `comments` WHERE `comments`.`id` = ".$delete_id);
		}
		$comments = select_comments($conn, $map_id);
		?><div class="comment-output" id="comment-output">
			<?php
			if (mysqli_num_rows($comments) > 0) {
				while ($result = mysqli_fetch_assoc($comments)) {
			?>
			<div class="comment">
				<div class="row">
					<div class="col-md-8">
						<p class="comment-author"><b><?php echo get_user_name($conn, $result['author_id']); ?></b></p>
					</div>
					<div class="col-md-4">
						<p class="comment-time"><?php echo get_date($result['time']); ?></p>
					</div>
				</div>
				<div class="alert alert-secondary comment-block">
					<p class="comment-text"><?php echo $result['text']; ?></p>
					<?php 
						if ($result['author_id'] == $userdata['id']) {
					?>
						<form action="handler/comment-handler.php" method="POST" class="comment-delete-form">
							<input type="hidden" name="delete_id" value="<?php echo $result['id']; ?>">
							<button class="comment-delete"><img class="comment-delete-img" src="img/basket.png" alt="Удалить"></button>
						</form>
					<?php
						}
					?>
				</div>
			</div>
			<?php
				}
			} else {
			?>
				<p class="comment-no">Пока что нет комментариев</p>
			<?php
			}
		?></div><?php
		close($conn);
		exit;
	}
?>