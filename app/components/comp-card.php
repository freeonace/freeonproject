<?php // variabel: $c (id, name), $href, $fallback ?>
<div class="col">
	<a class="text-decoration-none" href="<?= e($href) ?>">
		<div class="card">
			<img src="<?= e(comp_bg((int)$c['id'])) ?>" class="card-img-top" alt="" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='<?= e($fallback) ?>'">
			<div class="card-body">
				<span class="card-title"><?= e($c['name']) ?></span>
			</div>
		</div>
	</a>
</div>