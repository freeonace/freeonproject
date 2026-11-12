<?php
require __DIR__ . '/app/bs.php';
require APP . '/router.php';

//$menu  = require APP . '/menu.php';
//$route = resolve($menu, $_GET['route'] ?? '');

$menu  = menu_for_user(require APP . '/menu.php');
$route = resolve($menu, $_GET['route'] ?? '');

if ($route === null) {
	http_response_code(404);
	$route = [
		'section' => 'home', 'page' => null, 'group' => 'home',
		'file' => APP . '/pages/errors/404.php', 'title' => 'Not found',
	];
}

$section = $route['section'];
$page    = $route['page'];
$title   = $route['title'];
$subs    = $menu['groups'][$route['group']];

$args    = $route['args'] ?? [];
$content = render_page($route['file'], compact('menu', 'section', 'page', 'args'));

require APP . '/layout/main.php';