<section class="p-5 text-center">
	<h1>Welcome to <?= e(site('name')) ?></h1>
	<p>Kata pengantar singkat sebelum masuk ke konten utama.</p>
</section>

<section class="px-4 pb-5">
	<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
		<?php foreach ($menu['sections'] as $key => $sec): ?>
			<a class="card text-decoration-none" href="<?= url($key) ?>">
				<div class="card-body">
					<i class="fa-solid <?= e($sec['icon']) ?> fa-2x mb-3"></i>
					<h2 class="card-title"><?= e($sec['label']) ?></h2>
					<p class="card-text"><?= e($sec['desc']) ?></p>
				</div>
			</a>
		<?php endforeach; ?>
	</div>
</section>