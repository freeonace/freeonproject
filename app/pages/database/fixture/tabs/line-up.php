<?php
// tabs/line-up.php  (variabel dari detail.php: $m, $fallback)
$det    = fixture_details($m);
$played = $m['sh'] !== null && $m['sa'] !== null;

if (!$det['home'] && !$det['away']) {
	echo '<p class="text-center fg-secondary user-select-none">No line-up available.</p>';
	return;
}
?>
<div><?= $played ? 'Squad Formations' : 'Predicted Squad' ?></div>

<div class="xl:p-9">
	<div class="court">
		<div class="court-team">
			<div class="court-header">
				<div class="ch-h">
					<a class="text-decoration-none" href="<?= e(team_info_url((int)$m['home_id'], $m['home_alt'], $m['home_name'])) ?>">
						<div class="md:d-flex align-items-center gap-2 d-none">
							<img class="logo-md" src="<?= e(team_logo((int)$m['home_id'])) ?>" alt="" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='<?= e($fallback) ?>'">
							<div class="d-flex flex-column">
								<small><?= e($m['home_alt']) ?></small>
								<strong><?= e($det['home']['formName'] ?? '-') ?></strong>
							</div>
						</div>
					</a>
				</div>

				<div class="ch-a">
					<a class="text-decoration-none" href="<?= e(team_info_url((int)$m['away_id'], $m['away_alt'], $m['away_name'])) ?>">
						<div class="md:d-flex align-items-center justify-content-end gap-2 d-none">
							<div class="d-flex flex-column">
								<small><?= e($m['away_alt']) ?></small>
								<strong><?= e($det['away']['formName'] ?? '-') ?></strong>
							</div>
							<img class="logo-md" src="<?= e(team_logo((int)$m['away_id'])) ?>" alt="" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='<?= e($fallback) ?>'">
						</div>
					</a>
				</div>
			</div>
			<div class="ch-h md:d-none">
				<a class="text-decoration-none" href="<?= e(team_info_url((int)$m['home_id'], $m['home_alt'], $m['home_name'])) ?>">
					<div class="d-flex align-items-center justify-content-between">
						<div class="d-flex gap-2 align-items-center">
							<img class="logo-md" src="<?= e(team_logo((int)$m['home_id'])) ?>" alt="" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='<?= e($fallback) ?>'">
							<small><?= e($m['home_alt']) ?></small>
						</div>
						<strong><?= e($det['home']['formName'] ?? '-') ?></strong>
					</div>
				</a>
			</div>
			<div class="court-body">
				<div class="court-field">
					<div class="court-field-back">
						<div class="cfb-k"></div>
					</div>
					<div class="court-field-center"></div>
					<div class="court-field-circle"></div>
					<div class="court-field-forward">
						<div class="cff-k"></div>
					</div>
				</div>
				<div class="team-h">
					<?php component('lineup-slots', ['F' => $det['home']]); ?>
				</div>
				<div class="team-a">
					<?php component('lineup-slots', ['F' => $det['away']]); ?>
				</div>
			</div>
			<div class="ch-a md:d-none">
				<a class="text-decoration-none" href="<?= e(team_info_url((int)$m['away_id'], $m['away_alt'], $m['away_name'])) ?>">
					<div class="d-flex align-items-center justify-content-between">
						<div class="d-flex gap-2 align-items-center">
							<img class="logo-md" src="<?= e(team_logo((int)$m['away_id'])) ?>" alt="" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='<?= e($fallback) ?>'">
							<small><?= e($m['away_alt']) ?></small>
						</div>
						<strong><?= e($det['away']['formName'] ?? '-') ?></strong>
					</div>
				</a>
			</div>
		</div>
	</div>
</div>

<?php /* TODO: daftar pemain per tim (nama, nomor, posisi, menit) menunggu tabel player dan playerrecord. Markup contoh disimpan di bawah. */ ?>
<?php if (false): ?>
<div class="row row-cols-2 g-5">
	<div class="col">
		<div class="row row-cols-1 g-3">
			<div class="col">
				<div class="card lh-home">
					<a href="">
						<div class="d-flex align-items-center justify-content-between">
							<div class="d-flex align-items-center gap-5">
								<strong class="pos-PL">GK</strong>
								<div class="d-flex flex-column">
									<span class="pl-name">Player</span>
									<small>1 - Goalkeeper</small>
								</div>
							</div>
							<div class="times">
								<strong>60'</strong>
								<i class="fa-solid fa-square-caret-down"></i>
							</div>
						</div>
					</a>
				</div>
			</div>
		</div>
	</div>

	<div class="col">
		<div class="row row-cols-1">
			<div class="col">
				<div class="card lh-away"></div>
			</div>
		</div>
	</div>
</div>

<div class="row row-cols-2 g-5">
	<div class="col">
		<div class="row row-cols-1 g-3">
			<div class="col">
				<div class="card lh-home">
					<a href="">
						<div class="d-flex align-items-center justify-content-between">
							<div class="d-flex align-items-center gap-5">
								<strong class="pos-PL">GK</strong>
								<div class="d-flex flex-column">
									<span class="pl-name">Player</span>
									<small>1 - Goalkeeper</small>
								</div>
							</div>
							<div class="times">
								<strong>60'</strong>
								<i class="fa-solid fa-square-caret-up"></i>
							</div>
						</div>
					</a>
				</div>
			</div>
		</div>
	</div>

	<div class="col">
		<div class="row row-cols-1">
			<div class="col">
				<div class="card lh-away"></div>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>