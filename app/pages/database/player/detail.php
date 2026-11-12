<?php
$tabs = ['overview' => 'Overview', 'stats' => 'Stats', 'history' => 'History', 'matches' => 'Matches'];

$playerId = ctype_digit($args[0] ?? '') ? (int)$args[0] : 0;
$p        = $playerId ? player_by_id($playerId) : null;
$tab      = $args[2] ?? 'overview';

if (!$p || !isset($tabs[$tab])) {
	http_response_code(404);
	echo '<section class="text-center fg-secondary user-select-none"><h1>Player not found</h1></section>';
	return;
}

// alamat kanonis: nama mengikuti data (nama panggilan bila ada), overview tanpa segmen tab
if (($args[1] ?? '') !== player_slug($p) || ($tab === 'overview' && isset($args[2]))) {
	$qs = $_GET; unset($qs['route']);
	header('Location: ' . player_url($p, $tab) . ($qs ? '?' . http_build_query($qs) : ''), true, 302);
	exit;
}

$nick = trim((string)$p['nick']);
page_title(player_display_name($p));

// potret terbaru (posisi + kemampuan); halaman musim lampau nanti memakai player_snapshot($id, tanggal)
$snap = player_snapshot($playerId);
$abil = snapshot_abilities($snap);
$ovr  = $snap && $snap['pos_code'] !== null ? player_ovr($snap['pos_code'], $abil) : null;
$age  = player_age($p['birth']);
$born = $p['birth'] ? date('M j, Y', strtotime($p['birth'])) : '-';
$flagFallback = BASE_URL . '/img/flag/default.png';
?>
<div class="d-flex flex-column">
	<div class="player-team">
		<a type="button" class="btn-solid" href="<?= e(url('database', 'player')) ?>">
			<span>Player Menu</span>
		</a>
		<div class="player-details">
			<div class="pd-info">
				<div class="player">
					<!-- <i class="fa-solid fa-circle-user"></i> -->
					<?php /* nomor punggung (class "number") menunggu tabel assignment */ ?>
					<div class="player-name">
						<?php if ($nick !== ''): ?>
							<span class="last-name"><?= e($nick) ?></span>
						<?php else: ?>
							<span class="first-name"><?= e($p['first']) ?></span>
							<span class="last-name"><?= e($p['last']) ?></span>
						<?php endif; ?>
					</div>
				</div>

				<div class="info-detail">
					<div class="player-info">
						<span class="age"><?= $age !== null ? (int)$age : '-' ?></span>
						<span class="info"><?= e($born) ?></span>
					</div>

					<div class="divider"></div>

					<div class="player-info">
						<span class="pos"><?= e($snap['pos_code'] ?? '-') ?></span>
						<span class="info"><?= e($snap['pos_name'] ?? 'No position') ?></span>
					</div>

					<div class="divider"></div>

					<div class="player-info">
						<span class="national">
							<img src="<?= e(flag_url((int)$p['region_id'])) ?>" alt="" onerror="this.onerror=null;this.src='<?= e($flagFallback) ?>'">
						</span>
						<span class="info"><?= e($p['region_name'] ?? '-') ?></span>
					</div>

					<div class="divider"></div>

					<div class="player-info">
						<span class="height"><?= (int)$p['height'] ?></span>
						<span class="info">Height (cm)</span>
					</div>
				</div>
			</div>
		</div>

		<div class="nav-overflow" data-bs-toggle="nav-overflow">
			<ul class="nav nav-tabs">
				<?php foreach ($tabs as $key => $label): $on = ($key === $tab); ?>
				<li class="nav-item">
					<a class="nav-link<?= $on ? ' active' : '' ?>"<?= $on ? ' aria-current="page"' : '' ?> href="<?= e(player_url($p, $key)) ?>"><?= e($label) ?></a>
				</li>
			<?php endforeach; ?>
			</ul>
		</div>
	</div>

	<section class="md:p-7 lg:p-9 p-4">
		<?php $tabFile = __DIR__ . "/tabs/$tab.php"; if (is_file($tabFile)) require $tabFile; ?>
	</section>
</div>