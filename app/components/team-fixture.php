<?php
// variabel: $m (baris dari team_matches), $teamId, $fallback (opsional)
$fallback = $fallback ?? (BASE_URL . '/img/team/default.png');
$v        = fixture_view($m);
$outcome  = team_outcome($m, $teamId);
$isHome   = (int)$m['home_id'] === $teamId;
$opponent = $isHome ? $m['away_short'] : $m['home_short'];
$ts       = $m['d'] !== null ? strtotime($m['d']) : false;

// laga yang sudah dimainkan: ikon hasil + tooltip; laga mendatang: jam main
$kinds = [
	'W' => ['Won',  'fa-calendar-check', 'fg-success'],
	'L' => ['Lose', 'fa-calendar-xmark', 'fg-danger'],
	'D' => ['Draw', 'fa-calendar-minus', 'fg-secondary'],
];
?>
<div class="match-team <?= e($v['comp_class']) ?>">
	<a class="text-decoration-none fg-body" href="<?= e($v['url']) ?>">
	<div class="row text-center align-items-center">
		<div class="col-2 xl:col-1">
			<?php if ($outcome === null): ?>
				<strong data-bs-toggle="tooltip" data-bs-title="Upcoming Match"><?= $m['t'] !== null ? e(substr($m['t'], 0, 5)) : 'TBD' ?></strong>
			<?php else: [$word, $icon, $fg] = $kinds[$outcome]; ?>
				<strong>
					<i class="fa-solid <?= $icon ?> fs-2xl <?= $fg ?>" role="img" aria-label="<?= e($word) ?>" data-bs-toggle="tooltip" data-bs-title="<?= e($word . ' - ' . ($isHome ? 'HOME' : 'AWAY') . ' vs ' . $opponent) ?>"></i>
				</strong>
			<?php endif; ?>
		</div>

		<div class="col-8 xl:col-10">
			<div class="row">
				<div class="col-5">
					<div class="d-flex gap-2 align-items-center justify-content-end">
						<span class="text-truncate"><?= e($m['home_alt']) ?></span>
						<img class="logo-sm" src="<?= e(team_logo((int)$m['home_id'])) ?>" alt="" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='<?= e($fallback) ?>'">
					</div>
				</div>
				<div class="col-2">
					<span class="d-flex align-items-center justify-content-center gap-1">
						<?php if ($v['h'] !== null && $v['a'] !== null): ?>
							<span><?= (int)$v['h'] ?></span><span>-</span><span><?= (int)$v['a'] ?></span>
						<?php else: ?>
							<span>-</span>
						<?php endif; ?>
					</span>
				</div>
				<div class="col-5">
					<div class="d-flex gap-2 align-items-center justify-content-start">
						<img class="logo-sm" src="<?= e(team_logo((int)$m['away_id'])) ?>" alt="" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='<?= e($fallback) ?>'">
						<span class="text-truncate"><?= e($m['away_alt']) ?></span>
					</div>
				</div>
			</div>
		</div>

		<div class="col-2 xl:col-1">
			<div class="d-flex flex-column">
				<?php if ($ts): ?>
					<strong><?= e(date('j', $ts)) ?></strong>
					<strong><?= e(date('M', $ts)) ?></strong>
				<?php else: ?>
					<strong>TBD</strong>
				<?php endif; ?>
			</div>
		</div>
	</div>
	</a>
</div>
