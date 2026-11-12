<?php // app/layout/footer.php ?>
<footer class="footer bg-2">
	<div class="lg:p-9 p-5 lg:container">
		<div class="d-flex flex-column">
			<div class="row pb-9 g-9">
				<div class="lg:col-6 col-12">
					<p class="fw-semibold">About <?= e(site('name')) ?>'s</p>
					<p class="text-justify lh-lg my-3">
						Welcome to a <b><?= e(site('name')) ?>'s</b> sites, a sites made for reporting football database for <b><?= e(site('long')) ?></b>, a football federation from many countries. This sites provides team database, competition database, transfer news database, fixtures database, and others.
						<a class="text-decoration-none" href="<?= url('home', 'about') ?>"> Click here for more information…</a>
					</p>
				</div>
<!--				<div class="lg:col-6 col-12">
					<p class="fw-semibold">Main</p>
					<div class="flex-column p-0">
						<?php foreach ($menu['sections'] as $key => $sec): $on = ($key === $section); ?>
						<div class="mb-3">
							<?php
							$full  = $sec['name'] ?? $sec['label'];
							$short = $sec['label'];
							?>
							<a class="text-decoration-none" <?= $on ? 'aria-current="' . ($page === null ? 'page' : 'true') . '"' : '' ?>
						href="<?= url($key) ?>"><?= e($full) ?></a>
						</div>
						<?php endforeach; ?>
					</div>
				</div>
-->
				<div class="lg:col-6 col-12">
					<p class="fw-semibold">Get in Touch</p>
					<div class="d-flex align-items-center gap-3 my-3">
						<span class="fg-primary">
							<i class="fa-solid fa-phone"></i>
						</span>
						<span class="fg-inverse lh-1">+60 xxxxxxxx</span>
					</div>
					<div class="d-flex align-items-center gap-3 my-3">
						<span class="fg-primary">
							<i class="fa-solid fa-location-dot"></i>
						</span>
						<span class="fg-inverse lh-1">29 Chiyoshi Residence, <b>Gozachi, Amizu</b></span>
					</div>
					<div class="d-flex align-items-center gap-3 my-3">
						<span class="fg-primary">
							<i class="fa-solid fa-envelope"></i>
						</span>
						<a class="lh-1 text-decoration-none theme-inverse" href="">@csfsupportcompany.com</a>
					</div>
				</div>
			</div>

			<div class="text-center">
				<span class="fg-1">Don't have an account yet?</span>
				<a class="text-decoration-none" href="<?= url('account','signup') ?>">Sign Up now.</a>
			</div>
		</div>
	</div>
</footer>
<div class="copyright bg-primary fg-contrast-primary text-center">
	<span><b>&copy;<?= site('since') ?>&ndash;<?= date('Y') ?> <?= e(site('name')) ?></b>. All rights reserved.</span>
</div>

<script>
	document.addEventListener("DOMContentLoaded", () => {
		const maxChecked = 4;
		const checkboxes = document.querySelectorAll(".limit-check");
		const alertBox = document.getElementById("alertBox");
		const alertMessage = document.getElementById("alertMessage");

		let alertTimeout;

		function showAlert(msg) {
			alertMessage.textContent = msg;

			alertBox.classList.remove("d-none");
			void alertBox.offsetWidth;
			alertBox.classList.add("show");

			clearTimeout(alertTimeout);
			alertTimeout = setTimeout(() => {
				alertBox.classList.remove("show");

				setTimeout(() => {
					alertBox.classList.add("d-none");
				}, 200);
			}, 1000);
		}

		const selectionOrder = [...checkboxes].filter(cb => cb.checked);

		checkboxes.forEach(cb => {
			cb.addEventListener("change", () => {

				if (cb.checked) {
					selectionOrder.push(cb);

					if (selectionOrder.length > maxChecked) {
						const previousLast = selectionOrder.splice(-2, 1)[0];

						previousLast.checked = false;

						showAlert(`Max ${maxChecked} positions.`);
					}

				} else {
					const index = selectionOrder.indexOf(cb);

					if (index !== -1) {
						selectionOrder.splice(index, 1);
					}
				}
			});
		});
	});
</script>

<script>
	(function () {
		document.querySelectorAll('[data-mc]').forEach((root) => {
			const prev    = root.querySelector('.mc-prev');
			const next    = root.querySelector('.mc-next');
			const filters = root.querySelectorAll('[data-mc-filter]');
			const tracks  = root.querySelectorAll('.mc-track');
			const reduce  = matchMedia('(prefers-reduced-motion: reduce)');

			const current = () => root.querySelector('.mc-track:not([hidden])');

			const update = () => {
				const t = current();
				if (!t) return;
				prev.disabled = t.scrollLeft <= 1;
				next.disabled = t.scrollLeft >= t.scrollWidth - t.clientWidth - 1;
			};

			const go = (dir) => {
				const t = current();
				if (t) t.scrollBy({ left: dir * t.clientWidth * 0.9, behavior: reduce.matches ? 'auto' : 'smooth' });
			};

			prev.addEventListener('click', () => go(-1));
			next.addEventListener('click', () => go(1));
			tracks.forEach((t) => t.addEventListener('scroll', update, { passive: true }));
			new ResizeObserver(update).observe(root);

			filters.forEach((b) => b.addEventListener('click', () => {
				filters.forEach((x) => {
					const on = x === b;
					x.setAttribute('aria-pressed', String(on));
					x.classList.toggle('btn-solid', on);
					x.classList.toggle('btn-outline', !on);
				});
				tracks.forEach((t) => { t.hidden = t.dataset.mcPanel !== b.dataset.mcFilter; });
				const t = current();
				if (t) t.scrollLeft = 0;
				update();
			}));

			update();
		});
	})();
</script>

<script>
	(function () {
		const list = document.getElementById('tabnav');
		if (!list) return;

		const tabs = [...list.querySelectorAll('[data-bs-toggle="tab"]')];
		const key  = (b) => b.dataset.bsTarget.replace('#', '').replace('-tab-pane', '');

		const fromHash = () => {
			const k = decodeURIComponent(location.hash.slice(1));
			const t = tabs.find((b) => key(b) === k);
			if (t && !t.classList.contains('active')) t.click();
		};

		tabs.forEach((b) => b.addEventListener('click', () => {
			history.replaceState(null, '', '#' + key(b));
		}));

		fromHash();
		addEventListener('hashchange', fromHash);
	})();
</script>

<script>
	(function () {
		document.querySelectorAll('[data-week-root]').forEach((root) => {
			const panels = new Map(
				[...root.querySelectorAll('[data-week-panel]')].map((p) => [Number(p.dataset.weekPanel), p])
			);
			if (!panels.size) return;

			const nums   = [...panels.keys()];
			const label  = root.querySelector('[data-week-label]');
			const steps  = [...root.querySelectorAll('[data-week-step]')];
			const items  = [...root.querySelectorAll('[data-week-go]')];
			const toggle = root.querySelector('[data-week-toggle]');

			// menu pekan tanpa panel (data tidak lengkap) dibuang
			items.forEach((a) => {
				if (!panels.has(Number(a.dataset.weekGo))) a.closest('.col')?.remove();
			});

			let current = nums.find((n) => !panels.get(n).hidden) ?? nums[0];

			const show = (n) => {
				if (!panels.has(n)) return;
				current = n;
				panels.forEach((p, k) => { p.hidden = (k !== n); });
				if (label) label.textContent = n;

				const active = panels.get(n);
				active.scrollLeft = 0;
				active.dispatchEvent(new Event('scroll'));

				root.querySelectorAll('[data-week-link]').forEach((a) => {
					const u = new URL(a.getAttribute('href'), location.href);
					u.searchParams.set('week', n);
					a.setAttribute('href', u.pathname + u.search);
				});

				const i = nums.indexOf(n);
				steps.forEach((b) => {
					b.disabled = Number(b.dataset.weekStep) < 0 ? i <= 0 : i >= nums.length - 1;
				});
				items.forEach((a) => {
					const on = Number(a.dataset.weekGo) === n;
					a.classList.toggle('active', on);
					if (on) a.setAttribute('aria-current', 'true');
					else a.removeAttribute('aria-current');
				});
			};

			// listener dipasang langsung ke elemen (bukan ke root),
			// supaya tetap jalan kalau komponen menu memindahkan .menu ke tempat lain
			steps.forEach((b) => b.addEventListener('click', () => {
				if (b.disabled) return;
				const next = nums[nums.indexOf(current) + Number(b.dataset.weekStep)];
				if (next !== undefined) show(next);
			}));

			items.forEach((a) => a.addEventListener('click', (e) => {
				e.preventDefault();
				show(Number(a.dataset.weekGo));
				if (toggle && toggle.getAttribute('aria-expanded') === 'true') toggle.click();
			}));

			show(current);
		});
	})();
</script>