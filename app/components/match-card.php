<?php
// variabel: $m (baris dari matches_by_week / match_cards), $fallback (opsional)
$fallback = $fallback ?? (BASE_URL . '/img/team/default.png');
$v = fixture_view($m);
?>
<article class="card card-match <?= e($v['comp_class']) ?> border-primary" data-match="<?= (int)$m['id'] ?>">
	<a class="text-decoration-none fg-body" href="<?= e($v['url']) ?>">
		<div class="card-header border-primary">
			<span class="fs-sm date"><?= e($v['when']) ?></span>
		</div>
		<div class="card-body">
			<div class="d-flex flex-column gap-1">
				<?php foreach ([
					['home', $m['home_id'], $m['home_alt'], $v['h']],
					['away', $m['away_id'], $m['away_alt'], $v['a']],
				] as [$side, $tid, $short, $score]): ?>
					<div class="d-flex justify-content-between<?= $v['winner'] === $side ? ' fg-primary' : '' ?>">
						<div class="d-flex align-items-center gap-3">
							<img class="logo-sm" src="<?= e(team_logo((int)$tid)) ?>" alt="" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='<?= e($fallback) ?>'">
							<span><?= e($short) ?></span>
						</div>
						<?php if ($score !== null): ?><span><?= $score ?></span><?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</a>
</article>