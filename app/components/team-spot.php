<?php
// variabel: $title, $m (baris dari team_matches atau null), $teamId, $empty, $fallback (opsional)
$fallback = $fallback ?? (BASE_URL . '/img/team/default.png');
$v = $m !== null ? fixture_view($m) : null;
?>
<div class="card">
	<a class="text-decoration-none fg-body" href="<?= e($v['url']) ?>">
	<div class="card-header">
		<span class="card-title fw-semibold"><?= e($title) ?></span>
	</div>
	<div class="card-body">
		<?php if ($m === null): ?>
			<p class="text-center fg-secondary user-select-none mb-0"><?= e($empty) ?></p>
		<?php else:
			$v       = match_view($m);
			$outcome = team_outcome($m, $teamId);
			$note    = match_note($m);
			$ts      = $m['d'] !== null ? strtotime($m['d']) : false;
			$when    = $ts ? date('j F Y', $ts) . ($m['t'] !== null ? ' - ' . substr($m['t'], 0, 5) : '') : 'TBD';
			$badges  = ['W' => ['Won', 'theme-success'], 'L' => ['Lost', 'theme-danger'], 'D' => ['Draw', 'theme-secondary']];
		?>
			<div class="d-flex flex-column align-items-center gap-3">
				<span class="fg-1"><?= e($m['comp_name']) ?></span>
				<div class="d-flex gap-2 align-items-center">
					<div class="d-flex gap-2 align-items-center">
						<span><?= e($m['home_alt']) ?></span>
						<img class="logo-sm" src="<?= e(team_logo((int)$m['home_id'])) ?>" alt="" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='<?= e($fallback) ?>'">
					</div>
					<span>
						<?php if ($v['h'] !== null && $v['a'] !== null): ?>
							<?= (int)$v['h'] ?>-<?= (int)$v['a'] ?>
						<?php else: ?>
							-
						<?php endif; ?>
					</span>
					<div class="d-flex gap-2 align-items-center">
						<img class="logo-sm" src="<?= e(team_logo((int)$m['away_id'])) ?>" alt="" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='<?= e($fallback) ?>'">
						<span><?= e($m['away_alt']) ?></span>
					</div>
				</div>
				<?php if ($outcome !== null): ?>
					<div class="badge <?= $badges[$outcome][1] ?>"><?= $badges[$outcome][0] ?></div>
					<?php if ($note !== ''): ?><small class="fg-2"><?= e($note) ?></small><?php endif; ?>
				<?php else: ?>
					<span><?= e($when) ?></span>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
	</a>
</div>
