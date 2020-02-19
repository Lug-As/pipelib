<?php
	require_once 'include/config.php';
	require_once 'include/functions.php';
	account_exit();

	$conn = connect();
	$offset = 8;
	$search = trim($_GET['search']);
	$select = select($conn, $offset, $search, 0);
	$categories = select_cat($conn, $search, 0);
	$userdata = userdata($conn);
	
	if (admin_check($conn) == FALSE) {
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
	<title>Админ-панель | Pipe Library</title>
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
						<button class="btn btn-primary search-btn">Поиск</button>
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
							<li class="category-li"><a href="/admin.php" class="category-link">Все</a></li>
							<?php
								while ($result = mysqli_fetch_assoc($categories)) {
									echo "<li class='category-li'><a href='/admin.php?cat={$result['id']}' class='category-link'>{$result['title']}</a></li>";
								}
							?></ul>
					</div>
				</div>
				<div class="col-md-9">
					<div class="post-wrap">
						<div class="row">
							<div class="col-md-7">
								<h2 class="post-wrap-header">Новые предложенные карты</h2>
							</div>
							<div class="col-md-4 offset-md-1">
								<a href='/add.php' class='btn btn-success post-add-btn'><span class='post-add-plus'>+</span>Добавить карту</a>
							</div>
							<?php
								$pagination = get_pagination($conn, $offset, 0);
								echo $pagination;
							?>
						</div>
						<div class="row">
						<?php
							if (mysqli_num_rows($select) > 0) {
								while ($result = mysqli_fetch_assoc($select)) {
									 ?>
							<div class="col-md-12">
								<div class="post">
									<h3 class="post-header"><a class="post-header-link" href="/template.php?id=<?php echo $result['id'] ?>"><?php echo $result['name']; ?></a></h3>
									<p class="post-description"><?php echo $result['description'] ?></p>
									<img src="<?php echo map_img($result['link']); ?>" alt="<?php echo $result['name']; ?>" class="post-img">
									<div class="row">
										<div class="col-md-7">
											<p class="post-category"><span class="post-sm">Категория:</span> <a href="/admin.php?cat=<?php echo $result['category_id']; ?>"><?php echo get_cat_name($conn, $result['category_id']); ?></a></p>
										</div>
										<div class="col-md-5">
											<p class="post-author"><span class="post-sm">Автор:</span> <b><?php echo get_user_name($conn, $result['author_id']); ?></b></p>
										</div>
									</div>
									<p class="post-time"><?php echo get_date($result['time']); ?></p>
								</div>
								<hr class="post-line">
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
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js"></script>
	<script src="js/script.min.js"></script>
</body>
</html>
<?php
	close($conn);
?>