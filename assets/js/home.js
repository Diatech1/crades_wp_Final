(function () {
	'use strict';

	var config = window.cradesHome || {};

	function getMessage(key, fallback) {
		return config.messages && config.messages[key] ? config.messages[key] : fallback;
	}

	function setWorkerSource() {
		if (window.pdfjsLib && window.pdfjsLib.GlobalWorkerOptions) {
			window.pdfjsLib.GlobalWorkerOptions.workerSrc = config.workerSrc || (window.CRADES_PDF && window.CRADES_PDF.workerSrc) || '';
		}
	}

	function renderThumb(canvas) {
		var pdfUrl = canvas.getAttribute('data-pdf-url');

		if (!pdfUrl || !window.pdfjsLib) {
			canvas.style.display = 'none';
			return window.Promise.resolve();
		}

		return window.pdfjsLib.getDocument(pdfUrl).promise
			.then(function (pdf) {
				return pdf.getPage(1);
			})
			.then(function (page) {
				var parent = canvas.parentElement;
				var viewport = page.getViewport({ scale: 1 });
				var targetHeight = Math.max(150, parent ? parent.clientHeight : 150);
				var scale = Math.min((targetHeight / viewport.height) * 1.18, 1.35);
				var scaledViewport = page.getViewport({ scale: scale });
				var devicePixelRatio = window.devicePixelRatio || 1;
				var context = canvas.getContext('2d', { alpha: false });

				canvas.width = Math.floor(scaledViewport.width * devicePixelRatio);
				canvas.height = Math.floor(scaledViewport.height * devicePixelRatio);
				canvas.style.width = 'auto';
				canvas.style.height = '100%';
				canvas.style.display = 'block';

				return page.render({
					canvasContext: context,
					viewport: page.getViewport({ scale: scale * devicePixelRatio })
				}).promise;
			});
	}

	function bootHomePdfGallery() {
		var gallery = document.querySelector('[data-home-pdf-gallery]');
		var thumbs = Array.prototype.slice.call(document.querySelectorAll('[data-home-pdf-thumb]'));
		var filters = Array.prototype.slice.call(document.querySelectorAll('[data-taxonomy-filter]'));
		var cards = Array.prototype.slice.call(document.querySelectorAll('[data-publication-card]'));
		var modal = document.querySelector('[data-home-pdf-modal]');
		var viewer = document.querySelector('[data-home-pdf-viewer]');
		var closeButton = document.querySelector('[data-home-pdf-close]');
		var backdrop = document.querySelector('[data-home-pdf-backdrop]');
		var emptyState = document.querySelector('[data-publication-empty]');
		var renderToken = 0;

		if (!gallery || !thumbs.length) {
			return;
		}

		setWorkerSource();

		function closeModal() {
			if (!modal || !viewer) {
				return;
			}

			modal.classList.add('hidden');
			document.body.classList.remove('overflow-hidden');
			viewer.innerHTML = '';
		}

		function updateEmptyState() {
			if (!emptyState) {
				return;
			}

			var visibleCards = cards.filter(function (card) {
				return !card.classList.contains('hidden');
			});

			emptyState.classList.toggle('hidden', visibleCards.length > 0);
		}

		function applyFilter(filter) {
			if (!cards.length) {
				return;
			}

			cards.forEach(function (card) {
				var taxonomy = card.getAttribute('data-taxonomy') || '';
				var matches = filter === 'Toutes' || taxonomy === filter;

				card.classList.toggle('hidden', !matches);
			});

			updateEmptyState();
		}

		function openModal(url) {
			if (!modal || !viewer) {
				return;
			}

			modal.classList.remove('hidden');
			document.body.classList.add('overflow-hidden');
			renderToken += 1;
			var token = renderToken;

			viewer.innerHTML = '<div class="flex h-full items-center justify-center text-sm text-gray-400">' + getMessage('loading', 'Chargement du document...') + '</div>';

			if (!window.pdfjsLib) {
				viewer.innerHTML = '<div class="flex h-full items-center justify-center text-sm text-red-500">' + getMessage('error', 'Impossible de charger le PDF.') + '</div>';
				return;
			}

			window.pdfjsLib.getDocument(url).promise
				.then(function (pdf) {
					if (token !== renderToken) {
						return;
					}

					viewer.innerHTML = '';

					var availableWidth = Math.max(320, (viewer.clientWidth || 1100) - 32);
					var chain = window.Promise.resolve();

					for (var pageNumber = 1; pageNumber <= pdf.numPages; pageNumber += 1) {
						(function (currentPageNumber) {
							chain = chain.then(function () {
								if (token !== renderToken) {
									return null;
								}

								return pdf.getPage(currentPageNumber)
									.then(function (page) {
										var viewport = page.getViewport({ scale: 1 });
										var scale = Math.min((availableWidth / viewport.width), 1.5);
										var scaledViewport = page.getViewport({ scale: scale });
										var devicePixelRatio = window.devicePixelRatio || 1;
										var wrapper = document.createElement('div');
										var canvas = document.createElement('canvas');

										wrapper.className = 'mb-4 flex justify-center';
										canvas.style.width = Math.floor(scaledViewport.width) + 'px';
										canvas.style.height = Math.floor(scaledViewport.height) + 'px';
										canvas.width = Math.floor(scaledViewport.width * devicePixelRatio);
										canvas.height = Math.floor(scaledViewport.height * devicePixelRatio);
										wrapper.appendChild(canvas);
										viewer.appendChild(wrapper);

										return page.render({
											canvasContext: canvas.getContext('2d', { alpha: false }),
											viewport: page.getViewport({ scale: scale * devicePixelRatio })
										}).promise;
									});
							});
						})(pageNumber);
					}

					return chain;
				})
				.catch(function () {
					viewer.innerHTML = '<div class="flex h-full items-center justify-center text-sm text-red-500">' + getMessage('error', 'Impossible de charger le PDF.') + '</div>';
				});
		}

		document.addEventListener('click', function (event) {
			var trigger = event.target.closest('[data-home-pdf-open]');

			if (trigger) {
				event.preventDefault();
				openModal(trigger.getAttribute('data-home-pdf-open') || '');
				return;
			}

			if ((closeButton && closeButton.contains(event.target)) || (backdrop && backdrop.contains(event.target))) {
				event.preventDefault();
				closeModal();
			}
		});

		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
				closeModal();
			}
		});

		filters.forEach(function (button) {
			button.addEventListener('click', function () {
				var filter = button.getAttribute('data-taxonomy-filter') || 'Toutes';

				filters.forEach(function (item) {
					item.classList.remove('bg-brand-blue', 'text-white');
					item.classList.add('bg-white', 'text-gray-500', 'border', 'border-gray-200');
				});

				button.classList.add('bg-brand-blue', 'text-white');
				button.classList.remove('bg-white', 'text-gray-500', 'border', 'border-gray-200');

				applyFilter(filter);
			});
		});

		if ('IntersectionObserver' in window) {
			var observer = new window.IntersectionObserver(function (entries, instance) {
				entries.forEach(function (entry) {
					if (!entry.isIntersecting) {
						return;
					}

					instance.unobserve(entry.target);
					renderThumb(entry.target).catch(function () {
						entry.target.style.display = 'none';
					});
				});
			}, { rootMargin: '200px 0px' });

			thumbs.forEach(function (canvas) {
				observer.observe(canvas);
			});
		} else {
			thumbs.forEach(function (canvas) {
				renderThumb(canvas).catch(function () {
					canvas.style.display = 'none';
				});
			});
		}

		updateEmptyState();
	}

	function bootHomeDashboardCarousel() {
		var carousel = document.querySelector('[data-home-dashboard-carousel]');

		if (!carousel) {
			return;
		}

		var track = carousel.querySelector('[data-dashboard-carousel-track]');
		var slides = Array.prototype.slice.call(carousel.querySelectorAll('[data-dashboard-carousel-slide]'));
		var prevButton = document.querySelector('[data-dashboard-carousel-prev]');
		var nextButton = document.querySelector('[data-dashboard-carousel-next]');
		var originalCount = parseInt(carousel.getAttribute('data-carousel-original-count') || '', 10) || slides.length;
		var cloneCount = parseInt(carousel.getAttribute('data-carousel-clone-count') || '', 10) || 0;
		var index = 0;
		var ticking = false;
		var autoTimer = null;
		var resumeTimer = null;
		var resetTimer = null;
		var isPaused = false;
		var autoDelay = 5000;

		if (!track || slides.length < 2) {
			return;
		}

		function getVisibleCount() {
			return window.innerWidth >= 768 ? 2 : 1;
		}

		function getStepSize() {
			return 1;
		}

		function getMaxIndex() {
			return Math.max(0, originalCount);
		}

		function canAutoPlay() {
			return originalCount > getVisibleCount() && !(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches);
		}

		function getLoopedIndex(step) {
			if (originalCount <= getVisibleCount()) {
				return 0;
			}

			if (step > originalCount) {
				return 0;
			}

			if (step < 0) {
				return Math.max(0, originalCount - getStepSize());
			}

			return step;
		}

		function updateControls() {
			var hasMovement = originalCount > getVisibleCount();

			if (prevButton) {
				prevButton.disabled = !hasMovement;
			}

			if (nextButton) {
				nextButton.disabled = !hasMovement;
			}
		}

		function jumpToIndex(targetIndex) {
			track.style.scrollBehavior = 'auto';
			track.scrollLeft = slides[targetIndex] ? slides[targetIndex].offsetLeft : 0;

			window.requestAnimationFrame(function () {
				track.style.scrollBehavior = '';
			});
		}

		function scheduleLoopReset(targetIndex) {
			window.clearTimeout(resetTimer);
			resetTimer = window.setTimeout(function () {
				index = targetIndex;
				jumpToIndex(targetIndex);
				updateControls();
			}, 450);
		}

		function scrollToIndex(nextIndex) {
			var maxIndex = getMaxIndex();
			index = Math.max(0, Math.min(nextIndex, maxIndex));

			track.scrollTo({
				left: slides[index].offsetLeft,
				behavior: 'smooth'
			});

			if (cloneCount > 0 && index >= originalCount) {
				scheduleLoopReset(0);
			}

			updateControls();
		}

		function stopAutoPlay() {
			if (autoTimer) {
				window.clearInterval(autoTimer);
				autoTimer = null;
			}
		}

		function scheduleResume(delay) {
			window.clearTimeout(resumeTimer);
			resumeTimer = window.setTimeout(function () {
				isPaused = false;
				startAutoPlay();
			}, delay || 0);
		}

		function pauseAutoPlay() {
			isPaused = true;
			window.clearTimeout(resumeTimer);
			stopAutoPlay();
		}

		function startAutoPlay() {
			if (isPaused || autoTimer || !canAutoPlay()) {
				return;
			}

			autoTimer = window.setInterval(function () {
				scrollToIndex(index + getStepSize());
			}, autoDelay);
		}

		function syncIndexFromScroll() {
			var scrollLeft = track.scrollLeft;
			var closestIndex = 0;
			var smallestDelta = Number.POSITIVE_INFINITY;

			slides.forEach(function (slide, slideIndex) {
				var delta = Math.abs(slide.offsetLeft - scrollLeft);

				if (delta < smallestDelta) {
					smallestDelta = delta;
					closestIndex = slideIndex;
				}
			});

			index = Math.max(0, Math.min(closestIndex, getMaxIndex()));

			if (cloneCount > 0 && index >= originalCount) {
				scheduleLoopReset(0);
			}

			updateControls();
		}

		if (prevButton) {
			prevButton.addEventListener('click', function () {
				pauseAutoPlay();
				scrollToIndex(getLoopedIndex(index - getStepSize()));
				scheduleResume(6000);
			});
		}

		if (nextButton) {
			nextButton.addEventListener('click', function () {
				pauseAutoPlay();
				scrollToIndex(index + getStepSize());
				scheduleResume(6000);
			});
		}

		track.addEventListener('scroll', function () {
			if (ticking) {
				return;
			}

			ticking = true;
			window.requestAnimationFrame(function () {
				syncIndexFromScroll();
				ticking = false;
			});
		});

		carousel.addEventListener('mouseenter', pauseAutoPlay);
		carousel.addEventListener('mouseleave', function () {
			scheduleResume(1200);
		});
		carousel.addEventListener('focusin', pauseAutoPlay);
		carousel.addEventListener('focusout', function () {
			scheduleResume(1200);
		});
		carousel.addEventListener('touchstart', pauseAutoPlay, { passive: true });
		carousel.addEventListener('touchend', function () {
			scheduleResume(2500);
		}, { passive: true });

		document.addEventListener('visibilitychange', function () {
			if (document.hidden) {
				pauseAutoPlay();
				return;
			}

			scheduleResume(800);
		});

		window.addEventListener('resize', function () {
			window.clearTimeout(bootHomeDashboardCarousel.resizeTimer);
			bootHomeDashboardCarousel.resizeTimer = window.setTimeout(function () {
				var normalizedIndex = index >= originalCount ? 0 : Math.min(index, originalCount - getVisibleCount());
				index = Math.max(0, normalizedIndex);
				jumpToIndex(index);
				updateControls();
			}, 120);
		});

		jumpToIndex(0);
		updateControls();
		startAutoPlay();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function () {
			bootHomePdfGallery();
			bootHomeDashboardCarousel();
		}, { once: true });
	} else {
		bootHomePdfGallery();
		bootHomeDashboardCarousel();
	}
})();
