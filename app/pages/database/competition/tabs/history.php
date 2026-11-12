<?php
$by = [];
foreach (history_podium($compId) as $r) {
	$id = (int)$r['season_id'];
	$by[$id]['name'] = $r['season_name'];
	$by[$id]['year'] = $r['season_year'];
	$by[$id]['rows'][$r['status']] = $r;
}
if (!$by) {
	echo '<p class="text-center fg-secondary user-select-none">No records yet.</p>';
	return;
}

$medal = ['Winner' => '#FFD700', 'Runner-Up' => '#C0C0C0', 'Third' => '#CD7F32'];
?>
<div class="d-flex flex-column gap-3">
	<?php foreach ($by as $s): ?>
		<div id="season<?= e((string)$s['year']) ?>" class="history-season">
			<strong><?= e($s['name']) ?></strong>
			<div class="row row-cols-1 lg:row-cols-3 lg:g-5 g-3">
				<?php foreach ($medal as $status => $color): $t = $s['rows'][$status] ?? null; if (!$t) continue; ?>
					<div class="col">
						<div class="card card-body">
							<a class="stretched-link text-decoration-none theme-inverse" href="<?= e(team_info_url((int)$t['team_id'], $t['alt'], $t['name'])) ?>">
								<div class="d-flex align-items-center gap-3">
									<img class="logo-sm" src="<?= e(team_logo((int)$t['team_id'])) ?>" alt="" loading="lazy">
									<span class="text-truncate">
										<i class="fa-solid fa-award" style="color: <?= $color ?>;" title="<?= e($status) ?>"></i>
										<span class="lh-1"><?= e($t['name']) ?></span>
									</span>
								</div>
							</a>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endforeach; ?>
</div>