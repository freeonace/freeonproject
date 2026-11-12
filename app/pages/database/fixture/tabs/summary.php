<?php
// tabs/summary.php  (variabel dari detail.php: $m, $fallback)
$played = $m['sh'] !== null && $m['sa'] !== null;

// Head-to-Head: maksimal 5 pertemuan terakhir, dihitung dari sisi tim kandang laga ini
$h2h = fixture_h2h($m, 5);
$hw = $dw = $aw = 0;
foreach ($h2h as $r) {
	$o = team_outcome($r, (int)$m['home_id']);
	if ($o === 'W') $hw++;
	elseif ($o === 'D') $dw++;
	elseif ($o === 'L') $aw++;
}
$n    = count($h2h);
$hwP  = $n ? round($hw / $n * 100, 1) : 0;
$dwP  = $n ? round($dw / $n * 100, 1) : 0;
$awP  = $n ? round(100 - $hwP - $dwP, 1) : 0;
$pct  = fn($x): string => number_format($x, 1, '.', '');

// Tabel kedua tim pada kompetisi + musim laga ini (5 baris di sekitar posisi tiap tim)
$standing = standings((int)$m['comp_id'], (int)$m['season_id']);
$allForms = $standing ? last_five_all((int)$m['comp_id'], (int)$m['season_id']) : [];
$tables   = [];
foreach (['home', 'away'] as $side) {
	$tid   = (int)$m[$side . '_id'];
	$rows  = table_window($standing, $tid, 5);
	$forms = [];
	foreach ($rows as $r) {
		$id         = (int)$r['team_id'];
		$forms[$id] = $allForms[$id] ?? [];
	}
	$tables[$side] = ['title' => $m[$side . '_alt'], 'rows' => $rows, 'forms' => $forms, 'active' => $tid];
}
$fullTable = comp_url((int)$m['comp_id'], $m['comp_alt'], 'standings') . '?season=' . (int)$m['season_id'];
?>
<div class="d-flex flex-column gap-5">
	<div class="d-flex flex-column p-5">
		<div class="d-flex flex-column gap-2 text-center">
			<!-- Di sini bisa FULL TIME, bisa UPCOMING -->
			<h1 class="card-title"><?= $played ? 'FULL TIME' : 'UPCOMING' ?></h1>
			<span class="fw-semibold">Match Timeline</span>
		</div>
		<div class="cm-border"></div>
		<div class="d-flex flex-column gap-5 md:p-7 p-2">
			<!-- Boleh diabaikan dulu. Karena masih belum ada tabel player dan playerrecord. Tapi bisa dikerjakan layout-nya terlebih dahulu -->
			<!-- Fallbacknya bisa di bagian sini saja, sementara di bagian atas tetap. Fallbacknya tinggal tulis No Timeline Available -->
			<div class="card card-body"><!-- Border tergantung tim mana yang melakukan kegiatan -->A</div>
			<div class="card card-body">A</div>
			<div class="card card-body">A</div>
		</div>
		<!-- Maksimal sampai tiga -->
		<a class="text-decoration-none text-center align-self-center fs-lg" href="">See More | See Less</a>
	</div>

	<div class="card card-body gap-5">
		<h4 class="card-title text-center">Head-to-Head (<?= $n ?> <?= $n === 1 ? 'Match' : 'Matches' ?>)</h4>

		<div class="d-flex flex-column gap-5">
			<?php if ($n): ?>
			<div class="d-flex justify-content-between align-items-center">
				<div class="d-flex flex-column align-items-center">
					<div class="sum-card-home"><?= $hw ?></div>
					<span class="sum-card-info"><?= e($m['home_short']) ?></span>
				</div>
				<div class="d-flex flex-column align-items-center">
					<div class="sum-card-draw"><?= $dw ?></div>
					<span class="sum-card-info">Draw</span>
				</div>
				<div class="d-flex flex-column align-items-center">
					<div class="sum-card-away"><?= $aw ?></div>
					<span class="sum-card-info"><?= e($m['away_short']) ?></span>
				</div>
			</div>

			<div class="percentage-h2" style="--hw-percentage: <?= $pct($hwP) ?>%; --dw-percentage: <?= $pct($dwP) ?>%; --aw-percentage: <?= $pct($awP) ?>%;">
				<!-- hw=home win, dw=draw, aw=away win -->
				<div class="percentage-h"></div>
				<div class="percentage-d"></div>
				<div class="percentage-a"></div>
			</div>
			<?php endif; ?>

			<div class="row row-cols-1 xl:row-cols-2 g-9 xl:g-5">
				<div class="col">
					<?php if ($n): ?>
					<div class="row row-cols-1 g-3">
						<?php foreach ($h2h as $r):
							$rv = match_view($r);
							$ts = $r['d'] !== null ? strtotime($r['d']) : false;
						?>
						<div class="col">
							<div class="card card-body">
								<div class="d-flex flex-column">
									<small class="text-center"><?= e($r['comp_alt']) ?> &middot; <?= $ts ? e(date('M j, y', $ts)) : 'TBD' ?></small>
									<div class="row align-items-center">
										<div class="col-4 md:col-5">
											<div class="d-flex gap-2 align-items-center justify-content-end">
												<span class="d-none sm:d-inline"><?= e($r['home_alt']) ?></span>
												<span class="d-inline sm:d-none"><?= e($r['home_short']) ?></span>
												<img class="logo-sm" src="<?= e(team_logo((int)$r['home_id'])) ?>" alt="" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='<?= e($fallback) ?>'">
											</div>
										</div>
										<div class="col-4 md:col-2 text-center"><?= (int)$rv['h'] ?>-<?= (int)$rv['a'] ?></div>
										<div class="col-4 md:col-5">
											<div class="d-flex gap-2 align-items-center justify-content-start">
												<img class="logo-sm" src="<?= e(team_logo((int)$r['away_id'])) ?>" alt="" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='<?= e($fallback) ?>'">
												<span class="d-none sm:d-inline"><?= e($r['away_alt']) ?></span>
												<span class="d-inline sm:d-none"><?= e($r['away_short']) ?></span>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<?php endforeach; ?>
					</div>
					<?php else: ?>
						<p class="text-center fg-secondary user-select-none">No previous meetings.</p>
					<?php endif; ?>
				</div>

				<div class="col">
					<div class="row row-cols-1 g-5">
						<?php foreach ($tables as $t): ?>
						<div class="col">
							<div class="card card-body">
								<h4 class="card-title"><?= e($t['title']) ?> Tables</h4>
								<?php if ($t['rows']): ?>
									<?php component('standing-table', ['rows' => $t['rows'], 'forms' => $t['forms'], 'activeTeam' => $t['active']]); ?>
								<?php else: ?>
									<p class="text-center fg-secondary user-select-none mb-0">No standing yet.</p>
								<?php endif; ?>
							</div>
						</div>
						<?php endforeach; ?>

						<div class="col">
							<a class="text-decoration-none" href="<?= e($fullTable) ?>">To the full table <i class="bi bi-arrow-right fs-sm"></i></a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>