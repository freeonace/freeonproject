<?php
$tabs = ['overview' => 'Overview', 'squad' => 'Squad', 'stats'   => 'Stats', 'history'  => 'History'];

$teamId = ctype_digit($args[0] ?? '') ? (int)$args[0] : 0;
$team   = $teamId ? team_by_id($teamId) : null;
$tab    = $args[2] ?? 'overview';

if (!$team || !isset($tabs[$tab])) {
	http_response_code(404);
	echo '<section class="text-center fg-secondary user-select-none"><h1>Team not found</h1></section>';
	return;
}

if (($args[1] ?? '') !== team_slug($team['alt'], $team['name']) || ($tab === 'overview' && isset($args[2]))) {
	header('Location: ' . team_info_url($teamId, $team['alt'], $team['name'], $tab === 'overview' ? null : $tab), true, 302);
	exit;
}

page_title($team['name']);

$fallback = BASE_URL . '/img/team/default.png';
$estYear = substr((string)$team['est'], 0, 4);
$estYear = ($estYear !== '' && $estYear !== '0000') ? $estYear : null;
$stadium = trim((string)$team['stadium']);
$seats   = (int)$team['capacity'] > 0 ? number_format((int)$team['capacity'], 0, ',', '.') : null;
?>
<div class="d-flex flex-column teams" style="<?= e(team_style($team['color'], $team['subcol'])) ?>">
	<div class="team-details">
		<div class="d-flex flex-column" style="gap: 3rem;">
			<div class="d-flex justify-content-between">
				<div class="d-flex gap-3 align-items-start">
					<img class="logo-team" src="<?= e(team_logo((int)$team['id'])) ?>" alt="" loading="lazy" decoding="async"
					     onerror="this.onerror=null;this.src='<?= e($fallback) ?>'">

					<div class="d-flex flex-column gap-1">
						<div class="d-flex flex-column align-items-start mb-3">
							<span class="team">
								<span><?= e($team['alt']) ?></span>
								<span class="d-none sm:d-inline">(<?= e($team['short']) ?>)</span>
							</span>
							<?php if (trim((string)$team['entity']) !== ''): ?>
							<div class="badge theme-team"><?= e($team['entity']) ?></div>
							<?php endif; ?>
						</div>

						<?php if ($estYear !== null || $stadium !== ''): ?>
						<span class="info">
						<?php if ($estYear !== null): ?>
							<span class="established"><?= e($estYear) ?></span>
							<?php endif; ?>
							<?php if ($estYear !== null && $stadium !== ''): ?><span>|</span><?php endif; ?>
								<?php if ($stadium !== ''): ?>
								<span class="stadium">
									<span><?= e($stadium) ?></span>
									<?php if ($seats !== null): ?>
										<span class="d-none md:d-inline">(<?= e($seats) ?> seats)</span>
									<?php endif; ?>
								</span>
							<?php endif; ?>
						</span>
						<?php endif; ?>
						<span class="city"><?= e($team['city']) ?></span>
					</div>
				</div>

				<a class="btn-solid btn-sm theme-team align-self-start" href="<?= e(url('database', 'team')) ?>">
					<span class="d-none sm:d-inline">Team</span>
					<span>Menu</span>
				</a>
			</div>

			<ul class="nav nav-pills justify-content-center align-self-center" aria-label="Team sections">
			<?php foreach ($tabs as $key => $label): $on = ($key === $tab); ?>
				<li class="nav-item">
					<a class="nav-link<?= $on ? ' active' : '' ?>"<?= $on ? ' aria-current="page"' : '' ?> href="<?= e(team_info_url($teamId, $team['alt'], $team['name'], $key === 'overview' ? null : $key)) ?>"><?= e($label) ?></a>
				</li>
			<?php endforeach; ?>
			</ul>
		</div>
	</div>

	<section class="md:p-7 lg:p-9 p-4">
		<?php require __DIR__ . "/tabs/$tab.php"; ?>
	</section>
</div>