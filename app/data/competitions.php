<?php
function comp_bg(int $id): string {
	return BASE_URL . '/img/bg/' . sprintf('%06d', $id) . '.png';
}

function comp_img(int $id): string {
	return BASE_URL . '/img/comp/ic_' . sprintf('%04d', $id) . '_a.png';
}

function comp_fg(int $id): string {
	return BASE_URL . '/img/flag/in_' . sprintf('%03d', $id) . '.png';
}

function comp_url(int $id, ?string $alt = null, ?string $tab = null): string {
	$u = url('database', 'competition') . '/' . $id . '/' . (slugify($alt) ?: 'competition');
	return $tab ? $u . '/' . $tab : $u;
}

function competitions_by_ids(array $ids): array {
	$ids = array_values(array_unique(array_map('intval', $ids)));
	if (!$ids) return [];

	$in = implode(',', array_fill(0, count($ids), '?'));
	$st = db()->prepare(
		"SELECT compID AS id, compName AS name, compAlt AS alt
		 FROM competition WHERE compID IN ($in)"
	);
	$st->execute($ids);

	$out = [];
	foreach ($st->fetchAll() as $r) $out[(int)$r['id']] = $r;
	return $out;
}

function competitions_from(int $minId, array $exclude = []): array {
	$params = [$minId];
	$sql = 'SELECT compID AS id, compName AS name, compAlt AS alt
	        FROM competition WHERE compID >= ?';
	if ($exclude) {
		$sql .= ' AND compID NOT IN (' . implode(',', array_fill(0, count($exclude), '?')) . ')';
		array_push($params, ...array_map('intval', $exclude));
	}
	$sql .= ' ORDER BY compID';

	$st = db()->prepare($sql);
	$st->execute($params);
	return $st->fetchAll();
}

function competition_by_id(int $id): ?array {
	$st = db()->prepare(
		'SELECT c.compID AS id, c.compName AS name, c.compAlt AS alt,
		        c.compType AS type, c.compTier AS tier, c.compStatus AS status,
		        c.compEst AS est,
		        r.regionID AS region_id, r.regionName AS region
		 FROM competition c
		 LEFT JOIN region r ON r.regionID = c.compRegion
		 WHERE c.compID = :id'
	);
	$st->execute(['id' => $id]);
	return $st->fetch() ?: null;
}

function competitions_from_id(int $startId): array {
	$st = db()->prepare(
		"SELECT compID AS id, compName AS name, compAlt AS alt
		 FROM competition
		 WHERE compID >= ?
		 ORDER BY compID ASC"
	);

	$st->execute([$startId]);

	$out = [];
	foreach ($st->fetchAll() as $r) {
		$out[(int)$r['id']] = $r;
	}

	return $out;
}