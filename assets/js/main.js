(function () {
	'use strict';

	var navToggles = Array.prototype.slice.call(document.querySelectorAll('[data-nav-toggle]'));
	var mobilePanel = document.querySelector('[data-mobile-panel]');
	var navIcon = document.querySelector('[data-nav-icon]');
	var navLabel = document.querySelector('[data-nav-label]');
	var mobileLinks = Array.prototype.slice.call(document.querySelectorAll('[data-mobile-nav-link]'));
	var closeTimer = null;

	function clearCloseTimer() {
		if (closeTimer) {
			window.clearTimeout(closeTimer);
			closeTimer = null;
		}
	}

	function setNavigationState(isExpanded) {
		if (!navToggles.length || !mobilePanel) {
			return;
		}

		clearCloseTimer();

		if (isExpanded) {
			mobilePanel.classList.remove('hidden');
			window.requestAnimationFrame(function () {
				mobilePanel.classList.add('is-open');
			});
		} else {
			mobilePanel.classList.remove('is-open');
			closeTimer = window.setTimeout(function () {
				mobilePanel.classList.add('hidden');
				closeTimer = null;
			}, 220);
		}

		document.body.classList.toggle('overflow-hidden', isExpanded);
		mobilePanel.setAttribute('aria-hidden', String(!isExpanded));

		navToggles.forEach(function (toggle) {
			toggle.setAttribute('aria-expanded', String(isExpanded));
		});

		if (navIcon) {
			navIcon.classList.toggle('fa-bars', !isExpanded);
			navIcon.classList.toggle('fa-xmark', isExpanded);
		}

		if (navLabel) {
			navLabel.textContent = isExpanded ? 'Fermer la navigation' : 'Ouvrir la navigation';
		}
	}

	if (navToggles.length && mobilePanel) {
		navToggles.forEach(function (toggle) {
			toggle.addEventListener('click', function () {
				var isExpanded = navToggles[0].getAttribute('aria-expanded') === 'true';
				setNavigationState(!isExpanded);
			});
		});
	}

	if (mobileLinks.length) {
		mobileLinks.forEach(function (link) {
			link.addEventListener('click', function () {
				setNavigationState(false);
			});
		});
	}

	document.addEventListener('click', function (event) {
		if (!navToggles.length || !mobilePanel) {
			return;
		}

		if (mobilePanel.classList.contains('hidden')) {
			return;
		}

		var clickedToggle = navToggles.some(function (toggle) {
			return toggle.contains(event.target);
		});

		if (clickedToggle || mobilePanel.contains(event.target)) {
			return;
		}

		setNavigationState(false);
	});

	document.addEventListener('keydown', function (event) {
		if (event.key !== 'Escape' || !mobilePanel || mobilePanel.classList.contains('hidden')) {
			return;
		}

		setNavigationState(false);
	});

	window.addEventListener('resize', function () {
		if (window.innerWidth >= 1024) {
			setNavigationState(false);
		}
	});

	window.CRADES_THEME = window.CRADES_THEME || {};
	window.CRADES_THEME.enqueueChartJs = function () {
		return !!window.Chart;
	};
	window.CRADES_THEME.enqueuePdfJs = function () {
		return !!window.pdfjsLib;
	};
})();
