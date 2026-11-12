<div class="d-flex flex-column gap-5">
	<div class="d-flex flex-column gap-3">
		<h2 class="text-title"><?= e($team['alt']) ?> Squad</h2>
		<form method="get" class="pb-3">
			<div class="d-flex align-items-center gap-3">
				<div class="form-field">
					<select class="form-control" id="season" name="season">
						<option>Season 2005</option>
					</select>
				</div>
				<button type="submit" class="btn-solid theme-primary">Show</button>
			</div>
		</form>
	</div>

	<!-- Ini saya pakai untuk perpindahan atau pemilihan season <div class="d-flex flex-row align-items-center justify-content-center gap-5">
		<div class="spinner-grow" style="width: 3rem; height: 3rem;" role="status">
			<span class="visually-hidden">Loading…</span>
		</div>
		<div class="spinner-grow" style="width: 3rem; height: 3rem;" role="status">
			<span class="visually-hidden">Loading…</span>
		</div>
		<div class="spinner-grow" style="width: 3rem; height: 3rem;" role="status">
			<span class="visually-hidden">Loading…</span>
		</div>
	</div> -->

	<div class="row row-cols-1 g-5">
		<div class="col">
			<div class="d-flex flex-column gap-3">
				<!-- Abaikan dulu, nanti dibahas -->
				<div class="card card-body">
					<span class="card-title">Coach</span>
					<div class="squad-grid">
						<div class="card"></div>
						<div class="card"></div>
						<div class="card"></div>
					</div>
				</div>

				<div class="card card-body">
					<span class="card-title">Goalkeeper</span>
					<div class="squad-grid">
						<a class="card card-gk" href="" id="">
							<div class="card-squad">
								<div class="card-player">
									<i class="fa-regular fa-user"></i>
								</div>
								<div class="card-info">
									<span class="card-name"><!-- Prioritas menggunakan playerName. Kalau tidak ada berarti fallback playerFirst jadi R. dan playerLast -->R. Hedges</span>
									<div class="card-position">
										<span>Goalkeeper</span>
										<span>&middot;</span>
										<span>#1</span>
									</div>
								</div>
							</div>
						</a>
					</div>
				</div>

				<div class="card card-body">
					<span class="card-title">Defender</span>
					<div class="squad-grid">
					</div>
				</div>

				<div class="card card-body">
					<span class="card-title">Midfielder</span>
					<div class="squad-grid">
					</div>
				</div>

				<div class="card card-body">
					<span class="card-title">Forward</span>
					<div class="squad-grid">
					</div>
				</div>
			</div>
		</div>

		<div class="col">
			<div class="row row-cols-1 md:row-cols-2 g-5">
				<div>A</div>
				<div>B</div>
			</div>
		</div>
	</div>
</div>
