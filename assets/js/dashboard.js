(function () {
	'use strict';

	var dashboardState = window.cradesDashboard || {};
	var chartInstances = {};
	var chartPalette = [
		'#044bad',
		'#3a7fd4',
		'#b8943e',
		'#032d6b',
		'#0f766e',
		'#dc2626',
		'#7c3aed',
		'#ea580c'
	];

	function isFiniteNumber(value) {
		return typeof value === 'number' && window.isFinite(value);
	}

	function sanitizeText(value) {
		if (value === null || typeof value === 'undefined') {
			return '';
		}

		return String(value).trim();
	}

	function slugify(value) {
		return sanitizeText(value)
			.toLowerCase()
			.replace(/[^a-z0-9]+/g, '-')
			.replace(/^-+|-+$/g, '');
	}

	function normalizeAccentKey(value) {
		var text = sanitizeText(value).toLowerCase();

		if (typeof text.normalize === 'function') {
			text = text.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
		}

		return text.replace(/\s+/g, ' ').trim();
	}

	function coerceNumber(value) {
		if (isFiniteNumber(value)) {
			return value;
		}

		if (typeof value === 'string') {
			var normalized = value
				.replace(/\s/g, '')
				.replace(/,/g, '.')
				.replace(/[^0-9.\-]/g, '');

			if (!normalized) {
				return null;
			}

			var parsed = window.parseFloat(normalized);

			return window.isFinite(parsed) ? parsed : null;
		}

		return null;
	}

	function formatNumber(value) {
		if (!isFiniteNumber(value)) {
			return sanitizeText(value);
		}

		var absolute = Math.abs(value);
		var digits = 0;

		if (absolute !== 0 && absolute < 10) {
			digits = 2;
		} else if (absolute < 100) {
			digits = 1;
		}

		return value.toLocaleString('fr-FR', {
			maximumFractionDigits: digits,
			minimumFractionDigits: 0
		});
	}

	function looksTemporal(value) {
		var text = sanitizeText(value);

		if (!text) {
			return false;
		}

		return (
			/^\d{4}$/.test(text) ||
			/^\d{4}-\d{2}$/.test(text) ||
			/^\d{4}-\d{2}-\d{2}/.test(text) ||
			/^\d{4}\/\d{2}$/.test(text)
		);
	}

	function getSourceMap(payload) {
		var map = {};
		var sources = payload && payload.source && Array.isArray(payload.source.tables) ? payload.source.tables : [];

		sources.forEach(function (source) {
			if (source && source.key) {
				map[source.key] = source;
			}
		});

		return map;
	}

	function getTableEntries(payload) {
		if (!payload || !payload.data || !payload.data.tables) {
			return [];
		}

		return Object.keys(payload.data.tables).map(function (key) {
			return {
				key: key,
				table: payload.data.tables[key]
			};
		});
	}

	function findEntryByKey(payload, key) {
		return getTableEntries(payload).find(function (entry) {
			return entry.key === key;
		}) || null;
	}

	function getNumericColumns(table) {
		if (!table || !Array.isArray(table.columns)) {
			return [];
		}

		return table.columns.filter(function (column) {
			if (!column || !column.key || column.key === '_row') {
				return false;
			}

			if (column.type === 'number') {
				return true;
			}

			return Array.isArray(table.rows) && table.rows.some(function (row) {
				return coerceNumber(row[column.key]) !== null;
			});
		});
	}

	function getRenderableColumns(table) {
		if (!table || !Array.isArray(table.columns)) {
			return [];
		}

		return table.columns.filter(function (column) {
			return column && column.key && column.key !== '_row';
		});
	}

	function getLabelColumn(table, numericColumns) {
		var numericKeys = numericColumns.map(function (column) {
			return column.key;
		});

		if (!table || !Array.isArray(table.columns)) {
			return null;
		}

		for (var index = 0; index < table.columns.length; index += 1) {
			var column = table.columns[index];

			if (!column || column.key === '_row') {
				continue;
			}

			if (numericKeys.indexOf(column.key) !== -1) {
				continue;
			}

			return column;
		}

		return null;
	}

	function getBestSummaryRow(table, numericColumns) {
		if (!table || !Array.isArray(table.rows) || !table.rows.length) {
			return null;
		}

		var bestRow = table.rows[0];
		var bestScore = -1;

		table.rows.forEach(function (row) {
			var score = 0;

			numericColumns.forEach(function (column) {
				if (coerceNumber(row[column.key]) !== null) {
					score += 1;
				}
			});

			if (score > bestScore) {
				bestScore = score;
				bestRow = row;
			}
		});

		return bestRow;
	}

	function orderColumnsForMetrics(numericColumns) {
		return numericColumns.slice().sort(function (left, right) {
			var leftYear = /^\d{4}$/.test(sanitizeText(left.label));
			var rightYear = /^\d{4}$/.test(sanitizeText(right.label));

			if (leftYear && rightYear) {
				return window.parseInt(right.label, 10) - window.parseInt(left.label, 10);
			}

			if (leftYear) {
				return -1;
			}

			if (rightYear) {
				return 1;
			}

			return sanitizeText(left.label).localeCompare(sanitizeText(right.label));
		});
	}

	function createKpiLabel(baseLabel, columnLabel, totalColumns) {
		var cleanBase = sanitizeText(baseLabel);
		var cleanColumn = sanitizeText(columnLabel);

		if (!cleanBase && !cleanColumn) {
			return '';
		}

		if (!cleanBase) {
			return cleanColumn;
		}

		if (!cleanColumn || totalColumns <= 1 || slugify(cleanBase) === slugify(cleanColumn)) {
			return cleanBase;
		}

		return cleanBase + ' - ' + cleanColumn;
	}

	function buildKpiCandidatesForEntry(entry, payload) {
		var sourceMap = getSourceMap(payload);
		var sourceMeta = sourceMap[entry.key] || {};
		var table = entry.table;
		var numericColumns = orderColumnsForMetrics(getNumericColumns(table));
		var labelColumn = getLabelColumn(table, numericColumns);
		var summaryRow = getBestSummaryRow(table, numericColumns);
		var candidates = [];
		var rowLabel = labelColumn && summaryRow ? sanitizeText(summaryRow[labelColumn.key]) : '';
		var sourceLabel = sanitizeText(sourceMeta.label) || entry.key;

		if (!summaryRow || !numericColumns.length) {
			return candidates;
		}

		numericColumns.forEach(function (column) {
			var value = coerceNumber(summaryRow[column.key]);

			if (value === null) {
				return;
			}

			candidates.push({
				sourceKey: entry.key,
				columnKey: column.key,
				columnLabel: sanitizeText(column.label),
				label: createKpiLabel(rowLabel || sourceLabel, column.label, numericColumns.length),
				value: value,
				note: sourceLabel,
				badge: sanitizeText(column.label) || sourceLabel
			});
		});

		return candidates;
	}

	function buildKpiCandidates(payload) {
		var candidates = [];
		var seen = {};

		getTableEntries(payload).forEach(function (entry) {
			buildKpiCandidatesForEntry(entry, payload).forEach(function (candidate) {
				var identity = slugify(candidate.sourceKey + '-' + candidate.columnKey + '-' + candidate.label);

				if (!seen[identity]) {
					candidates.push(candidate);
					seen[identity] = true;
				}
			});
		});

		return candidates;
	}

	function resolveKpiCandidate(card, payload, defaultCandidates, index) {
		var sourceKey = sanitizeText(card.getAttribute('data-kpi-source'));
		var columnHint = sanitizeText(card.getAttribute('data-kpi-column'));

		if (!sourceKey) {
			return defaultCandidates[index] || null;
		}

		var entry = findEntryByKey(payload, sourceKey);

		if (!entry) {
			return null;
		}

		var candidates = buildKpiCandidatesForEntry(entry, payload);

		if (columnHint) {
			var hintedCandidate = candidates.find(function (candidate) {
				return candidate.columnKey === columnHint || slugify(candidate.columnLabel) === slugify(columnHint);
			});

			if (hintedCandidate) {
				return hintedCandidate;
			}
		}

		return candidates[0] || null;
	}

	function getKpiElements(card) {
		var paragraphs = card.querySelectorAll('p');

		return {
			badge: card.querySelector('[data-kpi-badge]') || card.querySelector('span'),
			label: card.querySelector('[data-kpi-label]') || paragraphs[0] || null,
			value: card.querySelector('[data-dashboard-kpi-value]'),
			note: card.querySelector('[data-kpi-note]') || paragraphs[paragraphs.length - 1] || null
		};
	}

	function renderKpis(payload) {
		var cards = document.querySelectorAll('[data-dashboard-kpi]');
		var candidates = buildKpiCandidates(payload);
		var sourceCounters = {};

		cards.forEach(function (card, index) {
			var elements = getKpiElements(card);
			var sourceKey = sanitizeText(card.getAttribute('data-kpi-source'));
			var candidate = null;

			if (!elements.value) {
				return;
			}

			if (sourceKey && !sanitizeText(card.getAttribute('data-kpi-column'))) {
				var entry = findEntryByKey(payload, sourceKey);
				var scopedCandidates = entry ? buildKpiCandidatesForEntry(entry, payload) : [];
				var scopedIndex = sourceCounters[sourceKey] || 0;

				candidate = scopedCandidates[scopedIndex] || scopedCandidates[0] || null;
				sourceCounters[sourceKey] = scopedIndex + 1;
			} else {
				candidate = resolveKpiCandidate(card, payload, candidates, index);
			}

			if (!candidate) {
				if (elements.badge) {
					elements.badge.textContent = 'En attente';
				}

				elements.value.textContent = '--';

				if (elements.note) {
					elements.note.textContent = 'Aucune mesure disponible pour le moment.';
				}

				card.classList.add('is-ready');
				return;
			}

			if (elements.badge) {
				elements.badge.textContent = candidate.badge;
			}

			if (elements.label) {
				elements.label.textContent = candidate.label;
			}

			elements.value.textContent = formatNumber(candidate.value);

			if (elements.note) {
				elements.note.textContent = candidate.note || 'Source Google Sheets';
			}

			window.setTimeout(function () {
				card.classList.add('is-ready');
			}, 60 * index);
		});
	}

	function buildSeriesChartCandidate(entry, payload) {
		var sourceMap = getSourceMap(payload);
		var sourceMeta = sourceMap[entry.key] || {};
		var table = entry.table;
		var numericColumns = getNumericColumns(table);
		var labelColumn = getLabelColumn(table, numericColumns);

		if (!table || !Array.isArray(table.rows) || table.rows.length < 2 || !numericColumns.length) {
			return null;
		}

		var chartColumns = orderColumnsForMetrics(numericColumns).slice(0, 4).reverse();
		var validRows = table.rows.filter(function (row) {
			return chartColumns.some(function (column) {
				return coerceNumber(row[column.key]) !== null;
			});
		});

		if (validRows.length < 2) {
			return null;
		}

		var labels = validRows.map(function (row, index) {
			var labelValue = labelColumn ? sanitizeText(row[labelColumn.key]) : '';

			if (!labelValue) {
				labelValue = 'Ligne ' + (index + 1);
			}

			return labelValue;
		});

		var isTemporal = labels.some(looksTemporal);
		var datasets = chartColumns.map(function (column, index) {
			var color = chartPalette[index % chartPalette.length];

			return {
				label: sanitizeText(column.label) || ('Serie ' + (index + 1)),
				data: validRows.map(function (row) {
					var numericValue = coerceNumber(row[column.key]);

					return numericValue === null ? 0 : numericValue;
				}),
				borderColor: color,
				backgroundColor: color + '24',
				tension: isTemporal ? 0.35 : 0.16,
				fill: isTemporal && index === 0,
				borderWidth: 2,
				pointRadius: isTemporal ? 2 : 0,
				pointHoverRadius: 4,
				borderRadius: 8,
				maxBarThickness: 40
			};
		});

		return {
			id: entry.key + '-series',
			sourceKey: entry.key,
			kind: 'series',
			title: sanitizeText(sourceMeta.label) || entry.key,
			type: isTemporal ? 'line' : 'bar',
			labels: labels,
			datasets: datasets,
			score: (labels.length * 3) + (datasets.length * 5)
		};
	}

	function buildDistributionChartCandidate(entry, payload) {
		var sourceMap = getSourceMap(payload);
		var sourceMeta = sourceMap[entry.key] || {};
		var table = entry.table;
		var numericColumns = orderColumnsForMetrics(getNumericColumns(table));
		var labelColumn = getLabelColumn(table, numericColumns);
		var summaryRow = getBestSummaryRow(table, numericColumns);
		var labels = [];
		var values = [];

		if (!summaryRow || numericColumns.length < 2) {
			return null;
		}

		numericColumns.slice(0, 6).forEach(function (column) {
			var numericValue = coerceNumber(summaryRow[column.key]);

			if (numericValue === null) {
				return;
			}

			labels.push(sanitizeText(column.label) || column.key);
			values.push(numericValue);
		});

		if (values.length < 2) {
			return null;
		}

		return {
			id: entry.key + '-distribution',
			sourceKey: entry.key,
			kind: 'distribution',
			title: sanitizeText(sourceMeta.label) || entry.key,
			type: values.length <= 5 ? 'doughnut' : 'bar',
			labels: labels,
			summaryLabel: labelColumn ? sanitizeText(summaryRow[labelColumn.key]) : '',
			datasets: [
				{
					label: sanitizeText(sourceMeta.label) || entry.key,
					data: values,
					borderColor: chartPalette.slice(0, values.length),
					backgroundColor: chartPalette.slice(0, values.length).map(function (color) {
						return color + 'cc';
					}),
					borderWidth: 0,
					maxBarThickness: 40
				}
			],
			score: 20 + values.length
		};
	}

	function buildChartCandidatesForEntry(entry, payload) {
		var candidates = [];
		var seriesCandidate = buildSeriesChartCandidate(entry, payload);
		var distributionCandidate = buildDistributionChartCandidate(entry, payload);

		if (seriesCandidate) {
			candidates.push(seriesCandidate);
		}

		if (distributionCandidate) {
			candidates.push(distributionCandidate);
		}

		return candidates;
	}

	function buildChartCandidates(payload) {
		var candidates = [];

		getTableEntries(payload).forEach(function (entry) {
			candidates = candidates.concat(buildChartCandidatesForEntry(entry, payload));
		});

		candidates.sort(function (left, right) {
			return right.score - left.score;
		});

		return candidates;
	}

	function resolveChartCandidate(card, payload, defaultCandidates, index) {
		var sourceKey = sanitizeText(card.getAttribute('data-chart-source'));
		var kindHint = sanitizeText(card.getAttribute('data-chart-kind'));

		if (!sourceKey) {
			return defaultCandidates[index] || null;
		}

		var entry = findEntryByKey(payload, sourceKey);

		if (!entry) {
			return null;
		}

		var candidates = buildChartCandidatesForEntry(entry, payload);

		if (kindHint) {
			var kindCandidate = candidates.find(function (candidate) {
				return candidate.kind === kindHint;
			});

			if (kindCandidate) {
				return kindCandidate;
			}
		}

		return candidates[0] || null;
	}

	function getChartTitleElement(card) {
		return card.querySelector('[data-chart-title]') || card.querySelector('h2, h3');
	}

	function getChartDescriptionElement(card) {
		return card.querySelector('[data-chart-description]');
	}

	function updateChartCopy(card, state, message) {
		var selector = state === 'error' ? '[data-chart-error-copy]' : '[data-chart-empty-copy]';
		var target = card.querySelector(selector) || (state === 'error' ? card.querySelector('[data-chart-error] p:last-child') : card.querySelector('[data-chart-empty] p:last-child'));

		if (target && message) {
			target.textContent = message;
		}
	}

	function showChartState(card, state, message) {
		var loading = card.querySelector('[data-chart-loading]');
		var empty = card.querySelector('[data-chart-empty]');
		var error = card.querySelector('[data-chart-error]');
		var canvas = card.querySelector('[data-chart-canvas]');

		if (loading) {
			loading.classList.add('hidden');
		}

		if (empty) {
			empty.classList.add('hidden');
		}

		if (error) {
			error.classList.add('hidden');
		}

		if (canvas) {
			canvas.classList.add('hidden');
		}

		if (state === 'empty' && empty) {
			updateChartCopy(card, 'empty', message);
			empty.classList.remove('hidden');
		}

		if (state === 'error' && error) {
			updateChartCopy(card, 'error', message);
			error.classList.remove('hidden');
		}

		if (state === 'ready' && canvas) {
			canvas.classList.remove('hidden');
		}
	}

	function destroyChart(cardId) {
		if (chartInstances[cardId]) {
			chartInstances[cardId].destroy();
			delete chartInstances[cardId];
		}
	}

	function getChartMode(card, candidate) {
		return sanitizeText(card.getAttribute('data-chart-mode')) || candidate.type || 'bar';
	}

	function buildChartConfig(candidate, card) {
		var mode = getChartMode(card, candidate);
		var labels = candidate.labels.slice();
		var datasets = candidate.datasets.map(function (dataset, index) {
			var baseDataset = {
				label: dataset.label,
				data: dataset.data.slice(),
				borderColor: dataset.borderColor || chartPalette[index % chartPalette.length],
				backgroundColor: dataset.backgroundColor || (chartPalette[index % chartPalette.length] + '24'),
				borderWidth: 2,
				tension: dataset.tension || 0.35,
				fill: dataset.fill || false,
				pointRadius: dataset.pointRadius || 0,
				pointHoverRadius: dataset.pointHoverRadius || 4,
				borderRadius: dataset.borderRadius || 8,
				maxBarThickness: dataset.maxBarThickness || 40
			};

			if ('bar' === mode || 'horizontal-bar' === mode) {
				baseDataset.fill = false;
				baseDataset.pointRadius = 0;
				baseDataset.tension = 0;
			}

			if ('doughnut' === mode) {
				return {
					label: dataset.label,
					data: dataset.data.slice(),
					backgroundColor: chartPalette.slice(0, dataset.data.length).map(function (color) {
						return color + 'cc';
					}),
					borderWidth: 0
				};
			}

			return baseDataset;
		});

		return {
			type: mode === 'horizontal-bar' ? 'bar' : mode,
			data: {
				labels: labels,
				datasets: datasets
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				indexAxis: mode === 'horizontal-bar' ? 'y' : 'x',
				interaction: {
					mode: 'index',
					intersect: false
				},
				plugins: {
					legend: {
						display: mode !== 'doughnut' ? datasets.length > 1 : true,
						position: mode === 'doughnut' ? 'bottom' : 'top',
						align: 'start',
						labels: {
							boxWidth: 12,
							boxHeight: 12,
							color: '#334155',
							font: {
								family: 'Montserrat',
								size: 11,
								weight: '600'
							}
						}
					},
					tooltip: {
						backgroundColor: '#032d6b',
						padding: 12,
						titleFont: {
							family: 'Montserrat',
							size: 13,
							weight: '700'
						},
						bodyFont: {
							family: 'Montserrat',
							size: 12
						},
						callbacks: {
							label: function (context) {
								return context.dataset.label + ': ' + formatNumber(context.parsed.y || context.parsed.x || context.parsed);
							}
						}
					}
				},
				scales: mode === 'doughnut' ? {} : {
					x: {
						grid: {
							display: false
						},
						ticks: {
							color: '#64748b',
							font: {
								family: 'Montserrat',
								size: 11
							}
						}
					},
					y: {
						beginAtZero: mode !== 'line',
						grid: {
							color: 'rgba(148, 163, 184, 0.16)'
						},
						ticks: {
							color: '#64748b',
							font: {
								family: 'Montserrat',
								size: 11
							},
							callback: function (value) {
								return formatNumber(value);
							}
						}
					}
				}
			}
		};
	}

	function renderCharts(payload) {
		var cards = document.querySelectorAll('[data-dashboard-chart]');
		var defaultCandidates = buildChartCandidates(payload);

		cards.forEach(function (card, index) {
			var candidate = resolveChartCandidate(card, payload, defaultCandidates, index);
			var cardId = card.getAttribute('data-chart-id') || ('chart-' + index);
			var canvas = card.querySelector('[data-chart-canvas]');
			var title = getChartTitleElement(card);
			var description = getChartDescriptionElement(card);

			destroyChart(cardId);

			if (!window.Chart) {
				showChartState(card, 'error', dashboardState.messages && dashboardState.messages.error ? dashboardState.messages.error : 'Chart.js est indisponible.');
				return;
			}

			if (!candidate || !canvas) {
				showChartState(card, 'empty', dashboardState.messages && dashboardState.messages.empty ? dashboardState.messages.empty : 'Aucune table exploitable n a ete retournee.');
				return;
			}

			if (title && card.hasAttribute('data-chart-autotitle')) {
				title.textContent = candidate.title;
			}

			if (description && card.hasAttribute('data-chart-autodescription')) {
				description.textContent = 'Visualisation generee depuis les tables normalisees renvoyees par l API WordPress.';
			}

			canvas.style.minHeight = card.getAttribute('data-chart-height') || '280px';
			showChartState(card, 'ready');

			chartInstances[cardId] = new window.Chart(
				canvas.getContext('2d'),
				buildChartConfig(candidate, card)
			);
		});
	}

	function buildTableCandidateFromEntry(entry, payload) {
		var sourceMap = getSourceMap(payload);
		var sourceMeta = sourceMap[entry.key] || {};
		var table = entry.table;
		var renderableColumns = getRenderableColumns(table);

		if (!table || !Array.isArray(table.rows) || !table.rows.length || !renderableColumns.length) {
			return null;
		}

		return {
			sourceKey: entry.key,
			title: sanitizeText(sourceMeta.label) || entry.key,
			columns: renderableColumns,
			rows: table.rows,
			rowCount: table.row_count || table.rows.length
		};
	}

	function buildTableCandidates(payload) {
		var candidates = [];

		getTableEntries(payload).forEach(function (entry) {
			var candidate = buildTableCandidateFromEntry(entry, payload);

			if (candidate) {
				candidates.push(candidate);
			}
		});

		return candidates.sort(function (left, right) {
			return right.rowCount - left.rowCount;
		});
	}

	function resolveTableCandidate(card, payload, defaultCandidates, index) {
		var sourceKey = sanitizeText(card.getAttribute('data-table-source'));

		if (!sourceKey) {
			return defaultCandidates[index] || null;
		}

		var entry = findEntryByKey(payload, sourceKey);

		return entry ? buildTableCandidateFromEntry(entry, payload) : null;
	}

	function showTableState(card, state, message) {
		var loading = card.querySelector('[data-table-loading]');
		var empty = card.querySelector('[data-table-empty]');
		var error = card.querySelector('[data-table-error]');
		var wrap = card.querySelector('[data-table-wrap]');

		if (loading) {
			loading.classList.add('hidden');
		}

		if (empty) {
			empty.classList.add('hidden');
		}

		if (error) {
			error.classList.add('hidden');
		}

		if (wrap) {
			wrap.classList.add('hidden');
		}

		if (state === 'empty' && empty) {
			var emptyCopy = empty.querySelector('[data-table-empty-copy]') || empty;
			emptyCopy.textContent = message || 'Aucune ligne disponible.';
			empty.classList.remove('hidden');
		}

		if (state === 'error' && error) {
			var errorCopy = error.querySelector('[data-table-error-copy]') || error;
			errorCopy.textContent = message || 'Impossible de charger le tableau.';
			error.classList.remove('hidden');
		}

		if (state === 'ready' && wrap) {
			wrap.classList.remove('hidden');
		}
	}

	function renderTables(payload) {
		var cards = document.querySelectorAll('[data-dashboard-table]');
		var defaultCandidates = buildTableCandidates(payload);

		cards.forEach(function (card, index) {
			var candidate = resolveTableCandidate(card, payload, defaultCandidates, index);
			var title = card.querySelector('[data-dashboard-table-title]');
			var head = card.querySelector('[data-dashboard-table-head]');
			var body = card.querySelector('[data-dashboard-table-body]');
			var limitRows = window.parseInt(card.getAttribute('data-table-rows') || '8', 10);
			var limitColumns = window.parseInt(card.getAttribute('data-table-columns') || '5', 10);

			if (!candidate || !head || !body) {
				showTableState(card, 'empty', 'Aucune table exploitable n a ete retournee.');
				return;
			}

			if (title && card.hasAttribute('data-table-autotitle')) {
				title.textContent = candidate.title;
			}

			var columns = candidate.columns.slice(0, limitColumns);
			var rows = candidate.rows.slice(0, limitRows);

			head.innerHTML = '<tr class="bg-gray-50 text-gray-500 text-left">' + columns.map(function (column) {
				return '<th class="px-4 py-2 font-medium whitespace-nowrap">' + sanitizeText(column.label || column.key) + '</th>';
			}).join('') + '</tr>';

			body.innerHTML = rows.map(function (row) {
				return '<tr class="border-t border-gray-50 hover:bg-gray-50/50">' + columns.map(function (column) {
					var rawValue = row[column.key];
					var numericValue = coerceNumber(rawValue);
					var displayValue = numericValue !== null ? formatNumber(numericValue) : sanitizeText(rawValue);

					return '<td class="px-4 py-2 text-gray-600 whitespace-nowrap">' + displayValue + '</td>';
				}).join('') + '</tr>';
			}).join('');

			showTableState(card, 'ready');
		});
	}

	function getViewModel(payload) {
		if (!payload || !payload.data || !payload.data.view_model) {
			return null;
		}

		return payload.data.view_model;
	}

	function escapeHtml(value) {
		return sanitizeText(value)
			.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;')
			.replace(/'/g, '&#39;');
	}

	function formatCompactBillions(value) {
		var numeric = coerceNumber(value);

		if (numeric === null) {
			return sanitizeText(value);
		}

		if (Math.abs(numeric) >= 1000) {
			return (numeric / 1000).toFixed(1) + ' Mds';
		}

		return numeric.toFixed(1);
	}

	function formatOneDecimal(value, suffix) {
		var numeric = coerceNumber(value);

		if (numeric === null) {
			return sanitizeText(value);
		}

		return numeric.toFixed(1) + (suffix || '');
	}

	function stripUnitLabel(label) {
		return sanitizeText(label).replace(/\s*\([^)]*\)\s*/g, ' ').replace(/\s+/g, ' ').trim();
	}

	function splitHeaderLabel(label) {
		var words = stripUnitLabel(label).split(/\s+/).filter(Boolean);
		var pivot = Math.ceil(words.length / 2);

		if (words.length <= 2) {
			return [words.join(' '), ''];
		}

		return [
			words.slice(0, pivot).join(' '),
			words.slice(pivot).join(' ')
		];
	}

	function formatMonthPeriodLabel(value) {
		var text = sanitizeText(value);
		var match = text.match(/^(\d{4})-(\d{2})/);
		var months = ['Jan', 'F\u00E9v', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Ao\u00FB', 'Sep', 'Oct', 'Nov', 'D\u00E9c'];

		if (!match) {
			return text;
		}

		return (months[window.parseInt(match[2], 10) - 1] || match[2]) + ' ' + match[1];
	}

	function getDenreesDisplayMeta(table) {
		var series = Array.isArray(table && table.series) ? table.series : [];
		var products = Array.isArray(table && table.products) ? table.products : [];
		var labels = series.map(function (item) {
			return sanitizeText(item && item.date);
		}).filter(Boolean);
		var isMonthlySeries = labels.length >= 10 && labels.every(function (label) {
			return /^\d{4}-\d{2}/.test(label);
		});
		var title = isMonthlySeries ? 'Prix des denr\u00E9es de base (FCFA/kg)' : 'Prix des denr\u00E9es s\u00E9lectionn\u00E9es (FCFA/kg)';
		var period = isMonthlySeries ? '12 derni\u00E8res p\u00E9riodes' : 'S\u00E9rie disponible';

		if (!isMonthlySeries && labels.length > 1) {
			period = labels[0] + ' - ' + labels[labels.length - 1];
		} else if (!isMonthlySeries && labels.length === 1) {
			period = labels[0];
		}

		return {
			title: title,
			period: period,
			source: isMonthlySeries ? 'Source: DPEE - Prix int\u00E9rieurs des denr\u00E9es de base' : 'Source: ANSD - Annexe A.7 Prix des denr\u00E9es s\u00E9lectionn\u00E9es'
		};
	}

	function cloneChartConfig(chart) {
		var nextChart = Object.assign({}, chart || {});
		var nextData = Object.assign({}, nextChart.data || {});

		nextData.datasets = Array.isArray(nextData.datasets) ? nextData.datasets.map(function (dataset) {
			return Object.assign({}, dataset || {});
		}) : [];

		nextChart.data = nextData;

		return nextChart;
	}

	function normalizeCommerceExterieurViewModel(viewModel) {
		if (!viewModel || dashboardState.pageKey !== 'commerce-exterieur') {
			return viewModel;
		}

		if (Array.isArray(viewModel.kpis)) {
			viewModel.kpis = viewModel.kpis.map(function (item, index) {
				var next = Object.assign({}, item || {});
				var numericValue = coerceNumber(next.display);

				if (index === 0) {
					next.label = 'Exportations (FCFA)';
					next.display = formatCompactBillions(numericValue);
					next.note = '+6.9% vs 2024';
					next.badge = '';
				}

				if (index === 1) {
					next.label = 'Importations (FCFA)';
					next.display = formatCompactBillions(numericValue);
					next.note = '+2.2% vs 2024';
					next.badge = '';
				}

				if (index === 2) {
					next.display = formatCompactBillions(numericValue);
					next.note = numericValue !== null && numericValue >= 0 ? '\u2191 Exc\u00e9dentaire' : '\u2193 D\u00e9ficit r\u00e9duit';
					next.badge = '';
				}

				if (index === 3) {
					next.display = formatCompactBillions(numericValue);
					next.note = 'Export + Import';
					next.badge = '';
				}

				if (index === 4 || index === 5) {
					next.display = formatOneDecimal(numericValue);
					next.badge = '';
				}

				if (index === 6) {
					next.label = "Termes de l'\u00e9change";
					next.display = formatOneDecimal(numericValue);
					next.note = numericValue !== null && numericValue >= 100 ? '\u2191 Favorable' : '\u2193 D\u00e9favorable';
					next.badge = '';
				}

				if (index === 7) {
					next.display = formatOneDecimal(numericValue, '%');
					next.note = 'Export / Import';
					next.badge = '';
				}

				return next;
			});
		}

		if (Array.isArray(viewModel.charts)) {
			viewModel.charts = viewModel.charts.map(function (chart) {
				var nextChart = cloneChartConfig(chart);

				if (nextChart.id === 'trade-evolution') {
					nextChart.title = 'Evolution du commerce (Mds FCFA)';
					nextChart.description = 'Exportations et importations 2016-2025.';
				}

				if (nextChart.id === 'trade-balance') {
					nextChart.description = 'Solde annuel des echanges.';
				}

				if (nextChart.id === 'import-partners' && nextChart.data.datasets[0]) {
					nextChart.data.datasets[0].backgroundColor = '#dc2626cc';
					nextChart.data.datasets[0].borderColor = '#dc2626';
				}

				if (nextChart.id === 'export-partners' && nextChart.data.datasets[0]) {
					nextChart.data.datasets[0].backgroundColor = '#059669cc';
					nextChart.data.datasets[0].borderColor = '#059669';
				}

				return nextChart;
			});
		}

		return viewModel;
	}

	function normalizeCommerceInterieurViewModel(viewModel) {
		if (!viewModel || dashboardState.pageKey !== 'commerce-interieur') {
			return viewModel;
		}

		if (Array.isArray(viewModel.charts)) {
			viewModel.charts = viewModel.charts.map(function (chart) {
				var nextChart = cloneChartConfig(chart);

				if (nextChart.id === 'ihpc-desagrege') {
					nextChart.options = mergeObjects(nextChart.options || {}, {
						interaction: {
							mode: 'nearest',
							intersect: true
						},
						plugins: {
							legend: {
								display: false
							}
						},
						scales: {
							y: {
								title: {
									display: true,
									text: 'Var. mensuelle (%)',
									color: '#94a3b8',
									font: {
										family: 'Montserrat',
										size: 9
									}
								},
								ticks: {
									callback: function (value) {
										var numeric = coerceNumber(value);
										if (numeric === null) {
											return sanitizeText(value);
										}

										return (numeric > 0 ? '+' : '') + numeric.toFixed(1) + '%';
									}
								}
							},
							x: {
								ticks: {
									maxRotation: 45,
									minRotation: 45
								}
							}
						}
					});
				}

				if (nextChart.id === 'inflation-threshold' && nextChart.data.datasets[0]) {
					var barDataset = nextChart.data.datasets.find(function (dataset) {
						return dataset && dataset.type === 'bar';
					});

					if (barDataset) {
						barDataset.backgroundColor = barDataset.data.map(function (value) {
							return coerceNumber(value) > 3 ? '#dc2626b3' : '#10b981b3';
						});
						barDataset.borderColor = barDataset.data.map(function (value) {
							return coerceNumber(value) > 3 ? '#dc2626' : '#10b981';
						});
					}

					nextChart.options = mergeObjects(nextChart.options || {}, {
						plugins: {
							title: {
								display: true,
								text: "Taux d'inflation",
								align: 'start',
								font: {
									size: 14,
									weight: '600'
								},
								color: '#0f172a',
								padding: {
									bottom: 10
								}
							},
							legend: {
								position: 'top',
								labels: {
									font: {
										size: 10
									},
									padding: 10,
									usePointStyle: true,
									generateLabels: function (chartInstance) {
										var original = window.Chart.defaults.plugins.legend.labels.generateLabels(chartInstance);

										return original.map(function (legendItem) {
											var dataset = chartInstance.data.datasets[legendItem.datasetIndex];

											if (dataset && dataset.type === 'line') {
												legendItem.strokeStyle = Array.isArray(dataset.borderColor) ? dataset.borderColor[0] : dataset.borderColor;
												legendItem.lineWidth = dataset.borderWidth || 2;
												legendItem.lineDash = dataset.borderDash || [5, 5];
												legendItem.fillStyle = 'rgba(0,0,0,0)';
												legendItem.pointStyle = 'line';
											}

											if (dataset && dataset.type === 'bar') {
												legendItem.fillStyle = Array.isArray(dataset.backgroundColor) ? dataset.backgroundColor[0] : dataset.backgroundColor;
												legendItem.strokeStyle = Array.isArray(dataset.borderColor) ? dataset.borderColor[0] : dataset.borderColor;
												legendItem.lineDash = [];
											}

											return legendItem;
										});
									}
								}
							},
							tooltip: {
								callbacks: {
									label: function (context) {
										return context.dataset.label + ': ' + Number(context.parsed.y).toFixed(2) + '%';
									}
								}
							}
						},
						scales: {
							y: {
								suggestedMax: 12,
								ticks: {
									callback: function (value) {
										return formatOneDecimal(value, '%');
									}
								}
							},
							x: {
								ticks: {
									font: {
										size: 11
									}
								}
							}
						}
					});
				}

				if (nextChart.id === 'icai-series') {
					nextChart.options = mergeObjects(nextChart.options || {}, {
						interaction: {
							mode: 'index',
							intersect: false
						},
						plugins: {
							legend: {
								position: 'bottom',
								labels: {
									font: {
										size: 9
									},
									boxWidth: 10,
									padding: 10
								}
							}
						},
						scales: {
							y: {
								min: 100,
								ticks: {
									font: {
										size: 10
									}
								}
							},
							x: {
								ticks: {
									font: {
										size: 9
									},
									maxRotation: 45,
									minRotation: 45
								}
							}
						}
					});
				}

				if (nextChart.id === 'icai-breakdown') {
					nextChart.options = mergeObjects(nextChart.options || {}, {
						plugins: {
							legend: {
								position: 'bottom',
								labels: {
									font: {
										size: 9
									},
									padding: 6,
									boxWidth: 10
								}
							},
							tooltip: {
								callbacks: {
									label: function (context) {
										return context.label + ': ' + Number(context.parsed).toFixed(1) + '%';
									}
								}
							}
						}
					});
				}

				return nextChart;
			});
		}

		return viewModel;
	}

	function normalizeIndustryViewModel(viewModel) {
		if (!viewModel || dashboardState.pageKey !== 'industrie') {
			return viewModel;
		}

		if (Array.isArray(viewModel.charts)) {
			viewModel.charts = viewModel.charts.map(function (chart) {
				var nextChart = cloneChartConfig(chart);

				if (nextChart.id === 'industry-ihpi' || nextChart.id === 'industry-icai') {
					var industryTopTickStep = nextChart.id === 'industry-ihpi' ? 50 : 100;
					var industryTopMax = nextChart.id === 'industry-ihpi' ? 450 : 700;

					nextChart.options = mergeObjects(nextChart.options || {}, {
						interaction: {
							mode: 'nearest',
							intersect: true
						},
						plugins: {
							legend: {
								display: false
							}
						},
						scales: {
							y: {
								beginAtZero: true,
								min: 0,
								suggestedMax: industryTopMax,
								grid: {
									color: '#f1f5f9'
								},
								ticks: {
									stepSize: industryTopTickStep,
									font: {
										size: 10
									}
								}
							},
							x: {
								ticks: {
									font: {
										size: 9
									},
									maxRotation: 45,
									minRotation: 45,
									autoSkip: true,
									maxTicksLimit: 12
								}
							}
						}
					});
				}

				if (nextChart.id === 'industry-ippi') {
					nextChart.options = mergeObjects(nextChart.options || {}, {
						interaction: {
							mode: 'index',
							intersect: false
						},
						plugins: {
							legend: {
								display: false
							},
							tooltip: {
								callbacks: {
									label: function (context) {
										return 'IPPI: ' + Number(context.parsed.y).toFixed(1);
									}
								}
							}
						},
						scales: {
							y: {
								beginAtZero: false,
								grid: {
									color: '#f1f5f9'
								},
								ticks: {
									font: {
										size: 10
									}
								}
							},
							x: {
								ticks: {
									font: {
										size: 9
									},
									maxRotation: 45,
									minRotation: 45,
									autoSkip: true,
									maxTicksLimit: 12
								}
							}
						}
					});
				}

				if (nextChart.id === 'industry-capacity') {
					nextChart.options = mergeObjects(nextChart.options || {}, {
						interaction: {
							mode: 'index',
							intersect: false
						},
						scales: {
							y: {
								min: 40,
								max: 100,
								grid: {
									color: '#f1f5f9'
								},
								ticks: {
									font: {
										size: 9
									},
									callback: function (value) {
										return sanitizeText(value) + '%';
									}
								}
							},
							x: {
								ticks: {
									font: {
										size: 8
									},
									maxRotation: 45,
									minRotation: 45,
									autoSkip: true,
									maxTicksLimit: 12
								}
							}
						},
						plugins: {
							legend: {
								display: false
							},
							tooltip: {
								callbacks: {
									label: function (context) {
										return Number(context.parsed.y).toFixed(1) + ' %';
									}
								}
							}
						}
					});
				}

				if (nextChart.id === 'industry-pci') {
					nextChart.options = mergeObjects(nextChart.options || {}, {
						plugins: {
							legend: {
								position: 'top',
								labels: {
									font: {
										family: 'Montserrat',
										size: 10,
										weight: '500'
									},
									color: '#475569',
									usePointStyle: true,
									pointStyle: 'circle',
									boxWidth: 8,
									padding: 10,
									generateLabels: function (chart) {
										return chart.data.datasets.map(function (dataset, datasetIndex) {
											var legendColor = dataset.pointBackgroundColor || dataset.borderColor || dataset.backgroundColor || '#334155';

											if (Array.isArray(legendColor)) {
												legendColor = legendColor[0] || '#334155';
											}

											return {
												text: dataset.label,
												fillStyle: legendColor,
												strokeStyle: legendColor,
												lineWidth: 0,
												fontColor: '#475569',
												color: '#475569',
												hidden: !chart.isDatasetVisible(datasetIndex),
												datasetIndex: datasetIndex,
												pointStyle: 'circle'
											};
										});
									}
								}
							}
						},
						scales: {
							r: {
								beginAtZero: true,
								max: 70,
								ticks: {
									stepSize: 20,
									backdropColor: 'transparent',
									color: '#94a3b8',
									font: {
										size: 8
									}
								},
								pointLabels: {
									color: '#64748b',
									font: {
										size: 9,
										weight: '500'
									}
								},
								grid: {
									color: '#e2e8f0'
								},
								angleLines: {
									color: '#e2e8f0'
								}
							}
						}
					});
				}

				if (nextChart.id === 'industry-pib') {
					nextChart.options = mergeObjects(nextChart.options || {}, {
						plugins: {
							legend: {
								position: 'top',
								labels: {
									font: {
										family: 'Montserrat',
										size: 10,
										weight: '600'
									},
									usePointStyle: true,
									boxWidth: 8
								}
							},
							tooltip: {
								callbacks: {
									label: function (context) {
										if (context.dataset && context.dataset.type === 'bar') {
											return ' Score : ' + Number(context.parsed.y).toFixed(3);
										}

										return ' Rang : ' + Number(context.parsed.y) + 'e';
									}
								}
							}
						},
						scales: {
							y: {
								position: 'left',
								title: {
									display: true,
									text: 'Score',
									font: {
										size: 10
									}
								},
								ticks: {
									font: {
										size: 9
									}
								}
							},
							y1: {
								position: 'right',
								reverse: true,
								grid: {
									display: false
								},
								title: {
									display: true,
									text: 'Rang',
									font: {
										size: 10
									}
								},
								ticks: {
									font: {
										size: 9
									}
								}
							},
							x: {
								grid: {
									display: false
								},
								ticks: {
									font: {
										size: 9
									}
								}
							}
						}
					});
				}

				return nextChart;
			});
		}

		return viewModel;
	}

	function normalizePmeViewModel(viewModel) {
		if (!viewModel || dashboardState.pageKey !== 'pme-pmi') {
			return viewModel;
		}

		if (Array.isArray(viewModel.charts)) {
			viewModel.charts = viewModel.charts.map(function (chart) {
				var nextChart = cloneChartConfig(chart);

				if (nextChart.id === 'pme-immatriculations') {
					nextChart.title = "Immatriculations par secteur d'activit\u00e9";
					nextChart.description = 'Entreprises individuelles \u2014 2019\u20132024';
					nextChart.period = 'Entreprises individuelles \u2014 2019\u20132024';
					nextChart.source = 'Source : ANSD/RNEA \u2014 BANIN 2024';
					nextChart.options = mergeObjects(nextChart.options || {}, {
						plugins: {
							legend: {
								position: 'bottom',
								labels: {
									color: '#6b7280',
									font: {
										family: 'Montserrat',
										size: 9
									},
									boxWidth: 10,
									boxHeight: 10,
									padding: 8
								}
							},
							tooltip: {
								mode: 'nearest',
								intersect: true,
								callbacks: {
									label: function (context) {
										var value = context && context.parsed ? context.parsed.y : 0;
										return ' ' + sanitizeText(context.dataset.label) + ' : ' + Number(value).toLocaleString('fr-FR');
									}
								}
							}
						},
						scales: {
							x: {
								stacked: true,
								grid: {
									display: false
								},
								ticks: {
									color: '#64748b',
									font: {
										family: 'Montserrat',
										size: 11,
										weight: '700'
									}
								}
							},
							y: {
								stacked: true,
								grid: {
									color: '#f1f5f9'
								},
								ticks: {
									color: '#64748b',
									font: {
										family: 'Montserrat',
										size: 9
									},
									callback: function (value) {
										return Math.round(Number(value) / 1000) + 'k';
									}
								}
							}
						}
					});
				}

				if (nextChart.id === 'pme-structure') {
					nextChart.type = 'pie';
					nextChart.title = 'R\u00e9partition par Taille';
					nextChart.description = 'Enqu\u00eate BM 2024';
					nextChart.period = 'Enqu\u00eate BM 2024';
					nextChart.source = 'Source : Banque Mondiale \u2014 Enterprise Surveys 2024';

					if (nextChart.data && Array.isArray(nextChart.data.datasets) && nextChart.data.datasets[0]) {
						nextChart.data.datasets[0].backgroundColor = ['#044badcc', '#059669cc', '#b8943ecc'];
						nextChart.data.datasets[0].borderColor = '#ffffff';
						nextChart.data.datasets[0].borderWidth = 1;
					}

					nextChart.options = mergeObjects(nextChart.options || {}, {
						plugins: {
							legend: {
								position: 'bottom',
								labels: {
									color: '#6b7280',
									font: {
										family: 'Montserrat',
										size: 10
									},
									boxWidth: 12,
									boxHeight: 12,
									padding: 12
								}
							},
							tooltip: {
								callbacks: {
									label: function (context) {
										return ' ' + sanitizeText(context.label) + ' : ' + Number(context.parsed).toFixed(0) + '%';
									}
								}
							}
						}
					});
				}

				if (nextChart.id === 'pme-enquete' && nextChart.data.datasets[0]) {
					nextChart.title = "R\u00e9partition par Tranche d'\u00c2ge";
					nextChart.description = 'Entrepreneurs individuels 2024';
					nextChart.period = 'Entrepreneurs individuels 2024';
					nextChart.source = 'Source : ANSD/RNEA \u2014 BANIN 2024';
					nextChart.data.datasets[0].backgroundColor = ['#b8943ecc', '#dc2626cc', '#059669cc', '#044badcc'];
					nextChart.data.datasets[0].borderColor = ['#b8943e', '#dc2626', '#059669', '#044bad'];
					nextChart.data.datasets[0].borderWidth = 1;
					nextChart.data.datasets[0].borderRadius = 4;
					nextChart.options = mergeObjects(nextChart.options || {}, {
						plugins: {
							legend: {
								display: false
							},
							tooltip: {
								callbacks: {
									label: function (context) {
										return Number(context.parsed.y).toFixed(1) + '%';
									}
								}
							}
						},
						scales: {
							y: {
								beginAtZero: true,
								max: 45,
								grid: {
									color: '#f1f5f9'
								},
								ticks: {
									color: '#64748b',
									font: {
										family: 'Montserrat',
										size: 10
									},
									callback: function (value) {
										return Number(value).toFixed(0) + '%';
									}
								}
							},
							x: {
								grid: {
									display: false
								},
								ticks: {
									color: '#64748b',
									font: {
										family: 'Montserrat',
										size: 10
									}
								}
							}
						}
					});
				}

				if (nextChart.id === 'pme-macro' && nextChart.data.datasets[0]) {
					nextChart.title = 'R\u00e9partition par R\u00e9gime Juridique';
					nextChart.description = '2024';
					nextChart.period = '2024';
					nextChart.source = 'Source : ANSD/RNEA \u2014 BANIN 2024';
					nextChart.options = mergeObjects(nextChart.options || {}, {
						plugins: {
							legend: {
								position: 'bottom',
								labels: {
									color: '#6b7280',
									font: {
										family: 'Montserrat',
										size: 9
									},
									boxWidth: 10,
									boxHeight: 10,
									padding: 8
								}
							},
							tooltip: {
								callbacks: {
									label: function (context) {
										return ' ' + sanitizeText(context.label) + ' : ' + Number(context.parsed).toFixed(1) + '%';
									}
								}
							}
						}
					});
				}

				return nextChart;
			});
		}

		return viewModel;
	}
	function normalizeViewModel(viewModel) {
		if (!viewModel) {
			return viewModel;
		}

		viewModel = normalizeCommerceExterieurViewModel(viewModel);
		viewModel = normalizeCommerceInterieurViewModel(viewModel);
		viewModel = normalizeIndustryViewModel(viewModel);
		viewModel = normalizePmeViewModel(viewModel);

		return viewModel;
	}

	function mergeObjects(base, extra) {
		var output = Array.isArray(base) ? base.slice() : Object.assign({}, base || {});

		if (!extra || typeof extra !== 'object' || Array.isArray(extra)) {
			return output;
		}

		Object.keys(extra).forEach(function (key) {
			var incoming = extra[key];
			var current = output[key];

			if (
				incoming &&
				typeof incoming === 'object' &&
				!Array.isArray(incoming) &&
				current &&
				typeof current === 'object' &&
				!Array.isArray(current)
			) {
				output[key] = mergeObjects(current, incoming);
				return;
			}

			output[key] = incoming;
		});

		return output;
	}

	function buildChartConfigFromViewModel(chart) {
		var baseOptions = {
			responsive: true,
			maintainAspectRatio: false,
			interaction: {
				mode: 'index',
				intersect: false
			},
			plugins: {
				legend: {
					display: true,
					position: 'top',
					labels: {
						color: '#334155',
						boxWidth: 12,
						boxHeight: 12,
						font: {
							family: 'Montserrat',
							size: 12,
							weight: '600'
						}
					}
				},
				tooltip: {
					backgroundColor: '#032d6b',
					padding: 12,
					titleFont: {
						family: 'Montserrat',
						size: 13,
						weight: '700'
					},
					bodyFont: {
						family: 'Montserrat',
						size: 12
					}
				}
			},
			scales: {
				x: {
					grid: {
						display: false
					},
					ticks: {
						color: '#64748b',
						font: {
							family: 'Montserrat',
							size: 11
						}
					}
				},
				y: {
					beginAtZero: true,
					grid: {
						color: 'rgba(148, 163, 184, 0.16)'
					},
					ticks: {
						color: '#64748b',
						font: {
							family: 'Montserrat',
							size: 11
						}
					}
				}
			}
		};

		if (chart.type === 'doughnut' || chart.type === 'pie') {
			delete baseOptions.scales;
		}

		if (chart.type === 'radar') {
			baseOptions.scales = {
				r: {
					beginAtZero: true,
					ticks: {
						color: '#64748b',
						backdropColor: 'transparent',
						font: {
							family: 'Montserrat',
							size: 10
						}
					},
					pointLabels: {
						color: '#64748b',
						font: {
							family: 'Montserrat',
							size: 10
						}
					},
					grid: {
						color: '#e2e8f0'
					}
				}
			};
		}

		return {
			type: chart.type || 'bar',
			data: chart.data || { labels: [], datasets: [] },
			options: mergeObjects(baseOptions, chart.options || {})
		};
	}

	function getPmePieLabelsPlugin() {
		return {
			id: 'cradesPmePieLabels',
			afterDatasetsDraw: function (chart) {
				var datasets = chart && chart.data ? chart.data.datasets : [];
				var meta = chart.getDatasetMeta(0);

				if (!datasets.length || !meta || !meta.data) {
					return;
				}

				var dataset = datasets[0];
				var values = Array.isArray(dataset.data) ? dataset.data : [];
				var ctx = chart.ctx;

				ctx.save();
				ctx.textAlign = 'center';
				ctx.textBaseline = 'middle';
				ctx.fillStyle = '#ffffff';
				ctx.font = '700 16px Montserrat';

				meta.data.forEach(function (element, index) {
					var value = coerceNumber(values[index]);

					if (value === null) {
						return;
					}

					var position = element.tooltipPosition();
					ctx.fillText(Math.round(value) + '%', position.x, position.y);
				});

				ctx.restore();
			}
		};
	}

	function updateElementTone(element, removeClasses, addClasses) {
		if (!element) {
			return;
		}

		(removeClasses || []).forEach(function (className) {
			element.classList.remove(className);
		});

		(addClasses || []).forEach(function (className) {
			element.classList.add(className);
		});
	}

	function applyCommerceExterieurKpiStyles(elements, item, index) {
		if (dashboardState.pageKey !== 'commerce-exterieur') {
			return;
		}

		var valueTones = ['text-brand-blue', 'text-brand-navy', 'text-brand-gold', 'text-emerald-600', 'text-red-600'];
		var noteTones = ['text-gray-400', 'text-gray-500', 'text-emerald-500', 'text-red-400'];
		var noteText = sanitizeText(item && item.note).toLowerCase();
		var numericValue = coerceNumber(item && item.display);

		if (index === 0) {
			updateElementTone(elements.value, valueTones, ['text-brand-blue']);
			updateElementTone(elements.note, noteTones, ['text-emerald-500']);
			return;
		}

		if (index === 1) {
			updateElementTone(elements.value, valueTones, ['text-brand-navy']);
			updateElementTone(elements.note, noteTones, ['text-red-400']);
			return;
		}

		if (index === 2) {
			if (numericValue !== null && numericValue < 0) {
				updateElementTone(elements.value, valueTones, ['text-red-600']);
				updateElementTone(elements.note, noteTones, ['text-red-400']);
				return;
			}

			if (numericValue !== null && numericValue > 0) {
				updateElementTone(elements.value, valueTones, ['text-emerald-600']);
				updateElementTone(elements.note, noteTones, ['text-emerald-500']);
				return;
			}
		}

		if (index === 3) {
			updateElementTone(elements.value, valueTones, ['text-brand-gold']);
			updateElementTone(elements.note, noteTones, ['text-gray-400']);
			return;
		}

		if (index === 4) {
			updateElementTone(elements.value, valueTones, ['text-brand-blue']);
			updateElementTone(elements.note, noteTones, ['text-gray-400']);
			return;
		}

		if (index === 5) {
			updateElementTone(elements.value, valueTones, ['text-brand-navy']);
			updateElementTone(elements.note, noteTones, ['text-gray-400']);
			return;
		}

		if (index === 6) {
			if (noteText.indexOf('defavorable') !== -1) {
				updateElementTone(elements.value, valueTones, ['text-red-600']);
				updateElementTone(elements.note, noteTones, ['text-red-400']);
				return;
			}

			if (noteText.indexOf('favorable') !== -1) {
				updateElementTone(elements.value, valueTones, ['text-emerald-600']);
				updateElementTone(elements.note, noteTones, ['text-emerald-500']);
			}

			return;
		}

		if (index === 7) {
			updateElementTone(elements.value, valueTones, ['text-brand-gold']);
			updateElementTone(elements.note, noteTones, ['text-gray-400']);
		}
	}

	function applyCommerceInterieurKpiStyles(elements, item, index) {
		if (dashboardState.pageKey !== 'commerce-interieur') {
			return;
		}

		var valueTones = ['text-brand-blue', 'text-brand-navy', 'text-brand-gold', 'text-emerald-600', 'text-red-600'];

		if (index === 0) {
			updateElementTone(elements.value, valueTones, ['text-brand-blue']);
		}

		if (index === 1) {
			updateElementTone(elements.value, valueTones, ['text-brand-navy']);
		}

		if (index === 2) {
			updateElementTone(elements.value, valueTones, ['text-emerald-600']);
		}

		if (index === 3) {
			updateElementTone(elements.value, valueTones, ['text-brand-gold']);
		}
	}

	function applyIndustryKpiStyles(elements, item, index) {
		if (dashboardState.pageKey !== 'industrie') {
			return;
		}

		var valueTones = ['text-brand-blue', 'text-brand-navy', 'text-brand-gold', 'text-emerald-600', 'text-red-600'];
		var numericValue = coerceNumber(item && item.display);
		var noteText = sanitizeText(item && item.note);

		if (index === 0 || index === 1 || index === 2) {
			updateElementTone(elements.value, valueTones, ['text-emerald-600']);
		}

		if (index === 3) {
			updateElementTone(elements.value, valueTones, ['text-red-600']);
		}

		if (index === 4) {
			if (numericValue !== null && numericValue >= 80) {
				updateElementTone(elements.value, valueTones, ['text-emerald-600']);
			} else if (numericValue !== null && numericValue >= 60) {
				updateElementTone(elements.value, valueTones, ['text-brand-gold']);
			} else {
				updateElementTone(elements.value, valueTones, ['text-red-600']);
			}
		}

		if (elements.label) {
			var industryLabels = [
				'IHPI \u2013 Production',
				"ICAI \u2013 Chiffre d'Affaires",
				'IPPI \u2013 Prix Production',
				'CIP \u2013 Comp\u00e9titivit\u00e9',
				'TUCP \u2013 Capacit\u00e9s'
			];

			if (industryLabels[index]) {
				elements.label.textContent = industryLabels[index];
			}
		}

		if (elements.note) {
			if (index === 0) {
				elements.note.textContent = noteText || 'D\u00e9c. 2024';
			}

			if ((index === 1 || index === 2) && noteText) {
				elements.note.textContent = noteText.indexOf('Var.') === 0 ? noteText : 'Var. annuelle ' + noteText;
			}

			if (index === 3 && noteText) {
				elements.note.textContent = noteText.replace(' - ', ' \u2013 ');
			}

			if (index === 4 && !noteText) {
				elements.note.textContent = '2025-T3';
			}
		}
	}

	function applyPmeKpiStyles(elements, item, index) {
		if (dashboardState.pageKey !== 'pme-pmi') {
			return;
		}

		var valueTones = ['text-brand-blue', 'text-brand-navy', 'text-brand-gold', 'text-emerald-600', 'text-red-600'];

		if (index === 0 || index === 2) {
			updateElementTone(elements.value, valueTones, ['text-emerald-600']);
		}

		if (index === 1) {
			updateElementTone(elements.value, valueTones, ['text-brand-gold']);
		}

		if (index === 3) {
			updateElementTone(elements.value, valueTones, ['text-red-600']);
		}
	}

	function applyDashboardSpecificKpiStyles(elements, item, index) {
		applyCommerceExterieurKpiStyles(elements, item, index);
		applyCommerceInterieurKpiStyles(elements, item, index);
		applyIndustryKpiStyles(elements, item, index);
		applyPmeKpiStyles(elements, item, index);
	}

	function renderViewModelKpis(viewModel) {
		var cards = document.querySelectorAll('[data-dashboard-kpi]');
		var kpis = viewModel && Array.isArray(viewModel.kpis) ? viewModel.kpis : [];

		cards.forEach(function (card, index) {
			var elements = getKpiElements(card);
			var item = kpis[index];

			if (!elements.value) {
				return;
			}

			if (!item) {
				card.classList.add('is-ready');
				return;
			}

			if (elements.label) {
				elements.label.textContent = sanitizeText(item.label);
			}

			elements.value.textContent = sanitizeText(item.display || item.value || '--');

			if (elements.note) {
				elements.note.textContent = sanitizeText(item.note);
			}

			if (elements.badge) {
				elements.badge.textContent = sanitizeText(item.badge);
			}

			applyDashboardSpecificKpiStyles(elements, item, index);

			window.setTimeout(function () {
				card.classList.add('is-ready');
			}, 60 * index);
		});
	}

	function renderViewModelCharts(viewModel) {
		var cards = document.querySelectorAll('[data-dashboard-chart]');
		var charts = viewModel && Array.isArray(viewModel.charts) ? viewModel.charts : [];

		cards.forEach(function (card, index) {
			var cardId = sanitizeText(card.getAttribute('data-chart-id'));
			var chart = charts.find(function (item) {
				return sanitizeText(item.id) === cardId;
			}) || charts[index] || null;
			var title = card.querySelector('[data-chart-title]');
			var description = card.querySelector('[data-chart-description]');
			var period = card.querySelector('[data-chart-period]');
			var source = card.querySelector('[data-chart-source-copy]');
			var canvas = card.querySelector('[data-chart-canvas]');

			destroyChart(cardId || ('chart-' + index));

			if (!chart || !canvas) {
				showChartState(card, 'empty', 'Aucune configuration de graphique n a ete retournee.');
				return;
			}

			if (!window.Chart) {
				showChartState(card, 'error', dashboardState.messages && dashboardState.messages.error ? dashboardState.messages.error : 'Chart.js est indisponible.');
				return;
			}

			if (title) {
				title.textContent = sanitizeText(chart.title);
			}

			if (description && sanitizeText(chart.description)) {
				description.textContent = sanitizeText(chart.description);
			}

			if (period && sanitizeText(chart.period)) {
				period.textContent = sanitizeText(chart.period);
			}

			if (source && sanitizeText(chart.source)) {
				source.textContent = sanitizeText(chart.source);
			}

			showChartState(card, 'ready');
			var chartConfig = buildChartConfigFromViewModel(chart);

			if (dashboardState.pageKey === 'pme-pmi' && sanitizeText(chart.id) === 'pme-structure') {
				chartConfig.plugins = (chartConfig.plugins || []).concat([getPmePieLabelsPlugin()]);
			}

			var chartInstance = new window.Chart(canvas.getContext('2d'), chartConfig);
			chartInstances[cardId || ('chart-' + index)] = chartInstance;
			renderIndustryChartMeta(card, chart, chartInstance);
		});
	}

	function formatIndustryPeriodRange(labels) {
		var cleanLabels = Array.isArray(labels) ? labels.map(function (label) {
			return sanitizeText(label);
		}).filter(Boolean) : [];

		if (!cleanLabels.length) {
			return '';
		}

		if (cleanLabels.length === 1) {
			return cleanLabels[0];
		}

		return cleanLabels[0] + ' \u2013 ' + cleanLabels[cleanLabels.length - 1];
	}

	function getChartDatasetColor(dataset) {
		if (!dataset) {
			return '#334155';
		}

		if (Array.isArray(dataset.borderColor)) {
			return dataset.borderColor[0] || '#334155';
		}

		if (dataset.borderColor) {
			return dataset.borderColor;
		}

		if (Array.isArray(dataset.backgroundColor)) {
			return dataset.backgroundColor[0] || '#334155';
		}

		return dataset.backgroundColor || '#334155';
	}

	function getLastNumericChartValue(dataset) {
		var values = dataset && Array.isArray(dataset.data) ? dataset.data : [];
		var lastValue = null;

		for (var idx = values.length - 1; idx >= 0; idx -= 1) {
			var numeric = coerceNumber(values[idx]);
			if (numeric !== null) {
				lastValue = numeric;
				break;
			}
		}

		return lastValue;
	}

	function renderIndustryPciDeltaGrid(target, chartInstance) {
		if (!target || !chartInstance) {
			return;
		}

		var labels = chartInstance.data && Array.isArray(chartInstance.data.labels) ? chartInstance.data.labels : [];
		var datasets = chartInstance.data && Array.isArray(chartInstance.data.datasets) ? chartInstance.data.datasets : [];

		if (datasets.length < 2 || !labels.length) {
			target.innerHTML = '';
			return;
		}

		var firstSeries = Array.isArray(datasets[0].data) ? datasets[0].data : [];
		var secondSeries = Array.isArray(datasets[1].data) ? datasets[1].data : [];

		target.innerHTML = labels.map(function (label, index) {
			var from = coerceNumber(firstSeries[index]);
			var to = coerceNumber(secondSeries[index]);
			var delta = from !== null && to !== null ? to - from : null;
			var tone = delta !== null && delta >= 0 ? 'text-emerald-600' : 'text-red-500';
			var display = delta !== null ? (delta >= 0 ? '+' : '') + formatOneDecimal(delta) : '--';

			return '<div class="flex items-center justify-between gap-2 text-[10px]">' +
				'<span class="text-gray-600 truncate pr-2">' + escapeHtml(sanitizeText(label)) + '</span>' +
				'<span class="font-semibold ' + tone + '">' + escapeHtml(display) + '</span>' +
			'</div>';
		}).join('');
	}

	function renderIndustryChartMeta(card, chart, chartInstance) {
		if (dashboardState.pageKey !== 'industrie') {
			return;
		}

		var chartId = sanitizeText(card.getAttribute('data-chart-id'));
		var descriptionTarget = card.querySelector('[data-chart-description]');
		var legendTarget = card.querySelector('[data-industry-chart-legend]');
		var latestTarget = card.querySelector('[data-industry-chart-latest]');
		var baseTarget = card.querySelector('[data-industry-chart-base]');
		var pciGridTarget = card.querySelector('[data-industry-pci-grid]');
		var periodTarget = card.querySelector('[data-chart-period]');
		var datasets = chartInstance.data && Array.isArray(chartInstance.data.datasets) ? chartInstance.data.datasets : [];
		var labels = chartInstance.data && Array.isArray(chartInstance.data.labels) ? chartInstance.data.labels : [];

		if (!chartInstance) {
			return;
		}

		if (chartId === 'industry-ippi') {
			var latestIppiDisplay = '--';

			if (periodTarget) {
				periodTarget.textContent = formatIndustryPeriodRange(labels) || sanitizeText(chart.period);
			}

			if (latestTarget && datasets.length) {
				var latestIppi = getLastNumericChartValue(datasets[0]);
				latestIppiDisplay = latestIppi !== null ? formatOneDecimal(latestIppi) : '--';
				latestTarget.textContent = latestIppiDisplay;
			}

			if (latestTarget && latestTarget.parentNode) {
				latestTarget.parentNode.innerHTML = 'Derni\u00e8re valeur : <strong class="text-rose-600" data-industry-chart-latest>' + escapeHtml(latestIppiDisplay) + '</strong>';
			}

			if (baseTarget) {
				baseTarget.textContent = 'Base 100 = 2015';
			}

			return;
		}

		if (chartId === 'industry-capacity') {
			var latestCapacityDisplay = '--';
			var latestCapacityClass = 'text-red-600';

			if (periodTarget) {
				periodTarget.textContent = formatIndustryPeriodRange(labels) || sanitizeText(chart.period);
			}

			if (latestTarget && datasets.length) {
				var latestCapacity = getLastNumericChartValue(datasets[0]);

				if (latestCapacity !== null && latestCapacity >= 80) {
					latestCapacityClass = 'text-emerald-600';
				} else if (latestCapacity !== null && latestCapacity >= 60) {
					latestCapacityClass = 'text-brand-gold';
				} else {
					latestCapacityClass = 'text-red-600';
				}

				latestCapacityDisplay = latestCapacity !== null ? formatOneDecimal(latestCapacity, ' %') : '--';
				latestTarget.textContent = latestCapacityDisplay;
			}

			if (latestTarget && latestTarget.parentNode) {
				latestTarget.parentNode.innerHTML = 'Derni\u00e8re valeur : <strong class="' + latestCapacityClass + '" data-industry-chart-latest>' + escapeHtml(latestCapacityDisplay) + '</strong>';
			}

			if (baseTarget) {
				baseTarget.textContent = 'Unit\u00e9 : %';
			}

			return;
		}

		if (chartId === 'industry-pci') {
			if (periodTarget && datasets.length > 1) {
				periodTarget.textContent = sanitizeText(datasets[0].label) + ' vs ' + sanitizeText(datasets[1].label);
			}

			renderIndustryPciDeltaGrid(pciGridTarget, chartInstance);

			return;
		}

		if (chartId === 'industry-pib') {
			if (periodTarget) {
				periodTarget.textContent = 'Score & Rang mondial \u2014 UNIDO';
			}

			return;
		}

		if ((chartId !== 'industry-ihpi' && chartId !== 'industry-icai') || !legendTarget) {
			return;
		}
		var legendColumns = chartId === 'industry-ihpi' ? 4 : 3;
		var branchCount = Math.max(datasets.length - 1, 0);
		var legendItems = datasets.map(function (dataset, datasetIndex) {
			return {
				idx: datasetIndex,
				label: sanitizeText(dataset.label),
				color: getChartDatasetColor(dataset)
			};
		});
		var ensembleItems = legendItems.filter(function (item) {
			return item.label === 'ENSEMBLE';
		});
		var otherItems = legendItems.filter(function (item) {
			return item.label !== 'ENSEMBLE';
		}).sort(function (left, right) {
			return left.label.localeCompare(right.label, 'fr');
		});

		if (periodTarget) {
			periodTarget.textContent = labels.length > 1 ? (sanitizeText(labels[0]) + ' \u2013 ' + sanitizeText(labels[labels.length - 1])) : (formatIndustryPeriodRange(labels) || sanitizeText(chart.period));
		}

		if (descriptionTarget) {
			descriptionTarget.textContent = branchCount + ' sous-secteurs \u00b7 cliquer pour isoler une ligne';
		}

		legendTarget.innerHTML = '';
		legendTarget.style.display = 'grid';
		legendTarget.style.gridTemplateColumns = 'repeat(' + legendColumns + ', 1fr)';
		legendTarget.style.gap = '4px 10px';
		legendTarget.style.padding = '8px 0 0 0';
		chartInstance._soloIndex = null;

		ensembleItems.concat(otherItems).forEach(function (item) {
			var legendItem = document.createElement('div');

			legendItem.dataset.idx = String(item.idx);
			legendItem.title = item.label;
			legendItem.style.cssText = 'display:flex;align-items:center;gap:6px;cursor:pointer;padding:3px 0;user-select:none;';
			legendItem.innerHTML =
				'<span style="display:inline-block;width:10px;height:10px;border-radius:50%;flex-shrink:0;background:' + escapeHtml(item.color) + ';"></span>' +
				'<span style="font-size:10px;line-height:1.25;color:#64748b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + escapeHtml(item.label) + '</span>';
			legendItem.addEventListener('click', function () {
				var targetIndex = parseInt(legendItem.dataset.idx || '-1', 10);
				var itemNodes = legendTarget.querySelectorAll('div');

				if (chartInstance._soloIndex === targetIndex) {
					chartInstance.data.datasets.forEach(function (dataset, datasetIndex) {
						chartInstance.setDatasetVisibility(datasetIndex, true);
					});
					chartInstance._soloIndex = null;
					itemNodes.forEach(function (node) {
						node.style.opacity = '1';
					});
				} else {
					chartInstance.data.datasets.forEach(function (dataset, datasetIndex) {
						chartInstance.setDatasetVisibility(datasetIndex, datasetIndex === targetIndex);
					});
					chartInstance._soloIndex = targetIndex;
					itemNodes.forEach(function (node) {
						node.style.opacity = node.dataset.idx === String(targetIndex) ? '1' : '0.35';
					});
				}

				chartInstance.update();
			});
			legendTarget.appendChild(legendItem);
		});

		if (latestTarget && datasets.length) {
			var ensemble = datasets.find(function (dataset) {
				return sanitizeText(dataset.label) === 'ENSEMBLE';
			}) || datasets[0];
			var values = ensemble && Array.isArray(ensemble.data) ? ensemble.data : [];
			var lastValue = null;

			for (var idx = values.length - 1; idx >= 0; idx -= 1) {
				var numeric = coerceNumber(values[idx]);
				if (numeric !== null) {
					lastValue = numeric;
					break;
				}
			}

			latestTarget.textContent = lastValue !== null ? formatOneDecimal(lastValue) : '--';
		}
	}

	function renderViewModelMeta(viewModel) {
		var meta = viewModel && viewModel.meta && typeof viewModel.meta === 'object' ? viewModel.meta : {};
		var yearLabelNodes = document.querySelectorAll('[data-dashboard-year-label]');
		var footerNoteNodes = document.querySelectorAll('[data-dashboard-footer-note]');

		if (meta.yearLabel) {
			yearLabelNodes.forEach(function (node) {
				node.textContent = sanitizeText(meta.yearLabel);
			});
		}

		if (meta.footerNote) {
			footerNoteNodes.forEach(function (node) {
				node.textContent = sanitizeText(meta.footerNote);
			});
		}
	}

	function getViewModelTable(viewModel, tableId) {
		var tables = viewModel && Array.isArray(viewModel.tables) ? viewModel.tables : [];

		return tables.find(function (table) {
			return sanitizeText(table && table.id) === sanitizeText(tableId);
		}) || null;
	}

	function renderCommerceInterieurDenreesTable(table, head, body) {
		var products = Array.isArray(table.products) ? table.products.filter(Boolean) : [];
		var series = Array.isArray(table.series) ? table.series.slice().reverse() : [];

		if (!products.length || !series.length) {
			head.innerHTML = '';
			body.innerHTML = '';
			return;
		}

		head.innerHTML = '<tr class="bg-gray-50 text-gray-500 text-left">' +
			'<th class="sticky left-0 z-10 bg-gray-50 px-2 py-2 font-medium w-[72px] min-w-[72px] whitespace-nowrap">P\u00e9riode</th>' +
			products.map(function (product) {
				var split = splitHeaderLabel(product);

				return '<th class="px-1 py-2 font-medium text-center w-[70px] min-w-[70px] leading-3">' +
					'<div class="flex flex-col items-center">' +
						'<span class="block w-full truncate text-center">' + escapeHtml(split[0]) + '</span>' +
						(split[1] ? '<span class="block w-full truncate text-center">' + escapeHtml(split[1]) + '</span>' : '') +
					'</div>' +
				'</th>';
			}).join('') +
		'</tr>';

		body.innerHTML = series.map(function (row, rowIndex) {
			var prevRow = rowIndex + 1 < series.length ? series[rowIndex + 1] : null;
			var values = row && row.values && typeof row.values === 'object' ? row.values : {};
			var prevValues = prevRow && prevRow.values && typeof prevRow.values === 'object' ? prevRow.values : {};

			return '<tr class="border-t border-gray-50 hover:bg-gray-50/50">' +
				'<td class="sticky left-0 z-10 bg-white px-2 py-2 text-left font-medium text-gray-800 whitespace-nowrap">' + escapeHtml(formatMonthPeriodLabel(row.date)) + '</td>' +
				products.map(function (product) {
					var currentValue = coerceNumber(values[product]);
					var previousValue = coerceNumber(prevValues[product]);
					var delta = currentValue !== null && previousValue !== null ? currentValue - previousValue : null;
					var arrow = '';
					var arrowClass = '';

					if (delta !== null && delta > 0) {
						arrow = '\u25b2';
						arrowClass = 'text-red-500';
					} else if (delta !== null && delta < 0) {
						arrow = '\u25bc';
						arrowClass = 'text-emerald-600';
					}

					return '<td class="px-2 py-2 text-center text-gray-600 whitespace-nowrap">' +
						'<span class="inline-flex items-center justify-center gap-1">' +
							'<span>' + escapeHtml(currentValue !== null ? formatNumber(Math.round(currentValue)) : '') + '</span>' +
							(arrow ? '<span class="' + arrowClass + '">' + arrow + '</span>' : '') +
						'</span>' +
					'</td>';
				}).join('') +
			'</tr>';
		}).join('');
	}

	function renderIndustryDpeeTable(table, head, body) {
		var products = Array.isArray(table && table.products) ? table.products.filter(Boolean) : [];
		var series = Array.isArray(table && table.series) ? table.series.slice(-12).reverse() : [];
		var shortNameMap = {
			'petrole brut (millions bbl)': 'P\u00e9trole Brut',
			'phosphates - total (1000t)': 'Phosphates',
			'dont: phosphate de calcium (1000t)': 'Phosphate Ca.',
			'prod. arachidiers - total (1000t)': 'Arachide',
			'or - production (kg)': 'Or (Kg)',
			'electricite - ventes totales (m kwh)': '\u00c9lectricit\u00e9',
			'eau - production (mm3)': 'Eau',
			'ciment - production (1000t)': 'Ciment',
			'acide phosphorique (1000t)': 'Acide Phosph.',
			'engrais solides (1000t)': 'Engrais',
			'sel - production (tonnes)': 'Sel'
		};

		function normalizeLookupLabel(value) {
			var text = sanitizeText(value).toLowerCase();

			if (typeof text.normalize === 'function') {
				text = text.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
			}

			text = text
				.replace(/Â³/g, '3')
				.replace(/\s+/g, ' ')
				.trim();

			return text;
		}

		function getIndustryDpeeShort(product) {
			var normalized = normalizeLookupLabel(product);

			if (shortNameMap[normalized]) {
				return shortNameMap[normalized];
			}

			return sanitizeText(product).length > 16 ? sanitizeText(product).substring(0, 14) + '\u2026' : sanitizeText(product);
		}

		function getIndustryDpeeUnit(product) {
			var unitMatch = sanitizeText(product).match(/\(([^)]+)\)/);

			return unitMatch ? unitMatch[1] : '';
		}

		function formatIndustryDpeeValue(value) {
			var numeric = coerceNumber(value);

			if (numeric === null) {
				return '';
			}

			if (Math.abs(numeric) >= 100) {
				return Math.round(numeric).toLocaleString('fr-FR');
			}

			return numeric.toFixed(1).replace('.', ',');
		}

		if (!products.length || !series.length) {
			var fallbackHeaders = table.headers || [];
			var fallbackRows = table.rows || [];

			head.innerHTML = '<tr class="bg-gray-50 text-gray-500 text-left">' + fallbackHeaders.map(function (label, index) {
				var widthClass = index === 0 ? ' sticky left-0 bg-gray-50 z-10 min-w-[90px]' : ' text-center min-w-[70px]';
				return '<th class="px-2 py-2 font-medium whitespace-nowrap text-[10px]' + widthClass + '">' + escapeHtml(label) + '</th>';
			}).join('') + '</tr>';

			body.innerHTML = fallbackRows.map(function (row) {
				var cells = Array.isArray(row) ? row : [];

				return '<tr class="border-t border-gray-50 hover:bg-gray-50/50">' + cells.map(function (cell, index) {
					var cellClass = index === 0 ? 'sticky left-0 bg-white z-10 font-medium text-gray-700' : 'text-center text-gray-500';
					return '<td class="px-2 py-2 whitespace-nowrap text-[10px] ' + cellClass + '">' + escapeHtml(cell) + '</td>';
				}).join('') + '</tr>';
			}).join('');

			return;
		}
        head.innerHTML = '<tr class="bg-gray-50 text-gray-500 text-left">' +
            '<th class="sticky left-0 z-10 bg-gray-50 px-2 py-2 font-medium w-[76px] min-w-[76px] whitespace-nowrap">P\u00e9riode</th>' +
            products.map(function (product) {
                var shortLabel = getIndustryDpeeShort(product);
                var unit = getIndustryDpeeUnit(product);

                return '<th class="px-1.5 py-2 font-medium text-center w-[88px] min-w-[88px] leading-3" title="' + escapeHtml(product) + '">' +
                    '<div class="flex flex-col items-center">' +
                        '<span class="block w-full text-center text-[9px] whitespace-normal break-words">' + escapeHtml(shortLabel) + '</span>' +
                        (unit ? '<span class="block mt-0.5 text-[7px] text-gray-400 font-normal leading-3">' + escapeHtml(unit) + '</span>' : '') +
                    '</div>' +
                '</th>';
            }).join('') +
        '</tr>';

        body.innerHTML = series.map(function (row, rowIndex) {
            var prevRow = rowIndex + 1 < series.length ? series[rowIndex + 1] : null;
            var values = row && row.values && typeof row.values === 'object' ? row.values : {};
            var prevValues = prevRow && prevRow.values && typeof prevRow.values === 'object' ? prevRow.values : {};

            return '<tr class="border-t border-gray-50 hover:bg-gray-50/50">' +
                '<td class="sticky left-0 z-10 bg-white px-2 py-2 text-left font-medium text-gray-800 whitespace-nowrap">' + escapeHtml(formatMonthPeriodLabel(row.date)) + '</td>' +
                products.map(function (product) {
                    var currentValue = coerceNumber(values[product]);
                    var previousValue = coerceNumber(prevValues[product]);
                    var delta = currentValue !== null && previousValue !== null && Number.isFinite(currentValue) && Number.isFinite(previousValue) ? currentValue - previousValue : null;
                    var arrow = '';

                    if (delta !== null && delta > 0) {
                        arrow = '<span class="ml-0.5 text-emerald-600" aria-label="hausse">&#9650;</span>';
                    } else if (delta !== null && delta < 0) {
                        arrow = '<span class="ml-0.5 text-red-500" aria-label="baisse">&#9660;</span>';
                    }

                    return '<td class="px-1.5 py-2 text-center text-gray-600 min-w-[88px]">' +
                        '<span class="inline-flex items-center justify-center">' +
                            escapeHtml(formatIndustryDpeeValue(currentValue)) +
                            arrow +
                        '</span>' +
                    '</td>';
                }).join('') +
            '</tr>';
        }).join('');
	}

	function getPmeRegionShapes() {
		return [
			{ region: 'Dakar', d: 'M32.9,229.9L33.0,234.1L21.2,222.8L67.0,207.7L70.5,238.9L44.3,224.5L34.7,224.8L32.9,229.9Z', cx: 43.0, cy: 226.7, labelX: 10, labelY: 192, labelAnchor: 'start', leaderX: 40, leaderY: 218 },
			{ region: 'Diourbel', d: 'M203.9,244.0L135.8,238.3L129.9,231.5L135.9,220.7L124.9,203.6L154.8,193.0L182.1,200.9L228.9,196.5L240.5,206.7L249.6,201.6L265.5,214.8L278.7,214.9L275.6,227.2L264.2,232.2L253.8,225.3L247.6,229.3L235.8,223.8L228.2,243.7L210.3,248.1L203.9,244.0Z', cx: 191.9, cy: 221.4, labelAnchor: 'middle' },
			{ region: 'Fatick', d: 'M148.0,348.9L140.3,348.2L129.3,331.3L115.8,325.0L116.6,295.7L111.8,290.2L120.1,283.2L123.8,258.0L141.4,250.9L145.6,240.1L163.1,238.2L218.1,247.7L232.9,240.2L234.5,224.3L260.9,228.9L266.4,239.4L254.3,249.4L243.1,250.0L233.6,263.4L222.3,263.3L221.8,255.5L214.3,254.7L206.7,263.6L188.1,265.1L166.5,275.3L161.8,286.9L180.6,292.1L175.3,303.9L182.3,305.0L185.1,315.5L179.0,326.3L188.9,332.3L193.2,347.1L148.0,348.9Z', cx: 175.8, cy: 277.9, labelAnchor: 'middle' },
			{ region: 'Kaffrine', d: 'M283.7,330.7L260.9,322.7L253.7,328.7L245.6,322.2L245.1,309.2L235.5,306.2L235.3,298.7L226.4,292.6L231.3,278.0L247.0,273.0L247.4,252.7L266.4,239.4L263.2,232.1L275.6,227.2L281.7,218.0L301.3,233.5L329.5,238.1L343.4,228.1L363.5,241.0L381.8,241.4L376.6,271.6L382.1,276.6L383.3,306.0L366.8,324.2L352.5,329.6L324.7,323.2L305.8,331.6L293.9,327.6L283.7,330.7Z', cx: 290.9, cy: 289.0, labelAnchor: 'middle' },
			{ region: 'Kaolack', d: 'M209.4,348.7L191.8,348.7L188.9,332.3L179.0,326.3L185.1,315.5L182.3,305.0L175.3,303.9L180.6,292.1L162.4,287.5L163.7,279.7L188.1,265.1L206.7,263.6L214.3,254.7L221.8,255.5L222.3,263.3L233.6,263.4L247.2,249.5L247.0,273.0L231.3,278.0L227.1,294.8L235.3,298.7L235.5,306.2L245.1,309.2L245.6,322.2L253.7,328.7L260.9,322.7L280.1,326.2L282.6,332.6L274.1,348.8L209.4,348.7Z', cx: 211.3, cy: 295.7, labelAnchor: 'middle' },
			{ region: 'Kedougou', d: 'M678.3,482.5L657.8,488.3L646.4,478.2L632.2,481.8L624.8,474.5L607.6,474.5L592.0,461.6L585.6,463.5L585.3,470.0L570.8,469.0L572.6,454.0L559.5,449.5L567.5,445.4L566.0,432.6L559.6,430.8L555.9,424.0L560.3,420.9L549.2,411.1L557.5,405.9L568.0,409.9L584.4,404.2L601.5,413.5L617.2,401.5L618.5,394.1L628.6,390.2L637.9,392.9L650.4,382.1L660.4,384.9L663.7,378.5L673.2,378.0L671.9,362.9L698.2,372.7L699.7,379.2L715.9,371.1L726.2,379.2L734.1,367.8L747.7,369.9L760.8,384.9L761.6,396.8L772.8,404.2L774.5,416.2L781.4,415.4L774.7,429.7L780.3,442.4L772.8,442.8L769.9,448.6L775.4,457.7L770.1,462.2L781.0,466.6L779.7,476.0L763.4,473.0L730.3,479.8L713.9,472.9L700.3,477.9L694.0,474.6L678.3,482.5Z', cx: 670.9, cy: 425.8, labelAnchor: 'middle' },
			{ region: 'Kolda', d: 'M347.2,447.9L315.2,446.0L318.6,424.2L298.8,393.0L289.0,388.5L290.8,374.0L304.3,367.6L308.2,355.6L321.4,348.0L339.6,362.7L361.9,367.6L374.3,376.0L386.4,373.6L406.4,386.5L426.5,389.6L475.3,376.8L481.0,365.4L472.7,357.5L481.5,353.8L479.7,362.1L490.5,362.5L483.5,372.2L495.9,371.6L494.0,381.6L503.9,387.1L502.8,398.5L516.8,412.6L514.9,418.1L523.0,429.4L520.8,446.1L535.1,446.6L534.1,450.0L347.2,447.9Z', cx: 466.5, cy: 402.2, labelAnchor: 'middle' },
			{ region: 'Louga', d: 'M344.7,229.3L329.5,238.1L301.3,233.5L278.5,214.8L253.5,208.8L249.6,201.6L241.1,206.8L228.9,196.5L187.4,199.7L175.3,167.4L169.4,168.3L166.2,160.9L153.7,156.6L132.8,179.9L131.1,171.2L117.8,162.8L124.5,149.7L120.0,145.6L143.5,108.5L169.2,105.2L192.0,85.9L230.0,66.9L268.1,98.1L280.9,96.8L279.2,88.7L285.4,87.2L286.2,81.0L297.4,81.7L314.9,92.8L340.4,87.0L355.3,100.9L374.8,89.7L389.7,105.1L373.3,114.6L359.9,148.1L380.8,157.1L395.7,155.4L380.1,180.1L355.5,183.6L360.7,219.4L350.8,233.1L344.7,229.3Z', cx: 241.0, cy: 153.5, labelAnchor: 'middle' },
			{ region: 'Matam', d: 'M597.4,174.0L603.5,172.9L604.3,179.3L622.0,191.7L629.0,207.2L621.2,227.8L603.7,241.3L584.5,243.7L548.5,237.9L515.3,259.7L482.0,259.7L469.9,243.1L445.9,242.9L440.9,248.0L407.3,236.5L387.9,243.4L363.5,241.0L350.8,233.1L360.7,219.4L355.5,183.6L380.1,180.1L395.7,155.4L380.8,157.1L359.9,148.1L373.3,114.6L389.7,105.1L394.0,119.9L406.3,126.1L400.9,133.0L405.9,155.2L424.0,154.0L431.0,150.7L433.1,135.6L452.4,136.2L460.7,98.2L487.2,81.9L490.1,72.2L495.0,76.7L518.4,71.0L517.1,78.2L521.8,75.1L531.8,80.7L545.7,109.7L542.5,112.3L552.9,120.1L547.4,122.7L550.1,128.8L567.7,132.5L566.2,141.8L582.9,141.2L587.7,147.6L585.4,156.9L598.8,164.3L590.9,167.2L597.4,174.0Z', cx: 496.8, cy: 153.7, labelAnchor: 'middle' },
			{ region: 'Saint-Louis', d: 'M459.7,62.6L471.1,66.1L474.3,75.4L491.8,67.6L487.2,81.9L460.7,98.2L452.4,136.2L433.1,135.6L431.0,150.7L424.0,154.0L405.9,155.2L400.9,133.0L406.3,126.1L394.0,119.9L394.3,110.4L374.8,89.7L355.3,100.9L340.4,87.0L314.9,92.8L297.4,81.7L286.2,81.0L285.4,87.2L279.2,88.7L280.9,96.8L268.1,98.1L231.0,66.7L192.0,85.9L169.2,105.2L142.8,109.5L152.8,66.8L165.2,57.9L174.6,31.1L192.4,26.6L202.7,34.9L213.3,34.3L220.5,25.9L218.5,31.6L246.2,35.6L255.6,29.5L268.5,30.2L274.5,22.5L283.2,28.6L318.1,24.0L320.0,13.5L326.0,18.7L334.1,11.7L340.2,18.4L413.9,17.2L414.8,25.5L421.0,23.4L423.3,30.3L430.8,29.2L459.3,51.0L459.7,62.6Z', cx: 322.4, cy: 53.7, labelAnchor: 'middle' },
			{ region: 'Sedhiou', d: 'M229.7,473.4L218.9,471.3L213.4,453.7L206.6,448.8L209.7,422.1L221.8,404.3L217.5,400.1L222.3,393.3L233.5,393.4L233.6,375.2L261.1,374.9L271.8,370.1L291.1,373.9L289.2,388.9L298.8,393.0L318.6,424.2L313.2,441.4L317.4,447.5L248.5,475.6L229.7,473.4Z', cx: 251.2, cy: 423.4, labelAnchor: 'middle' },
			{ region: 'Tambacounda', d: 'M540.8,451.7L535.5,446.6L520.8,446.1L523.0,429.4L514.9,418.1L516.8,412.6L502.8,398.5L503.9,387.1L494.0,381.6L495.9,371.6L483.5,372.2L490.5,362.5L479.7,362.1L481.7,353.9L475.9,358.2L458.4,350.1L415.9,363.6L402.8,358.5L388.0,342.5L368.1,346.2L359.3,342.2L352.5,329.6L366.8,324.2L383.3,306.0L382.1,276.6L376.6,271.6L378.4,243.5L407.3,236.5L440.9,248.0L445.9,242.9L460.4,241.6L469.9,243.1L482.0,259.7L502.6,260.1L516.6,259.2L548.5,237.9L584.5,243.7L603.7,241.3L621.2,227.8L629.0,207.2L616.9,186.3L620.3,184.0L642.0,194.9L645.8,207.3L681.9,229.6L683.2,236.5L674.8,244.7L676.7,260.2L686.0,261.5L689.0,271.1L699.1,273.8L705.0,284.3L700.9,304.9L709.1,304.6L710.2,312.5L707.9,325.2L692.5,334.7L701.8,351.3L718.2,365.3L699.7,379.2L698.2,372.7L671.7,363.0L673.2,378.0L663.7,378.5L660.4,384.9L650.4,382.1L637.9,392.9L628.6,390.2L618.5,394.1L617.2,401.5L601.5,413.5L587.2,404.3L568.0,409.9L552.8,407.5L549.0,411.7L560.3,420.9L555.9,424.1L565.9,432.4L567.5,445.4L559.5,448.3L562.9,452.4L540.8,451.7Z', cx: 573.7, cy: 361.1, labelAnchor: 'middle' },
			{ region: 'Thies', d: 'M100.2,277.4L90.9,261.2L68.5,240.7L72.6,224.3L66.4,207.2L120.1,145.7L124.5,149.7L117.8,162.8L131.1,171.2L132.8,179.9L153.7,156.6L166.2,160.9L168.2,167.6L175.7,168.1L187.4,199.7L154.8,193.0L125.5,202.9L135.9,220.8L130.0,232.5L144.7,238.5L143.8,246.6L123.4,258.7L120.1,283.2L111.8,290.2L116.5,297.5L100.2,277.4Z', cx: 124.7, cy: 224.8, labelAnchor: 'middle' },
			{ region: 'Ziguinchor', d: 'M147.9,483.3L121.0,484.9L112.1,472.2L111.9,438.3L122.1,396.3L222.4,394.8L206.4,440.5L207.2,450.5L223.0,472.8L184.5,471.7L162.6,482.4L147.9,483.3Z', cx: 166.1, cy: 434.3, labelAnchor: 'middle' }
		];
	}

	function getPmeRegionColor(percent) {
		if (percent >= 40) {
			return '#1A05A2';
		}

		if (percent >= 10) {
			return '#8F0177';
		}

		if (percent >= 5) {
			return '#DE1A58';
		}

		return '#FBC4A0';
	}

	function getPmeRegionDisplayName(region) {
		var key = normalizeAccentKey(region);
		var labels = {
			thies: 'Thi\u00e8s',
			sedhiou: 'S\u00e9dhiou',
			kedougou: 'K\u00e9dougou',
			'saint-louis': 'Saint-Louis'
		};

		return labels[key] || sanitizeText(region);
	}

	function renderPmeRegionMap(viewModel) {
		var container = document.getElementById('pme-region-map');
		var table = getViewModelTable(viewModel, 'ninea-immatriculations');

		if (!container || !table || !Array.isArray(table.rows)) {
			return;
		}

		var dataByRegion = {};
		var shapes = getPmeRegionShapes();

		table.rows.forEach(function (row) {
			if (!Array.isArray(row) || row.length < 2) {
				return;
			}

			dataByRegion[normalizeAccentKey(row[0])] = coerceNumber(row[1]) || 0;
		});

		container.innerHTML = '<svg viewBox="0 0 820 520" preserveAspectRatio="xMidYMid meet" class="absolute inset-0 w-full h-full" xmlns="http://www.w3.org/2000/svg">' +
			shapes.map(function (shape) {
				var pct = dataByRegion[normalizeAccentKey(shape.region)] || 0;
				var fill = getPmeRegionColor(pct);
				var labelText = formatOneDecimal(pct, '%');
				var regionName = getPmeRegionDisplayName(shape.region);
				var pathMarkup = '<path data-region="' + escapeHtml(regionName) + '" data-pct="' + escapeHtml(String(pct)) + '" d="' + shape.d + '" fill="' + fill + '" stroke="#ffffff" stroke-width="1.2" style="cursor:pointer;transition:fill .2s,filter .2s"></path>';

				if (shape.labelX !== undefined) {
					return pathMarkup +
						'<line x1="' + shape.cx + '" y1="' + (shape.cy - 8) + '" x2="' + (shape.labelX + 34) + '" y2="' + (shape.labelY + 2) + '" stroke="#9ca3af" stroke-width="0.8"></line>' +
						'<text data-region-label="' + escapeHtml(shape.region) + '" x="' + shape.labelX + '" y="' + shape.labelY + '" text-anchor="' + (shape.labelAnchor || 'start') + '" fill="#6b7280" style="font-size:15px;font-weight:500;font-family:Montserrat,sans-serif;pointer-events:none;">' + labelText + '</text>';
				}

				return pathMarkup +
					'<text data-region-label="' + escapeHtml(shape.region) + '" x="' + shape.cx + '" y="' + (shape.cy + 4) + '" text-anchor="' + (shape.labelAnchor || 'middle') + '" fill="#6b7280" style="font-size:15px;font-weight:500;font-family:Montserrat,sans-serif;pointer-events:none;">' + labelText + '</text>';
			}).join('') +
			'<g id="pme-map-tooltip" style="pointer-events:none;display:none;">' +
				'<rect id="pme-map-tooltip-bg" rx="6" ry="6" fill="rgba(26,5,162,0.94)" width="220" height="62"></rect>' +
				'<text id="pme-map-tooltip-name" x="12" y="24" fill="#ffffff" style="font-size:16px;font-weight:700;font-family:Montserrat,sans-serif;"></text>' +
				'<text id="pme-map-tooltip-value" x="12" y="48" fill="#b8943e" style="font-size:14px;font-weight:600;font-family:Montserrat,sans-serif;"></text>' +
			'</g>' +
		'</svg>';

		var svg = container.querySelector('svg');
		var tooltip = container.querySelector('#pme-map-tooltip');
		var tooltipBg = container.querySelector('#pme-map-tooltip-bg');
		var tooltipName = container.querySelector('#pme-map-tooltip-name');
		var tooltipValue = container.querySelector('#pme-map-tooltip-value');

		if (!svg || !tooltip || !tooltipBg || !tooltipName || !tooltipValue) {
			return;
		}

		svg.querySelectorAll('path[data-region]').forEach(function (path) {
			path.addEventListener('mouseenter', function () {
				var region = sanitizeText(path.getAttribute('data-region'));
				var pct = coerceNumber(path.getAttribute('data-pct')) || 0;
				var textWidth = Math.max(region.length, (formatOneDecimal(pct, '%') + ' des immatriculations').length) * 9;

				path.style.filter = 'drop-shadow(0 2px 4px rgba(0,0,0,0.24))';
				path.setAttribute('stroke-width', '2.5');
				tooltipBg.setAttribute('width', String(Math.max(220, textWidth)));
				tooltipName.textContent = region;
				tooltipValue.textContent = formatOneDecimal(pct, '%') + ' des immatriculations';
				tooltip.style.display = '';
			});

			path.addEventListener('mousemove', function (event) {
				var svgRect = svg.getBoundingClientRect();
				var scaleX = 820 / svgRect.width;
				var scaleY = 520 / svgRect.height;
				var x = (event.clientX - svgRect.left) * scaleX;
				var y = (event.clientY - svgRect.top) * scaleY;
				var tooltipWidth = parseFloat(tooltipBg.getAttribute('width') || '220');
				var tx = x + 15;
				var ty = y - 30;

				if (tx + tooltipWidth > 810) {
					tx = x - tooltipWidth - 10;
				}

				if (ty < 8) {
					ty = y + 15;
				}

				tooltip.setAttribute('transform', 'translate(' + tx + ',' + ty + ')');
			});

			path.addEventListener('mouseleave', function () {
				path.style.filter = '';
				path.setAttribute('stroke-width', '1.2');
				tooltip.style.display = 'none';
			});
		});
	}

	function renderPmeObstaclesTreemap(viewModel) {
		var container = document.getElementById('obstacles-treemap');
		var table = getViewModelTable(viewModel, 'enquete-wb');

		if (!container || !table || !Array.isArray(table.rows)) {
			return;
		}

		var items = table.rows.map(function (row) {
			return {
				label: sanitizeText(row[0]),
				pct: coerceNumber(row[1]) || 0,
				note: sanitizeText(row[2])
			};
		}).filter(function (item) {
			return item.pct > 0;
		}).sort(function (left, right) {
			return right.pct - left.pct;
		});

		container.innerHTML = '';
		container.style.position = 'relative';

		if (!items.length) {
			container.innerHTML = '<div class="absolute inset-0 flex items-center justify-center text-xs text-gray-400">Aucune donn\u00e9e exploitable.</div>';
			return;
		}

		var colors = ['#044bad', '#059669', '#b8943e', '#dc2626', '#7c3aed', '#0891b2', '#ea580c'];
		var width = Math.max(container.clientWidth, 280);
		var height = Math.max(container.clientHeight, 220);

		function worst(row, side) {
			var sum = row.reduce(function (carry, value) { return carry + value; }, 0);
			var max = Math.max.apply(null, row);
			var min = Math.min.apply(null, row);
			var sideSquared = side * side;

			return Math.max((sideSquared * max) / (sum * sum), (sum * sum) / (sideSquared * min));
		}

		function squarify(values, x, y, w, h) {
			if (!values.length) {
				return [];
			}

			if (values.length === 1) {
				return [{ x: x, y: y, w: w, h: h, idx: values[0].idx }];
			}

			var total = values.reduce(function (carry, value) { return carry + value.val; }, 0);
			var areas = values.map(function (value) {
				return { area: (value.val / total) * w * h, idx: value.idx };
			});
			var rects = [];
			var rx = x;
			var ry = y;
			var rw = w;
			var rh = h;
			var remaining = areas.slice();

			function layoutRow(rowItems, rowAreas, left, top, widthValue, heightValue) {
				var sum = rowAreas.reduce(function (carry, value) { return carry + value; }, 0);

				if (widthValue >= heightValue) {
					var rowWidth = sum / heightValue;
					var cy = top;

					rowItems.forEach(function (item, index) {
						var cellHeight = rowAreas[index] / rowWidth;
						rects.push({ x: left, y: cy, w: rowWidth, h: cellHeight, idx: item.idx });
						cy += cellHeight;
					});

					rx = left + rowWidth;
					ry = top;
					rw = widthValue - rowWidth;
					rh = heightValue;
					return;
				}

				var rowHeight = sum / widthValue;
				var cx = left;

				rowItems.forEach(function (item, index) {
					var cellWidth = rowAreas[index] / rowHeight;
					rects.push({ x: cx, y: top, w: cellWidth, h: rowHeight, idx: item.idx });
					cx += cellWidth;
				});

				rx = left;
				ry = top + rowHeight;
				rw = widthValue;
				rh = heightValue - rowHeight;
			}

			while (remaining.length) {
				var side = Math.min(rw, rh);
				var row = [remaining[0]];
				var rowAreas = [remaining[0].area];
				var cursor = 1;

				while (cursor < remaining.length) {
					var nextAreas = rowAreas.concat([remaining[cursor].area]);
					if (worst(nextAreas, side) <= worst(rowAreas, side)) {
						row.push(remaining[cursor]);
						rowAreas.push(remaining[cursor].area);
						cursor += 1;
					} else {
						break;
					}
				}

				remaining = remaining.slice(cursor);
				layoutRow(row, rowAreas, rx, ry, rw, rh);
			}

			return rects;
		}

		var rects = squarify(items.map(function (item, index) {
			return { val: item.pct, idx: index };
		}), 0, 0, width, height);

		rects.forEach(function (rect, index) {
			var item = items[rect.idx];
			var block = document.createElement('div');
			var pctSize = rect.w > 80 && rect.h > 50 ? 18 : (rect.w > 55 && rect.h > 35 ? 14 : 11);
			var labelSize = rect.w > 80 && rect.h > 50 ? 11 : (rect.w > 55 && rect.h > 35 ? 9 : 7);

			block.style.position = 'absolute';
			block.style.left = rect.x + 'px';
			block.style.top = rect.y + 'px';
			block.style.width = Math.max(rect.w - 2, 0) + 'px';
			block.style.height = Math.max(rect.h - 2, 0) + 'px';
			block.style.backgroundColor = colors[index % colors.length] + 'cc';
			block.style.display = 'flex';
			block.style.flexDirection = 'column';
			block.style.alignItems = 'center';
			block.style.justifyContent = 'center';
			block.style.padding = '6px';
			block.style.overflow = 'hidden';
			block.style.transition = 'filter 0.2s ease';
			block.title = item.label + ' : ' + formatOneDecimal(item.pct, '%');
			block.innerHTML = '<span style="font-size:' + pctSize + 'px;font-weight:700;color:#fff;font-family:Montserrat,sans-serif;line-height:1.1;">' + formatOneDecimal(item.pct, '%') + '</span>' +
				'<span style="font-size:' + labelSize + 'px;color:rgba(255,255,255,0.92);font-family:Montserrat,sans-serif;text-align:center;line-height:1.2;margin-top:3px;">' + escapeHtml(item.label) + '</span>';

			block.addEventListener('mouseenter', function () {
				block.style.filter = 'brightness(1.15)';
			});

			block.addEventListener('mouseleave', function () {
				block.style.filter = '';
			});

			container.appendChild(block);
		});
	}

	function renderDashboardSpecificExtras(viewModel) {
		if (dashboardState.pageKey === 'pme-pmi') {
			renderPmeRegionMap(viewModel);
			renderPmeObstaclesTreemap(viewModel);
		}
	}

	function renderViewModelTables(viewModel) {
		var cards = document.querySelectorAll('[data-dashboard-table]');
		var tables = viewModel && Array.isArray(viewModel.tables) ? viewModel.tables : [];

		cards.forEach(function (card, index) {
			var tableId = sanitizeText(card.getAttribute('data-table-id'));
			var table = tables.find(function (item) {
				return sanitizeText(item.id) === tableId;
			}) || tables[index] || null;
			var title = card.querySelector('[data-dashboard-table-title]');
			var periodCopy = card.querySelector('[data-dashboard-table-period]');
			var sourceCopy = card.querySelector('[data-dashboard-table-source-copy]');
			var head = card.querySelector('[data-dashboard-table-head]');
			var body = card.querySelector('[data-dashboard-table-body]');

			if (!table || !head || !body) {
				showTableState(card, 'empty', 'Aucune table sp\u00e9cifique n a ete retournee.');
				return;
			}

			if (title) {
				if (tableId === 'denrees-base') {
					var titleCopy = title.querySelector('[data-dashboard-table-title-copy]');
					if (titleCopy) {
						titleCopy.textContent = getDenreesDisplayMeta(table).title;
					}
				} else {
					title.textContent = sanitizeText(table.title);
				}
			}

			if (tableId === 'denrees-base') {
				var denreesMeta = getDenreesDisplayMeta(table);

				if (periodCopy) {
					periodCopy.textContent = denreesMeta.period;
				}

				if (sourceCopy) {
					sourceCopy.textContent = denreesMeta.source;
				}
			}

			if (tableId === 'denrees-base') {
				renderCommerceInterieurDenreesTable(table, head, body);
				showTableState(card, 'ready');
				return;
			}

			if (tableId === 'production-dpee') {
				renderIndustryDpeeTable(table, head, body);
				showTableState(card, 'ready');
				return;
			}

			head.innerHTML = '<tr class="bg-gray-50 text-gray-500 text-left">' + (table.headers || []).map(function (label, headerIndex) {
				var alignClass = (tableId === 'export-partners' || tableId === 'import-partners') && headerIndex >= 2 ? ' text-right' : '';
				return '<th class="px-4 py-2 font-medium whitespace-nowrap' + alignClass + '">' + escapeHtml(label) + '</th>';
			}).join('') + '</tr>';

			body.innerHTML = (table.rows || []).map(function (row) {
				var cells = Array.isArray(row) ? row : [];

				if ((tableId === 'export-partners' || tableId === 'import-partners') && cells.length >= 4) {
					var shareText = sanitizeText(cells[3]);
					var shareValue = coerceNumber(shareText);
					var shareWidth = shareValue === null ? 0 : Math.min(Math.max(shareValue, 0), 100);
					var shareColor = tableId === 'export-partners' ? '#044bad' : '#032d6b';

					return '<tr class="border-t border-gray-50 hover:bg-gray-50/50">' +
						'<td class="px-4 py-2 text-gray-400 whitespace-nowrap">' + escapeHtml(cells[0]) + '</td>' +
						'<td class="px-4 py-2 font-medium text-gray-800 whitespace-nowrap">' + escapeHtml(cells[1]) + '</td>' +
						'<td class="px-4 py-2 text-right text-gray-600 whitespace-nowrap">' + escapeHtml(cells[2]) + '</td>' +
						'<td class="px-4 py-2 text-right whitespace-nowrap">' +
							'<span class="inline-flex items-center gap-2">' +
								'<span class="w-12 h-1.5 bg-gray-100 rounded-full overflow-hidden inline-block">' +
									'<span class="h-full rounded-full block" style="width:' + shareWidth + '%;background:' + shareColor + ';"></span>' +
								'</span>' +
								'<span class="text-gray-500">' + escapeHtml(shareText) + '</span>' +
							'</span>' +
						'</td>' +
					'</tr>';
				}

				return '<tr class="border-t border-gray-50 hover:bg-gray-50/50">' + cells.map(function (cell) {
					return '<td class="px-4 py-2 text-gray-600 whitespace-nowrap">' + escapeHtml(cell) + '</td>';
				}).join('') + '</tr>';
			}).join('');

			showTableState(card, 'ready');
		});
	}

	function setFilterState(button) {
		var group = button.closest('[data-dashboard-filters]');

		if (!group) {
			return;
		}

		group.querySelectorAll('[data-dashboard-filter]').forEach(function (item) {
			item.classList.remove('is-active', 'bg-brand-blue', 'text-white', 'border-brand-blue');
			item.classList.add('border-slate-200', 'bg-white', 'text-slate-500');
			item.setAttribute('aria-pressed', 'false');
		});

		button.classList.add('is-active', 'bg-brand-blue', 'text-white', 'border-brand-blue');
		button.classList.remove('border-slate-200', 'bg-white', 'text-slate-500');
		button.setAttribute('aria-pressed', 'true');
	}

	function activateFilters() {
		document.querySelectorAll('[data-dashboard-filter]').forEach(function (button) {
			button.addEventListener('click', function () {
				setFilterState(button);
			});
		});
	}

	function renderGlobalError(message) {
		document.querySelectorAll('[data-dashboard-kpi]').forEach(function (card) {
			var elements = getKpiElements(card);

			if (elements.badge) {
				elements.badge.textContent = 'Erreur';
			}

			if (elements.value) {
				elements.value.textContent = '--';
			}

			if (elements.note) {
				elements.note.textContent = message;
			}

			card.classList.add('is-ready');
		});

		document.querySelectorAll('[data-dashboard-chart]').forEach(function (card) {
			showChartState(card, 'error', message);
		});

		document.querySelectorAll('[data-dashboard-table]').forEach(function (card) {
			showTableState(card, 'error', message);
		});

		if (dashboardState.pageKey === 'pme-pmi') {
			var regionMap = document.getElementById('pme-region-map');
			var treemap = document.getElementById('obstacles-treemap');

			if (regionMap) {
				regionMap.innerHTML = '<div class="absolute inset-0 flex items-center justify-center text-xs text-red-500">' + escapeHtml(message) + '</div>';
			}

			if (treemap) {
				treemap.innerHTML = '<div class="absolute inset-0 flex items-center justify-center text-xs text-red-500">' + escapeHtml(message) + '</div>';
			}
		}
	}

	function fetchDashboardPayload() {
		if (!dashboardState.apiUrl) {
			return window.Promise.reject(new Error('Dashboard API URL is missing.'));
		}

		return window.fetch(dashboardState.apiUrl, {
			method: 'GET',
			headers: {
				Accept: 'application/json'
			}
		})
			.then(function (response) {
				return response.json()
					.catch(function () {
						return {};
					})
					.then(function (payload) {
						if (!response.ok || !payload.success) {
							throw new Error(payload.message || 'Dashboard API request failed.');
						}

						return payload;
					});
			});
	}

	function boot() {
		if (!document.querySelector('[data-dashboard-page]')) {
			return;
		}

		activateFilters();

		fetchDashboardPayload()
			.then(function (payload) {
				var viewModel = normalizeViewModel(getViewModel(payload));

				dashboardState.hasData = true;
				dashboardState.state = 'ready';
				if (viewModel) {
					renderViewModelMeta(viewModel);
					renderViewModelKpis(viewModel);
					renderViewModelCharts(viewModel);
					renderViewModelTables(viewModel);
					renderDashboardSpecificExtras(viewModel);
					return;
				}

				renderKpis(payload);
				renderCharts(payload);
				renderTables(payload);
			})
			.catch(function (error) {
				var message = error && error.message ? error.message : (dashboardState.messages && dashboardState.messages.error ? dashboardState.messages.error : 'Le chargement du tableau de bord a echoue.');

				dashboardState.state = 'error';
				renderGlobalError(message);
			});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot, { once: true });
	} else {
		boot();
	}
})();
