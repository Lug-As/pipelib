<?php
	require_once 'include/config.php';
	require_once 'include/functions.php';
	account_exit();

	$conn = connect();
	$offset = 16;
	$search = trim($_GET['search']);
	$select = select($conn, $offset, $search);
	$categories = select_cat($conn, $search);
	$cat = get_cat($conn);
	$userdata = userdata($conn);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Главная | Pipe Library</title>
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
		<div class="row">
			<div class="col-lg-12">
				<div class="search">
					<form action="" method="GET">
						<input autocomplete="off" id="search-input" class="search-input" name="search" placeholder="Введите название карты" value="<?php echo $search; ?>">
						<input type="hidden" name="cat" value="<?php echo $cat; ?>">
						<button id="search-btn" class="btn btn-primary search-btn">Поиск</button>
					</form>
				</div>
			</div>
		</div>

		<div class="main-content">
			<div class="row">
				<div class="col-md-3">
					<div class="category">
						<h4 class="category-header">Категории</h4>
						<ul class="category-list">
							<li class="category-li"><a href="/<?php if($search !== "") echo "?search={$search}"; ?>" class="category-link">Все</a></li>
							<?php
								while ($result = mysqli_fetch_assoc($categories)) {
									echo "<li class='category-li'><a href='/?cat={$result['id']}";
									if($search !== "") echo "&search={$search}";
									echo "' class='category-link'>{$result['title']}</a></li>";
								}
							?></ul>
					</div>
				</div>
				<div class="col-md-9">
					<div class="post-wrap">
						<div class="post-head-line">
							<div class="row">
								<div class="col-md-2">
									<h2 class="post-wrap-header">Карты</h2>
								</div>
								<div class="col-md-4 offset-md-6">
									<?php
									if ($userdata !== []) {
										echo "<a href='/add.php' class='btn btn-success post-add-btn'><span class='post-add-plus'>+</span>Добавить карту</a>";
									} else echo "<a href='#notice' class='show-message-link btn btn-success post-add-btn'><span class='post-add-plus'>+</span>Добавить карту</a>";
									?>
								</div>
								<?php
								if ($cat) {
									?>
								<div class="col-md-12">
									<p class="post-head-cat">Категория: <b><?php echo get_cat_name($conn, $cat); ?></b></p>
								</div>
									<?php
								}
									$pagination = get_pagination($conn, $offset);
									echo $pagination;
								?>
							</div>
						</div>
						<div class="row">
							<?php
								if (mysqli_num_rows($select) > 0) {
									while ($result = mysqli_fetch_assoc($select)) {
										 ?>
								<div class="col-md-6">
									<div class="post">
										<div class="post-row">
											<div class="post-col ">
												<h4 class="post-header"><a class="post-header-link" href="/template.php?id=<?php echo $result['id'] ?>"><?php echo $result['name']; ?></a></h4>
											</div>
											<div class="post-col post-col-right">
												<p class="post-time"><?php echo get_date($result['time']); ?></p>
											</div>
										</div>
										<a href="/template.php?id=<?php echo $result['id'] ?>"><div class="post-img-wrap"><img src="<?php echo map_img($result['link']); ?>" alt="<?php echo $result['name']; ?>" class="post-img"><div class="post-img-hover"></div></div></a>
										<p class="post-description"><?php echo get_description($result['description'], $result['id']); ?></p>
										<hr class="post-inner-line">
										<div class="row">
											<div class="col-md-5">
												<p class="post-category"><span class="post-sm">Категория:</span><a class="post-sm-link" href="/?cat=<?php echo $result['category_id']; ?>"><?php echo get_cat_name($conn, $result['category_id']); ?></a></p>
											</div>
											<div class="col-md-7">
												<p class="post-author"><span class="post-sm">Автор:</span> <b><?php echo get_user_name($conn, $result['author_id']); ?></b></p>
											</div>
										</div>
									</div>
								</div>
									<?php 
									}
								} else {
									?>
								<div class="col-md-8 offset-md-2">
									<p class="post-nomaps">Не нашлось записей по данному запросу</p>
								</div>
									<?php
								}
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php
	if ($userdata == []) {
		?>
	<div id="notice" class="mfp-hide white-popup-block popup-form alert alert-success">
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