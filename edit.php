<?php
	require_once 'include/config.php';
	require_once 'include/functions.php';
	account_exit();

	$conn = connect();
	$categories = select_all_cat($conn);
	$userdata = userdata($conn);
	$map_id = $_POST['edit_id'];
	$map = get_article_assoc($conn, $map_id);
	$category = get_cat_name($conn, $map['category_id']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Изменить карту | Pipe Library</title>
	<link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
	<link rel="stylesheet" href="css/style.css">
</head>
<body>

	<div class="container">
		<?php
			include 'include/top-panel.php';
		?>
		<div class="row">
			<div class="col-md-12">
				<div class="form">
					<h2 class="form-header">Изменить карту</h2>
					<form action="handler/edit-handler.php" method="POST">
						<input class="form-input" name="mapname" type="text" placeholder="Новое название" value="<?php echo $map['name']; ?>" required>
						<textarea class="form-input" name="description" cols="70" rows="5" placeholder="Новое описание" required><?php echo $map['description']; ?></textarea>
						<input id="old-cat" name="cat_select" type="radio" checked>
						<label for="old-cat">Существующая категория</label>
						<select id="old-cat-select" class="old-cat-list form-input" name="category">
							<option></option>
							<?php
								while ($result = mysqli_fetch_assoc($categories)) {
									if ($map['category_id'] == $result['id']) {
										echo "<option value='{$result['id']}' selected>{$result['title']}</option>";
									}
									else echo "<option value='{$result['id']}'>{$result['title']}</option>";
								}
							?>
						</select>
						<br>
						<input id="new-cat" name="cat_select" type="radio">
						<label for="new-cat">Новая категория</label>
						<input id="new-cat-input" class="new-cat-input form-input" name="newcategory" type="text" placeholder="Имя новой категории" value="<?php echo $category; ?>" disabled>
						<br>
						<input class="form-input" name="link" type="text" placeholder="Новая ссылка" value="<?php echo $map['link']; ?>">
						<input type="hidden" name="map_id" value="<?php echo $map_id; ?>">
						<button class="btn btn-primary form-btn">Повторить заявку</button>
					</form>
				</div>
			</div>
		</div>
	</div>
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
	<script>
		$(document).ready(function() {
			document.querySelector("#old-cat").addEventListener("click", function(){
				document.getElementById("new-cat-input").disabled = true;
				document.getElementById("old-cat-select").disabled = false;
			});
			document.querySelector("#new-cat").addEventListener("click", function(){
				document.getElementById("old-cat-select").disabled = true;
				document.getElementById("new-cat-input").disabled = false;
			});
		});
	</script>
	<script src="/js/script.js"></script>
</body>
</html>