<?php
page_title('Players');

// Filter lewat GET (semua boleh kosong). OVR dihitung di PHP, jadi penyaringan dan pengurutan juga di PHP.
$int = fn(string $k): ?int => (isset($_GET[$k]) && is_string($_GET[$k]) && ctype_digit($_GET[$k])) ? (int)$_GET[$k] : null;
$f = [
	'q'    => is_string($_GET['q'] ?? null) ? trim(substr($_GET['q'], 0, 60)) : '',
	'nat'  => $int('nat'),  'pos'  => $int('pos'),
	'amin' => $int('amin'), 'amax' => $int('amax'),
	'omin' => $int('omin'), 'omax' => $int('omax'),
];
$perPage = 20;
$all     = players_find($f);
$total   = count($all);
$pages   = max(1, (int)ceil($total / $perPage));
$page    = min($pages, max(1, $int('page') ?? 1));
$rows    = array_slice($all, ($page - 1) * $perPage, $perPage);

$regions   = regions_all();
$positions = positions_all();
$flagFallback = BASE_URL . '/img/flag/default.png';
// alamat halaman: filter yang aktif + nomor halaman (halaman 1 tanpa parameter page)
$pageUrl = fn(int $n): string => '?' . http_build_query(array_filter($f + ($n > 1 ? ['page' => $n] : []), fn($v) => $v !== null && $v !== ''));
?>
<div class="md:p-7 lg:p-9 p-4">
	<div class="d-flex flex-column">
		<div class="pb-8">
			<h2 class="text-title">List of All Players</h2>
			<span class="fg-inverse"><?= e(site('name')) ?> Players in All Teams</span>
		</div>

		<div class="d-flex flex-column gap-5">
			<div class="nav-overflow" data-bs-toggle="nav-overflow">
				<ul class="nav nav-underline">
					<li class="nav-item">
						<a class="nav-link active" aria-current="page" href="">All</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="">Free Agents</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="">National Players</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="">Retired</a>
					</li>
				</ul>
			</div>

			<div>
				<label for="search-player" class="form-label">Search Player</label>
				<form class="input-group" id="player-filter" method="get">
					<input class="form-control" type="text" id="search-player" name="q" value="<?= e($f['q']) ?>" maxlength="60" placeholder="Search Player" aria-label="Search Player" style="width: 100%; max-width: 700px; outline: none;">
					<button type="submit" class="btn-solid theme-primary">
						<i class="fa-solid fa-magnifying-glass"></i>
					</button>
				</form>
			</div>

			<div class="row g-5">
				<div class="col-12 lg:col-4 xl:col-3">
					<button type="button" class="btn-solid theme-primary btn-sm btn-advance" data-bs-toggle="dialog" data-bs-target="#advance-search">Advance Search</button>
					<form>
						<div class="card card-search d-none lg:d-block">
							<div class="card-advance">
								<div class="row row-cols-1 g-4">
									<div class="col">
										<small class="fg-body fg-50" style="user-select: none;">General</small>
									</div>

									<div class="col">
										<label for="nationality" class="form-label">Nationality</label>
										<select class="form-control" id="nationality" name="nat" form="player-filter">
											<option value="" disabled selected hidden>Nationality</option>
										<?php foreach ($regions as $rg): ?>
											<option value="<?= (int)$rg['id'] ?>"<?= $f['nat'] === (int)$rg['id'] ? ' selected' : '' ?>><?= e($rg['name']) ?></option>
										<?php endforeach; ?>
										</select>
									</div>

									<div class="col">
										<!-- sort compID compAlt -->
										<label for="league" class="form-label">League</label>
										<select class="form-control" id="league">
											<option value="" disabled selected hidden>League</option>
											<option value="10">Elite League</option>
											<option value="11">Fortshyre 1.</option>
											<option value="12">Extier-1</option>
										</select>
									</div>

									<div class="col">
										<!-- sort teamID teamAlt. Hanya mengambil tim dengan teamEntity bukan 1 alias dari 2 dst, kalau sudah pilih liga maka tidak perlu ambil semua tim -->
										<label for="team" class="form-label">Team</label>
										<select class="form-control" id="league">
											<option value="" disabled selected hidden>Team</option>
											<option value="100">Ackertonville</option>
											<option value="101">Newells UTD</option>
											<option value="102">Readling</option>
										</select>
									</div>

									<div class="col">
										<label for="age-min" class="form-label">Age</label>
										<div class="row g-2">
											<div class="col">
												<input type="text" inputmode="numeric" class="form-control" placeholder="Min" aria-label="min-age" id="age-min" name="amin" form="player-filter" value="<?= $f['amin'] ?>">
											</div>
											<div class="col">
												<input type="text" inputmode="numeric" class="form-control" placeholder="Max" aria-label="max-age" id="age-max" name="amax" form="player-filter" value="<?= $f['amax'] ?>">
											</div>
										</div>
									</div>

									<div class="col">
										<label for="ovr-min" class="form-label">Overall</label>
										<div class="row g-2">
											<div class="col">
												<input type="text" inputmode="numeric" class="form-control" placeholder="Min" aria-label="min-ovr" id="ovr-min" name="omin" form="player-filter" value="<?= $f['omin'] ?>">
											</div>
											<div class="col">
												<input type="text" inputmode="numeric" class="form-control" placeholder="Max" aria-label="max-ovr" id="ovr-max" name="omax" form="player-filter" value="<?= $f['omax'] ?>">
											</div>
										</div>
									</div>

									<div class="col">
										<small class="fg-body fg-50" style="user-select: none;">Select Position</small>
									</div>

									<div class="col">
										<label for="pos1" class="form-label">Position</label>
										<select class="form-control" id="pos1">
											<option value="" disabled selected hidden>Primary position</option>
											<?php foreach ($positions as $ps): ?>
											<option value="<?= (int)$ps['id'] ?>"<?= $f['pos'] === (int)$ps['id'] ? ' selected' : '' ?>><?= e($ps['name']) ?></option>
										<?php endforeach; ?>
										</select>
									</div>

									<div class="col">
										<div class="d-flex flex-column">
											<label for="random" class="form-label">All Position</label>
											<div class="d-flex flex-wrap gap-2">
												<label class="btn-check btn-outline theme-primary">
													<input type="checkbox" class="limit-check" autocomplete="off" id="1">GK
												</label>
												<label class="btn-check btn-outline theme-primary">
													<input type="checkbox" class="limit-check" autocomplete="off" id="2">CB
												</label>
												<label class="btn-check btn-outline theme-primary">
													<input type="checkbox" class="limit-check" autocomplete="off" id="3">RB
												</label>
												<label class="btn-check btn-outline theme-primary">
													<input type="checkbox" class="limit-check" autocomplete="off" id="4">LB
												</label>
												<label class="btn-check btn-outline theme-primary">
													<input type="checkbox" class="limit-check" autocomplete="off" id="5">DM
												</label>
												<label class="btn-check btn-outline theme-primary">
													<input type="checkbox" class="limit-check" autocomplete="off" id="6">CM
												</label>
												<label class="btn-check btn-outline theme-primary">
													<input type="checkbox" class="limit-check" autocomplete="off" id="7">AM
												</label>
												<label class="btn-check btn-outline theme-primary">
													<input type="checkbox" class="limit-check" autocomplete="off" id="8">RM
												</label>
												<label class="btn-check btn-outline theme-primary">
													<input type="checkbox" class="limit-check" autocomplete="off" id="9">LM
												</label>
												<label class="btn-check btn-outline theme-primary">
													<input type="checkbox" class="limit-check" autocomplete="off" id="10">ST
												</label>
												<label class="btn-check btn-outline theme-primary">
													<input type="checkbox" class="limit-check" autocomplete="off" id="11">RW
												</label>
												<label class="btn-check btn-outline theme-primary">
													<input type="checkbox" class="limit-check" autocomplete="off" id="12">LW
												</label>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<dialog class="dialog dialog-instant md-down:dialog-fullscreen" id="advance-search">
							<div class="dialog-header">
								<h1 class="dialog-title">Advance Search</h1>
								<button type="button" class="btn-close" commandfor="advance-search" command="close" aria-label="Close"></button>
							</div>
							<div class="dialog-body">
								<form class="row row-cols-1 g-4">
									<div class="col">
										<small class="fg-body fg-50" style="user-select: none;">General</small>
									</div>

									<div class="col">
										<!-- sort regionName regionID -->
										<label for="nationality" class="form-label">Nationality</label>
										<select class="form-control" id="nationality">
											<option value="" disabled selected hidden>Nationality</option>
											<option value="10">Amizu</option>
											<option value="5">Cartbridge</option>
											<option value="16">Cevigia</option>
										</select>
									</div>

									<div class="col">
										<!-- sort compID compAlt -->
										<label for="league" class="form-label">League</label>
										<select class="form-control" id="league">
											<option value="" disabled selected hidden>League</option>
											<option value="10">Elite League</option>
											<option value="11">Fortshyre 1.</option>
											<option value="12">Extier-1</option>
										</select>
									</div>

									<div class="col">
										<!-- sort teamID teamAlt. Hanya mengambil tim dengan teamEntity bukan 1 alias dari 2 dst, kalau sudah pilih liga maka tidak perlu ambil semua tim -->
										<label for="team" class="form-label">Team</label>
										<select class="form-control" id="league">
											<option value="" disabled selected hidden>Team</option>
											<option value="100">Ackertonville</option>
											<option value="101">Newells UTD</option>
											<option value="102">Readling</option>
										</select>
									</div>

									<div class="col">
										<label for="age" class="form-label">Age</label>
										<div class="row g-2">
											<div class="col">
												<input type="text" class="form-control" placeholder="Min" aria-label="min-age" id="age">
											</div>
											<div class="col">
												<input type="text" class="form-control" placeholder="Max" aria-label="max-age" id="age">
											</div>
										</div>
									</div>

									<div class="col">
										<label for="ovr" class="form-label">Overall</label>
										<div class="row g-2">
											<div class="col">
												<input type="text" class="form-control" placeholder="Min" aria-label="min-ovr" id="ovr">
											</div>
											<div class="col">
												<input type="text" class="form-control" placeholder="Max" aria-label="max-ovr" id="ovr">
											</div>
										</div>
									</div>

									<div class="col">
										<small class="fg-body fg-50" style="user-select: none;">Select Position</small>
									</div>

									<div class="col">
										<label for="pos1" class="form-label">Position</label>
										<select class="form-control" id="pos1">
											<option value="" disabled selected hidden>Primary position</option>
											<option value="1">Goalkeeper</option>
											<option value="2">Center Back</option>
											<option value="3">Right Back</option>
										</select>
									</div>

									<div class="col">
										<div class="d-flex flex-column">
											<label for="random" class="form-label">All Position</label>
											<div class="d-flex flex-wrap gap-2">
												<label class="btn-check btn-outline theme-primary">
													<input type="checkbox" class="limit-check" autocomplete="off" id="1">GK
												</label>
												<label class="btn-check btn-outline theme-primary">
													<input type="checkbox" class="limit-check" autocomplete="off" id="2">CB
												</label>
												<label class="btn-check btn-outline theme-primary">
													<input type="checkbox" class="limit-check" autocomplete="off" id="3">RB
												</label>
												<label class="btn-check btn-outline theme-primary">
													<input type="checkbox" class="limit-check" autocomplete="off" id="4">LB
												</label>
												<label class="btn-check btn-outline theme-primary">
													<input type="checkbox" class="limit-check" autocomplete="off" id="5">DM
												</label>
												<label class="btn-check btn-outline theme-primary">
													<input type="checkbox" class="limit-check" autocomplete="off" id="6">CM
												</label>
												<label class="btn-check btn-outline theme-primary">
													<input type="checkbox" class="limit-check" autocomplete="off" id="7">AM
												</label>
												<label class="btn-check btn-outline theme-primary">
													<input type="checkbox" class="limit-check" autocomplete="off" id="8">RM
												</label>
												<label class="btn-check btn-outline theme-primary">
													<input type="checkbox" class="limit-check" autocomplete="off" id="9">LM
												</label>
												<label class="btn-check btn-outline theme-primary">
													<input type="checkbox" class="limit-check" autocomplete="off" id="10">ST
												</label>
												<label class="btn-check btn-outline theme-primary">
													<input type="checkbox" class="limit-check" autocomplete="off" id="11">RW
												</label>
												<label class="btn-check btn-outline theme-primary">
													<input type="checkbox" class="limit-check" autocomplete="off" id="12">LW
												</label>
											</div>
										</div>
									</div>
								</form>
							</div>
							<div class="dialog-footer">
								<button type="submit" class="btn-solid theme-primary">Search</button>
							</div>
						</dialog>
					</form>
				</div>

				<div class="col-12 lg:col-8 xl:col-9">
					<div class="d-flex flex-column gap-4" style="position: relative;">
						<div id="alertBox" class="alert theme-primary d-none position-absolute" role="alert" style="left: 0; right: 0; top: 8.5px;">
							<span id="alertMessage"></span>
						</div>
						<div class="table-responsive">
							<table class="table table-player">
								<thead>
									<tr>
										<th>PLAYER</th>
										<th>AGE</th>
										<th>POS</th>
										<th>OVR</th>
										<th>
											<div class="d-flex flex-column lh-1">
												<span>DRI/</span>
												<span>HAN</span>
											</div>
										</th>
										<th>
											<div class="d-flex flex-column lh-1">
												<span>ATT/</span>
												<span>REF</span>
											</div>
										</th>
										<th>
											<div class="d-flex flex-column lh-1">
												<span>PAS/</span>
												<span>KIC</span>
											</div>
										</th>
										<th>
											<div class="d-flex flex-column lh-1">
												<span>PHY/</span>
												<span>POS</span>
											</div>
										</th>
										<th>
											<div class="d-flex flex-column lh-1">
												<span>DEF/</span>
												<span>DIV</span>
											</div>
										</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($rows as $r):
										$hasAbil = $r['pos_code'] !== null;
										$labels  = ability_labels($r['pos_code']);
										$abil    = snapshot_abilities($r);   // GK: a6-a10, lainnya: a1-a5
									?>
									<tr>
										<td><a class="text-decoration-none theme-inverse" href="<?= e(player_url($r)) ?>" title="<?= e($r['full'] ?? $r['name']) ?>">
											<div class="d-flex flex-column align-items-start">
												<span><?= e($r['name']) ?></span>
												<div class="d-flex gap-2 align-items-center">
													<img class="flag-sm" src="<?= e(flag_url((int)$r['region_id'])) ?>">
													<img class="logo-sm" src="/csf/img/team/default.png">
												</div>
											</div></a>
										</td>
										<td><?= $r['age'] !== null ? (int)$r['age'] : '-' ?></td>
										<td>
											<div class="d-inline">
												<span title="Main position: <?= e((string)$r['pos_name']) ?>"><?= e($r['pos_code'] ?? '-') ?></span>
											</div>
										</td>
										<td><?= $r['ovr'] !== null ? (int)$r['ovr'] : '-' ?></td>
										<?php foreach ($labels as $i => $lb): ?>
										<td title="<?= e($lb[1]) ?>"><?= $hasAbil ? (int)$abil[$i] : '-' ?></td>
										<?php endforeach; ?>
									</tr>
								<?php endforeach; ?>
								<?php if (!$rows): ?>
									<tr>
										<td colspan="9" class="text-center fg-secondary user-select-none">No players found.</td>
									</tr>
								<?php endif; ?>
								</tbody>
							</table>
						</div>
						<?php if ($pages > 1): ?>
						<nav aria-label="Page navigation">
							<ul class="pagination">
								<li class="page-item">
									<a class="page-link<?= $page <= 1 ? ' disabled' : '' ?>" href="<?= e($pageUrl(max(1, $page - 1))) ?>" aria-label="Previous">
										<span aria-hidden="true">&laquo;</span>
									</a>
								</li>
							<?php for ($n = max(1, $page - 2); $n <= min($pages, $page + 2); $n++): ?>
								<li class="page-item"><a class="page-link<?= $n === $page ? ' active' : '' ?>"<?= $n === $page ? ' aria-current="page"' : '' ?> href="<?= e($pageUrl($n)) ?>"><?= $n ?></a></li>
							<?php endfor; ?>
								<li class="page-item">
									<a class="page-link<?= $page >= $pages ? ' disabled' : '' ?>" href="<?= e($pageUrl(min($pages, $page + 1))) ?>" aria-label="Next">
										<span aria-hidden="true">&raquo;</span>
									</a>
								</li>
							</ul>
						</nav>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>