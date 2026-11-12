<?php
$topIds   = [10, 11, 12, 13, 16];
$fromId   = 10;
$fallback = BASE_URL . '/img/bg/default.png';

$menuComp = [];
foreach ($menu['sections'] as $key => $sec) {
	if (isset($sec['comp'])) $menuComp[(int)$sec['comp']] = $key;
}
$found = competitions_by_ids(array_keys($menuComp));
$csfCards = [];
foreach ($menuComp as $id => $key) {
	if (isset($found[$id])) $csfCards[] = [$found[$id], url($key)];
}

$found = competitions_by_ids($topIds);
$topCards = [];
foreach ($topIds as $id) {
	if (isset($found[$id])) $topCards[] = [$found[$id], comp_url($id, $found[$id]['alt'])];
}

$rest = competitions_from($fromId, $topIds);
?>
<div class="md:p-7 lg:p-9 p-4">
	<div class="d-flex flex-column">
		<div class="pb-8">
			<h2 class="text-title">List of All Competitions</h2>
			<span class="fg-inverse"><?= e(site('name')) ?> Competitions and Leagues</span>
		</div>

		<div class="d-flex flex-column gap-5">

			<?php if ($csfCards): ?>
			<div class="competition">
				<div class="fw-bold fs-lg pb-5"><?= e(site('name')) ?> Competitions</div>
				<div class="row row-cols-2 md:row-cols-3 lg:row-cols-4 text-center justify-content-center g-5">
					<?php foreach ($csfCards as [$c, $href]): component('comp-card', compact('c', 'href', 'fallback')); endforeach; ?>
				</div>
			</div>
			<?php endif; ?>

			<?php if ($topCards || $rest): ?>
			<div class="league">
				<div class="d-flex flex-column gap-5">

					<?php if ($topCards): ?>
					<div id="top-league">
						<div class="fw-bold fs-lg pb-5">Top-5 Leagues</div>
						<div class="row row-cols-3 lg:row-cols-5 text-center justify-content-center g-5">
							<?php foreach ($topCards as [$c, $href]): component('comp-card', compact('c', 'href', 'fallback')); endforeach; ?>
						</div>
					</div>
					<?php endif; ?>

					<?php if ($rest): ?>
					<div id="all-league">
						<ul class="list-group comp-list">
							<?php foreach ($rest as $c): ?>
								<li class="list-group-item">
									<a class="d-block text-decoration-none theme-inverse" href="<?= e(comp_url((int)$c['id'], $c['alt'])) ?>">
										<div class="d-flex align-items-center gap-3">
											<img class="logo-sm" src="<?= e(comp_img((int)$c['id'])) ?>">
											<span><?= e($c['name']) ?></span>
										</div>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
					<?php endif; ?>

				</div>
			</div>
			<?php endif; ?>

		</div>
	</div>
</div>