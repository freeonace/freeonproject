(function () {
	var pref = 'system';
	try { pref = localStorage.getItem('theme') || 'system'; } catch (e) {}
	var dark = pref === 'dark' ||
	(pref === 'system' && matchMedia('(prefers-color-scheme: dark)').matches);
	var root = document.documentElement;
	root.dataset.theme = pref;
	root.classList.toggle('dark', dark);
	root.style.colorScheme = dark ? 'dark' : 'light';
})();

(function () {
	var root = document.documentElement;
	var buttons = document.querySelectorAll('.switches button');
	var mq = matchMedia('(prefers-color-scheme: dark)');

	function apply(pref) {
		var dark = pref === 'dark' || (pref === 'system' && mq.matches);
		root.dataset.theme = pref;
		root.classList.toggle('dark', dark);
		root.style.colorScheme = dark ? 'dark' : 'light';
		buttons.forEach(function (b) {
			b.setAttribute('aria-checked', String(b.dataset.value === pref));
		});
	}

	buttons.forEach(function (b) {
		b.addEventListener('click', function () {
			var pref = b.dataset.value;
			try { localStorage.setItem('theme', pref); } catch (e) {}
			apply(pref);
		});
	});

	mq.addEventListener('change', function () {
		if (root.dataset.theme === 'system') apply('system');
	});

	addEventListener('storage', function (e) {
		if (e.key === 'theme') apply(e.newValue || 'system');
	});

	apply(root.dataset.theme || 'system');
	requestAnimationFrame(function () {
		requestAnimationFrame(function () { root.classList.add('ready'); });
	});
})();