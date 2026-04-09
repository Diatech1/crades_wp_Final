(function () {
	'use strict';

	var config = window.cradesPdfReader || {};
	var documentCache = {};
	var state = {
		activeUrl: '',
		activeTitle: '',
		activeDownload: '',
		pdfDocument: null,
		pageNumber: 1,
		totalPages: 0,
		renderTask: null,
		resizeTimer: null,
		lastTrigger: null,
		elements: {}
	};

	function getMessage(key, fallback) {
		return config.messages && config.messages[key] ? config.messages[key] : fallback;
	}

	function setWorkerSource() {
		if (window.pdfjsLib && window.pdfjsLib.GlobalWorkerOptions) {
			window.pdfjsLib.GlobalWorkerOptions.workerSrc = config.workerSrc || (window.CRADES_PDF && window.CRADES_PDF.workerSrc) || '';
		}
	}

	function getDocumentPromise(url) {
		if (!documentCache[url]) {
			documentCache[url] = window.pdfjsLib.getDocument(url).promise;
		}

		return documentCache[url];
	}

	function setFilterState(button) {
		var group = button.closest('[data-pdf-filters]');

		if (!group) {
			return;
		}

		group.querySelectorAll('[data-pdf-filter]').forEach(function (item) {
			item.classList.remove('border-brand-blue', 'bg-brand-blue', 'text-white');
			item.classList.add('border-gray-200', 'bg-white', 'text-gray-500');
			item.setAttribute('aria-pressed', 'false');
		});

		button.classList.add('border-brand-blue', 'bg-brand-blue', 'text-white');
		button.classList.remove('border-gray-200', 'bg-white', 'text-gray-500');
		button.setAttribute('aria-pressed', 'true');
	}

	function updateVisibleCount() {
		if (!state.elements.count) {
			return;
		}

		var visibleCount = 0;

		state.elements.cards.forEach(function (card) {
			if (!card.classList.contains('hidden')) {
				visibleCount += 1;
			}
		});

		state.elements.count.textContent = String(visibleCount);

		if (state.elements.empty) {
			state.elements.empty.classList.toggle('hidden', visibleCount !== 0);
		}
	}

	function applyFilter(term) {
		state.elements.cards.forEach(function (card) {
			var cardTerms = card.getAttribute('data-pdf-terms') || '';
			var isVisible = term === 'all' || cardTerms.split(/\s+/).indexOf(term) !== -1;

			card.classList.toggle('hidden', !isVisible);
		});

		updateVisibleCount();
	}

	function activateFilters() {
		state.elements.filters.forEach(function (button) {
			button.addEventListener('click', function () {
				var term = button.getAttribute('data-pdf-filter') || 'all';
				setFilterState(button);
				applyFilter(term);
			});
		});
	}

	function showThumbnailError(card) {
		var loading = card.querySelector('[data-pdf-thumb-loading]');

		if (loading) {
			loading.innerHTML = '<i class="fa-regular fa-file-pdf text-4xl" aria-hidden="true"></i><p class="text-xs font-semibold uppercase tracking-[0.22em]">' + getMessage('thumbnailError', 'Aperçu indisponible') + '</p>';
		}
	}

	function renderThumbnail(card) {
		var canvas = card.querySelector('[data-pdf-thumb-canvas]');
		var loading = card.querySelector('[data-pdf-thumb-loading]');
		var pdfUrl = card.getAttribute('data-pdf-url');

		if (!canvas || !pdfUrl || !window.pdfjsLib) {
			showThumbnailError(card);
			return;
		}

		getDocumentPromise(pdfUrl)
			.then(function (pdfDocument) {
				return pdfDocument.getPage(1);
			})
			.then(function (page) {
				var containerWidth = canvas.parentElement ? canvas.parentElement.clientWidth : 320;
				var baseViewport = page.getViewport({ scale: 1 });
				var scale = containerWidth / baseViewport.width;
				var viewport = page.getViewport({ scale: scale });
				var context = canvas.getContext('2d');

				canvas.width = viewport.width;
				canvas.height = viewport.height;
				canvas.style.width = viewport.width + 'px';
				canvas.style.height = viewport.height + 'px';

				return page.render({
					canvasContext: context,
					viewport: viewport
				}).promise;
			})
			.then(function () {
				if (loading) {
					loading.classList.add('hidden');
				}
			})
			.catch(function () {
				showThumbnailError(card);
			});
	}

	function observeThumbnails() {
		if (!state.elements.cards.length) {
			return;
		}

		if (!window.IntersectionObserver) {
			state.elements.cards.forEach(renderThumbnail);
			return;
		}

		var observer = new window.IntersectionObserver(function (entries, instance) {
			entries.forEach(function (entry) {
				if (!entry.isIntersecting) {
					return;
				}

				renderThumbnail(entry.target);
				instance.unobserve(entry.target);
			});
		}, {
			rootMargin: '180px 0px'
		});

		state.elements.cards.forEach(function (card) {
			observer.observe(card);
		});
	}

	function setReaderState(mode, message) {
		var loading = state.elements.loading;
		var error = state.elements.error;
		var errorMessage = state.elements.errorMessage;
		var canvas = state.elements.canvas;

		if (loading) {
			loading.classList.add('hidden');
		}

		if (error) {
			error.classList.add('hidden');
			error.classList.remove('flex');
		}

		if (canvas) {
			canvas.classList.remove('hidden');
		}

		if (mode === 'loading' && loading) {
			loading.classList.remove('hidden');
		}

		if (mode === 'error' && error) {
			error.classList.remove('hidden');
			error.classList.add('flex');
			if (errorMessage) {
				errorMessage.textContent = message || getMessage('readerError', 'Impossible de charger ce PDF.');
			}
			if (canvas) {
				canvas.classList.add('hidden');
			}
		}

		if (state.elements.stage) {
			state.elements.stage.setAttribute('aria-busy', mode === 'loading' ? 'true' : 'false');
		}
	}

	function updateReaderMeta() {
		if (state.elements.title) {
			state.elements.title.textContent = state.activeTitle || 'Document';
		}

		if (state.elements.download) {
			state.elements.download.href = state.activeDownload || '#';
		}

		if (state.elements.pageIndicator) {
			state.elements.pageIndicator.textContent = getMessage('pageLabel', 'Page') + ' ' + state.pageNumber + ' ' + getMessage('ofLabel', 'sur') + ' ' + state.totalPages;
		}

		if (state.elements.prev) {
			state.elements.prev.disabled = state.pageNumber <= 1;
		}

		if (state.elements.next) {
			state.elements.next.disabled = state.pageNumber >= state.totalPages;
		}
	}

	function renderCurrentPage() {
		if (!state.pdfDocument || !state.elements.canvas) {
			return;
		}

		setReaderState('loading');
		updateReaderMeta();

		if (state.renderTask && typeof state.renderTask.cancel === 'function') {
			state.renderTask.cancel();
		}

		state.pdfDocument.getPage(state.pageNumber)
			.then(function (page) {
				var stage = state.elements.stage;
				var canvas = state.elements.canvas;
				var availableWidth = stage ? Math.max(stage.clientWidth - 48, 280) : 900;
				var availableHeight = stage ? Math.max(stage.clientHeight - 48, 400) : 1000;
				var baseViewport = page.getViewport({ scale: 1 });
				var widthScale = availableWidth / baseViewport.width;
				var heightScale = availableHeight / baseViewport.height;
				var scale = Math.min(widthScale, heightScale);
				var viewport = page.getViewport({ scale: scale });

				canvas.width = viewport.width;
				canvas.height = viewport.height;
				canvas.style.width = viewport.width + 'px';
				canvas.style.height = viewport.height + 'px';

				state.renderTask = page.render({
					canvasContext: canvas.getContext('2d'),
					viewport: viewport
				});

				return state.renderTask.promise;
			})
			.then(function () {
				setReaderState('ready');
				updateReaderMeta();
			})
			.catch(function (error) {
				if (error && error.name === 'RenderingCancelledException') {
					return;
				}

				setReaderState('error', getMessage('readerError', 'Impossible de charger ce PDF.'));
			});
	}

	function openReaderFromCard(card) {
		var pdfUrl = card.getAttribute('data-pdf-url');
		var title = card.getAttribute('data-pdf-title') || 'Document';
		var downloadUrl = card.getAttribute('data-pdf-download') || pdfUrl;

		state.lastTrigger = card.querySelector('[data-pdf-open]');
		state.elements.modal.classList.remove('hidden');
		state.elements.modal.classList.add('flex');
		state.elements.modal.setAttribute('aria-hidden', 'false');
		document.body.classList.add('crades-pdf-modal-open');

		if (!pdfUrl) {
			setReaderState('error', getMessage('noDocument', 'Aucun document PDF n est attaché à cette fiche.'));
			return;
		}

		state.activeUrl = pdfUrl;
		state.activeTitle = title;
		state.activeDownload = downloadUrl;
		state.pageNumber = 1;

		setReaderState('loading', getMessage('loading', 'Chargement du document...'));
		updateReaderMeta();

		if (!window.pdfjsLib) {
			setReaderState('error', getMessage('readerError', 'Impossible de charger ce PDF.'));
			return;
		}

		getDocumentPromise(pdfUrl)
			.then(function (pdfDocument) {
				state.pdfDocument = pdfDocument;
				state.totalPages = pdfDocument.numPages || 1;
				renderCurrentPage();
				if (state.elements.close) {
					state.elements.close.focus({ preventScroll: true });
				}
			})
			.catch(function () {
				setReaderState('error', getMessage('readerError', 'Impossible de charger ce PDF.'));
			});
	}

	function closeReader() {
		if (!state.elements.modal) {
			return;
		}

		state.elements.modal.classList.add('hidden');
		state.elements.modal.classList.remove('flex');
		state.elements.modal.setAttribute('aria-hidden', 'true');
		document.body.classList.remove('crades-pdf-modal-open');

		if (state.lastTrigger && typeof state.lastTrigger.focus === 'function') {
			state.lastTrigger.focus({ preventScroll: true });
		}
	}

	function bindReaderControls() {
		if (state.elements.close) {
			state.elements.close.addEventListener('click', closeReader);
		}

		if (state.elements.modal) {
			state.elements.modal.addEventListener('click', function (event) {
				if (event.target === state.elements.modal) {
					closeReader();
				}
			});
		}

		if (state.elements.prev) {
			state.elements.prev.addEventListener('click', function () {
				if (state.pageNumber <= 1) {
					return;
				}

				state.pageNumber -= 1;
				renderCurrentPage();
			});
		}

		if (state.elements.next) {
			state.elements.next.addEventListener('click', function () {
				if (state.pageNumber >= state.totalPages) {
					return;
				}

				state.pageNumber += 1;
				renderCurrentPage();
			});
		}

		document.addEventListener('keydown', function (event) {
			if (!state.elements.modal || state.elements.modal.classList.contains('hidden')) {
				return;
			}

			if (event.key === 'Escape') {
				closeReader();
			} else if (event.key === 'ArrowLeft') {
				if (state.pageNumber > 1) {
					state.pageNumber -= 1;
					renderCurrentPage();
				}
			} else if (event.key === 'ArrowRight') {
				if (state.pageNumber < state.totalPages) {
					state.pageNumber += 1;
					renderCurrentPage();
				}
			}
		});

		window.addEventListener('resize', function () {
			if (!state.elements.modal || state.elements.modal.classList.contains('hidden') || !state.pdfDocument) {
				return;
			}

			window.clearTimeout(state.resizeTimer);
			state.resizeTimer = window.setTimeout(renderCurrentPage, 160);
		});
	}

	function bindOpenButtons() {
		state.elements.cards.forEach(function (card) {
			var trigger = card.querySelector('[data-pdf-open]');

			if (!trigger) {
				return;
			}

			trigger.addEventListener('click', function () {
				openReaderFromCard(card);
			});
		});
	}

	function collectElements() {
		state.elements.cards = Array.prototype.slice.call(document.querySelectorAll('[data-pdf-item]'));
		state.elements.filters = Array.prototype.slice.call(document.querySelectorAll('[data-pdf-filter]'));
		state.elements.count = document.querySelector('[data-pdf-count]');
		state.elements.modal = document.querySelector('[data-pdf-modal]');
		state.elements.title = document.querySelector('[data-pdf-reader-title]');
		state.elements.download = document.querySelector('[data-pdf-reader-download]');
		state.elements.close = document.querySelector('[data-pdf-close]');
		state.elements.prev = document.querySelector('[data-pdf-prev]');
		state.elements.next = document.querySelector('[data-pdf-next]');
		state.elements.pageIndicator = document.querySelector('[data-pdf-page-indicator]');
		state.elements.canvas = document.querySelector('[data-pdf-reader-canvas]');
		state.elements.stage = document.querySelector('[data-pdf-reader-stage]');
		state.elements.loading = document.querySelector('[data-pdf-reader-loading]');
		state.elements.error = document.querySelector('[data-pdf-reader-error]');
		state.elements.errorMessage = document.querySelector('[data-pdf-reader-error-message]');
		state.elements.empty = document.querySelector('[data-pdf-empty]');
	}

	function boot() {
		if (!document.querySelector('[data-pdf-library]')) {
			return;
		}

		collectElements();
		setWorkerSource();
		activateFilters();
		bindOpenButtons();
		bindReaderControls();
		observeThumbnails();
		updateVisibleCount();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot, { once: true });
	} else {
		boot();
	}
})();
