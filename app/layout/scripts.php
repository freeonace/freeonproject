<?php // app/layout/script.php ?>
<script>
	(function () {
		var root = document.documentElement;
		var items = document.querySelectorAll('[data-theme-value]');
		var mq = matchMedia('(prefers-color-scheme: dark)');

		function apply(pref) {
			var dark = pref === 'dark' || (pref === 'system' && mq.matches);
			root.setAttribute('data-theme-pref', pref);
			root.setAttribute('data-bs-theme', dark ? 'dark' : 'light');
			root.style.colorScheme = dark ? 'dark' : 'light';
			items.forEach(function (el) {
				var on = el.dataset.themeValue === pref;
				el.classList.toggle('active', on);
				if (on) el.setAttribute('aria-current', 'true');
				else el.removeAttribute('aria-current');
			});
		}

		items.forEach(function (el) {
			el.addEventListener('click', function () {
				var pref = el.dataset.themeValue;
				try { localStorage.setItem('theme', pref); } catch (err) {}
				apply(pref);
			});
		});

		mq.addEventListener('change', function () {
			if (root.getAttribute('data-theme-pref') === 'system') apply('system');
		});

		addEventListener('storage', function (e) {
			if (e.key === 'theme') apply(e.newValue || 'system');
		});

		addEventListener('pageshow', function (e) {
			if (!e.persisted) return;
			var p = 'system';
			try { p = localStorage.getItem('theme') || 'system'; } catch (err) {}
			apply(p);
		});

		apply(root.getAttribute('data-theme-pref') || 'system');

		addEventListener('load', function () {
			requestAnimationFrame(function () {
				requestAnimationFrame(function () { root.classList.add('ready'); });
			});
		});

		var interacted = false;
		['pointerdown', 'keydown'].forEach(function (t) {
			addEventListener(t, function () { interacted = true; }, true);
		});
		var menuEl = document.querySelector('.menu');
		if (menuEl) {
			new MutationObserver(function () {
				if (menuEl.classList.contains('show') && !interacted) {
					console.warn('[menu] open', {
						ms: Math.round(performance.now()),
						readyState: document.readyState,
						ready: root.classList.contains('ready')
					});
				}
			}).observe(menuEl, { attributes: true, attributeFilter: ['class'] });
		}
	})();
</script>

<script>
	(function () {
		const root = document.documentElement;
		const btn = document.querySelector('.sidebar-toggle');
		const header = document.querySelector('header');
		const setH = () => root.style.setProperty('--header-h', header.offsetHeight + 'px');
		new ResizeObserver(setH).observe(header);
		setH();

		function sync() {
			const collapsed = root.classList.contains('sidebar-collapsed');
			btn.setAttribute('aria-expanded', String(!collapsed));
			btn.setAttribute('aria-label', collapsed ? 'collapsed sidebar' : 'toggle sidebar');
		}

		btn.addEventListener('click', () => {
			const collapsed = root.classList.toggle('sidebar-collapsed');
			try { localStorage.setItem('sidebar', collapsed ? 'collapsed' : 'expanded'); } catch (e) {}
			sync();
		});

		sync();
	})();
</script>

<script>
	(function () {
		const search = document.querySelector('.search');
		if (!search) return;

		const header = document.querySelector('header');
		const label  = search.querySelector('.form-control');
		const input  = search.querySelector('input[type="search"]');
		const panel  = search.querySelector('.search-panel');

		const place = () => {
			const h = header.getBoundingClientRect();
			const s = label.getBoundingClientRect();
			search.style.setProperty('--search-x', (s.left - h.left) + 'px');
			search.style.setProperty('--search-w', s.width + 'px');
		};

		const open = () => {
			place();
			panel.hidden = false;
			input.setAttribute('aria-expanded', 'true');
		};
		const close = () => {
			panel.hidden = true;
			input.setAttribute('aria-expanded', 'false');
		};

		addEventListener('resize', () => { if (!panel.hidden) place(); });

		input.addEventListener('focus', open);
		input.addEventListener('input', open);

		search.addEventListener('focusout', (e) => {
			if (!search.contains(e.relatedTarget)) close();
		});

		document.addEventListener('pointerdown', (e) => {
			if (!search.contains(e.target)) close();
		});

		search.addEventListener('keydown', (e) => {
			if (e.key === 'Escape') { close(); input.blur(); }
		});
	})();
</script>