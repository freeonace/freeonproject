<?php
page_title('Match Schedules');

// Halaman ini hanya memuat laga bertipe 'League' (matchType). Cup, domestik, dan internasional dipisah nanti.
$type     = 'League';
$seasons  = fixture_seasons($type);
$seasonId = pick_season($seasons);
$comps    = $seasonId !== null ? fixture_comps($seasonId, $type) : [];
$compIds  = array_map(fn($c) => (int)$c['id'], $comps);
$rawComp  = $_GET['comp'] ?? null;
$compId   = (is_string($rawComp) && ctype_digit($rawComp) && in_array((int)$rawComp, $compIds, true)) ? (int)$rawComp : null;

// Pekan = gabungan pekan semua kompetisi terpilih; pekan awal = pekan pertama yang belum selesai semua (atau ?week=N)
$rows     = $seasonId !== null ? fixture_list($seasonId, $compId, $type) : [];
$weeks    = fixture_weeks($rows);
$week     = pick_current_week($weeks);
$byWeek   = fixture_group($rows);
$fallback = BASE_URL . '/img/team/default.png';
?>
<div class="md:p-7 lg:p-9 p-4">
	<div class="d-flex flex-column">
		<div class="pb-8">
			<h2 class="text-title">Match Schedules</h2>
			<span class="fg-inverse"><?= e(site('name')) ?> Fixture &amp; Result in All League Competitions</span>
		</div>

		<div class="d-flex flex-column gap-9">
			<div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
				<form method="get">
					<div class="d-flex align-items-center gap-2">
						<div class="form-field">
							<select class="form-control" id="select-season" name="season" aria-label="Season">
							<?php foreach ($seasons as $s): ?>
								<option value="<?= (int)$s['id'] ?>"<?= (int)$s['id'] === $seasonId ? ' selected' : '' ?>><?= e($s['name']) ?></option>
							<?php endforeach; ?>
							</select>
						</div>

						<div class="form-field">
							<select class="form-control" id="select-comp" name="comp" aria-label="Competition">
								<option value="">All Competition</option>
							<?php foreach ($comps as $c): ?>
								<option value="<?= (int)$c['id'] ?>"<?= (int)$c['id'] === $compId ? ' selected' : '' ?>><?= e($c['name']) ?></option>
							<?php endforeach; ?>
							</select>
						</div>

						<button type="submit" class="btn-solid theme-primary">
							<span>Show</span>
						</button>
					</div>
				</form>

				<div class="d-flex align-items-center gap-5">
					<span class="fs-xl">League</span>
					<button type="button" class="btn-solid theme-primary" id="careticon">
						<div class="d-inline">
							<span>Select Type</span>
							<i class="fa-solid fa-caret-down"></i>
						</div>
					</button>
				</div>
			</div>

			<?php if ($week === null): ?>
				<p class="text-center fg-secondary user-select-none">No matches yet.</p>
			<?php else: ?>
			<div class="d-flex flex-column gap-9" data-week-root>
				<?php component('week-nav', compact('weeks', 'week')); ?>

				<?php foreach ($byWeek as $w => $groups): ?>
				<div class="week-panel" data-week-panel="<?= (int)$w ?>"<?= (int)$w === $week ? '' : ' hidden' ?>>
					<div class="row row-cols-1 g-9">
						<?php foreach ($groups as $list): ?>
						<div class="col">
							<div class="d-flex flex-column gap-3">
								<div class="align-self-center">
									<span class="fs-xl"><?= e($list[0]['comp_alt']) ?></span>
								</div>
								<div class="row row-cols-1 g-5">
									<?php foreach ($list as $m):
										$v  = match_view($m);
										$ts = $m['d'] !== null ? strtotime($m['d']) : false;
									?>
									<div class="col">
										<a class="text-decoration-none" href="<?= e(fixture_url($m)) ?>">
											<div class="match-fixture <?= e($v['comp_class']) ?>" style="<?= e(match_style($m)) ?>">
												<div class="row h-100 align-items-center g-3">
													<div class="col-12 lg:col-3 xl:col-2 lg:text-start">
														<div class="d-flex lg:flex-column justify-content-between">
															<span class="fw-semibold"><?= $ts ? e(date('D, j M', $ts)) : 'TBD' ?></span>
															<small class="fw-light"><?= $m['t'] !== null ? e(substr($m['t'], 0, 5)) : '' ?></small>
														</div>
													</div>
													<div class="col-12 lg:col-6 xl:col-8 text-center">
														<div class="d-flex flex-column gap-3">
															<div><?= e((string)$m['stadium']) ?></div>
															<div class="row">
																<div class="col-5" style="<?= e(team_style($m['home_color'], $m['home_subcol'])) ?>">
																	<div class="d-flex gap-2 align-items-center justify-content-end">
																		<span class="d-none sm:d-inline"><?= e($m['home_alt']) ?></span>
																		<span class="d-inline sm:d-none"><?= e($m['home_short']) ?></span>
																		<img class="logo-sm" src="<?= e(team_logo((int)$m['home_id'])) ?>" alt="" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='<?= e($fallback) ?>'">
																	</div>
																</div>
																<div class="col-2"><?= ($v['h'] !== null && $v['a'] !== null) ? (int)$v['h'] . ' - ' . (int)$v['a'] : '-' ?></div>
																<div class="col-5" style="<?= e(team_style($m['away_color'], $m['away_subcol'])) ?>">
																	<div class="d-flex gap-2 align-items-center justify-content-start">
																		<img class="logo-sm" src="<?= e(team_logo((int)$m['away_id'])) ?>" alt="" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='<?= e($fallback) ?>'">
																		<span class="d-none sm:d-inline"><?= e($m['away_alt']) ?></span>
																		<span class="d-inline sm:d-none"><?= e($m['away_short']) ?></span>
																	</div>
																</div>
															</div>
														</div>
													</div>
													<div class="col-12 lg:col-3 xl:col-2 lg:text-end">
														<div class="d-flex gap-3 align-items-center lg:align-items-end lg:gap-0 lg:flex-column">
															<span class="fw-semibold">Matchweek <?= (int)$m['week'] ?></span>
															<small class="fw-light"><?= e((string)$m['comp_entity']) ?></small>
														</div>
													</div>
												</div>
											</div>
										</a>
									</div>
									<?php endforeach; ?>
								</div>
							</div>
						</div>
						<?php endforeach; ?>
					</div>
				</div>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>
		</div>
	</div>
</div>
