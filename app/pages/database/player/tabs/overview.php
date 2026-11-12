<?php
// tabs/overview.php  (variabel dari detail.php: $p, $snap, $abil, $ovr, $age, $born)
$abil   = snapshot_abilities($snap);                  // GK: a6-a10, lainnya: a1-a5
$ovr    = ($abil && !empty($snap['pos_code'])) ? player_ovr($snap['pos_code'], $abil) : null;
$pts    = $abil ? radar_points($abil) : [];
$sec    = snapshot_secondary($snap);
$labels = ability_labels($snap['pos_code'] ?? null);
$weak   = $snap && $snap['weak'] !== null ? max(0, min(5, (int)$snap['weak'])) : null;
$city   = $p['city'] ?? null;
$rows   = [
	'Full Name'			 => trim((string)$p['full']) !== '' ? $p['full'] : player_display_name($p),
	'Date of Birth (Age)'=> $born . ((string)$age ? ' (' . $age . ')' : ''),
	'Place of Birth'	 => $city !== null ? (string)$city : '-',
	'Nationality'		 => (string)($p['region_name'] ?? '-'),
	'Height'			 => (int)$p['height'] . ' cm',
	'Weight'			 => (int)$p['weight'] . ' kg',
	'Preferred Foot'	 => trim((string)$p['foot']) !== '' ? $p['foot'] : '-',
	'Weak Foot'			 => $weak !== null ? $weak . ' / 5' : '-',
	'Position'			 => $snap ? trim(($snap['pos_name'] ?? '-') . ($snap['pos_code'] ? ' (' . $snap['pos_code'] . ')' : '')) : '-',
];
?>
<div class="row g-5">
	<div class="col-12 xl:col-4">
		<div class="row row-cols-1 lg:row-cols-2 xl:row-cols-1 g-5">
			<div class="col">
				<div class="card card-body h-100">
					<h4 class="card-title">General Information</h4>
						<div class="general-info">
						<?php foreach ($rows as $label => $value): ?>
							<span class="fg-2"><?= e($label) ?></span>
							<span class="fw-semibold text-end"><?= e((string)$value) ?></span>
						<?php endforeach; ?>
						</div>
				</div>
			</div>

			<div class="col">
				<div class="card h-100">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center gap-3">
							<h4 class="card-title">Team Information</h4>

							<div class="d-flex gap-1">
								<button class="btn-icon border" onclick="plusDivs(-1)">&#10094;</button>
								<button class="btn-icon border" onclick="plusDivs(+1)">&#10095;</button>
							</div>
						</div>
						<div id="teamSlides">
							<div class="d-flex flex-column gap-5">
								<div class="team-info">
									<div class="team-name">
										<img class="logo-md" src="">
										<div class="d-flex flex-column">
											<strong class="fs-lg">teamName</strong>
											<small class="fg-2">teamAlt &middot; compAlt</small>
										</div>
									</div>
									<div class="badge theme-success theme-subtle">Active</div>
								</div>
								<div class="general-team">
									<span class="fg-2">Joined since</span>
									<span class="fw-semibold text-end">January 13, 20xx</span>
									<span class="fg-2">League level</span>
									<span class="fw-semibold text-end">First Tier</span>
									<span class="fg-2">Contract until</span>
									<span class="fw-semibold text-end">January 1, 20xx</span>
								</div>
							</div>
						</div>
						<div id="teamSlides">
							<div class="d-flex flex-column gap-5">
								<div class="team-info">
									<div class="team-name">
										<img class="flag-md" src="">
										<div class="d-flex flex-column">
											<strong class="fs-lg">regionName</strong>
											<small class="fg-2">Southernia</small>
										</div>
									</div>
									<div class="badge theme-danger theme-subtle">Inactive</div>
								</div>
								<div class="general-info">
									<span class="fg-2">First Debut</span>
									<span class="fw-semibold text-end">January 13, 20xx</span>
									<span class="fg-2">Latest Match</span>
									<span class="fw-semibold text-end">January 1, 20xx</span>
									<span class="fg-2">Appearances</span>
									<span class="fw-semibold text-end">First Tier</span>
								</div>
							</div>
						</div>
						<div id="teamSlides">
							<div class="d-flex flex-column gap-5">
								<div class="team-info">
									<div class="team-name">
										<img class="logo-md" src="/csf/img/team/retired|free.png">
										<div class="d-flex flex-column">
											<strong class="fs-lg">Retired/Free Agent</strong>
											<small class="fg-2">Since - 20xx</small>
										</div>
									</div>
									<div class="badge theme-secondary theme-subtle">Retired/Free</div>
								</div>
								<div class="general-team">
									<span class="fg-2">Retired since</span>
									<span class="fw-semibold text-end">January 13, 20xx</span>
									<span class="fg-2">Last team</span>
									<span class="fw-semibold text-end">teamAlt</span>
									<span class="fg-2">Most appearances</span>
									<span class="fw-semibold text-end">teamAlt</span>
								</div>
							</div>
						</div>
					</div>
					<script>
						let slideIndex = 1;
						showDivs(slideIndex);

						function plusDivs(n) {
							showDivs(slideIndex += n);
						}

						function showDivs(n) {
							const slides = document.querySelectorAll("#teamSlides");

							if (n > slides.length) {
								slideIndex = 1
							}
							if (n < 1) {
								slideIndex = slides.length
							}

							slides.forEach(slide => {
								slide.style.display = "none";
							});

							slides[slideIndex - 1].style.display = "block";
						}
					</script>
					<div class="card-footer">
						<div class="player-value">
							<span class="market-value">100k</span>
							<span class="wage-value">100k</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-12 xl:col-8">
		<div class="card card-body">
			<div class="d-flex justify-content-between align-items-center">
				<h4 class="card-title">Abilities and Positions</h4>
				<button type="button" class="btn-solid btn-sm border" data-bs-toggle="menu" aria-expanded="false">
					<span class="d-inline">
						<span><?= $snap['pa_date'] ? e(date('M j, Y', strtotime($snap['pa_date']))) : '' ?></span>
						<i class="fa-solid fa-angle-down fs-xs" id="careticon"></i>
					</span>
				</button>
				<div class="menu">
					<a class="menu-item" href="">Jan 11, 2005</a>
					<a class="menu-item" href="">Jan 10, 2005</a>
					<a class="menu-item" href="">Jan 1, 2005</a>
				</div>
			</div>
			<?php if ($abil === null): ?>
				<p class="text-center fg-secondary user-select-none mb-0">No ability data.</p>
			<?php else: ?>
			<div class="d-flex flex-column gap-5">
				<div class="d-flex flex-column gap-5">
						<div class="base-ovr">
							<div class="ovr-info">
								<span class="overall"><?= (int)$ovr ?></span>
								<span class="ovb-info">
									<span class="posName"><?= e((string)$snap['pos_name']) ?></span>
									<span class="posCode"><?= e((string)$snap['pos_code']) ?></span>
								</span>
							</div>
							<?php foreach ($sec as $s): ?>
							<?php if ($s): ?>
							<div class="ovr-info">
								<span class="overall"><?= (int)player_ovr($s['code'], snapshot_abilities($snap, $s['code'])) ?></span>
								<span class="ovb-info">
									<span class="posName"><?= e($s['name']) ?></span>
									<span class="posCode"><?= e($s['code']) ?></span>
								</span>
							</div>
							<?php else: ?>
							<div class="ovr-info">
								<span class="overall">-</span>
								<span class="ovb-info">
									<span>None</span>
								</span>
							</div>
							<?php endif; ?>
							<?php endforeach; ?>
						</div>

					<div class="row align-items-center g-5">
						<div class="col-12 md:col-6 lg:col-5">
							<div class="radar-chart">
								<svg viewBox="0 0 400 330">
									<g class="radar-grid">
										<polygon points="200,142.5 226.2,161.5 216.2,192.3 183.8,192.3 173.8,161.5"/>
										<polygon points="200,115 252.3,153 232.3,215 167.7,215 147.7,153"/>
										<polygon points="200,87.5 278.5,144.5 248.5,237.5 151.5,237.5 121.5,144.5"/>
										<polygon points="200,60 304.6,136 264.6,258.9 135.4,258.9 95.4,136"/>

										<path d="
											M200 170 L200 60
											M200 170 L304.6 136
											M200 170 L264.6 258.9
											M200 170 L135.4 258.9
											M200 170 L95.4 136
										"/>
									</g>
									<polygon class="radar-area" points="<?= e(implode(' ', array_map(fn($p) => $p['x'] . ',' . $p['y'], $pts))) ?>"/>

									<?php $lp = [[200, 50, 'middle'], [315, 140, 'start'], [250, 280, 'start'], [150, 280, 'end'], [85, 140, 'end']]; ?>
									<g class="radar-labels">
									<?php foreach ($labels as $i => [$short]): [$lx, $ly, $anchor] = $lp[$i]; ?>
										<text x="<?= $lx ?>" y="<?= $ly ?>" text-anchor="<?= $anchor ?>"><?= e($short) ?></text>
									<?php endforeach; ?>
									</g>
								</svg>

								<?php foreach ($labels as $i => [, $name]): ?>
								<span class="radar-point" style="--x: <?= $pts[$i]['px'] ?>%; --y: <?= $pts[$i]['py'] ?>%;" tabindex="0">
									<span class="radar-tooltip"><?= e($name) ?>: <?= (int)$abil[$i] ?></span>
								</span>
								<?php endforeach; ?>
							</div>
						</div>

						<div class="col-12 md:col-6 lg:col-7">
							<div class="d-flex flex-column gap-3 lg:gap-5">
						<?php foreach ($labels as $i => [$short, $name]): $v = max(40, min(100, $abil[$i])); ?>
							<div class="d-flex flex-column">
								<div class="d-flex justify-content-between align-items-center">
									<span><?= e($name) ?> <small class="fg-2">(<?= e($short) ?>)</small></span>
									<strong><?= (int)$abil[$i] ?></strong>
								</div>
								<div class="ovr-bar" style="--ovr-base: <?= $v ?>%;" aria-label="<?= e($name) ?>" aria-value="<?= $v ?>"></div>
							</div>
						<?php endforeach; ?>
							</div>
						</div>
					</div>
				</div>
				<div class="row g-5 align-items-start">
					<div class="col-12 md:col-3">
						<div class="pos-desc">
							<dl>
								<dt>Main Position</dt>
								<dd><?= e((string)$snap['pos_name']) ?></dd>
							</dl>
							<?php $secActive = array_filter($sec); ?>
							<dl>
								<dt>Second Position</dt>
								<?php foreach ($secActive as $s): ?>
								<dd><?= e($s['name']) ?></dd>
								<?php endforeach; ?>
								<?php if (!$secActive): ?>
								<dd>None</dd>
								<?php endif; ?>
							</dl>
						</div>
					</div>

					<div class="col-12 md:col-9">
						<div class="court-player">
							<div class="court-square">
								<div class="cs-circle"></div>
							</div>
							<div class="court-circle"></div>
							<?php
							$posNames = array_column(positions_all(), 'name', 'code');
							$main     = strtoupper(trim((string)($snap['pos_code'] ?? '')));
							$secCodes = array_column(array_filter($sec), 'code'); ?>
							<div class="court-position">
							<?php foreach (array_keys(ovr_weights()) as $code):
								$cls = $code === $main ? ' main' : (in_array($code, $secCodes, true) ? '' : ' disabled');
								$o   = player_ovr($code, snapshot_abilities($snap, $code)); ?>
								<div class="p-<?= e($code) ?><?= $cls ?>" title="<?= e($posNames[$code] ?? $code) ?>"><?= $o ?></div>
							<?php endforeach; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php endif; ?>
		</div>
	</div>
</div>



