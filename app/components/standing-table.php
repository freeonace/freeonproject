<?php
// variabel: $rows, $forms, $limit (opsional)
$limit = $limit ?? null;
$activeTeam = (int)($activeTeam ?? 0);
$show  = $limit ? array_slice($rows, 0, $limit) : $rows;
?>
<div class="table-responsive">
	<table class="table table-standing">
		<thead>
			<tr>
				<th>#</th><th>Team</th><th>P</th><th>W</th><th>D</th><th>L</th>
				<th>GF</th><th>GA</th><th>GD</th><th>Pts</th><th>Form</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($show as $i => $r): $teamID = (int)$r['team_id']; ?>
			<tr <?= $teamID === $activeTeam ? ' class="table-active fw-bold"' : '' ?>>
				<td><?= $i + 1 ?></td>
				<td>
					<a class="text-decoration-none theme-inverse" href="<?= e(team_info_url((int)$r['team_id'], $r['alt'], $r['name'])) ?>">
						<div class="d-flex align-items-center gap-3">
							<img class="logo-sm" src="<?= e(team_logo($teamID)) ?>" alt="" width="24" height="24" loading="lazy">
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
				<td>
					<?php $lastFive = array_pad(array_values($forms[$teamID] ?? []), -5, null); ?>
					<div class="form-list">
					<?php foreach ($lastFive as $match):
						$res   = matchResult($match, $teamID);
						$class = match ($res) {
							'W' => 'bg-success fg-contrast-success',
							'L' => 'bg-danger fg-contrast-danger',
							'D' => 'bg-secondary fg-contrast-secondary',
							default => 'bg-1',
						};
						$tooltipAttr = '';
						if ($match !== null && $res !== '-') {
							$isHome   = (int)$match['matchHome'] === $teamID;
							$opponent = $isHome ? $match['awayShort'] : $match['homeShort'];
							$score    = $isHome ? (int)$match['matchSH'] . '-' . (int)$match['matchSA']
							                    : (int)$match['matchSA'] . '-' . (int)$match['matchSH'];
							$tooltip = 'Matchweek ' . (int)$match['matchWeek'] . ' - '
							         . ($isHome ? 'HOME vs ' : 'AWAY vs ') . e($opponent) . '<br>(' . $score . ')';
							$tooltipAttr = 'data-bs-toggle="tooltip" data-bs-html="true" data-bs-title="' . e($tooltip) . '"';
						}
					?>
						<span class="badge <?= e($class) ?>" <?= $tooltipAttr ?>><?= e($res) ?></span>
					<?php endforeach; ?>
					</div>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>