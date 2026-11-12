<?php
// data/fixtures.php - data halaman Fixture (daftar & detail pertandingan)

/** SELECT bersama untuk daftar & detail: kedua tim lengkap dengan warna (teamColor / teamSubCol). */
function fixture_select(): string {
	return 'SELECT m.matchID AS id, m.matchWeek AS week,
	        m.matchDate AS d, m.matchTime AS t,
	        m.matchSH AS sh, m.matchSA AS sa,
	        m.matchSHE AS she, m.matchSAE AS sae,
	        m.matchSHP AS shp, m.matchSAP AS sap,
	        m.matchSeason AS season_id, se.seasonName AS season_name,
	        c.compID AS comp_id, c.compName AS comp_name, c.compAlt AS comp_alt, en.entityName AS comp_entity,
	        sd.stadeName AS stadium,
	        m.matchHome AS home_id, h.teamName AS home_name, h.teamAlt AS home_alt, h.teamShort AS home_short,
	        h.teamColor AS home_color, h.teamSubCol AS home_subcol,
	        m.matchAway AS away_id, a.teamName AS away_name, a.teamAlt AS away_alt, a.teamShort AS away_short,
	        a.teamColor AS away_color, a.teamSubCol AS away_subcol
	 FROM matches m
	 JOIN team h ON h.teamID = m.matchHome
	 JOIN team a ON a.teamID = m.matchAway
	 JOIN competition c ON c.compID = m.matchComp
	 JOIN season se ON se.seasonID = m.matchSeason
	 LEFT JOIN entity en ON en.entityID = c.compEntity
	 LEFT JOIN stadium sd ON sd.stadeID = m.matchStade';
}

function fixture_by_id(int $id): ?array {
	$st = db()->prepare(fixture_select() . ' WHERE m.matchID = :id');
	$st->execute(['id' => $id]);
	return $st->fetch() ?: null;
}

/**
 * Laga satu musim dengan matchType tertentu (bawaan 'League'; nanti bisa 'Cup' dsb.),
 * opsional satu kompetisi. Urut: pekan, kompetisi, lalu waktu main.
 */
function fixture_list(int $seasonId, ?int $compId = null, string $type = 'League'): array {
	$st = db()->prepare(
		fixture_select()
		. ' WHERE m.matchSeason = :s AND m.matchType = :ty' . ($compId !== null ? ' AND m.matchComp = :c' : '')
		. ' ORDER BY m.matchWeek, c.compID, (m.matchDate IS NULL), m.matchDate, (m.matchTime IS NULL), m.matchTime, m.matchID'
	);
	$params = ['s' => $seasonId, 'ty' => $type];
	if ($compId !== null) $params['c'] = $compId;
	$st->execute($params);
	return $st->fetchAll();
}

/**
 * Daftar pekan dari hasil fixture_list(): gabungan pekan semua kompetisi (jadi kompetisi dengan
 * pekan terbanyak menjadi patokan). Tiap pekan: week, played (laga yang sudah ada skornya),
 * total (semua laga pekan itu). Kunci week dan played cocok dengan komponen week-nav.
 */
function fixture_weeks(array $rows): array {
	$weeks = [];
	foreach ($rows as $r) {
		$w = (int)$r['week'];
		if (!isset($weeks[$w])) $weeks[$w] = ['week' => $w, 'played' => 0, 'total' => 0];
		$weeks[$w]['total']++;
		if ($r['sh'] !== null && $r['sa'] !== null) $weeks[$w]['played']++;
	}
	ksort($weeks);
	return array_values($weeks);
}

/**
 * Pekan yang tampil: ?week=N kalau valid. Selain itu pekan pertama yang belum selesai semua
 * (tetap di pekan itu selama masih ada satu laga yang belum dimainkan; pindah ke pekan berikutnya
 * baru setelah semua laganya selesai). Kalau semua pekan sudah selesai, pekan terakhir.
 */
function pick_current_week(array $weeks): ?int {
	if (!$weeks) return null;
	$nums = array_map(fn($r) => (int)$r['week'], $weeks);

	$want = $_GET['week'] ?? null;
	if (is_string($want) && ctype_digit($want) && in_array((int)$want, $nums, true)) return (int)$want;

	foreach ($weeks as $r) {
		if ((int)$r['played'] < (int)$r['total']) return (int)$r['week'];
	}
	return $nums[count($nums) - 1];
}

/** [pekan => [kompetisi => [baris, ...]]] dari hasil fixture_list(). */
function fixture_group(array $rows): array {
	$out = [];
	foreach ($rows as $r) $out[(int)$r['week']][(int)$r['comp_id']][] = $r;
	ksort($out);
	return $out;
}

/** Musim yang punya laga bertipe $type (terbaru dulu), format sama dengan comp_seasons(). */
function fixture_seasons(string $type = 'League'): array {
	$st = db()->prepare(
		'SELECT s.seasonID AS id, s.seasonName AS name
		 FROM season s
		 WHERE EXISTS (SELECT 1 FROM matches m WHERE m.matchSeason = s.seasonID AND m.matchType = :ty)
		 ORDER BY s.seasonYear DESC, s.seasonID DESC'
	);
	$st->execute(['ty' => $type]);
	return $st->fetchAll();
}

/** Kompetisi yang punya laga bertipe $type pada satu musim. */
function fixture_comps(int $seasonId, string $type = 'League'): array {
	$st = db()->prepare(
		'SELECT c.compID AS id, c.compName AS name
		 FROM competition c
		 WHERE EXISTS (SELECT 1 FROM matches m
		               WHERE m.matchComp = c.compID AND m.matchSeason = :s AND m.matchType = :ty)
		 ORDER BY c.compName'
	);
	$st->execute(['s' => $seasonId, 'ty' => $type]);
	return $st->fetchAll();
}

function fixture_h2h(array $m, int $limit = 5): array {
	$sql = fixture_select()
		. ' WHERE ((m.matchHome = :h1 AND m.matchAway = :a1) OR (m.matchHome = :a2 AND m.matchAway = :h2))'
		. ' AND m.matchSH IS NOT NULL AND m.matchSA IS NOT NULL AND m.matchID <> :id'
		. ($m['d'] !== null ? ' AND (m.matchDate IS NULL OR m.matchDate <= :d)' : '')
		. ' ORDER BY m.matchDate DESC, m.matchTime DESC, m.matchID DESC LIMIT :lim';

	$st = db()->prepare($sql);
	$st->bindValue(':h1', (int)$m['home_id'], PDO::PARAM_INT);
	$st->bindValue(':a1', (int)$m['away_id'], PDO::PARAM_INT);
	$st->bindValue(':a2', (int)$m['away_id'], PDO::PARAM_INT);
	$st->bindValue(':h2', (int)$m['home_id'], PDO::PARAM_INT);
	$st->bindValue(':id', (int)$m['id'], PDO::PARAM_INT);
	if ($m['d'] !== null) $st->bindValue(':d', $m['d']);
	$st->bindValue(':lim', $limit, PDO::PARAM_INT);
	$st->execute();
	return $st->fetchAll();
}

function formation_slots(): array {
	return [
		'GK'  => 'gkpos',
		'RB'  => 'sbpos', 'RCB' => 'cbpos', 'CB'  => 'cbpos', 'LCB' => 'cbpos', 'LB' => 'sbpos',
		'RDM' => 'dmpos', 'DM'  => 'dmpos', 'LDM' => 'dmpos',
		'RCM' => 'cmpos', 'CM'  => 'cmpos', 'LCM' => 'cmpos',
		'RM'  => 'smpos', 'LM'  => 'smpos',
		'RAM' => 'ampos', 'AM'  => 'ampos', 'LAM' => 'ampos',
		'RW'  => 'swpos', 'LW'  => 'swpos',
		'RS'  => 'stpos', 'ST'  => 'stpos', 'LS'  => 'stpos',
	];
}

function fixture_details(array $m): array {
	$st = db()->prepare(
		'SELECT d.*, f.*
		 FROM mdetails d
		 LEFT JOIN formation f ON f.formID = d.mdFor
		 WHERE d.mdMatch = :m
		 ORDER BY d.mdID'
	);
	$st->execute(['m' => (int)$m['id']]);

	$out = ['home' => null, 'away' => null];
	foreach ($st->fetchAll() as $r) {
		$team = (int)$r['mdTeam'];
		$side = $team === (int)$m['home_id'] ? 'home' : ($team === (int)$m['away_id'] ? 'away' : null);
		if ($side !== null && $out[$side] === null) $out[$side] = $r;
	}
	return $out;
}

function stat_share(?int $h, ?int $a): array {
	if ($h === null || $a === null || $h + $a <= 0) return [50.0, 50.0];
	$hs = round($h / ($h + $a) * 100, 2);
	return [$hs, round(100 - $hs, 2)];
}

/* ---------- URL: /database/fixture/{musim}/{id}/{alt-home-vs-alt-away} ---------- */

function fixture_season_slug(array $m): string {
	return slugify($m['season_name']) ?: (string)(int)$m['season_id'];
}

function fixture_slug(array $m): string {
	return slugify($m['home_alt']) . '-vs-' . slugify($m['away_alt']);
}

// Satu-satunya tempat yang perlu diubah kalau alamat detail pindah (mis. ke /matches/...).
function fixture_url(array $m, string $tab = 'summary'): string {
	$u = url('database', 'fixture') . '/' . fixture_season_slug($m) . '/' . (int)$m['id'] . '/' . fixture_slug($m);
	return $tab !== 'summary' ? $u . '/' . $tab : $u;
}

/* ---------- Warna kedua tim ---------- */

/**
 * Variabel CSS untuk satu pertandingan, dipasang di style="" pada pembungkusnya.
 * Tiap tim dapat awalan sisi: --home-* dan --away-*
 *   -color   warna utama tim      -subcol  warna kedua
 *   -fg      teks yang terbaca di atas -color
 *   -subfg   teks yang terbaca di atas -subcol
 *   -accent  warna aksen (subcol bila kontrasnya cukup, selain itu -fg)
 * Warna kosong / tidak valid diganti warna bawaan yang sama dengan team_style().
 */
function match_style(array $m): string {
	$out = '';
	foreach (['home', 'away'] as $side) {
		$main = hex_rgb($m[$side . '_color']  ?? null) ?? [248, 249, 250];
		$sub  = hex_rgb($m[$side . '_subcol'] ?? null) ?? [33, 37, 41];
		$text = readable_on($main);
		$stxt = readable_on($sub);
		$accent = contrast_ratio($sub, $main) >= 3 ? $sub : $text;

		$out .= '--' . $side . '-color: '  . rgb_hex($main)
		      . '; --' . $side . '-subcol: ' . rgb_hex($sub)
		      . '; --' . $side . '-fg: '     . rgb_hex($text)
		      . '; --' . $side . '-subfg: '  . rgb_hex($stxt)
		      . '; --' . $side . '-accent: ' . rgb_hex($accent) . ';';
		$out .= ' ';
	}
	return trim($out);
}
