<?php
$tabs = ['summary' => 'Summary', 'line-up' => 'Line-Up', 'stats'   => 'Stats'];

$matchId = ctype_digit($args[1] ?? '') ? (int)$args[1] : 0;
$m       = $matchId ? fixture_by_id($matchId) : null;
$tab     = $args[3] ?? 'summary';

if (!$m || !isset($tabs[$tab])) {
	http_response_code(404);
	echo '<section class="text-center fg-secondary user-select-none"><h1>Match not found</h1></section>';
	return;
}

// alamat kanonis: musim dan alt-h-vs-alt-a mengikuti data, id yang menentukan
if (($args[0] ?? '') !== fixture_season_slug($m) || ($args[2] ?? '') !== fixture_slug($m) || ($tab === 'summary' && isset($args[3]))) {
	$qs = $_GET; unset($qs['route']);
	header('Location: ' . fixture_url($m, $tab) . ($qs ? '?' . http_build_query($qs) : ''), true, 302);
	exit;
}

page_title($m['home_alt'] . ' vs ' . $m['away_alt'] . ($tab === 'summary' ? '' : ' - ' . $tabs[$tab]));

$v        = match_view($m);
$note     = match_note($m);
$fallback = BASE_URL . '/img/team/default.png';
$kickoff  = kickoff_label($m['d'], $m['t']);
?>
<div class="d-flex flex-column" style="<?= e(match_style($m)) ?>">
	<div class="match-details">
		<a class="btn-solid btn-sm theme-home" href="<?= e(url('database', 'fixture')) ?>">
			<span class="d-none sm:d-inline">Fixture</span>
			<span>Menu</span>
		</a>
		<div class="d-flex flex-column">
			<span class="text-center mb-5"><?= e($m['comp_alt']) ?> &middot; Matchweek <?= (int)$m['week'] ?></span>
			<div class="md-teams">
				<div class="md-home" style="<?= e(team_style($m['home_color'], $m['home_subcol'])) ?>">
					<a class="md-h-link" href="<?= e(team_info_url((int)$m['home_id'], $m['home_alt'], $m['home_name'])) ?>">
						<div class="d-flex align-items-center gap-3">
							<span class="team-home">
								<span class="d-none sm:d-inline"><?= e($m['home_alt']) ?></span>
								<span class="d-inline sm:d-none"><?= e($m['home_short']) ?></span>
							</span>
							<img class="logo-md" src="<?= e(team_logo((int)$m['home_id'])) ?>" alt="" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='<?= e($fallback) ?>'">
						</div>
					</a>
				</div>
				<div class="md-score">
					<span class="team-score"><?= ($v['h'] !== null && $v['a'] !== null) ? (int)$v['h'] . ' - ' . (int)$v['a'] : 'VS' ?></span>
					<?php if ($note !== ''): ?><small class="d-block text-center"><?= e($note) ?></small><?php endif; ?>
				</div>
				<div class="md-away" style="<?= e(team_style($m['away_color'], $m['away_subcol'])) ?>">
					<a class="md-a-link" href="<?= e(team_info_url((int)$m['away_id'], $m['away_alt'], $m['away_name'])) ?>">
						<div class="d-flex align-items-center gap-3">
							<img class="logo-md" src="<?= e(team_logo((int)$m['away_id'])) ?>" alt="" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='<?= e($fallback) ?>'">
							<span class="team-away">
								<span class="d-none sm:d-inline"><?= e($m['away_alt']) ?></span>
								<span class="d-inline sm:d-none"><?= e($m['away_short']) ?></span>
							</span>
						</div>
					</a>
				</div>
			</div>

			<div class="d-flex flex-column lh-1 mt-3">
				<small class="text-center"><?= e($kickoff) ?></small>
				<small class="text-center fw-semibold"><?= e((string)$m['stadium']) ?></small>
			</div>
		</div>

		<div class="text-center mt-5">
			<ul class="nav nav-pills" aria-label="Match sections">
			<?php foreach ($tabs as $key => $label): $on = ($key === $tab); ?>
				<li class="nav-item">
					<a class="nav-link<?= $on ? ' active' : '' ?>"<?= $on ? ' aria-current="page"' : '' ?> href="<?= e(fixture_url($m, $key)) ?>"><?= e($label) ?></a>
				</li>
			<?php endforeach; ?>
			</ul>
		</div>
	</div>

	<section class="md:p-7 lg:p-9 p-4">
		<?php $tabFile = __DIR__ . "/tabs/$tab.php"; if (is_file($tabFile)) require $tabFile; ?>
	</section>
</div>
