<?php
$main      = team_main_competition($teamId);
$tableRows = [];
$forms     = [];
$fullTable = null;
if ($main) {
	$mainComp   = (int)$main['comp_id'];
	$mainSeason = (int)$main['season_id'];

	$tableRows = table_window(standings($mainComp, $mainSeason), $teamId, 5);
	$allForms  = last_five_all($mainComp, $mainSeason);
	foreach ($tableRows as $r) {
		$tid         = (int)$r['team_id'];
		$forms[$tid] = $allForms[$tid] ?? [];
	}
	$fullTable = comp_url($mainComp, $main['comp_alt'], 'standings') . '?season=' . $mainSeason;
}

$fm = team_form_matches($teamId);

$estTs  = $estYear !== null ? strtotime((string)$team['est']) : false;
$estTxt = $estTs ? date('d F Y', $estTs) : null;
$age    = $estTs ? (new DateTime('@' . $estTs))->diff(new DateTime())->y : null;
$place  = implode(', ', array_filter([trim((string)$team['city']), trim((string)$team['region'])], 'strlen'));
$entity = trim((string)$team['entity']);
?>
<div class="row g-5">
	<div class="col-12 lg:col-7 xl:col-8 order-2 lg:order-1">
		<div class="row row-cols-1 g-5">
			<div class="col order-2 lg:order-1">
				<div class="card">
					<div class="card-header">
						<div class="d-flex justify-content-between align-items-center">
							<span class="card-title fw-semibold"><?= e($team['alt']) ?><?= $entity !== '' ? ' - ' . e($entity) : '' ?> Table</span>
							<?php if ($fullTable !== null): ?>
							<a type="button" class="btn-solid btn-sm theme-primary" href="<?= e($fullTable) ?>">See full table</a>
							<?php endif; ?><!-- Mengarah ke standing sesuai kompetisi -->
						</div>
					</div>
					<div class="card-body">
					<?php if ($tableRows): ?>
						<?php component('standing-table', ['rows' => $tableRows, 'forms' => $forms, 'activeTeam' => $teamId]); ?>
					<?php else: ?>
						<p class="text-center fg-secondary user-select-none mb-0">No standing yet.</p>
					<?php endif; ?>
					</div>
				</div>
			</div>

			<div class="col order-3 lg:order-2">
				<div class="row row-cols-1 md:row-cols-2 lg:row-cols-1 xl:row-cols-2 g-5">
					<div class="col">
					<?php component('team-spot', ['title' => 'Latest Match Result', 'm' => $fm['last'], 'teamId' => $teamId, 'empty' => 'No result yet.', 'fallback' => $fallback]); ?>
					</div>

					<div class="col">
					<?php component('team-spot', ['title' => 'Upcoming Match', 'm' => $fm['next'], 'teamId' => $teamId, 'empty' => 'No upcoming match.', 'fallback' => $fallback]); ?>
					</div>
				</div>
			</div>

			<div class="col order-1 lg:order-3">
				<!-- Tambahkan alt tiap kompetisi terkait ke class untuk menambahkan background-image dari CSS karena saya akan membuat banner khusus tiap kompetisi -->
				<!-- Dan kalau semisal sisa satu Upcoming Match, maka yang diambil 4 match sebelum dan 1 Upcoming Match -->
				<h4 class="mb-5"><?= e($team['name']) ?> - Fixtures &amp; Results</h4>
				<?php if ($fm['list']): ?>
				<div class="row row-cols-1 g-3">
				<?php foreach ($fm['list'] as $m): ?>
					<div class="col">
						<?php component('team-fixture', ['m' => $m, 'teamId' => $teamId, 'fallback' => $fallback]); ?>
					</div>
				<?php endforeach; ?>
				</div>
				<?php else: ?>
					<p class="text-center fg-secondary user-select-none">No fixtures yet.</p>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<div class="col-12 lg:col-5 xl:col-4 order-1 lg:order-2">
		<div class="row row-cols-1 g-5">
			<div class="col">
				<div class="card card-body">
					<div class="row row-cols-1">
						<div class="col">
							<h4 class="fw-semibold">About <?= e($team['alt']) ?></h4>
							<p class="text-justify"><?= e($team['name']) ?> is a football club based in <?= e($team['city']) ?>, <?= e($team['region']) ?>. <?= e($team['alt']) ?> competing in the <?= e($team['entity']) ?>. <?= e($team['alt']) ?> established in <?= e(date('d F Y', strtotime($team['est']))) ?>, participating in most of <?= e(site('name')) ?> competitions and leagues since <?= (new DateTime($team['est']))->diff(new DateTime())->y ?> years ago. <a class="text-decoration-none" href="<?= e(team_info_url($teamId, $team['alt'], $team['name'], 'history')) ?>">Check for more club history <i class="bi bi-arrow-right fs-xs"></i></a></p>
						</div>
						<div class="col">
							<h4>Team Informations</h4>
							<div class="d-flex flex-column">
								<?php if ($stadium !== ''): ?>
								<span class="d-inline">
									<span>Stadium:</span>
									<span><?= e($stadium) ?></span>
								</span>
								<?php endif; ?>

								<?php if ($seats !== null): ?>
								<span class="d-inline">
									<span>Capacity:</span>
									<span><?= e($seats) ?> seats</span>
								</span>
								<?php endif; ?>

								<?php if (trim((string)$team['city']) !== ''): ?>
								<span class="d-inline">
									<span>City:</span>
									<span><?= e($team['city']) ?></span>
								</span>
								<?php endif; ?>

								<?php if ($estTxt !== null): ?>
								<span class="d-inline">
									<span>Founded:</span>
									<span><?= e($estTxt) ?> (<?= (int)$age ?> Years)</span>
								</span>
								<?php endif; ?>

								<span class="d-flex align-items-center gap-1">
									<span>Club Color:</span>
									<div class="d-flex gap-2">
										<div class="card card-col"></div>
										<div class="card card-sub"></div>
									</div>
								</span>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col d-none md:d-block">
				<!-- Abaikan dulu, nanti dibahas -->
				<div class="card card-body">
					<h4>5-Goals & Assists</h4>
					<div class="row row-cols-1">
						<div class="col">
							<div class="table-responsive">
								<table class="table table-score">
									<thead>
										<tr>
											<th>#</th>
											<th>Player</th>
											<th>Goals</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td></td>
											<td></td>
											<td></td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>

						<div class="col">
							<div class="table-responsive">
								<table class="table table-score">
									<thead>
										<tr>
											<th>#</th>
											<th>Player</th>
											<th>Assists</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td></td>
											<td></td>
											<td></td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<a class="text-decoration-none align-self-start" href="<?= e(team_info_url($teamId, $team['alt'], $team['name'], 'stats')) ?>">To the stats <i class="bi bi-arrow-right fs-xs"></i></a>
				</div>
			</div>
		</div>
	</div>

</div>

