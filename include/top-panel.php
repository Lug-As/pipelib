<div class="row">
	<div class="col-md-12">
		<div class="top-panel">
			<h1 class="main-header"><a class="main-header-link" href="/">Pipe Library</a></h1>
			<div class="acc">
				<?php
					if ($userdata !== []) {
						?>
				<div class="acc-dropdown dropdown">
					<img class="acc-img" src="/img/user.png" alt="User">
					<button id="dropdown-btn" class="acc-dropdown-btn btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo $userdata['username']; ?></button>
					<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu2">
						<a href='/notices.php' class='dropdown-item dropdown-input'>Уведомления</a>
						<?php
						if ($userdata['admin']) {
							echo "<a href='/admin.php' class='dropdown-item dropdown-input'>Админ-панель</a>";
						}
						?>
						<form class="dropdown-item" action="" method="POST">
							<input class="btn dropdown-input dropdown-input-form" type="submit" name="exit" value="Выйти из аккаунта">
						</form>
					</div>
				</div>
				<?php
					} else {
					?><a class="enter-link" href="/login.php">Войти в аккаунт</a><?php
					}
				?>		
			</div>
		</div>
	</div>
</div>
