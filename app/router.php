<?php
	function resolve(array $menu, string $route): ?array {
		$parts = array_values(array_filter(explode('/', trim($route, '/')), 'strlen'));
		if (count($parts) > 6) return null;

		$section = $parts[0] ?? 'home';
		$page    = $parts[1] ?? null;
		$args    = array_slice($parts, 2);

		if (!isset($menu['sections'][$section])) return null;
		$group = $menu['sections'][$section]['group'];
		$subs  = $menu['groups'][$group];

		if ($page !== null && !isset($subs[$page])) return null;
		if (count($args) > ($menu['args']["$group/$page"] ?? 0)) return null;

		$name = $page ?? 'index';
		$file = null;
		foreach ([APP . "/pages/$section/$name.php", APP . "/pages/$group/$name.php"] as $candidate) {
			if (is_file($candidate)) { $file = $candidate; break; }
		}
		if ($file === null) return null;

		return [
			'section' => $section, 'page' => $page, 'group' => $group,
			'args'    => $args,
			'file'    => $file,
			'title'   => $menu['sections'][$section]['label'] . ($page ? ' - ' . $subs[$page] : ''),
		];
	}