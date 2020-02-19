<?php
	require_once 'include/config.php';
	require_once 'include/functions.php';
	account_exit();

	$conn = connect();
	$userdata = userdata($conn);
	$select = select_notices($conn, $userdata['id']);

	if ($userdata == []) {
		header('Location: /');
		exit;
	}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Уведомления | Pipe Library</title>
	<link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.min.css">
	<link rel="stylesheet" href="css/style.css">
</head>
<body>

	<div class="container">
		<?php
			include 'include/top-panel.php';
		?>
		<div class="main-content">
			<div class="notices">
				<div class="row">
					<div class="col-md-8">
						<div class="notices-user">
							<img src="img/user.png" alt="User" class="notices-user-img">
							<span class="notices-user-name"><?php echo $userdata['username']; ?></span>
						</div>
						<div class="notices-header">
							<h2>Новые уведомления</h2>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-7">
						<?php
						if (mysqli_num_rows($select) > 0) {
							while ($result = mysqli_fetch_assoc($select)) {
							?>
							<div class="notices-post">
								<p class="notices-post-text">Карта <b>"<?php echo $result['name']; ?>"</b> от <b><?php echo get_date($result['time']); ?></b> отклонена администратором с таким комментарием:</p>
								<p class="notices-post-comm alert alert-secondary"><?php echo $result['admin_comm'] ?></p>
								<img src="<?php echo map_img($result['link']); ?>" alt="<?php echo $result['name']; ?>" class="notices-post-img">
								<form action="edit.php" method="POST" class="notices-post-form">
									<input type="hidden" value="<?php echo $result['id'] ?>" name="edit_id">
									<button class="btn btn-success notices-post-form-btn">Изменить карту</button>
								</form>
								<form action="handler/delete-handler.php" method="POST" class="notices-post-form">
									<input type="hidden" value="<?php echo $result['id'] ?>" name="del_id">
									<button class="btn btn-danger notices-post-form-btn">Удалить карту</button>
								</form>
							</div>
							<?php 
							}
						} else {
							?>
						<div class="col-md-8 offset-md-2">
							<p class="post-nomaps">Пока что нет новых уведомлений</p>
						</div>
							<?php
						}
						?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php
	if ($userdata == []) {
		?>
	<div id="notice" class="mfp-hide white-popup-block popup-form alert alert-success" role="alert">
		<h4 class="alert-heading">Мы не знаем как именовать карту!</h4>
		<p>Чтобы создавать новые карты требуется <a class="alert-link" href="/login.php">войти в аккаунт</a> или <a class="alert-link" href="/signup.php">зарегестрироваться.</a></p>
		</div>
		<?php
	}
	?>
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js"></script>
	<script src="/js/script.js"></script>
</body>
</html>
<?php
	close($conn);
?>