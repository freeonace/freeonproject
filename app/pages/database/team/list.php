<?php
$perLeague = 8;
$perSmall  = 4;

$leagues  = team_leagues();
$byEntity = teams_by_entities(array_column($leagues, 'id'));

$byId = $leagues;
usort($byId, fn($a, $b) => (int)$a['id'] <=> (int)$b['id']);
?>
<div class="md:p-7 lg:p-9 p-4">
	<div class="d-flex flex-column">
		<div class="pb-8">
			<h2 class="text-title">List of All Teams</h2>
			<span class="fg-inverse"><?= e(site('name')) ?> Teams in All Leagues</span>
		</div>

		<?php if (!$leagues): ?>
			<p class="text-center fg-secondary user-select-none">No teams yet.</p>
		<?php else: ?>
		<div class="d-flex flex-column gap-5">
			<div class="nav-overflow team-select" data-bs-toggle="nav-overflow">
				<ul class="nav" id="nav-tab" role="tablist">
					<li class="nav-item" role="presentation">
						<button class="nav-link active" id="nav-all-tab" data-bs-toggle="tab" data-bs-target="#nav-all" type="button" role="tab" aria-controls="nav-all" aria-selected="true">All League</button>
					</li>
					<?php foreach ($leagues as $l): ?>
						<li class="nav-item" role="presentation">
							<button class="nav-link" id="nav-e<?= (int)$l['id'] ?>-tab" data-bs-toggle="tab" data-bs-target="#nav-e<?= (int)$l['id'] ?>" type="button" role="tab" aria-controls="nav-e<?= (int)$l['id'] ?>" aria-selected="false"><?= e($l['name']) ?></button>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>

			<div class="tab-content" id="nav-tabContent">
				<div class="tab-pane show active" id="nav-all" role="tabpanel" aria-labelledby="nav-all-tab" tabindex="0">
					<div class="row row-cols-1 g-9">
						<?php foreach ($byId as $l): $teams = array_slice($byEntity[(int)$l['id']] ?? [], 0, $perLeague); if (!$teams) continue; ?>
							<div class="col mb-9">
								<div class="row row-cols-2 sm:row-cols-4 md:row-cols-2 xl:row-cols-4 g-5">
								<?php foreach ($teams as $i => $t): ?>
									<div class="col<?= $i >= $perSmall ? ' d-none md:d-block' : '' ?>"><?php component('team-card', compact('t')); ?></div>
								<?php endforeach; ?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>

				<?php foreach ($leagues as $l): $teams = $byEntity[(int)$l['id']] ?? []; ?>
					<div class="tab-pane" id="nav-e<?= (int)$l['id'] ?>" role="tabpanel" aria-labelledby="nav-e<?= (int)$l['id'] ?>-tab" tabindex="0">
						<?php if (!$teams): ?>
							<p class="text-center fg-secondary user-select-none">No teams yet.</p>
						<?php else: ?>
							<div class="row row-cols-2 sm:row-cols-4 md:row-cols-2 xl:row-cols-4 g-5">
								<?php foreach ($teams as $t): ?>
									<div class="col"><?php component('team-card', compact('t')); ?></div>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php endif; ?>
	</div>
</div>