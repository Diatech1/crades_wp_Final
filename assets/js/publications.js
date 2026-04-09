(function () {
	'use strict';

	var config = window.cradesPublications || {};

	function getMessage(key, fallback) {
		return config.messages && config.messages[key] ? config.messages[key] : fallback;
	}

	function ensurePdfJs() {
		if (window.pdfjsLib) {
			return window.Promise.resolve(window.pdfjsLib);
		}

		if (!config.pdfJsSrc) {
			return window.Promise.reject(new Error('PDF.js source is missing.'));
		}

		return new window.Promise(function (resolve, reject) {
			var existing = document.querySelector('script[data-crades-publications-pdfjs]');

			if (existing) {
				existing.addEventListener('load', function () {
					resolve(window.pdfjsLib);
				}, { once: true });
				existing.addEventListener('error', reject, { once: true });
				return;
			}

			var script = document.createElement('script');
			script.src = config.pdfJsSrc;
			script.async = true;
			script.dataset.cradesPublicationsPdfjs = 'true';
			script.onload = function () {
				resolve(window.pdfjsLib);
			};
			script.onerror = reject;
			document.head.appendChild(script);
		});
	}

	function setWorkerSource(pdfjsLib) {
		if (pdfjsLib && pdfjsLib.GlobalWorkerOptions) {
			pdfjsLib.GlobalWorkerOptions.workerSrc = config.workerSrc || (window.CRADES_PDF && window.CRADES_PDF.workerSrc) || '';
		}
	}

	function renderThumb(canvas) {
		var pdfUrl = canvas.getAttribute('data-pdf-url');

		if (!pdfUrl) {
			return window.Promise.resolve();
		}

		return ensurePdfJs()
			.then(function (pdfjsLib) {
				setWorkerSource(pdfjsLib);

				return pdfjsLib.getDocument(pdfUrl).promise;
			})
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

	function boot() {
		var page = document.querySelector('[data-publications-page]');
		var chips = Array.prototype.slice.call(document.querySelectorAll('[data-taxonomy-filter]'));
		var cards = Array.prototype.slice.call(document.querySelectorAll('[data-publication-card]'));
		var thumbs = Array.prototype.slice.call(document.querySelectorAll('[data-pdf-thumb]'));
		var modal = document.getElementById('pdfModal');
		var modalViewer = document.getElementById('pdfModalViewer');
		var renderToken = 0;

		if (!page) {
			return;
		}

		function closeModal() {
			if (!modal) {
				return;
			}

			modal.classList.add('hidden');
			document.body.classList.remove('overflow-hidden');

			if (modalViewer) {
				modalViewer.innerHTML = '';
			}
		}

		function openModal(url) {
			if (!modal || !modalViewer) {
				return;
			}

			modal.classList.remove('hidden');
			document.body.classList.add('overflow-hidden');
			renderToken += 1;

			var token = renderToken;

			modalViewer.innerHTML = '<div class="flex h-full items-center justify-center text-sm text-gray-400">' + getMessage('loading', 'Chargement du document...') + '</div>';

			ensurePdfJs()
				.then(function (pdfjsLib) {
					setWorkerSource(pdfjsLib);

					return pdfjsLib.getDocument(url).promise;
				})
				.then(function (pdf) {
					if (token !== renderToken) {
						return;
					}

					modalViewer.innerHTML = '';
					var availableWidth = Math.max(320, (modalViewer.clientWidth || 1100) - 32);
					var chain = window.Promise.resolve();

					for (var pageNumber = 1; pageNumber <= pdf.numPages; pageNumber += 1) {
						(function (currentPageNumber) {
							chain = chain.then(function () {
								if (token !== renderToken) {
									return null;
								}

								return pdf.getPage(currentPageNumber).then(function (pageDocument) {
									var viewport = pageDocument.getViewport({ scale: 1 });
									var scale = Math.min(availableWidth / viewport.width, 1.5);
									var scaledViewport = pageDocument.getViewport({ scale: scale });
									var devicePixelRatio = window.devicePixelRatio || 1;
									var wrapper = document.createElement('div');
									var canvas = document.createElement('canvas');

									wrapper.className = 'mb-4 flex justify-center';
									canvas.style.width = Math.floor(scaledViewport.width) + 'px';
									canvas.style.height = Math.floor(scaledViewport.height) + 'px';
									canvas.width = Math.floor(scaledViewport.width * devicePixelRatio);
									canvas.height = Math.floor(scaledViewport.height * devicePixelRatio);
									wrapper.appendChild(canvas);
									modalViewer.appendChild(wrapper);

									return pageDocument.render({
										canvasContext: canvas.getContext('2d', { alpha: false }),
										viewport: pageDocument.getViewport({ scale: scale * devicePixelRatio })
									}).promise;
								});
							});
						})(pageNumber);
					}

					return chain;
				})
				.catch(function () {
					modalViewer.innerHTML = '<div class="flex h-full items-center justify-center text-sm text-red-500">' + getMessage('error', 'Impossible de charger le PDF.') + '</div>';
				});
		}

		function setActiveChip(label) {
			chips.forEach(function (chip) {
				var active = chip.getAttribute('data-taxonomy-filter') === label;

				chip.className = active
					? 'text-xs font-medium bg-brand-blue text-white px-3 py-1.5 rounded-full cursor-pointer transition-colors'
					: 'text-xs font-medium bg-white text-gray-500 border border-gray-200 hover:border-brand-blue hover:text-brand-blue px-3 py-1.5 rounded-full cursor-pointer transition-colors';
			});
		}

		function applyFilter(label) {
			cards.forEach(function (card) {
				var taxonomy = card.getAttribute('data-taxonomy') || '';
				var visible = label === 'Toutes' || taxonomy === label;

				card.classList.toggle('hidden', !visible);
			});

			setActiveChip(label);
		}

		document.addEventListener('click', function (event) {
			var trigger = event.target.closest('[data-pdf-open]');

			if (trigger) {
				event.preventDefault();
				openModal(trigger.getAttribute('data-pdf-open') || '');
				return;
			}

			var closer = event.target.closest('[data-pdf-modal-close]');

			if (closer) {
				event.preventDefault();
				closeModal();
			}
		});

		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
				closeModal();
			}
		});

		chips.forEach(function (chip) {
			chip.addEventListener('click', function () {
				applyFilter(chip.getAttribute('data-taxonomy-filter') || 'Toutes');
			});
		});

		if (thumbs.length) {
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
		}

		applyFilter('Toutes');
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot, { once: true });
	} else {
		boot();
	}
})();
