<?php
function comp_seasons(int $compId): array {
	$st = db()->prepare(
		'SELECT s.seasonID AS id, s.seasonName AS name
		 FROM season s
		 WHERE EXISTS (SELECT 1 FROM entry e WHERE e.entryComp = :c1 AND e.entrySeason = s.seasonID)
		    OR EXISTS (SELECT 1 FROM matches m WHERE m.matchComp = :c2 AND m.matchSeason = s.seasonID)
		 ORDER BY s.seasonYear DESC, s.seasonID DESC'
	);
	$st->execute(['c1' => $compId, 'c2' => $compId]);
	return $st->fetchAll();
}

function pick_season(array $seasons): ?int {
	$ids  = array_map(fn($s) => (int)$s['id'], $seasons);
	$want = (isset($_GET['season']) && ctype_digit((string)$_GET['season'])) ? (int)$_GET['season'] : null;
	return ($want !== null && in_array($want, $ids, true)) ? $want : ($ids[0] ?? null);
}

// Peserta = tim di tabel entry untuk kompetisi + musim itu, ditambah tim yang punya laga di sana
// (cadangan untuk data lama yang belum punya entry). Tim tanpa laga tampil dengan nilai 0.
function standings(int $compId, int $seasonId): array {
	$st = db()->prepare(
		'SELECT t.teamID AS team_id, t.teamShort AS short, t.teamName AS name, t.teamAlt AS alt,
				COUNT(r.gf) AS p,
				COALESCE(SUM(r.gf > r.ga), 0) AS w, COALESCE(SUM(r.gf = r.ga), 0) AS d, COALESCE(SUM(r.gf < r.ga), 0) AS l,
				COALESCE(SUM(r.gf), 0) AS gf, COALESCE(SUM(r.ga), 0) AS ga,
				COALESCE(SUM(r.gf), 0) - COALESCE(SUM(r.ga), 0) AS gd,
				COALESCE(SUM(CASE WHEN r.gf > r.ga THEN 3 WHEN r.gf = r.ga THEN 1 ELSE 0 END), 0) AS pts
		 FROM (
				SELECT entryTeam AS team FROM entry
				WHERE entryComp = :c1 AND entrySeason = :s1
				UNION
				SELECT matchHome FROM matches
				WHERE matchComp = :c2 AND matchSeason = :s2
				UNION
				SELECT matchAway FROM matches
				WHERE matchComp = :c3 AND matchSeason = :s3
		 ) pt
		 JOIN team t ON t.teamID = pt.team
		 LEFT JOIN (
				SELECT matchHome AS team, matchSH AS gf, matchSA AS ga FROM matches
				WHERE matchComp = :c4 AND matchSeason = :s4 AND matchSH IS NOT NULL AND matchSA IS NOT NULL
				UNION ALL
				SELECT matchAway, matchSA, matchSH FROM matches
				WHERE matchComp = :c5 AND matchSeason = :s5 AND matchSH IS NOT NULL AND matchSA IS NOT NULL
		 ) r ON r.team = t.teamID
		 GROUP BY t.teamID, t.teamShort, t.teamName, t.teamAlt
		 ORDER BY pts DESC, gd DESC, gf DESC, t.teamName'
	);
	$st->execute([
		'c1' => $compId, 's1' => $seasonId, 'c2' => $compId, 's2' => $seasonId, 'c3' => $compId, 's3' => $seasonId,
		'c4' => $compId, 's4' => $seasonId, 'c5' => $compId, 's5' => $seasonId,
	]);
	return $st->fetchAll();
}

function history_podium(int $compId): array {
	$st = db()->prepare(
		"SELECT s.seasonID AS season_id, s.seasonName AS season_name, s.seasonYear AS season_year,
				tr.trStatus AS status,
				t.teamID AS team_id, t.teamName AS name, t.teamShort AS short, t.teamAlt AS alt
		 FROM teamrecord tr
		 JOIN season s ON s.seasonID = tr.trSeason
		 JOIN team   t ON t.teamID   = tr.trTeam
		 WHERE tr.trComp = :c AND tr.trStatus IN ('Winner', 'Runner-Up', 'Third')
		 ORDER BY s.seasonYear DESC, s.seasonID DESC"
	);
	$st->execute(['c' => $compId]);
	return $st->fetchAll();
}

function getLastFive(int $teamID, int $compID, int $seasonID): array {
	$sql = "SELECT m.matchID, m.matchHome, m.matchAway, m.matchSH, m.matchSA, m.matchStatus, m.matchWeek, th.teamShort AS homeShort, ta.teamShort AS awayShort
			FROM matches m
			JOIN team th ON m.matchHome = th.teamID
			JOIN team ta ON m.matchAway = ta.teamID
			WHERE m.matchComp = ?
				AND m.matchSeason = ?
				AND (m.matchHome = ? OR m.matchAway = ?)
				AND m.matchStatus = 'Finished'
			ORDER BY m.matchDate DESC LIMIT 5";

	$st = db()->prepare($sql);
	$st->execute([$compID, $seasonID, $teamID, $teamID]);

	return array_reverse($st->fetchAll(PDO::FETCH_ASSOC));
}

function matchResult(?array $match, int $teamID): string {
	if ($match === null) return '-';

	if ((int)$match['matchHome'] === $teamID) {
		$scored = $match['matchSH'];
		$conceded = $match['matchSA'];
	} elseif ((int)$match['matchAway'] === $teamID) {
		$scored = $match['matchSA'];
		$conceded = $match['matchSH'];
	} else {
		return '-';
	}

	if ($scored === null || $conceded === null) return '-';

	if ((int)$scored > (int)$conceded) return 'W';
	if ((int)$scored < (int)$conceded) return 'L';

	return 'D';
}

/**
 * Juara per musim dari tabel teamrecord (trStatus: Winner / Runner-Up / Third).
 * Hasil: [ ['id' => seasonID, 'name' => seasonName, 'teams' => [baris, ...]], ... ]
 * urut dari musim terbaru.
 */
function history_records(int $compId): array {
	$st = db()->prepare(
		'SELECT s.seasonID AS season_id, s.seasonName AS season_name,
		        t.teamID AS team_id, t.teamName AS name, t.teamAlt AS alt,
		        tr.trStatus AS status
		 FROM teamrecord tr
		 JOIN season s ON s.seasonID = tr.trSeason
		 JOIN team t   ON t.teamID   = tr.trTeam
		 WHERE tr.trComp = :c
		 ORDER BY s.seasonYear DESC, s.seasonID DESC, tr.trID'
	);
	$st->execute(['c' => $compId]);

	$out = [];
	foreach ($st->fetchAll() as $r) {
		$sid = (int)$r['season_id'];
		if (!isset($out[$sid])) $out[$sid] = ['id' => $sid, 'name' => $r['season_name'], 'teams' => []];
		$out[$sid]['teams'][] = $r;
	}
	return array_values($out);
}

function last_five_all(int $compId, int $seasonId): array {
	$st = db()->prepare(
		'SELECT x.team, x.matchID, x.matchHome, x.matchAway, x.matchSH, x.matchSA, x.matchWeek,
		        x.homeShort, x.awayShort
		 FROM (
		   SELECT u.*, ROW_NUMBER() OVER (PARTITION BY u.team ORDER BY u.matchDate DESC, u.matchID DESC) AS rn
		   FROM (
		     SELECT m.matchHome AS team, m.matchID, m.matchHome, m.matchAway, m.matchSH, m.matchSA,
		            m.matchWeek, m.matchDate, h.teamShort AS homeShort, a.teamShort AS awayShort
		     FROM matches m
		     JOIN team h ON h.teamID = m.matchHome
		     JOIN team a ON a.teamID = m.matchAway
		     WHERE m.matchComp = :c1 AND m.matchSeason = :s1 AND m.matchSH IS NOT NULL AND m.matchSA IS NOT NULL
		     UNION ALL
		     SELECT m.matchAway, m.matchID, m.matchHome, m.matchAway, m.matchSH, m.matchSA,
		            m.matchWeek, m.matchDate, h.teamShort, a.teamShort
		     FROM matches m
		     JOIN team h ON h.teamID = m.matchHome
		     JOIN team a ON a.teamID = m.matchAway
		     WHERE m.matchComp = :c2 AND m.matchSeason = :s2 AND m.matchSH IS NOT NULL AND m.matchSA IS NOT NULL
		   ) u
		 ) x
		 WHERE x.rn <= 5
		 ORDER BY x.team, x.rn DESC'
	);
	$st->execute(['c1' => $compId, 's1' => $seasonId, 'c2' => $compId, 's2' => $seasonId]);

	$out = [];
	foreach ($st->fetchAll() as $r) $out[(int)$r['team']][] = $r;
	return $out;
}