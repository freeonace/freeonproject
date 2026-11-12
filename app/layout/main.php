<?php // app/layout/main.php ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php require __DIR__ . '/head.php'; ?>
</head>
<body>
	<?php require __DIR__ . '/header.php'; ?>

	<div class="pager">
		<?php require __DIR__ . '/sidebar.php'; ?>
		<div class="content">
			<main class="main"><?= $content ?></main>
			<?php require __DIR__ . '/footer.php'; ?>
		</div>
	</div>

	<?php require __DIR__ . '/scripts.php'; ?>
</body>
</html>