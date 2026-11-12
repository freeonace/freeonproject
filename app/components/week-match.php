<?php
// variabel: $games (hasil group_by_kickoff)
if (!$games) {
	echo '<p class="text-center fg-secondary user-select-none">No matches yet.</p>';
	return;
}
$fallback = BASE_URL . '/img/team/default.png';
?>
<div class="row row-cols-1 g-5">
	<?php foreach ($games as $g): ?>
		<div class="col">
			<div class="card">
				<div class="card-header">
					<span><?= e($g['label']) ?></span>
				</div>
				<div class="card-body">
					<div class="d-flex flex-column gap-3">
						<?php foreach ($g['rows'] as $m): $v = fixture_view($m); $note = match_note($m); ?>
							<a class="text-decoration-none fg-body" href="<?= e($v['url']) ?>">
								<div class="match-row">
									<div class="side home<?= $v['winner'] === 'home' ? ' fw-bold' : '' ?>">
										<span class="text-truncate"><?= e($m['home_name']) ?></span>
										<img class="logo-sm" src="<?= e(team_logo((int)$m['home_id'])) ?>" alt="" width="24" height="24"
										     loading="lazy" onerror="this.onerror=null;this.src='<?= e($fallback) ?>'">
									</div>

									<div class="score">
										<?php if ($v['h'] !== null && $v['a'] !== null): ?>
											<span class="<?= $v['winner'] === 'home' ? 'fw-bold' : '' ?>"><?= $v['h'] ?></span>
											-
											<span class="<?= $v['winner'] === 'away' ? 'fw-bold' : '' ?>"><?= $v['a'] ?></span>
											<?php if ($note !== ''): ?><div class="fs-sm"><?= e($note) ?></div><?php endif; ?>
										<?php else: ?>
											<span class="fs-sm">vs</span>
										<?php endif; ?>
									</div>

									<div class="side away<?= $v['winner'] === 'away' ? ' fw-bold' : '' ?>">
										<img class="logo-sm" src="<?= e(team_logo((int)$m['away_id'])) ?>" alt="" width="24" height="24"
										     loading="lazy" onerror="this.onerror=null;this.src='<?= e($fallback) ?>'">
										<span class="text-truncate"><?= e($m['away_name']) ?></span>
									</div>
								</div>
							</a>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
</div>