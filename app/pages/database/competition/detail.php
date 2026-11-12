<?php
$tabs = ['overview' => 'Overview', 'standings' => 'Standings',
         'matches'  => 'Matches',  'history'   => 'History'];

$compId = ctype_digit($args[0] ?? '') ? (int)$args[0] : 0;
$comp   = $compId ? competition_by_id($compId) : null;
$tab    = $args[2] ?? 'overview';
$competitions = competitions_from_id(10);

if (!$comp || !isset($tabs[$tab])) {
	http_response_code(404);
	echo '<section class="text-center fg-secondary user-select-none"><h1>Competition not found</h1></section>';
	return;
}

$wantAlt = slugify($comp['alt']) ?: 'competition';
if (($args[1] ?? '') !== $wantAlt || ($tab === 'overview' && isset($args[2]))) {
	$qs = $_GET; unset($qs['route']);
	$to = comp_url($compId, $comp['alt'], $tab === 'overview' ? null : $tab)
	    . ($qs ? '?' . http_build_query($qs) : '');
	header('Location: ' . $to, true, 302);
	exit;
}

page_title($comp['name'] . ($tab === 'overview' ? '' : ' - ' . $tabs[$tab]));
?>
<section class="md:p-7 lg:p-9 p-4">
	<div class="d-flex flex-column gap-3">
		<div class="d-flex align-items-center justify-content-between">
			<h1 class="fw-bold text-truncate"><?= e($comp['alt']) ?> Detail</h1>
			<a class="btn-solid theme-primary btn-sm" href="<?= url('database', 'competition') ?>">Back to Competition Menu</a>
		</div>

		<div class="d-flex flex-column md:flex-row justify-content-center md:justify-content-start align-items-center gap-5">
			<div class="card border-0" style="width: 7.5rem;">
				<img src="<?= e(comp_img((int)$comp['id'])) ?>">
			</div>

			<div class="d-flex flex-column gap-4">
				<span class="text-xl fw-semibold text-uppercase"><?= e(($comp['region'] ? $comp['region'] . ' - ' : '') . $comp['name']) ?></span>

				<div class="d-flex flex-wrap align-items-center justify-content-center md:justify-content-start md:column-gap-4 column-gap-5">

					<div class="d-none md:d-block">
						<div class="d-flex align-items-center gap-2">
							<img class="border border-2 flag-md" src="<?= e(comp_fg((int)$comp['region_id'])) ?>">
							<span class="fg-1"><?= e($comp['tier']) ?></span>
						</div>
					</div>

					<button class="btn-sm btn-solid theme-primary rounded-pill" type="button" data-bs-toggle="collapse" data-bs-target="#collapseLeague" aria-expanded="false" aria-controls="collapseLeague">
						<div class="d-flex align-items-center gap-1">
							<span>Show League</span>
							<i class="fa-solid fa-caret-down" id="careticon" style="font-size: 12px;"></i>
						</div>
					</button>

					<div class="">C</div>
				</div>
			</div>
		</div>

		<div class="container-md" style="position: relative;">
			<div class="collapse" id="collapseLeague" style="position: absolute; z-index: 5; left: 0; right: 0; top: 8px;">
				<div class="card">
					<div class="card-body">
						<span class="card-title">Select <?= e(site('name')) ?> Leagues</span>
						<div class="row row-cols-2 lg:row-cols-3 align-items-center g-4">
						<?php foreach ($competitions as $c): ?>
							<div class="col">
								<a class="theme-inverse text-decoration-none" href="<?= e(comp_url((int)$c['id'])) ?>">
									<div class="d-flex align-items-center gap-3">
										<img class="logo-sm" src="<?= e(comp_img((int)$c['id'])) ?>">
										<span class="fs-md"><?= e($c['name']) ?></span>
									</div>
								</a>
							</div>
						<?php endforeach; ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="nav-overflow" data-bs-toggle="nav-overflow">
			<nav class="league-content" aria-label="Competition sections">
				<ul class="nav nav-tabs">
					<?php foreach ($tabs as $key => $label): $on = ($key === $tab); ?>
					<li class="nav-item">
						<a class="nav-link<?= $on ? ' active' : '' ?>"<?= $on ? ' aria-current="page"' : '' ?> href="<?= e(comp_url($compId, $comp['alt'], $key === 'overview' ? null : $key)) ?>"><?= e($label) ?></a>
					</li>
					<?php endforeach; ?>
				</ul>
			</nav>
		</div>

		<div class="p-4">
			<?php require __DIR__ . "/tabs/$tab.php"; ?>
		</div>
	</div>

</section>

<!-- Sepertinya mending masing-masing tab memiliki masing-masing halaman. Kenapa? Karena memuat banyak data. Misalkan saya ingin membuat tabel klasemen dan di bawahnya ada mc-carousel seperti di halaman Home, di Overview terdapat top skor dan top asis serta info performance lainnya mengenai Liga $id, di tab History terdapat tabel 3 besar. -->

<!-- Saat mengakses .../database/competition/id/alt yang terjadi masih tetap di halaman .../database/competition meskipun di link URL sudah di halaman id/alt -->