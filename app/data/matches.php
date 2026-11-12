<?php
function team_logo(int $id): string {
	return BASE_URL . '/img/team/' . sprintf('id_%05d', $id) . '.png';
}

function match_competitions(): array {
	return db()->query(
		'SELECT c.compID AS id, c.compName AS name
		 FROM competition c
		 WHERE EXISTS (SELECT 1 FROM matches m WHERE m.matchComp = c.compID)
		 ORDER BY c.compName'
	)->fetchAll();
}

function match_cards(?int $comp = null, int $limit = 20): array {
	$sql = 'SELECT m.matchID AS id, m.matchDate AS d, m.matchTime AS t,
	               m.matchSH AS sh,  m.matchSA AS sa,
	               m.matchSHE AS she, m.matchSAE AS sae,
	               m.matchSHP AS shp, m.matchSAP AS sap,
	               se.seasonName AS season_name,
	               c.compID  AS comp_id,
	               c.compAlt AS comp_alt,
	               m.matchHome AS home_id, h.teamAlt AS home_alt,
	               m.matchAway AS away_id, a.teamAlt AS away_alt
			FROM matches m
			JOIN team h ON h.teamID = m.matchHome
			JOIN team a ON a.teamID = m.matchAway
			JOIN season se ON se.seasonID = m.matchSeason
			JOIN competition c ON c.compID = m.matchComp'
	     . ($comp !== null ? ' WHERE m.matchComp = :comp' : '')
	     . ' ORDER BY m.matchDate DESC, m.matchTime DESC, m.matchID DESC
			LIMIT :lim';

	$st = db()->prepare($sql);
	if ($comp !== null) $st->bindValue(':comp', $comp, PDO::PARAM_INT);
	$st->bindValue(':lim', $limit, PDO::PARAM_INT);
	$st->execute();

	return $st->fetchAll();
}

function css_slug(?string $s): string {
	$s = strtolower(trim((string)$s));
	return trim(preg_replace('/[^a-z0-9]+/', '-', $s), '-');
}

function match_view(array $m): array {
	$n = fn($v) => $v === null ? null : (int)$v;
	[$sh, $sa, $she, $sae, $shp, $sap] = [$n($m['sh']), $n($m['sa']), $n($m['she']),
	                                      $n($m['sae']), $n($m['shp']), $n($m['sap'])];

	$et = ($she !== null && $sae !== null);
	$h  = $et ? $she : $sh;
	$a  = $et ? $sae : $sa;

	$winner = null;
	if ($h !== null && $a !== null) {
		if ($h !== $a) $winner = $h > $a ? 'home' : 'away';
		elseif ($shp !== null && $sap !== null && $shp !== $sap) $winner = $shp > $sap ? 'home' : 'away';
	}

	$when = 'TBD';
	if ($m['d'] !== null) {
		$when = date('j M y', strtotime($m['d']));
		if ($m['t'] !== null) $when .= ' - ' . substr($m['t'], 0, 5);
	}
		return [
		'h' => $h, 'a' => $a, 'winner' => $winner, 'when' => $when,
		'comp_class' => 'comp-' . (css_slug($m['comp_alt']) ?: 'other'),
	];
}

function fixture_view(array $m): array {
	$v = match_view($m);
	$v['url'] = fixture_url($m);

	return $v;
}

function team_url(int $id, ?string $alt = null): string {
	return url('database', 'team') . '/' . $id . '/' . (slugify($alt) ?: 'team');
}

function match_when_long(array $m): string {
	if ($m['d'] === null) return 'TBD';
	$s = date('F j, Y', strtotime($m['d']));
	if ($m['t'] !== null) $s .= ' - ' . substr($m['t'], 0, 5);
	return $s;
}

/**
 * Semua pertandingan satu kompetisi + musim, dikelompokkan per matchweek:
 * [ week => [ baris, baris, ... ], ... ] (urut matchweek, lalu tanggal & jam).
 * Nama kolom sama dengan match_cards() supaya bisa dipakai match_view().
 */
function season_matches(int $compId, int $seasonId): array {
	$st = db()->prepare(
		'SELECT m.matchID AS id, m.matchWeek AS week,
		        m.matchDate AS d, m.matchTime AS t,
		        m.matchSH AS sh,  m.matchSA AS sa,
		        m.matchSHE AS she, m.matchSAE AS sae,
		        m.matchSHP AS shp, m.matchSAP AS sap,
		        m.matchStatus AS status,
		        m.matchSeason AS season_id,
		        se.seasonName AS season_name,
		        c.compAlt AS comp_alt,
		        m.matchHome AS home_id, h.teamAlt AS home_alt, h.teamShort AS home_short,
		        m.matchAway AS away_id, a.teamAlt AS away_alt, a.teamShort AS away_short
		 FROM matches m
		 JOIN team h ON h.teamID = m.matchHome
		 JOIN team a ON a.teamID = m.matchAway
		 JOIN season se ON se.seasonID = m.matchSeason
		 JOIN competition c ON c.compID = m.matchComp
		 WHERE m.matchComp = :c AND m.matchSeason = :s
		 ORDER BY m.matchWeek, (m.matchDate IS NULL), m.matchDate, (m.matchTime IS NULL), m.matchTime, m.matchID'
	);
	$st->execute(['c' => $compId, 's' => $seasonId]);

	$out = [];
	foreach ($st->fetchAll() as $r) $out[(int)$r['week']][] = $r;
	return $out;
}

/** Matchweek terbaru yang sudah ada skornya; kalau belum ada, matchweek pertama. */
function latest_week(array $byWeek): ?int {
	if (!$byWeek) return null;

	$latest = null;
	foreach ($byWeek as $w => $rows) {
		foreach ($rows as $r) {
			if ($r['sh'] !== null && $r['sa'] !== null) { $latest = (int)$w; break; }
		}
	}
	return $latest ?? (int)array_key_first($byWeek);
}

/** ?week=N kalau valid, selain itu matchweek terbaru. */
function pick_week(array $byWeek): ?int {
	if (!$byWeek) return null;

	$raw  = $_GET['week'] ?? null;
	$want = (is_string($raw) && ctype_digit($raw)) ? (int)$raw : null;
	return ($want !== null && isset($byWeek[$want])) ? $want : latest_week($byWeek);
}
