<?php
define('APP', __DIR__);
define('BASE_URL', rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/'));
require __DIR__ . '/db.php';
require __DIR__ . '/data/matches.php';
require __DIR__ . '/data/competitions.php';
require __DIR__ . '/data/standings.php';
require __DIR__ . '/data/teams.php';
require __DIR__ . '/data/matchweek.php';
require __DIR__ . '/data/users.php';
require __DIR__ . '/data/fixtures.php';
require __DIR__ . '/data/players.php';

function component(string $name, array $vars = []): void {
	$file = APP . '/components/' . $name . '.php';
	if (!is_file($file)) {
		echo "<!-- components/$name.php not found -->";
		return;
	}
	extract($vars, EXTR_SKIP);
	require $file;
}

function partial(string $name): void {
	$file = APP . '/layout/' . $name . '.php';
	if (!is_file($file)) {
		echo "<!-- layout/$name.php not found -->";
		return;
	}
	require $file;
}

function e(?string $s): string {
	return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}

function url(string $section = '', ?string $page = null): string {
	return BASE_URL . '/' . ltrim($section . ($page ? "/$page" : ''), '/');
}

function asset(string $path): string {
	$file = dirname(APP) . '/asset/' . $path;
	$v = is_file($file) ? filemtime($file) : 0;
	return BASE_URL . '/asset/' . $path . '?v=' . $v;
}

function render_page(string $file, array $vars): string {
	extract($vars, EXTR_SKIP);
	ob_start();
	require $file;
	return ob_get_clean();
}

$GLOBALS['config'] = require __DIR__ . '/config.php';

function site(string $key): string {
	return (string) ($GLOBALS['config'][$key] ?? '');
}

function slugify(?string $s): string {
	$s = strtolower(trim((string)$s));
	return trim(preg_replace('/[^a-z0-9]+/', '-', $s), '-');
}

function page_title(?string $set = null): ?string {
	static $t = null;
	if ($set !== null) $t = $set;
	return $t;
}