<?php
$seasons  = comp_seasons($compId);
$seasonId = pick_season($seasons);
if ($seasonId === null) { echo '<p class="text-center fg-secondary user-select-none">No standing yet.</p>'; return; }

$rows  = standings($compId, $seasonId);
$forms = last_five_all($compId, $seasonId);

$weeks    = comp_weeks($compId, $seasonId);
$week     = pick_matchweek($weeks);
$byWeek   = matches_by_week($compId, $seasonId);
$fallback = BASE_URL . '/img/team/default.png';

//foreach ($rows as $r) {
//	$teamID = (int)$r['team_id'];
//	$forms = last_five_all($compId, $seasonId);
//}
foreach ($seasons as $s) {
	if ((int)$s['id'] === $seasonId) {
		$seasonName = $s['name'];
		break;
	}
}

foreach ($byWeek as $w => $matches) {
	foreach ($matches as $i => $m) {
		$byWeek[$w][$i]['season_id']   = $seasonId;
		$byWeek[$w][$i]['season_name'] = $seasonName;
	}
}
?>

<div class="d-flex flex-column gap-3">
	<div class="standing">
		<form method="get" class="pb-3">
			<div class="d-flex align-items-center gap-3">
				<div class="form-field">
					<select class="form-control" id="season" name="season">
					<?php foreach ($seasons as $s): ?>
						<option value="<?= (int)$s['id'] ?>"<?= (int)$s['id'] === $seasonId ? ' selected' : '' ?>><?= e($s['name']) ?></option>
					<?php endforeach; ?>
					</select>
				</div>
				<button type="submit" class="btn-solid theme-primary">Show</button>
			</div>
		</form>

		<div class="table-responsive">
			<table class="table table-standing">
				<thead>
					<tr>
						<th>#</th>
						<th>Team</th>
						<th>P</th>
						<th>W</th>
						<th>D</th>
						<th>L</th>
					    <th>GF</th>
					    <th>GA</th>
					    <th>GD</th>
					    <th>Pts</th>
					    <th>Form</th>
					</tr>
				</thead>
				<tbody>
				<?php foreach ($rows as $i => $r): ?>
					<tr>
						<td><?= $i + 1 ?></td>
						<td>
							<a class="theme-inverse text-decoration-none"
								href="<?= e(team_info_url((int)$r['team_id'], $r['alt'], $r['name'])) ?>">
								<div class="d-flex align-items-center gap-3">
									<img class="logo-sm" src="<?= e(team_logo((int)$r['team_id'])) ?>" alt="" loading="lazy">
									<span class="lh-1"><?= e($r['name']) ?></span>
								</div>
							</a>
						</td>
						<td><?= (int)$r['p'] ?></td>
						<td><?= (int)$r['w'] ?></td>
						<td><?= (int)$r['d'] ?></td>
						<td><?= (int)$r['l'] ?></td>
						<td><?= (int)$r['gf'] ?></td>
						<td><?= (int)$r['ga'] ?></td>
						<td><?= (int)$r['gd'] ?></td>
						<td class="fw-bold"><?= (int)$r['pts'] ?></td>
						<td><?php $teamID = (int)$r['team_id'];
								  $lastFive = array_pad(array_values($forms[$teamID] ?? []), -5, null); ?>
							<div class="form-list">
							<?php foreach ($lastFive as $match): ?>
							<?php $res = matchResult($match, $teamID);
								  $class = match ($res) {
								  	'W' => 'bg-success fg-contrast-success',
								  	'L' => 'bg-danger fg-contrast-danger',
								  	'D' => 'bg-secondary fg-contrast-secondary',
								  	default => 'bg-1'
								  };
								  $tooltipAttr = '';
								  if ($match !== null && $res !== '-') {
								  	  $isHome = (int)$match['matchHome'] === $teamID;
								  	  $opponent = $isHome ? $match['awayShort'] : $match['homeShort'];

								  	  $score = $isHome ? $match['matchSH'] . '-' . $match['matchSA'] : $match['matchSA'] . '-' . $match['matchSH'];

								  	  $tooltip = 'Matchweek ' . (int)$match['matchWeek'] . ' - ' . ($isHome ? 'HOME vs ' : 'AWAY vs ') . e($opponent) . '<br>(' . (int)$match['matchSH'] . '-' . (int)$match['matchSA'] . ')';

								  	  $tooltipAttr = 'data-bs-toggle="tooltip" data-bs-html="true" data-bs-title="' . e($tooltip) . '"';
								  } ?>
								<span class="badge <?= e($class) ?>" <?= $tooltipAttr ?>><?= e($res) ?></span>
							<?php endforeach; ?>
							</div>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<div class="w-75">
			<div class="row row-cols-2 md:row-cols-3 g-2">
				<div class="col">
					<span class="d-flex align-items-center gap-1">
						<span>P:</span>
						<span>Play</span>
					</span>
				</div>
				<div class="col">
					<span class="d-flex align-items-center gap-1">
						<span>W:</span>
						<span>Win</span>
					</span>
				</div>
				<div class="col">
					<span class="d-flex align-items-center gap-1">
						<span>D:</span>
						<span>Draw</span>
					</span>
				</div>
				<div class="col">
					<span class="d-flex align-items-center gap-1">
						<span>L:</span>
						<span>Lose</span>
					</span>
				</div>
				<div class="col">
					<span class="d-flex align-items-center gap-1">
						<span>GF:</span>
						<span>Goal For</span>
					</span>
				</div>
				<div class="col">
					<span class="d-flex align-items-center gap-1">
						<span>GA:</span>
						<span>Goal Against</span>
					</span>
				</div>
				<div class="col">
					<span class="d-flex align-items-center gap-1">
						<span>GD:</span>
						<span>Goal Difference</span>
					</span>
				</div>
				<div class="col">
					<span class="d-flex align-items-center gap-1">
						<span>Pts:</span>
						<span>Points</span>
					</span>
				</div>
			</div>
		</div>
	</div>

	<?php if ($week !== null && $byWeek): ?>
	<div class="standing-matches">
		<div class="d-flex flex-column md:flex-row gap-5 align-items-start" data-week-root>

			<div class="card">
				<div class="card-body">
					<div class="d-flex flex-column gap-3">
						<button class="btn-solid btn-sm theme-primary" type="button"
						        data-bs-toggle="menu" aria-expanded="false" data-week-toggle>
							<span class="d-flex align-items-center">
								<span>Matchweek <span data-week-label><?= (int)$week ?></span></span>
								<i class="fa-solid fa-caret-down" id="careticon"></i>
							</span>
						</button>

						<div class="menu overflow-y-auto" style="max-height: 15rem;">
							<?php foreach (array_keys($byWeek) as $w): $on = ((int)$w === (int)$week); ?>
								<a class="menu-item<?= $on ? ' active' : '' ?>"<?= $on ? ' aria-current="true"' : '' ?>
								   href="#" data-week-go="<?= (int)$w ?>">Matchweek <?= (int)$w ?></a>
							<?php endforeach; ?>
						</div>

						<a class="btn-solid btn-sm theme-primary" data-week-link
						   href="<?= e(comp_url($compId, $comp['alt'], 'matches') . '?season=' . $seasonId . '&week=' . (int)$week) ?>">
							<span>Match details</span>
						</a>
					</div>
				</div>
			</div>

			<div class="mc" data-mc>
				<div class="mc-wrap">
					<button type="button" class="btn-icon btn-sm mc-nav mc-prev border" aria-label="Previous matches" disabled>
						<i class="fa-solid fa-chevron-left"></i>
					</button>

					<?php foreach ($byWeek as $w => $rows): ?>
						<div class="mc-track" data-week-panel="<?= (int)$w ?>" tabindex="0"
						     role="region" aria-label="Matchweek <?= (int)$w ?> matches"<?= (int)$w === (int)$week ? '' : ' hidden' ?>>
							<?php foreach ($rows as $m) component('match-card', compact('m', 'fallback')); ?>
						</div>
					<?php endforeach; ?>

					<button type="button" class="btn-icon btn-sm mc-nav mc-next border" aria-label="Next matches">
						<i class="fa-solid fa-chevron-right"></i>
					</button>
				</div>
			</div>

		</div>
	</div>
	<?php endif; ?>
</div>
