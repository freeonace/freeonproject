<?php
$seasonRows = team_season_history($teamId);
$titles     = team_titles($teamId);

$count = ['league' => 0, 'cup' => 0, 'other' => 0];
foreach ($titles as $t) $count[title_kind($t['type'])]++;
?>
<div class="row g-5">
	<div class="col-12 lg:col-7 xl:col-8">
		<div class="row row-cols-1 g-5">
			<div class="col">
				<div class="card card-body">
					<h4 class="card-title"><?= e($team['name']) ?> Season History</h4>
					<div class="table-responsive">
						<table class="table table-season">
							<thead>
								<tr>
									<th>Season</th>
									<th>Play</th>
									<th>W</th>
									<th>D</th>
									<th>L</th>
									<th>GF</th>
									<th>GA</th>
								</tr>
							</thead>
							<tbody>
							<?php foreach ($seasonRows as $r): ?>
								<tr>
									<td>
										<a class="text-decoration-none theme-inverse" href="<?= e(team_info_url($teamId, $team['alt'], $team['name'], 'stats') . '?season=' . (int)$r['season_id']) ?>"><?= e($r['season_name']) ?></a>
									</td>
									<td><?= (int)$r['p'] ?></td>
									<td><?= (int)$r['w'] ?></td>
									<td><?= (int)$r['d'] ?></td>
									<td><?= (int)$r['l'] ?></td>
									<td><?= (int)$r['gf'] ?></td>
									<td><?= (int)$r['ga'] ?></td>
								</tr>
							<?php endforeach; ?>
							<?php if (!$seasonRows): ?>
								<tr>
									<td colspan="7" class="text-center fg-secondary user-select-none">No season records yet.</td>
								</tr>
							<?php endif; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<div class="col">
				<!-- Kalau masih nol, tinggal tambah "No achievments yet" -->
				<div class="card card-body">
					<h4 class="card-title"><?= e($team['name']) ?> Achievements</h4>
					<?php if (!$titles): ?>
						<p class="text-center fg-secondary user-select-none mb-0">No achievements yet.</p>
					<?php else: ?>
					<div class="d-flex flex-column gap-5">
						<div class="d-flex justify-content-around">
							<div class="d-flex flex-column align-items-center gap-2">
								<i class="bi bi-trophy-fill fs-2xl" style="color: gold;"></i>
								<span class="fw-semibold fs-lg"><?= (int)$count['league'] ?></span>
								<small class="fg-2">League Titles</small>
							</div>

							<div class="d-flex flex-column align-items-center gap-2">
								<i class="bi bi-trophy fs-2xl" style="color: silver;"></i>
								<span class="fw-semibold fs-lg"><?= (int)$count['cup'] ?></span>
								<small class="fg-2">Cup Titles</small>
							</div>
							<?php if ($count['other'] > 0): ?>
							<div class="d-flex flex-column align-items-center gap-2">
								<i class="bi bi-award fs-2xl" style="color: #CD7F32;"></i>
								<span class="fw-semibold fs-lg"><?= (int)$count['other'] ?></span>
								<small class="fg-2">Other Titles</small>
							</div>
							<?php endif; ?>
						</div>

						<div class="d-flex flex-column gap-3">
							<?php foreach ($titles as $t): ?>
							<span><?= e($t['season_name']) ?> — <?= e($t['comp_name']) ?> Champion</span>
							<?php endforeach; ?>
						</div>
					</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>

	<div class="col-12 lg:col-5 xl:col-4">
		<!-- Abaikan dulu, nanti dibahas -->
		<div class="card card-body">
			<h4 class="card-title"><?= e($team['name']) ?> Timeline</h4>
			<ol class="stepper stepper-vertical">
				<li class="stepper-item align-items-start">
					<div class="pt-1">
						<div class="fw-semibold"><?= e($estYear) ?> — Founded</div>
						<small class="fg-2"><?= e($team['alt']) ?> was established in <?= e($estYear) ?>.</small>
					</div>
				</li>
				<li class="stepper-item align-items-start">
					<div class="pt-1">
						<div class="fw-semibold"> — First League Trophy</div>
						<small class="fg-2"><?= e($team['alt']) ?> won its first league trophy in 20XX.</small>
					</div>
				</li>
				<li class="stepper-item align-items-start">
					<div class="pt-1">
						<div class="fw-semibold"> — First Cup Trophy</div>
						<small class="fg-2"><?= e($team['alt']) ?> won its first cup trophy in 20XX.</small>
					</div>
				</li>
				<li class="stepper-item align-items-start">
					<div class="pt-1">
						<div class="fw-semibold"> — First <?= e(site('totm')) ?></div>
						<small class="fg-2"><?= e($team['alt']) ?> achieve its first <?= e(site('totm')) ?> in <?= e($team['entity']) ?>.</small>
					</div>
				</li>
			</ol>
		</div>
	</div>
</div>