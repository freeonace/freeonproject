<?php
$seasons  = comp_seasons($compId);
$seasonId = pick_season($seasons);
if ($seasonId === null) {
	echo '<p class="text-center fg-secondary user-select-none">No matches yet.</p>';
	return;
}

$weeks  = comp_weeks($compId, $seasonId);
$week   = pick_matchweek($weeks);
$byWeek = matches_by_week($compId, $seasonId);
?>
<?php component('season-form', compact('seasons', 'seasonId')); ?>

<div class="card">
	<div class="card-body">
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