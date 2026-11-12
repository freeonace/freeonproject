<?php
$seasons  = team_seasons($teamId);
$seasonId = pick_season($seasons);
if ($seasonId === null) {
	echo '<p class="text-center fg-secondary user-select-none">No statistics yet.</p>';
	return;
}

$comps   = team_comps($teamId, $seasonId);
$compIds = array_map(fn($c) => (int)$c['id'], $comps);
$rawComp = $_GET['comp'] ?? null;
$compId  = (is_string($rawComp) && ctype_digit($rawComp) && in_array((int)$rawComp, $compIds, true)) ? (int)$rawComp : null;

$rec    = team_record($teamId, $seasonId, $compId);
$all    = $rec['all'];
$played = $all['p'];
$diff   = $all['gf'] - $all['ga'];
$pct    = fn(int $n): string => $played > 0 ? number_format($n / $played * 100, 1, '.', '') : '0';

$summary = [
	['Win',     $all['w']],
	['Draw',    $all['d']],
	['Lose',    $all['l']],
	['Matches', $played],
	['Goal',    $all['gf']],
	['Diff.',   ($diff > 0 ? '+' : '') . $diff],
];
?>
<div class="row row-cols-1 lg:row-cols-2 g-5">
	<div class="col">
		<div class="row row-cols-1 g-5">
			<div class="col">
				<div class="card card-body">
					<div class="d-flex flex-wrap justify-content-between align-items-center">
						<h4 class="card-title"><?= e($team['name']) ?> Statistics</h4>

						<form method="get">
							<div class="d-flex flex-wrap align-items-center gap-2">
								<div class="form-field">
									<select class="form-control form-control-sm" id="season" name="season" aria-label="Season">
									<?php foreach ($seasons as $s): ?>
										<option value="<?= (int)$s['id'] ?>"<?= (int)$s['id'] === $seasonId ? ' selected' : '' ?>><?= e($s['name']) ?></option>
									<?php endforeach; ?>
									</select>
								</div>
								<div class="form-field">
									<select class="form-control form-control-sm" id="comp" name="comp" aria-label="Competition">
										<option value="">All competitions</option>
									<?php foreach ($comps as $c): ?>
										<option value="<?= (int)$c['id'] ?>"<?= (int)$c['id'] === $compId ? ' selected' : '' ?>><?= e($c['name']) ?></option>
									<?php endforeach; ?>
									</select>
								</div>
								<button type="submit" class="btn-outline btn-sm theme-primary">Show</button>
							</div>
						</form>
					</div>


					<div class="row row-cols-2 sm:row-cols-3 g-5 text-center">
						<?php foreach ($summary as [$name, $value]): ?>
						<div class="col">
							<div class="d-flex flex-column">
								<strong class="fs-lg"><?= e((string)$value) ?></strong>
								<small class="fg-2"><?= e($name) ?></small>
							</div>
						</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>

			<div class="col">
				<!-- Abaikan dulu, nanti dibahas -->
				<div class="row row-cols-1 md:row-cols-2 lg:row-cols-1 g-5">
					<div class="col">
						<div class="card card-body">
							<h4 class="card-title">Top Scores</h4>
							<div class="table-responsive">
								<table class="table table-score">
									<thead>
										<tr>
											<th>#</th>
											<th>Player</th>
											<th>Goals</th>
										</tr>
									</thead>
									<tbody></tbody>
								</table>
							</div>
						</div>
					</div>

					<div class="col">
						<div class="card card-body">
							<h4 class="card-title">Top Assists</h4>
							<div class="table-responsive">
								<table class="table table-score">
									<thead>
										<tr>
											<th>#</th>
											<th>Player</th>
											<th>Assists</th>
										</tr>
									</thead>
									<tbody></tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col">
		<div class="row row-cols-1 md:row-cols-2 lg:row-cols-1 g-5">
			<div class="col">
				<div class="card card-body">
					<h4 class="card-title"><?= e($team['name']) ?> Performances</h4>
					<div class="row row-cols-1 g-5">
						<div class="col">
							<div class="d-flex flex-column gap-3">
								<span>Season Results</span>
								<?php if ($played > 0): ?>
								<div class="progress" role="progressbar" aria-label="Season results" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
									<?php if ($all['w'] > 0): ?>
									<div class="progress-bar bg-success" style="width: <?= $pct($all['w']) ?>%; border-radius: .5rem;">
										<?= (int)$all['w'] ?> Win
									</div>
									<?php endif; ?>
									<?php if ($all['d'] > 0): ?>
									<div class="progress-bar bg-secondary" style="width: <?= $pct($all['d']) ?>%; border-radius: .5rem;">
										<?= (int)$all['d'] ?> Draw
									</div>
									<?php endif; ?>
									<?php if ($all['l'] > 0): ?>
									<div class="progress-bar bg-danger" style="width: <?= $pct($all['l']) ?>%; border-radius: .5rem;">
										<?= (int)$all['l'] ?> Lose
										</div>
									<?php endif; ?>
								</div>
								<?php else: ?>
								<p class="fg-secondary user-select-none mb-0">No matches played yet.</p>
								<?php endif; ?>
								<div class="d-flex gap-5 align-items-center">
									<small class="fg-2">
										<i class="fa-solid fa-circle fg-success fs-xs"></i>Win
									</small>
									<small class="fg-2">
										<i class="fa-solid fa-circle fg-secondary fs-xs"></i>Draw
									</small>
									<small class="fg-2">
										<i class="fa-solid fa-circle fg-danger fs-xs"></i>Lose
									</small>
								</div>
							</div>
						</div>

						<div class="col">
							<div class="d-flex flex-column gap-3">
								<div class="d-flex justify-content-center">
									<canvas id="myChart" style="width:100%; max-width:20rem; height:100%; max-height:20rem"></canvas>
									<script>
										const summary = <?= json_encode($summary) ?>;
										const chartSummary = summary.slice(0, 3);
										const xValues = chartSummary.map(item => item[0] + " Percentage");
										const yValues = chartSummary.map(item => Number(item[1]));
										const barColors = [
											"oklch(64% .22 160)",
											"oklch(60% .02 245)",
											"oklch(60% .22 20)"
										];

										const ctx = document.getElementById('myChart');

										new Chart(ctx, {
											type: "doughnut",
											data: {
											//	labels: xValues,
												datasets: [{
													backgroundColor: barColors,
													data: yValues
												}]
											},
											options: {
												plugins: {
													legend: {display:true},
													//title: {
													//	display: true,
													//	text: "Match Percentage",
													//	font: {size:16}
													//}
													tooltip: {
														callbacks: {
															label: function(context) {
																const value = Number(context.raw);
																const data = context.dataset.data;

																const total = data.reduce(
																	(sum, item) => sum + Number(item), 0
																);

																const percentage = total > 0 ? (value / total * 100).toFixed(1) : '0.0';

																return `${context.label}: ${percentage}% (${value})`;
															}
														}
													}
												}
											}
										});
									</script>
								</div>

								<div class="d-flex flex-column">
									<span>Win: <?= $pct($all['w']) ?>%</span>
									<span>Draw: <?= $pct($all['d']) ?>%</span>
									<span>Lose: <?= $pct($all['l']) ?>%</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col">
				<div class="card card-body">
					<h4 class="card-title"><?= e($team['name']) ?> Home &amp; Away</h4>
					<div class="row row-cols-1 md:row-cols-2">
						<?php foreach ([['Home', $rec['home']], ['Away', $rec['away']]] as [$side, $r]): ?>
						<div class="col">
							<div class="d-flex flex-column">
								<strong><?= e($side) ?></strong>
								<span class="fs-lg"><?= (int)$r['w'] ?>-<?= (int)$r['d'] ?>-<?= (int)$r['l'] ?></span>
								<small class="fg-2">W-D-L</small>
								<br>
								<div class="d-inline">
									<span class="fg-2">Goals</span>
									<strong class="fs-lg"><?= (int)$r['gf'] ?></strong>
								</div>
								<div class="d-inline">
									<span class="fg-2">Points</span>
									<strong class="fs-lg"><?= (int)$r['pts'] ?></strong>
								</div>
							</div>
						</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
	</div>

</div>