<?php
// variabel: $byWeek ([pekan => baris]), $week (pekan yang tampil)
foreach ($byWeek as $w => $rows):
	$games = group_by_kickoff($rows);
?>
<div class="week-panel" data-week-panel="<?= (int)$w ?>"<?= (int)$w === (int)$week ? '' : ' hidden' ?>>
	<?php component('week-match', compact('games')); ?>
</div>
<?php endforeach; ?>