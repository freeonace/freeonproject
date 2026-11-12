<?php // Index ?>
<div class="d-flex flex-column">
	<div class="md:p-5 p-4 border-bottom">
		<?php component('match-carousel', ['limit' => 20]); ?>
	</div>
	<div id="carouselDatabase" class="carousel carousel-fade" data-bs-autoplay="true">
		<div class="button-fade">
			<button class="btn-icon border" type="button" data-bs-target="#carouselDatabase" data-bs-slide="prev">
				<span class="carousel-icon-prev" aria-hidden="true"></span>
				<span class="visually-hidden">Previous</span>
			</button>
			<button class="btn-icon border" type="button" data-bs-target="#carouselDatabase" data-bs-slide="next">
				<span class="carousel-icon-next" aria-hidden="true"></span>
				<span class="visually-hidden">Next</span>
			</button>
		</div>
		<div class="carousel-inner">
			<div class="carousel-item active" data-bs-interval="4000">
				<div class="dbmenu">
					<h2 class="text-title"><?= e(site('name')) ?> Team Database</h2>
					<div class="row row-cols-1 lg:row-cols-2 g-5">
						<div class="col">
							<div class="d-flex flex-column py-9">
								<p>Browse teams across all <?= e(site('name')) ?> leagues and competitions.</p>
								<a class="btn-solid theme-primary align-self-start" href="<?= e(url('database', 'team')) ?>">See All</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="carousel-item" data-bs-interval="4000">
				<div class="dbmenu">
					<h2 class="text-title"><?= e(site('name')) ?> Player Database</h2>
					<div class="row row-cols-1 lg:row-cols-2 g-5">
						<div class="d-flex flex-column py-9">
							<p>Discover players, profiles and career statistics across <?= e(site('name')) ?>.</p>
							<a class="btn-solid theme-primary align-self-start" href="<?= e(url('database', 'player')) ?>">See All</a>
						</div>
					</div>
				</div>
			</div>
			<div class="carousel-item" data-bs-interval="4000">
				<div class="dbmenu">
					<h2 class="text-title"><?= e(site('name')) ?> Fixture Database</h2>
					<div class="row row-cols-1 lg:row-cols-2 g-5">
						<div class="d-flex flex-column py-9">
							<p>Explore upcoming fixtures and match results across <?= e(site('name')) ?> competitions.</p>
							<a class="btn-solid theme-primary align-self-start" href="<?= e(url('database', 'fixture')) ?>">See All</a>
						</div>
					</div>
				</div>
			</div>
			<div class="carousel-item" data-bs-interval="4000">
				<div class="dbmenu">
					<h2 class="text-title"><?= e(site('name')) ?> Competition Database</h2>
					<div class="row row-cols-1 lg:row-cols-2 g-5">
						<div class="d-flex flex-column py-9">
							<p>Discover <?= e(site('name')) ?> leagues, cups and competition records.</p>
							<a class="btn-solid theme-primary align-self-start" href="<?= e(url('database', 'competition')) ?>">See All</a>
						</div>
					</div>
				</div>
			</div>
			<div class="carousel-item" data-bs-interval="4000">
				<div class="dbmenu">
					<h2 class="text-title"><?= e(site('name')) ?> Transfer Database</h2>
					<div class="row row-cols-1 lg:row-cols-2 g-5">
						<div class="d-flex flex-column py-9">
							<p>Follow player movements and transfer activity across <?= e(site('name')) ?> clubs.</p>
							<a class="btn-solid theme-primary align-self-start" href="<?= e(url('database', 'transfer')) ?>">See All</a>
						</div>
					</div>
				</div>
			</div>
			<div class="carousel-indicators">
				<button type="button" data-bs-target="#carouselDatabase" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
				<button type="button" data-bs-target="#carouselDatabase" data-bs-slide-to="1" aria-label="Slide 2"></button>
				<button type="button" data-bs-target="#carouselDatabase" data-bs-slide-to="2" aria-label="Slide 3"></button>
				<button type="button" data-bs-target="#carouselDatabase" data-bs-slide-to="3" aria-label="Slide 4"></button>
				<button type="button" data-bs-target="#carouselDatabase" data-bs-slide-to="4" aria-label="Slide 5"></button>
			</div>
			<button class="btn-icon btn-sm carousel-control-play-pause" type="button" data-bs-target="#carouselDatabase" aria-label="Pause" data-bs-pause-label="Pause" data-bs-play-label="Play">
				<span class="carousel-icon-pause" aria-hidden="true"></span>
				<span class="carousel-icon-play" aria-hidden="true"></span>
			</button>
		</div>
	</div>
	<div class="bg-1 md:p-7 p-4 border-bottom">
		<h2 class="text-title"><?= e(site('name')) ?> Coefficient</h2>
	</div>
	<div class="md:p-7 p-4">
		<h2 class="text-title"><?= e(site('name')) ?> News</h2>
		<p class="text-justify">Stay up to date with our <?= e(site('name')) ?>'s club, national team, and competition news, and learn how we are developing the <?= e(site('name')) ?>'s game in Southernia.</p>
	</div>
</div>