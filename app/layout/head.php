<?php // app/layout/head.php ?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- <title><?= e($title) ?> | <?= e(site('name')) ?></title> -->
<title><?= e(page_title() ?? $title) ?> | <?= e(site('name')) ?></title>
<link rel="icon" type="image/x-icon" href="<?= asset('favicon.ico') ?>" sizes="any">

<script>
	(function () {
		var pref = 'system';
		try { pref = localStorage.getItem('theme') || 'system'; } catch (e) {}
		var dark = pref === 'dark' ||
		(pref === 'system' && matchMedia('(prefers-color-scheme: dark)').matches);
		var root = document.documentElement;
		root.setAttribute('data-theme-pref', pref);
		root.setAttribute('data-bs-theme', dark ? 'dark' : 'light');
		root.style.colorScheme = dark ? 'dark' : 'light';
	})();
</script>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@6.0.0-alpha.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-B/GM4XqrwHnWXNOWMbloTmrYXZg10cakYGmpfsR/bbzQ6JAJI4ihuyADKLnBgrCe" crossorigin="anonymous">

<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.min.css">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.2/font/bootstrap-icons.min.css">
	
<script type="module" src="https://cdn.jsdelivr.net/npm/bootstrap@6.0.0-alpha.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-1a/pXj49ZQ1aHEmrJ+gMw1otqoVsYwlEnlD8mIfY2TV03r20Y0CN7uqx1tQogjPL" crossorigin="anonymous"></script>

<script>
	try {
		if (localStorage.getItem('sidebar') === 'collapsed')
			document.documentElement.classList.add('sidebar-collapsed');
	} catch (e) {}
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.1"></script>

<link rel="stylesheet" href="<?= asset('mod.css') ?>">

<link rel="stylesheet" href="<?= asset('theme.css') ?>">

<link rel="preconnect" href="https://fonts.googleapis.com">

<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<!-- <link href='https://fonts.googleapis.com/css?family=Average Sans' rel='stylesheet'>
<style>
body {
	font-family: 'Average Sans';
}
</style>
-->