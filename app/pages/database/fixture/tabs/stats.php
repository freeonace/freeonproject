<?php
// tabs/stats.php  (variabel dari detail.php: $m, $fallback)
// Sumber data: tabel mdetails, satu baris per tim per laga (lihat fixture_details()).
$det    = fixture_details($m);
$H      = $det['home'];
$A      = $det['away'];
$played = $m['sh'] !== null && $m['sa'] !== null;

if (!$played || !$H || !$A) {
	echo '<p class="text-center fg-secondary user-select-none">No statistics available.</p>';
	return;
}

$num  = fn(array $r, string $k): ?int => $r[$k] === null ? null : (int)$r[$k];
$show = fn(?int $v, string $suffix = ''): string => $v === null ? '-' : $v . $suffix;
$pct  = fn(float $v): string => number_format($v, 2, '.', '');

// Jumlah umpan dihitung dari umpan sukses (mdPC) dan akurasi (mdPA): umpan = mdPC * 100 / mdPA
$passes = function (array $r): ?int {
	$pa = $r['mdPA'] === null ? 0 : (int)$r['mdPA'];
	return ($pa > 0 && $r['mdPC'] !== null) ? (int)round((int)$r['mdPC'] * 100 / $pa) : null;
};
$pair  = fn(string $label, ?int $h, ?int $a): array => ['label' => $label, 'h' => $h, 'a' => $a];
$shots = fn(array $r): string => $show($num($r, 'mdShot')) . (($r['mdShot'] !== null && $r['mdOn'] !== null) ? ' (' . (int)$r['mdOn'] . ')' : '');
$cards = fn(array $r): string => $show($num($r, 'mdYC')) . '/' . $show($num($r, 'mdRC'));

// Kolom tengah kartu "Stats": label, nilai home, nilai away
$summary = [
	['Possession',        $show($num($H, 'mdPoss'), '%'), $show($num($A, 'mdPoss'), '%')],
	['Shots (On Target)', $shots($H),                      $shots($A)],
	['Pass Accuracy',     $show($num($H, 'mdPA'), '%'),    $show($num($A, 'mdPA'), '%')],
	['Free Kick',         $show($num($H, 'mdFK')),         $show($num($A, 'mdFK'))],
	['Corner Kick',       $show($num($H, 'mdCK')),         $show($num($A, 'mdCK'))],
	['Fouls',             $show($num($H, 'mdFoul')),       $show($num($A, 'mdFoul'))],
	['Yellow/Red',        $cards($H),                      $cards($A)],
];

// Kartu bar per kelompok: hs = home stats, as = away stats
$groups = [
	'Possession Game Ratings' => [
		$pair('Passes',          $passes($H),             $passes($A)),
		$pair('Passes Complete', $num($H, 'mdPC'),        $num($A, 'mdPC')),
		$pair('Touches',         $num($H, 'mdTouch'),     $num($A, 'mdTouch')),
	],
	'Attacking Game Ratings' => [
		$pair('Shots',     $num($H, 'mdShot'),   $num($A, 'mdShot')),
		$pair('On Target', $num($H, 'mdOn'),     $num($A, 'mdOn')),
		$pair('Crosses',   $num($H, 'mdCross'),  $num($A, 'mdCross')),
		$pair('Chances',   $num($H, 'mdChance'), $num($A, 'mdChance')),
		$pair('Dribbles',  $num($H, 'mdDri'),    $num($A, 'mdDri')),
	],
	'Defending Game Ratings' => [
		$pair('Clearances', $num($H, 'mdClear'),  $num($A, 'mdClear')),
		$pair('Tackles',    $num($H, 'mdTackle'), $num($A, 'mdTackle')),
		$pair('Saves',      $num($H, 'mdSave'),   $num($A, 'mdSave')),
		$pair('Blocks',     $num($H, 'mdBlock'),  $num($A, 'mdBlock')),
		$pair('Offsides',   $num($H, 'mdOff'),    $num($A, 'mdOff')),
	],
];

// Overall Matches: total seluruh angka pembanding home / (home + away); pasangan yang tidak lengkap dilewati
$sumH = $sumA = 0;
foreach ($groups as $rows) {
	foreach ($rows as $r) {
		if ($r['h'] !== null && $r['a'] !== null) { $sumH += $r['h']; $sumA += $r['a']; }
	}
}
[$overH, $overA] = stat_share($sumH, $sumA);
?>
<div class="d-flex flex-column gap-5">
	<div class="d-flex flex-column gap-5">
		<div class="d-flex justify-content-between align-items-center">
			<div class="d-flex flex-column align-items-center" style="flex: 1;">
				<img class="logo-lg" src="<?= e(team_logo((int)$m['home_id'])) ?>" alt="" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='<?= e($fallback) ?>'">
			</div>
			<div class="d-flex flex-column" style="flex: 1; text-align: center;">
				<strong class="text-2xl"><?= (int)round($overH) ?>:<?= 100 - (int)round($overH) ?></strong>
				<small class="lh-1">Overall Matches</small>
			</div>
			<div class="d-flex flex-column align-items-center" style="flex: 1;">
				<img class="logo-lg" src="<?= e(team_logo((int)$m['away_id'])) ?>" alt="" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='<?= e($fallback) ?>'">
			</div>
		</div>

		<div class="percentage" style="--hs-percentage: <?= $pct($overH) ?>%; --as-percentage: <?= $pct($overA) ?>%;"></div>
	</div>

	<div class="card card-body">
		<div class="d-flex flex-column gap-3">
			<div class="d-flex justify-content-evenly align-items-end">
				<div class="d-flex flex-column align-items-center">
					<img class="logo-md" src="<?= e(team_logo((int)$m['home_id'])) ?>" alt="" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='<?= e($fallback) ?>'">
				</div>
				<div class="d-flex flex-column" style="text-align: center;">
					<span>Stats</span>
				</div>
				<div class="d-flex flex-column align-items-center">
					<img class="logo-md" src="<?= e(team_logo((int)$m['away_id'])) ?>" alt="" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='<?= e($fallback) ?>'">
				</div>
			</div>

			<div class="row">
				<div class="col-3 md:col-4 lg:col-5 text-end">
					<div class="d-flex flex-column">
						<?php foreach ($summary as $row): ?>
						<span><?= e($row[1]) ?></span>
						<?php endforeach; ?>
					</div>
				</div>

				<div class="col-6 md:col-4 lg:col-2 text-center">
					<div class="d-flex flex-column">
						<?php foreach ($summary as $row): ?>
						<span><?= e($row[0]) ?></span>
						<?php endforeach; ?>
					</div>
				</div>

				<div class="col-3 md:col-4 lg:col-5 text-start">
					<div class="d-flex flex-column">
						<?php foreach ($summary as $row): ?>
						<span><?= e($row[2]) ?></span>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php foreach ($groups as $title => $rows): ?>
	<div class="card card-body">
		<span class="text-center fs-lg"><?= e($title) ?></span>
		<div class="d-flex flex-column gap-2">
			<?php foreach ($rows as $r):
				[$hs, $as] = stat_share($r['h'], $r['a']);
			?>
			<div class="d-flex flex-column">
				<span><?= e($r['label']) ?></span>
				<div class="d-flex justify-content-center align-items-center gap-3">
					<small class="data-home"><?= e($show($r['h'])) ?></small>
					<div class="percentage" style="--hs-percentage: <?= $pct($hs) ?>%; --as-percentage: <?= $pct($as) ?>%;"><!-- hs=home stats, as=away stats --></div>
					<small class="data-away"><?= e($show($r['a'])) ?></small>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php endforeach; ?>
</div>
