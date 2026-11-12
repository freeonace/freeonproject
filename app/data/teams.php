<?php // \app\data\teams.php
function team_slug(?string $alt, ?string $name = null): string {
	$src = trim((string)$alt) !== '' ? $alt : $name;
	return slugify($src) ?: 'team';
}

function team_info_url(int $id, ?string $alt = null, ?string $name = null, ?string $tab = null): string {
	$u = url('database', 'team') . '/' . $id . '/' . team_slug($alt, $name);
	return $tab ? $u . '/' . $tab : $u;
}

function team_by_id(int $id): ?array {
	$st = db()->prepare(
		'SELECT t.teamID AS id, t.teamName AS name, t.teamShort AS short, t.teamCity AS city, t.teamAlt AS alt, t.teamEst AS est,
		        t.teamColor AS color, t.teamSubCol AS subcol,
		        t.teamRegion AS region_id,
		        r.regionName AS region,
		        e.entityName AS entity,
		        s.stadeName  AS stadium, s.stadeCapc AS capacity
		 FROM team t
		 LEFT JOIN region r ON r.regionID = t.teamRegion
		 LEFT JOIN stadium s ON s.stadeID = t.teamStade
		 LEFT JOIN entity e ON e.entityID = t.teamEntity
		 WHERE t.teamID = :id'
	);
	$st->execute(['id' => $id]);
	return $st->fetch() ?: null;
}

function team_leagues(): array {
	return db()->query(
		"SELECT entityId AS id, entityName AS name
		 FROM entity
		 WHERE entityName LIKE '%League%'
		 AND entityID NOT IN (18,19,20,21)
		 ORDER BY entityName"
	)->fetchAll();
}

function teams_by_entities(array $entityIds): array {
	$ids = array_values(array_unique(array_map('intval', $entityIds)));
	if (!$ids) return [];

	$in = implode(',', array_fill(0, count($ids), '?'));
	$st = db()->prepare(
		"SELECT t.teamID AS id, t.teamName AS name, t.teamShort AS short, t.teamAlt AS alt,
		        t.teamCity AS city, t.teamColor AS color, t.teamSubCol AS subcol,
		        t.teamEntity AS entity_id, e.entityName AS entity, s.stadeName AS stadium
		 FROM team t
		 JOIN entity e ON e.entityId = t.teamEntity
		 LEFT JOIN stadium s ON s.stadeID = t.teamStade
		 WHERE t.teamEntity IN ($in)
		 ORDER BY t.teamID, t.teamName"
	);
	$st->execute($ids);

	$out = [];
	foreach ($st->fetchAll() as $r) $out[(int)$r['entity_id']][] = $r;
	return $out;
}

function hex_rgb(?string $c): ?array {
	$c = ltrim(trim((string)$c), '#');
	if (strlen($c) === 3) $c = $c[0] . $c[0] . $c[1] . $c[1] . $c[2] . $c[2];
	if (strlen($c) === 8) $c = substr($c, 0, 6);
	if (!preg_match('/^[0-9a-f]{6}$/i', $c)) return null;
	return [hexdec(substr($c, 0, 2)), hexdec(substr($c, 2, 2)), hexdec(substr($c, 4, 2))];
}

function rel_luminance(array $rgb): float {
	$f = static function ($v) {
		$v /= 255;
		return $v <= 0.03928 ? $v / 12.92 : (($v + 0.055) / 1.055) ** 2.4;
	};
	return 0.2126 * $f($rgb[0]) + 0.7152 * $f($rgb[1]) + 0.0722 * $f($rgb[2]);
}

function contrast_ratio(array $a, array $b): float {
	$la = rel_luminance($a);
	$lb = rel_luminance($b);
	return (max($la, $lb) + 0.05) / (min($la, $lb) + 0.05);
}

function rgb_hex(array $c): string {
	return sprintf('#%02x%02x%02x', $c[0], $c[1], $c[2]);
}

//function readable_on(array $bg): array {
//	return contrast_ratio($bg, [0, 0, 0]) >= contrast_ratio($bg, [255, 255, 255]) ? [0, 0, 0] : [255, 255, 255];
//}
function readable_on(array $bg, float $whiteMin = 3.5): array {
	$white = contrast_ratio($bg, [255, 255, 255]);
	$black = contrast_ratio($bg, [0, 0, 0]);
	return ($white >= $whiteMin || $white > $black) ? [255, 255, 255] : [0, 0, 0];
}

function team_style(?string $color, ?string $subcol): string {
	$main = hex_rgb($color)  ?? [248, 249, 250];
	$sub  = hex_rgb($subcol) ?? [33, 37, 41];

	$text   = readable_on($main);
	$stxt   = readable_on($sub);
	$accent = contrast_ratio($sub, $main) >= 3 ? $sub : $text;

	return '--team-color: ' . rgb_hex($main) . '; --team-subcol: ' . rgb_hex($sub)
	     . '; --hover: ' . rgb_hex($sub) . '; --color: ' . rgb_hex($text)
	     . '; --sbcolor: ' . rgb_hex($stxt) . '; --accent: ' . rgb_hex($accent) . ';';
}

function css_color(?string $c, string $fallback = 'transparent'): string {
	$c = trim((string)$c);
	if (preg_match('/^#?([0-9a-f]{3}|[0-9a-f]{6}|[0-9a-f]{8})$/i', $c, $m)) return '#' . $m[1];
	if (preg_match('/^[a-z]{3,20}$/i', $c)) return strtolower($c);
	return $fallback;
}

function team_color(?string $hex, string $fallback = '#f8f9fa'): string {
	$hex = ltrim(trim((string)$hex), '#');
	return preg_match('/^[0-9a-f]{6}$/i', $hex) ? '#' . strtolower($hex) : $fallback;
}

function text_color(string $hex, string $textOnBright = '#000', string $textOnDark = '#fff', int $threshold = 128): string {
	$hex = ltrim($hex, '#');
	$r = hexdec(substr($hex, 0, 2));
	$g = hexdec(substr($hex, 2, 2));
	$b = hexdec(substr($hex, 4, 2));
	$brightness = ($r * 299 + $g * 587 + $b * 114) / 1000;
	return $brightness > $threshold ? $textOnBright : $textOnDark;
}

function team_fg(int $id): string {
	return BASE_URL . '/img/flag/in_' . sprintf('%03d', $id) . '.png';
}

/* ---------- Halaman detail team: data ---------- */

/**
 * Kompetisi + musim "utama" sebuah tim (dipakai untuk tabel di Overview).
 * Dari tabel entry: musim terbaru yang diikuti tim; di musim itu dipilih kompetisi yang
 * entity-nya sama dengan entity tim, lalu yang laganya terbanyak. Kalau tim belum punya
 * entry sama sekali, dipakai cara lama dari data pertandingan.
 */
function team_main_competition(int $teamId): ?array {
	$st = db()->prepare(
		'SELECT e.entryComp AS comp_id, c.compName AS comp_name, c.compAlt AS comp_alt, e.entrySeason AS season_id
		 FROM entry e
		 JOIN competition c ON c.compID = e.entryComp
		 JOIN season s ON s.seasonID = e.entrySeason
		 JOIN team t ON t.teamID = e.entryTeam
		 WHERE e.entryTeam = :t
		 ORDER BY s.seasonYear DESC, e.entrySeason DESC,
		          (c.compEntity = t.teamEntity) DESC,
		          (SELECT COUNT(*) FROM matches m
		           WHERE m.matchComp = e.entryComp AND m.matchSeason = e.entrySeason
		             AND (m.matchHome = e.entryTeam OR m.matchAway = e.entryTeam)) DESC,
		          e.entryComp
		 LIMIT 1'
	);
	$st->execute(['t' => $teamId]);
	return $st->fetch() ?: team_main_competition_from_matches($teamId);
}

/** Cadangan untuk tim tanpa entry: tebakan dari data pertandingan (entity, musim terbaru yang sudah main, laga terbanyak). */
function team_main_competition_from_matches(int $teamId): ?array {
	$st = db()->prepare(
		'SELECT m.matchComp AS comp_id, c.compName AS comp_name, c.compAlt AS comp_alt,
		        m.matchSeason AS season_id, COUNT(*) AS n
		 FROM matches m
		 JOIN competition c ON c.compID = m.matchComp
		 JOIN season s ON s.seasonID = m.matchSeason
		 JOIN team t ON t.teamID = :t1
		 WHERE m.matchHome = :t2 OR m.matchAway = :t3
		 GROUP BY m.matchComp, c.compName, c.compAlt, m.matchSeason, s.seasonYear, c.compEntity, t.teamEntity
		 HAVING SUM(m.matchSH IS NOT NULL AND m.matchSA IS NOT NULL) > 0
		 ORDER BY (c.compEntity = t.teamEntity) DESC, s.seasonYear DESC, m.matchSeason DESC, n DESC
		 LIMIT 1'
	);
	$st->execute(['t1' => $teamId, 't2' => $teamId, 't3' => $teamId]);
	return $st->fetch() ?: null;
}

/**
 * Potongan tabel klasemen sebanyak $size baris yang memuat tim $teamId.
 * Tim di 3 besar -> baris 1-5, tim di 3 terbawah -> 5 baris terakhir, selain itu tim di tengah.
 * Kunci array (posisi - 1) dipertahankan agar nomor urut tetap benar.
 */
function table_window(array $rows, int $teamId, int $size = 5): array {
	$n = count($rows);
	if ($n <= $size) return $rows;

	$pos = null;
	foreach ($rows as $i => $r) {
		if ((int)$r['team_id'] === $teamId) { $pos = $i; break; }
	}
	if ($pos === null) return array_slice($rows, 0, $size, true);

	$start = max(0, min($pos - intdiv($size, 2), $n - $size));
	return array_slice($rows, $start, $size, true);
}

/** Laga sebuah tim lintas kompetisi: sudah dimainkan (terbaru dulu) atau belum (terdekat dulu). */
function team_matches(int $teamId, bool $played, int $limit): array {
	$cond  = $played
		? 'm.matchSH IS NOT NULL AND m.matchSA IS NOT NULL'
		: '(m.matchSH IS NULL OR m.matchSA IS NULL)';
	$order = $played
		? 'm.matchDate DESC, m.matchTime DESC, m.matchID DESC'
		: '(m.matchDate IS NULL), m.matchDate, (m.matchTime IS NULL), m.matchTime, m.matchID';

	$st = db()->prepare(
		'SELECT m.matchID AS id, m.matchWeek AS week,
		        m.matchDate AS d, m.matchTime AS t,
		        m.matchSH AS sh,  m.matchSA AS sa,
		        m.matchSHE AS she, m.matchSAE AS sae,
		        m.matchSHP AS shp, m.matchSAP AS sap,
		        m.matchSeason AS season_id,
		        se.seasonName AS season_name,
		        c.compID AS comp_id, c.compName AS comp_name, c.compAlt AS comp_alt,
		        m.matchHome AS home_id, h.teamName AS home_name, h.teamShort AS home_short, h.teamAlt AS home_alt,
		        m.matchAway AS away_id, a.teamName AS away_name, a.teamShort AS away_short, a.teamAlt AS away_alt
		 FROM matches m
		 JOIN team h ON h.teamID = m.matchHome
		 JOIN team a ON a.teamID = m.matchAway
		 JOIN season se ON se.seasonID = m.matchSeason
		 JOIN competition c ON c.compID = m.matchComp
		 WHERE (m.matchHome = :t1 OR m.matchAway = :t2) AND ' . $cond . '
		 ORDER BY ' . $order . '
		 LIMIT :lim'
	);
	$st->bindValue(':t1', $teamId, PDO::PARAM_INT);
	$st->bindValue(':t2', $teamId, PDO::PARAM_INT);
	$st->bindValue(':lim', $limit, PDO::PARAM_INT);
	$st->execute();
	return $st->fetchAll();
}

/**
 * Bahan Overview: laga terakhir ('last'), laga berikutnya ('next'), dan daftar 5 laga ('list',
 * terbaru di atas). Biasanya 2 laga mendatang + 3 hasil; kalau tinggal 1 laga mendatang,
 * 1 laga mendatang + 4 hasil; sisa slot diisi dari sisi yang masih punya data.
 */
function team_form_matches(int $teamId, int $total = 5, int $upcomingMax = 2): array {
	$up   = team_matches($teamId, false, $total);
	$past = team_matches($teamId, true, $total);

	$takeUp   = min(count($up), $upcomingMax);
	$takePast = min(count($past), $total - $takeUp);
	$takeUp   = min(count($up), $total - $takePast);

	return [
		'next' => $up[0] ?? null,
		'last' => $past[0] ?? null,
		'list' => array_merge(array_reverse(array_slice($up, 0, $takeUp)), array_slice($past, 0, $takePast)),
	];
}

/** 'W' / 'D' / 'L' dari sudut pandang tim; null kalau laga belum dimainkan. */
function team_outcome(array $m, int $teamId): ?string {
	$v = match_view($m);
	if ($v['h'] === null || $v['a'] === null) return null;
	if ($v['winner'] === null) return 'D';

	$side = ((int)$m['home_id'] === $teamId) ? 'home' : 'away';
	return $v['winner'] === $side ? 'W' : 'L';
}

/** Musim-musim yang diikuti tim (dari entry atau dari laga), terbaru dulu, format sama dengan comp_seasons(). */
function team_seasons(int $teamId): array {
	$st = db()->prepare(
		'SELECT s.seasonID AS id, s.seasonName AS name
		 FROM season s
		 WHERE EXISTS (SELECT 1 FROM entry e WHERE e.entryTeam = :t1 AND e.entrySeason = s.seasonID)
		    OR EXISTS (SELECT 1 FROM matches m
		               WHERE m.matchSeason = s.seasonID AND (m.matchHome = :t2 OR m.matchAway = :t3))
		 ORDER BY s.seasonYear DESC, s.seasonID DESC'
	);
	$st->execute(['t1' => $teamId, 't2' => $teamId, 't3' => $teamId]);
	return $st->fetchAll();
}

/** Kompetisi yang diikuti tim pada satu musim (dari entry atau dari laga). */
function team_comps(int $teamId, int $seasonId): array {
	$st = db()->prepare(
		'SELECT c.compID AS id, c.compName AS name
		 FROM competition c
		 WHERE EXISTS (SELECT 1 FROM entry e
		               WHERE e.entryTeam = :t1 AND e.entryComp = c.compID AND e.entrySeason = :s1)
		    OR EXISTS (SELECT 1 FROM matches m
		               WHERE m.matchComp = c.compID AND m.matchSeason = :s2
		                 AND (m.matchHome = :t2 OR m.matchAway = :t3))
		 ORDER BY c.compName'
	);
	$st->execute(['t1' => $teamId, 's1' => $seasonId, 's2' => $seasonId, 't2' => $teamId, 't3' => $teamId]);
	return $st->fetchAll();
}

/**
 * Rekor tim pada satu musim (opsional: satu kompetisi), hanya laga yang sudah ada skornya.
 * Hasil: ['all' => [...], 'home' => [...], 'away' => [...]], tiap isi: p, w, d, l, gf, ga, pts.
 */
function team_record(int $teamId, int $seasonId, ?int $compId = null): array {
	$c1 = $compId !== null ? ' AND matchComp = :c1' : '';
	$c2 = $compId !== null ? ' AND matchComp = :c2' : '';

	$st = db()->prepare(
		"SELECT r.side, COUNT(*) AS p,
		        SUM(r.gf > r.ga) AS w, SUM(r.gf = r.ga) AS d, SUM(r.gf < r.ga) AS l,
		        SUM(r.gf) AS gf, SUM(r.ga) AS ga
		 FROM (
		 	SELECT 'home' AS side, matchSH AS gf, matchSA AS ga FROM matches
		 	WHERE matchHome = :t1 AND matchSeason = :s1 AND matchSH IS NOT NULL AND matchSA IS NOT NULL" . $c1 . "
		 	UNION ALL
		 	SELECT 'away', matchSA, matchSH FROM matches
		 	WHERE matchAway = :t2 AND matchSeason = :s2 AND matchSH IS NOT NULL AND matchSA IS NOT NULL" . $c2 . "
		 ) r
		 GROUP BY r.side"
	);
	$params = ['t1' => $teamId, 's1' => $seasonId, 't2' => $teamId, 's2' => $seasonId];
	if ($compId !== null) { $params['c1'] = $compId; $params['c2'] = $compId; }
	$st->execute($params);

	$zero = ['p' => 0, 'w' => 0, 'd' => 0, 'l' => 0, 'gf' => 0, 'ga' => 0, 'pts' => 0];
	$out  = ['all' => $zero, 'home' => $zero, 'away' => $zero];

	foreach ($st->fetchAll() as $r) {
		$row = [];
		foreach (['p', 'w', 'd', 'l', 'gf', 'ga'] as $k) $row[$k] = (int)$r[$k];
		$row['pts'] = $row['w'] * 3 + $row['d'];

		$out[$r['side']] = $row;
		foreach ($row as $k => $v) $out['all'][$k] += $v;
	}
	return $out;
}

/** Rekor tim per musim (semua kompetisi), terbaru dulu. Musim yang diikuti lewat entry tetap tampil walau belum ada laga. */
function team_season_history(int $teamId): array {
	$st = db()->prepare(
		'SELECT s.seasonID AS season_id, s.seasonName AS season_name,
		        COUNT(r.gf) AS p,
		        COALESCE(SUM(r.gf > r.ga), 0) AS w, COALESCE(SUM(r.gf = r.ga), 0) AS d, COALESCE(SUM(r.gf < r.ga), 0) AS l,
		        COALESCE(SUM(r.gf), 0) AS gf, COALESCE(SUM(r.ga), 0) AS ga
		 FROM season s
		 LEFT JOIN (
		 	SELECT matchSeason AS season, matchSH AS gf, matchSA AS ga FROM matches
		 	WHERE matchHome = :t1 AND matchSH IS NOT NULL AND matchSA IS NOT NULL
		 	UNION ALL
		 	SELECT matchSeason, matchSA, matchSH FROM matches
		 	WHERE matchAway = :t2 AND matchSH IS NOT NULL AND matchSA IS NOT NULL
		 ) r ON r.season = s.seasonID
		 WHERE r.season IS NOT NULL
		    OR EXISTS (SELECT 1 FROM entry e WHERE e.entryTeam = :t3 AND e.entrySeason = s.seasonID)
		 GROUP BY s.seasonID, s.seasonName, s.seasonYear
		 ORDER BY s.seasonYear DESC, s.seasonID DESC'
	);
	$st->execute(['t1' => $teamId, 't2' => $teamId, 't3' => $teamId]);
	return $st->fetchAll();
}

/** Gelar juara (teamrecord.trStatus = 'Winner') sebuah tim, terbaru dulu. */
function team_titles(int $teamId): array {
	$st = db()->prepare(
		"SELECT tr.trSeason AS season_id, s.seasonName AS season_name,
		        c.compID AS comp_id, c.compName AS comp_name, c.compType AS type
		 FROM teamrecord tr
		 JOIN season s ON s.seasonID = tr.trSeason
		 JOIN competition c ON c.compID = tr.trComp
		 WHERE tr.trTeam = :t AND tr.trStatus = 'Winner'
		 ORDER BY s.seasonYear DESC, s.seasonID DESC, c.compID"
	);
	$st->execute(['t' => $teamId]);
	return $st->fetchAll();
}

/** 'league' / 'cup' / 'other' berdasarkan competition.compType. */
function title_kind(?string $type): string {
	$t = strtolower((string)$type);
	if (str_contains($t, 'league')) return 'league';
	if (str_contains($t, 'cup')) return 'cup';
	return 'other';
}
