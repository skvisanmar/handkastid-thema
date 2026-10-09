// Valmynd í síma
document.querySelectorAll('.menu-btn').forEach((btn) => {
	btn.addEventListener('click', () => {
		const open = document.body.classList.toggle('nav-open');
		btn.setAttribute('aria-expanded', String(open));
	});
});

// Síuhnappar í „Nýjustu fréttir“
document.querySelectorAll('.filters').forEach((group) => {
	const feed = group.parentElement.querySelector('.feed');
	group.querySelectorAll('button').forEach((b) => {
		b.addEventListener('click', () => {
			group.querySelectorAll('button').forEach((x) => x.setAttribute('aria-pressed', String(x === b)));
			const f = b.dataset.f;
			feed.querySelectorAll('.item').forEach((it) => {
				it.hidden = f !== 'all' && !(it.dataset.c || '').split(' ').includes(f);
			});
			// Auglýsingar í straumnum eru faldar á meðan síað er svo þær standi ekki tvær saman
			feed.querySelectorAll('.ad-wrap').forEach((ad) => { ad.hidden = f !== 'all'; });
		});
	});
});

// Flipar á stöðutöflum
document.querySelectorAll('.tabs').forEach((tabs) => {
	tabs.querySelectorAll('button').forEach((b) => {
		b.addEventListener('click', () => {
			tabs.querySelectorAll('button').forEach((x) => {
				const on = x === b;
				x.setAttribute('aria-pressed', String(on));
				const panel = document.getElementById(x.dataset.tab);
				if (panel) panel.hidden = !on;
			});
		});
	});
});

// Afrita tengil á frétt
document.querySelectorAll('.copy-link').forEach((b) => {
	b.addEventListener('click', async () => {
		try {
			await navigator.clipboard.writeText(b.dataset.url);
			b.textContent = 'Tengill afritaður';
		} catch {
			window.prompt('Afritaðu tengilinn:', b.dataset.url);
		}
	});
});

// Deildahnappar: skipta um töflu (Stöðutöflur) eða sía leiki (úrslit, næstu leikir)
document.querySelectorAll('.league-tabs').forEach((tabs) => {
	const buttons = tabs.querySelectorAll('button');
	const panels = document.querySelectorAll('.league-panel');
	const select = (key, push) => {
		buttons.forEach((b) => b.setAttribute('aria-pressed', String(b.dataset.league === key)));
		if (panels.length) {
			panels.forEach((p) => { p.hidden = p.dataset.panel !== key; });
		} else {
			document.querySelectorAll('.match').forEach((m) => { m.hidden = key !== 'all' && m.dataset.league !== key; });
			document.querySelectorAll('.day').forEach((d) => { d.hidden = !d.querySelector('.match:not([hidden])'); });
		}
		if (push) history.replaceState(null, '', key === 'all' ? location.pathname : '#' + key);
	};
	buttons.forEach((b) => b.addEventListener('click', () => select(b.dataset.league, true)));
	const hash = location.hash.slice(1);
	if (hash && tabs.querySelector('[data-league="' + hash + '"]')) select(hash, false);
});
