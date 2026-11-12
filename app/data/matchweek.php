<?php
function comp_weeks(int $compId, int $seasonId): array {
	$st = db()->prepare(
		'SELECT matchWeek AS week,
				SUM(matchSH IS NOT NULL AND matchSA IS NOT NULL) AS played
		 FROM matches
		 WHERE matchComp = :c AND matchSeason = :s
		 GROUP BY matchWeek
		 ORDER BY matchWeek'
	);
	$st->execute(['c' => $compId, 's' => $seasonId]);
	return $st->fetchAll();
}

function pick_matchweek(array $weeks): ?int {
	if (!$weeks) return null;
	$nums = array_map(fn($r) => (int)$r['week'], $weeks);

	$want = $_GET['week'] ?? null;
	if (is_string($want) && ctype_digit($want) && in_array((int)$want, $nums, true)) {
		return (int)$want;
	}

	$latest = null;
	foreach ($weeks as $r) {
		if ((int)$r['played'] > 0) $latest = (int)$r['week'];
	}
	return $latest ?? $nums[0];
}

// semua laga satu kompetisi dan musim, dikelompokkan per pekan: [pekan => [baris, ...]]
function matches_by_week(int $compId, int $seasonId): array {
	$st = db()->prepare(
		'SELECT m.matchWeek AS week,
		        m.matchID AS id, m.matchDate AS d, m.matchTime AS t,
		        m.matchSH AS sh,  m.matchSA AS sa,
		        m.matchSHE AS she, m.matchSAE AS sae,
		        m.matchSHP AS shp, m.matchSAP AS sap,
		        c.compID AS comp_id, c.compAlt AS comp_alt,
		        se.seasonName AS season_name,
		        m.matchHome AS home_id, h.teamName AS home_name, h.teamShort AS home_short, h.teamAlt AS home_alt,
		        m.matchAway AS away_id, a.teamName AS away_name, a.teamShort AS away_short, a.teamAlt AS away_alt
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

function kickoff_label(?string $d, ?string $t): string {
	if ($d === null) return 'TBD';
	$s = date('F j, Y', strtotime($d));
	return $t !== null ? $s . ' - ' . substr($t, 0, 5) : $s;
}

function group_by_kickoff(array $rows): array {
	$out = [];
	foreach ($rows as $m) {
		$key = ($m['d'] ?? 'tbd') . ' ' . ($m['t'] ?? '');
		if (!isset($out[$key])) $out[$key] = ['label' => kickoff_label($m['d'], $m['t']), 'rows' => []];
		$out[$key]['rows'][] = $m;
	}
	return array_values($out);
}

function match_note(array $m): string {
	$n = fn($v) => $v === null ? null : (int)$v;
	$notes = [];
	if ($n($m['she']) !== null && $n($m['sae']) !== null) $notes[] = 'AET';
	if ($n($m['shp']) !== null && $n($m['sap']) !== null) $notes[] = $n($m['shp']) . '-' . $n($m['sap']) . ' pens';
	return implode(', ', $notes);
}