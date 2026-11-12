<?php
// data/players.php - perhitungan pemain: OVR dan nama kemampuan

/**
 * Bobot kemampuan 1-5 untuk tiap posisi utama (persen; jumlah tiap baris 100).
 * Posisi di luar daftar ini mendapat OVR 40. Kunci = positionCode di tabel position.
 */
function ovr_weights(): array {
	return [
		'GK' => [20, 20, 20, 20, 20],
		'CB' => [10, 10, 10, 30, 40],
		'RB' => [20, 15, 15, 25, 25], 'LB' => [20, 15, 15, 25, 25],
		'DM' => [15, 10, 20, 30, 25],
		'CM' => [15, 15, 45, 15, 10],
		'AM' => [35, 20, 20, 20, 5],
		'RM' => [30, 20, 20, 20, 10], 'LM' => [30, 20, 20, 20, 10],
		'ST' => [15, 40, 10, 30, 5],
		'RW' => [30, 30, 20, 15, 5],  'LW' => [30, 30, 20, 15, 5],
	];
}

function is_gk(?string $position): bool {
	return strtoupper(trim((string)$position)) === 'GK';
}

/**
 * OVR dari kode posisi utama dan lima kemampuan [kemampuan1 .. kemampuan5], dibulatkan ke bilangan bulat.
 * Sama dengan rumus spreadsheet: MAX(40, jumlah kemampuan x bobot). Dihitung dengan bilangan bulat
 * (bobot dalam persen) supaya hasilnya tepat, tanpa selisih desimal. Bisa dipakai untuk posisi mana pun,
 * mis. OVR di posisi yang dimainkan pada suatu pertandingan.
 */
function player_ovr(?string $position, array $abilities): int {
	$w = ovr_weights()[strtoupper(trim((string)$position))] ?? null;
	if ($w === null) return 40;

	$sum = 0;                                    // dalam seperseratus
	foreach ($w as $i => $weight) $sum += (int)($abilities[$i] ?? 0) * $weight;
	return intdiv(max(4000, $sum) + 50, 100);    // 4000 = lantai 40; +50 lalu bagi 100 = pembulatan setengah ke atas
}

/** Lima kemampuan dari satu baris pability sebagai daftar [1..5]. */
function pability_values(array $row, ?string $position = null): array {
	$all = [(int)$row['paDri'], (int)$row['paAtt'], (int)$row['paPas'], (int)$row['paPhy'], (int)$row['paDef'],
			(int)$row['paHan'], (int)$row['paRef'], (int)$row['paKic'], (int)$row['paPoi'], (int)$row['paDiv']];
	if ($position === null) return $all;
	return array_slice($all, is_gk($position) ? 5 : 0, 5);
}

/**
 * Singkatan dan nama lima kemampuan sesuai posisi utama: kiper memakai kemampuan kiper,
 * posisi lain memakai kemampuan pemain luar. Singkatan kiper bebas Anda ganti.
 */
function ability_labels(?string $position): array {
	if (strtoupper(trim((string)$position)) === 'GK') {
		return [['HAN', 'GK Handling'], ['REF', 'GK Reflexing'], ['KIC', 'GK Kicking'], ['POS', 'GK Positioning'], ['DIV', 'GK Diving']];
	}
	return [['DRI', 'Dribbling'], ['ATT', 'Attacking'], ['PAS', 'Passing'], ['PHY', 'Physical'], ['DEF', 'Defending']];
}

/* ---------- Data pemain (tabel player + pability + region + cities + position) ---------- */

/** Nama tampilan: kalau playerName terisi hanya itu, selain itu playerFirst + playerLast. */
function player_display_name(array $p): string {
	$nick = trim((string)($p['nick'] ?? ''));
	if ($nick !== '') return $nick;
	$name = trim(trim((string)($p['first'] ?? '')) . ' ' . trim((string)($p['last'] ?? '')));
	return $name !== '' ? $name : trim((string)($p['full'] ?? ''));
}

function player_slug(array $p): string {
	return slugify(player_display_name($p)) ?: 'player';
}

// /database/player/{id}/{nama}[/{tab}] - tab 'overview' tanpa segmen tambahan
function player_url(array $p, string $tab = 'overview'): string {
	$u = url('database', 'player') . '/' . (int)$p['id'] . '/' . player_slug($p);
	return $tab !== 'overview' ? $u . '/' . $tab : $u;
}

/** Bendera = img/flag/in_{regionID 3 digit}.png. Gambar yang tidak ada ditangani onerror (img/flag/default.png). */
function flag_url(int $regionId): string {
	return BASE_URL . '/img/flag/in_' . sprintf('%03d', $regionId) . '.png';
}

/** Usia pada tanggal $asOf (kosong = hari ini). */
function player_age(?string $birth, ?string $asOf = null): ?int {
	$ts = $birth ? strtotime($birth) : false;
	if (!$ts) return null;
	return (new DateTime(date('Y-m-d', $ts)))->diff(new DateTime($asOf ?? 'today'))->y;
}

/** Lima kemampuan dari baris potret ber-alias a1..a5, atau null kalau pemain belum punya potret. */
function snapshot_abilities(?array $snap, ?string $position = null): ?array {
	if (!$snap) return null;
	$start = is_gk($position ?? ($snap['pos_code'] ?? null)) ? 6 : 1;
	$out = [];
	for ($i = $start; $i < $start + 5; $i++) $out[] = (int)($snap['a' . $i] ?? 0);
	return $out;
}

/** Satu pemain (data yang tidak berubah) beserta wilayah dan kota. playerCity boleh berupa ID di tabel cities atau teks. */
function player_by_id(int $id): ?array {
	$st = db()->prepare(
		'SELECT p.playerID AS id, p.playerFirst AS first, p.playerLast AS last, p.playerFull AS full, p.playerName AS nick,
		        p.playerRegion AS region_id, rg.regionName AS region_name, rg.regionCode AS region_code,
		        p.playerCity AS city_raw, ct.cityName AS city_name,
		        p.playerBirth AS birth, p.playerHeight AS height, p.playerWeight AS weight, p.playerFoot AS foot
		 FROM player p
		 LEFT JOIN region rg ON rg.regionID = p.playerRegion
		 LEFT JOIN cities ct ON ct.cityID = p.playerCity
		 WHERE p.playerID = :id'
	);
	$st->execute(['id' => $id]);
	$r = $st->fetch();
	if (!$r) return null;

	$raw      = trim((string)$r['city_raw']);
	$r['city'] = $r['city_name'] ?? (($raw !== '' && !ctype_digit($raw)) ? $raw : null);
	return $r;
}

/**
 * Potret pability sebuah pemain: yang terbaru, atau (dengan $asOf) yang terbaru dengan tanggal tidak lebih
 * dari $asOf. Kalau belum ada potret sampai tanggal itu, dipakai potret paling awal. null kalau tidak ada potret.
 */
function player_snapshot(int $playerId, ?string $asOf = null): ?array {
	$base = 'SELECT pa.paID AS pa_id, pa.paWeak AS weak, pa.paDate AS pa_date, pa.paPos AS pos_id,
					ps.positionCode AS pos_code, ps.positionName AS pos_name,
					pa.paDri AS a1, pa.paAtt AS a2, pa.paPas AS a3, pa.paPhy AS a4, pa.paDef AS a5, pa.paHan AS a6, pa.paRef AS a7, pa.paKic AS a8, pa.paPoi AS a9, pa.paDiv AS a10
			 FROM pability pa
			 LEFT JOIN `position` ps  ON ps.positionID  = pa.paPos
			 LEFT JOIN `position` ps1 ON ps1.positionID = pa.paPos1
			 LEFT JOIN `position` ps2 ON ps2.positionID = pa.paPos2
			 LEFT JOIN `position` ps3 ON ps3.positionID = pa.paPos3
			 WHERE pa.paPlayer = :p';

	if ($asOf !== null) {
		$st = db()->prepare($base . ' AND pa.paDate <= :d ORDER BY pa.paDate DESC, pa.paID DESC LIMIT 1');
		$st->execute(['p' => $playerId, 'd' => $asOf]);
		if ($row = $st->fetch()) return $row;
		$order = ' ORDER BY pa.paDate ASC, pa.paID ASC LIMIT 1';
	} else {
		$order = ' ORDER BY pa.paDate DESC, pa.paID DESC LIMIT 1';
	}
	$st = db()->prepare($base . $order);
	$st->execute(['p' => $playerId]);
	return $st->fetch() ?: null;
}

function snapshot_secondary(?array $snap): array {
	$out = [];
	foreach ([1, 2, 3] as $n) {
		$code = strtoupper(trim((string)($snap['pos' . $n . '_code'] ?? '')));
		$out[] = $code !== '' ? ['code' => $code, 'name' => (string)($snap['pos' . $n . '_name'] ?? $code)] : null;
	}
	return $out;
}

function radar_points(array $values, float $cx = 200, float $cy = 170, float $r = 110, int $w = 400, int $h = 330): array {
	$out = [];
	foreach (array_values($values) as $i => $v) {
		$a = deg2rad(-90 + $i * 72);
		$k = max(0, min(100, (int)$v)) / 100 * $r;
		$x = $cx + $k * cos($a);
		$y = $cy + $k * sin($a);
		$out[] = ['x' => round($x, 1), 'y' => round($y, 1),
				  'px' => round($x / $w * 100, 2), 'py' => round($y / $h * 100, 2)];
	}
	return $out;
}

/** Semua pemain dengan potret terbarunya masing-masing (pemain tanpa potret tetap ikut, kolom potretnya null). */
function players_current(): array {
	return db()->query(
		'SELECT p.playerID AS id, p.playerFirst AS first, p.playerLast AS last, p.playerFull AS full, p.playerName AS nick,
		        p.playerRegion AS region_id, rg.regionName AS region_name, p.playerBirth AS birth,
		        pa.paDate AS pa_date, pa.paWeak AS weak, pa.paPos AS pos_id, ps.positionCode AS pos_code, ps.positionName AS pos_name,
		        pa.paDri AS a1, pa.paAtt AS a2, pa.paPas AS a3, pa.paPhy AS a4, pa.paDef AS a5, pa.paHan AS a6, pa.paRef AS a7, pa.paKic AS a8, pa.paPoi AS a9, pa.paDiv AS a10
		 FROM player p
		 LEFT JOIN region rg ON rg.regionID = p.playerRegion
		 LEFT JOIN pability pa ON pa.paPlayer = p.playerID
		      AND pa.paID = (SELECT pb.paID FROM pability pb WHERE pb.paPlayer = p.playerID
		                     ORDER BY pb.paDate DESC, pb.paID DESC LIMIT 1)
		 LEFT JOIN `position` ps ON ps.positionID = pa.paPos
		 ORDER BY p.playerID'
	)->fetchAll();
}

/**
 * Daftar pemain terfilter dan terurut (OVR tertinggi dulu, lalu nama). Filter di $f (semua boleh kosong):
 * q (nama), nat (regionID), pos (posisi utama), amin / amax (usia), omin / omax (OVR).
 * Tiap baris ditambah: name, age, ovr. OVR dihitung di PHP, jadi penyaringannya juga di PHP.
 */
function players_find(array $f): array {
	$lower = function_exists('mb_strtolower') ? 'mb_strtolower' : 'strtolower';
	$q     = $lower(trim((string)($f['q'] ?? '')));

	$out = [];
	foreach (players_current() as $r) {
		$r['name'] = player_display_name($r);
		$r['age']  = player_age($r['birth']);
		$r['ovr']  = $r['pos_code'] !== null ? player_ovr($r['pos_code'], snapshot_abilities($r)) : null;

		if ($q !== '') {
			$hay = $lower($r['name'] . ' ' . $r['full'] . ' ' . $r['first'] . ' ' . $r['last'] . ' ' . $r['nick']);
			if (!str_contains($hay, $q)) continue;
		}
		if (($f['nat'] ?? null) !== null && (int)$r['region_id'] !== (int)$f['nat']) continue;
		if (($f['pos'] ?? null) !== null && (int)$r['pos_id'] !== (int)$f['pos']) continue;
		if (($f['amin'] ?? null) !== null && ($r['age'] === null || $r['age'] < $f['amin'])) continue;
		if (($f['amax'] ?? null) !== null && ($r['age'] === null || $r['age'] > $f['amax'])) continue;
		if (($f['omin'] ?? null) !== null && ($r['ovr'] === null || $r['ovr'] < $f['omin'])) continue;
		if (($f['omax'] ?? null) !== null && ($r['ovr'] === null || $r['ovr'] > $f['omax'])) continue;

		$out[] = $r;
	}
	usort($out, fn($a, $b) => [$b['ovr'] ?? -1, $a['name']] <=> [$a['ovr'] ?? -1, $b['name']]);
	return $out;
}

function regions_all(): array {
	return db()->query('SELECT regionID AS id, regionName AS name FROM region ORDER BY regionName, regionID')->fetchAll();
}

function positions_all(): array {
	return db()->query('SELECT positionID AS id, positionCode AS code, positionName AS name FROM `position` ORDER BY positionID')->fetchAll();
}