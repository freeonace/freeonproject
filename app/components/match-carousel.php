<?php
$limit  = $limit ?? 20;
$panels = ['all' => ['label' => 'All', 'comp' => null]];

foreach (match_competitions() as $c) {
	$panels['c' . $c['id']] = ['label' => $c['name'], 'comp' => (int)$c['id']];
}

foreach ($panels as &$p) {
	$p['rows'] = match_cards($p['comp'], $limit);
}
unset($p);

if (!$panels['all']['rows']) {
	echo '<p class="mc-empty">No matches yet.</p>';
	return;
}

$fallback = BASE_URL . '/img/team/default.png';
?>

<div class="mc" data-mc>

	<?php if (count($panels) > 1): ?>
	<div class="mc-filter" role="group" aria-label="Filter by competition">
		<?php $first = true; foreach ($panels as $key => $p): ?>
			<button type="button" class="btn btn-sm theme-primary <?= $first ? 'btn-solid' : 'btn-outline' ?>" data-mc-filter="<?= e($key) ?>" aria-pressed="<?= $first ? 'true' : 'false' ?>"><?= e($p['label']) ?>
			</button>
		<?php $first = false; endforeach; ?>
	</div>
	<?php endif; ?>

	<div class="mc-wrap">
		<button type="button" class="btn-icon btn-sm mc-nav mc-prev border" aria-label="Previous matches" disabled>
			<i class="fa-solid fa-chevron-left"></i>
		</button>

		<?php $first = true; foreach ($panels as $key => $p): $rows = $p['rows']; ?>
			<div class="mc-track" data-mc-panel="<?= e($key) ?>" tabindex="0" role="region" aria-label="<?= e($p['label']) ?> matches"<?= $first ? '' : ' hidden' ?>>

				<?php if (!$rows): ?><p class="mc-empty">No matches yet.</p><?php endif;?>

				<?php foreach ($rows as $m): $v = match_view($m); ?>
					<?php component('match-card', compact('m', 'fallback')); ?>
<!--					<article class="card card-match <?= e($v['comp_class']) ?> border-primary" data-match="<?= (int)$m['id'] ?>">
						<div class="card-header border-primary">
							<span class="fs-sm date"><?= e($v['when']) ?></span>
						</div>
						<div class="card-body">
							<div class="d-flex flex-column gap-1">
								<?php foreach ([
									['home', $m['home_id'], $m['home_alt'], $v['h']],
									['away', $m['away_id'], $m['away_alt'], $v['a']],
								] as [$side, $tid, $alt, $score]): ?>
									<div class="d-flex justify-content-between<?= $v['winner'] === $side ? ' fg-primary' : '' ?>">
										<div class="d-flex align-items-center gap-3">
											<img class="logo-sm" src="<?= e(team_logo((int)$tid)) ?>" alt="" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='<?= e($fallback) ?>'">
											<span><?= e($alt) ?></span>
										</div>
										<?php if ($score !== null): ?><span><?= $score ?></span><?php endif; ?>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					</article> -->
				<?php endforeach; ?>
			</div>
		<?php $first = false; endforeach; ?>

		<button type="button" class="btn-icon btn-sm mc-nav mc-next border" aria-label="Next matches">
			<i class="fa-solid fa-chevron-right"></i>
		</button>

	</div>
</div>