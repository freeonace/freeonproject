<?php
// variabel: $t (baris dari teams_by_entities), $fallback (opsional)
$fallback = $fallback ?? (BASE_URL . '/img/team/default.png');
?>
<div class="card card-team" style="<?= e(team_style($t['color'], $t['subcol'])) ?>" title="<?= e($t['name']) ?>">
	<div class="card-team-body">
		<!-- <div class="top">
			<div class="card">A</div> 
		</div> -->
		<div class="d-flex gap-3 md:align-items-start align-items-center flex-column md:flex-row">
			<img class="logo-lg" src="<?= e(team_logo((int)$t['id'])) ?>" alt="" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='<?= e($fallback) ?>'">
			<div class="d-flex flex-column lh-sm">
				<span class="team">
					<span><?= e($t['name']) ?></span>
					<span class="d-none md:d-inline">(<?= e($t['short']) ?>)</span>
				</span>
				<span class="city"><?= e($t['city']) ?></span>
				<span class="stadium"><?= e($t['stadium'] ?? '') ?></span>
			</div>
		</div>
	</div>
	<div class="card-team-bottom border-top">
		<div class="bottom">
			<div class="d-flex justify-content-between align-items-center gap-2">
				<a class="stretched-link" href="<?= e(team_info_url((int)$t['id'], $t['alt'], $t['name'])) ?>">Team Info</a>
				<div class="badge theme-team"><?= e($t['entity']) ?></div>
			</div>
		</div>
	</div>
</div>