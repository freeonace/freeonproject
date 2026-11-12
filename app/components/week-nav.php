<?php
// variabel: $weeks, $week
$nums = array_map(fn($r) => (int)$r['week'], $weeks);
$idx  = array_search((int)$week, $nums, true);
?>
<div class="d-flex justify-content-between align-items-center">
	<button type="button" class="btn btn-menu" data-week-step="-1" aria-label="Previous matchweek"<?= ($idx === false || $idx <= 0) ? ' disabled' : '' ?>>
		<i class="fa-solid fa-chevron-left"></i>
	</button>

	<button class="btn-text" type="button" data-bs-toggle="menu" data-bs-placement="bottom" aria-expanded="false" data-week-toggle>
		<span class="d-flex align-items-center gap-2">
			<span class="fs-lg">Matchweek <span data-week-label><?= (int)$week ?></span></span>
			<i class="fa-solid fa-caret-down" id="careticon"></i>
		</span>
	</button>

	<button type="button" class="btn btn-menu" data-week-step="1" aria-label="Next matchweek"<?= ($idx === false || $idx >= count($nums) - 1) ? ' disabled' : '' ?>>
		<i class="fa-solid fa-chevron-right"></i>
	</button>
</div>

<div class="menu menu-week">
	<div class="row row-cols-3 md:row-cols-4 text-center g-2">
		<?php foreach ($nums as $n): $on = ($n === (int)$week); ?>
			<div class="col">
				<a class="card rounded-1 text-decoration-none<?= $on ? ' active' : '' ?>"<?= $on ? ' aria-current="true"' : '' ?>
				   href="#" role="button" data-week-go="<?= $n ?>"><?= $n ?></a>
			</div>
		<?php endforeach; ?>
	</div>
</div>