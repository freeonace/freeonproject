<?php
$seasons  = comp_seasons($compId);
$seasonId = pick_season($seasons);
if ($seasonId === null) {
	echo '<p class="text-center fg-secondary user-select-none">No overview yet.</p>';
	return;
}

$rows  = standings($compId, $seasonId);
$forms = last_five_all($compId, $seasonId);
$weeks = comp_weeks($compId, $seasonId);
$week  = pick_matchweek($weeks);
$byWeek = matches_by_week($compId, $seasonId);
//$games = $week !== null ? group_by_kickoff(week_matches($compId, $seasonId, $week)) : [];
$base  = comp_url($compId, $comp['alt']);
?>
<div class="row g-5">
	<div class="col-12 xl:col-8 lg:order-1">
		<div class="row row-cols-1 g-5">
			<div class="col">
				<div class="card">
					<div class="card-body">
						<?php if ($rows): ?>
							<?php component('standing-table', ['rows' => $rows, 'forms' => $forms, 'limit' => 10]); ?>
							<a class="btn-solid btn-sm theme-primary" href="<?= e(comp_url($compId, $comp['alt'], 'standings') . '?season=' . $seasonId) ?>">Full standings</a>
						<?php else: ?>
							<p class="text-center fg-secondary user-select-none">No standing yet.</p>
						<?php endif; ?>
					</div>
				</div>
			</div>

			<div class="col">
				<div class="card card-body" style="position: relative;">
					<h4 class="card-title">Home Match</h4>
					<div class="button-change">
						<button type="button" class="btn-icon btn-lg border">
							<i class="bi bi-chevron-left"></i>
						</button>
						<button type="button" class="btn-icon btn-lg border">
							<i class="bi bi-chevron-right"></i>
						</button>
					</div>
					<div class="card overflow-hidden">
						<div class="table-responsive pt-9">
							<table class="table text-center">
								<thead>
									<tr>
										<th>-/-</th>
										<th>ShortA</th>
										<th>ShortB</th>
										<th>ShortC</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<th>ShortA</th>
										<td>-</td>
										<td>2</td>
										<td>1</td>
									</tr>
									<tr>
										<th>ShortB</th>
										<td>1</td>
										<td>-</td>
										<td>3</td>
									</tr>
									<tr>
										<th>ShortC</th>
										<td>1</td>
										<td>0</td>
										<td>-</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-12 xl:col-4 order-3 lg:order-2">
		<div class="card card-body">
			<div class="d-flex flex-column gap-5" data-week-root>
				<?php if ($week !== null && $byWeek): ?>
					<?php component('week-nav', compact('weeks', 'week')); ?>
					<?php component('week-panel', compact('byWeek', 'week')); ?>
				<?php else: ?>
					<p class="text-center fg-secondary user-select-none">No matches yet.</p>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<div class="col-12 xl:col-8 order-2 lg:order-3">
		<!-- Top Score | Top Assist: menunggu tabel pemain dan kejadian -->
	</div>
</div>