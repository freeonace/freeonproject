<?php // app/layout/sidebar.php ?>
<dialog class="drawer drawer-start sidebar" id="sidebar" tabindex="-1" aria-labelledby="sidebarLabel">
	<div class="drawer-header">
		<h5 class="drawer-title" id="sidebarLabel"><?= e(site('name')) ?></h5>
		<button type="button" class="btn-close" data-bs-dismiss="drawer" aria-label="Tutup"></button>
	</div>
	<div class="drawer-body">

		<ul class="nav nav-main">
			<?php foreach ($menu['sections'] as $key => $sec):
				if (!empty($sec['hidden'])) continue;
				$to = $sec['url'] ?? $key;   // 'url': tampilan section diganti (mis. Home -> Admin untuk admin)
				$on = ($to === $section); ?>
				<li class="nav-item">
					<?php
					$full  = $sec['name'] ?? $sec['label'];
					$short = $sec['label'];
					?>
					<a class="nav-link<?= $on ? ' active' : '' ?>"
						<?= $on ? 'aria-current="' . ($page === null ? 'page' : 'true') . '"' : '' ?>
						href="<?= url($to) ?>">
						<i class="fa-solid <?= e($sec['icon']) ?> fg-primary"></i>
						<span class="nav-text">
							<span class="nav-full"><?= e($full) ?></span>
							<?php if ($short !== $full): ?>
							<span class="nav-short"><?= e($short) ?></span>
						<?php endif; ?>
						</span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>

		<?php if (empty($menu['sections'][$section]['hidden']) || ($section === 'admin' && $subs)): ?>
		<div class="nav-sub">
			<ul class="nav nav-underline">
				<?php foreach ($subs as $key => $label): $on = ($key === $page); ?>
					<li class="nav-item">
						<a class="nav-link<?= $on ? ' active' : '' ?>"
							<?= $on ? 'aria-current="page"' : '' ?>
							href="<?= url($section, $key) ?>"
							title="<?= e($label) ?>"><?= e($label) ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php endif; ?>

		<div class="sidebar-foot">
			<small>
				<span class="nav-full">Version <?= e(site('version')) ?></span>
				<span class="nav-short">v<?= e(site('version')) ?></span>
			</small>
		</div>

	</div>
</dialog>