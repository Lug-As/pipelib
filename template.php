<?php
	require_once 'include/config.php';
	require_once 'include/functions.php';
	account_exit();

	$conn = connect();
	$id = trim($_GET['id']);
	$article = get_article_assoc($conn, $id);
	$checked = $article['checked'];
	$userdata = userdata($conn);
	if ($checked) {
		$comments = select_comments($conn, $article['id']);
	}

	if (admin_check($conn) == FALSE and $checked == FALSE) {
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
	<title><?php echo $article['name']; ?> | Pipe Library</title>
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
		<div class="post-content">
			<div class="row">
				<div class="col-md-8">
					<div class="article">
						<div class="row">
							<div class="col-md-8">
								<h2 class="post-header"><?php echo $article['name']; ?></h2>
							</div>
							<div class="col-md-4">
								<a href="<?php echo $article['link']; ?>" class="post-link btn-lg btn btn-outline-primary">Ссылка на карту</a>
							</div>
						</div>
						<p class="post-time"><?php echo get_date($article['time']); ?></p>
						<a href="<?php echo $article['link']; ?>"><div class="post-img-wrap post-img-wrap_big"><img src="<?php echo map_img($article['link']); ?>" alt="<?php echo $article['name']; ?>" class="post-img post-img_big"><div class="post-img-hover"></div></div></a>
						<p class="article-description"><?php echo $article['description']; ?></p>
						<div class="row">
							<div class="col-md-7">
								<p class="post-category"><span class="post-sm">Категория:</span> <a href="/?cat=<?php echo $result['category_id']; ?>"><?php echo get_cat_name($conn, $article['category_id']); ?></a></p>
							</div>
							<div class="col-md-5">
								<p class="post-author"><span class="post-sm">Автор:</span> <b><?php echo get_user_name($conn, $article['author_id']); ?></b></p>
							</div>
						</div>
					</div>
				<?php
					if ($checked == FALSE) {
				?>
					<div class="post-form">
						<form action="handler/request-handler.php" method="POST">
							<a class="popup-comment btn btn-danger" href="#test-form">Отклонить карту</a>
							<input type="hidden" name="public_id" value="<?php echo $article['id']; ?>">
							<button class="btn btn-success">Опубликовать карту</button>
						</form>
					</div>
				<?php
					}
				?> 
				</div>
			</div>
		</div>
		<?php
			if ($checked) {
		?>
		<div class="comments">
			<div class="row">
				<div class="col-md-8">
					<hr>
					<h3 class="comment-header">Комментарии</h3>
					<div class="comment-form">
						<form action="handler/comment-handler.php" method="POST" id="comment-form">
							<textarea id="comment-input" name="comment" class="comment-input" cols="60" rows="4" placeholder="Что вы думаете по этому поводу?" required></textarea>
						<?php
							if ($userdata !== []) {
								echo "<button id='comment-btn' class='btn btn-primary comment-btn'>Оставить комментарий</button>";
							} else echo "<a id='comment-btn' href='#notice' class='show-message-link btn btn-primary post-add-btn'>Оставить комментарий</a>";
						?>
						</form>
					</div>
					<div class="comment-output" id="comment-output">
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
								<form action="handler/comment-handler.php" method="POST" class="comment-delete-form" onclick="delCommFormsAddEvent()">
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
					?></div>
				</div>
			</div>
		</div>
		<?php 
			}
		?>
	</div>

	<?php
	if ($userdata == []) {
		?>
	<div id="notice" class="mfp-hide white-popup-block popup-form alert alert-success">
		<h4 class="alert-heading">Мы не знаем как именовать комментарий!</h4>
		<p>Чтобы комментировать карты требуется <a class="alert-link" href="/login.php">войти в аккаунт</a> или <a class="alert-link" href="/signup.php">зарегестрироваться.</a></p>
	</div>
		<?php
	}
	if ($checked == FALSE) {
		?>
	<!-- popup form -->
	<div id="test-form" class="popup-form white-popup-block mfp-hide">
		<form action="handler/request-handler.php" method="POST">
			<h3 class="popup-form-header">Оставьте комментарий для пользователя</h3>
			<textarea class="form-input popup-form-text" name="admincomm" id="admin-comm" cols="75" rows="5" placeholder="Комментарий..." required></textarea>
			<input type="hidden" name="reject_id" value="<?php echo $article['id']; ?>">
			<button class="btn btn-danger">Отклонить карту</button>
		</form>
	</div>
		<?php
	}
	?>
	
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js"></script>
	<script src="js/serialize.js"></script>
	<script src="js/script.js"></script>
	<script>
		$(document).ready(function() {
			$('.popup-comment').magnificPopup({
				type: 'inline',
				preloader: false,
				focus: '#admin-comm',
	
				// When elemened is focused, some mobile browsers in some cases zoom in
				// It looks not nice, so we disable it:
				callbacks: {
					beforeOpen: function() {
						if($(window).width() < 700) {
							this.st.focus = false;
						} else {
							this.st.focus = '#name';
						}
					}
				}
			});
		});
	</script>
</body>
</html>
<?php
	close($conn);
?>