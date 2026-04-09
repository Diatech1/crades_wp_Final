(function () {
	'use strict';

	var hubState = window.cradesDashboardHub || {};
	var palette = ['#044bad', '#3a7fd4', '#b8943e', '#032d6b', '#0f766e', '#dc2626'];

	function sanitizeText(value) {
		if (value === null || typeof value === 'undefined') {
			return '';
		}

		return String(value).trim();
	}

	function coerceNumber(value) {
		if (typeof value === 'number' && window.isFinite(value)) {
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

	function buildCandidates(payload) {
		var candidates = [];

		getTableEntries(payload).forEach(function (entry) {
			var table = entry.table;
			var numericColumns = getNumericColumns(table);
			var labelColumn = getLabelColumn(table, numericColumns);

			if (!table || !Array.isArray(table.rows) || table.rows.length < 2 || !numericColumns.length) {
				return;
			}

			var chartColumns = numericColumns.slice(0, 3);
			var labels = table.rows.map(function (row, index) {
				var labelValue = labelColumn ? sanitizeText(row[labelColumn.key]) : '';

				return labelValue || ('Ligne ' + (index + 1));
			});
			var temporal = labels.some(looksTemporal);

			candidates.push({
				type: temporal ? 'line' : 'bar',
				score: labels.length + chartColumns.length,
				labels: labels,
				datasets: chartColumns.map(function (column, columnIndex) {
					return {
						label: sanitizeText(column.label) || ('Serie ' + (columnIndex + 1)),
						data: table.rows.map(function (row) {
							var numericValue = coerceNumber(row[column.key]);
							return numericValue === null ? 0 : numericValue;
						}),
						borderColor: palette[columnIndex % palette.length],
						backgroundColor: palette[columnIndex % palette.length] + '24',
						borderWidth: 2,
						tension: temporal ? 0.35 : 0.14,
						fill: temporal && 0 === columnIndex,
						pointRadius: temporal ? 2 : 0,
						maxBarThickness: 32
					};
				})
			});
		});

		candidates.sort(function (left, right) {
			return right.score - left.score;
		});

		return candidates;
	}

	function renderPreview(canvas, payload) {
		var candidates = buildCandidates(payload);
		var candidate = candidates[0];

		if (!candidate || !window.Chart) {
			return false;
		}

		new window.Chart(canvas.getContext('2d'), {
			type: candidate.type,
			data: {
				labels: candidate.labels,
				datasets: candidate.datasets
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				animation: false,
				plugins: {
					legend: {
						display: true,
						position: 'bottom',
						labels: {
							boxWidth: 10,
							boxHeight: 10,
							usePointStyle: true,
							pointStyle: 'circle',
							padding: 10,
							color: '#5f6f86',
							font: {
								size: 10,
								family: 'Montserrat'
							}
						}
					},
					tooltip: {
						enabled: false
					}
				},
				scales: candidate.type === 'doughnut' ? {} : {
					x: {
						grid: {
							display: false
						},
						ticks: {
							color: '#7b8ca5',
							autoSkip: true,
							maxRotation: 0,
							font: {
								size: 10,
								family: 'Montserrat'
							}
						}
					},
					y: {
						beginAtZero: candidate.type !== 'line',
						grid: {
							color: '#dbe6f5',
							drawBorder: false
						},
						border: {
							display: false
						},
						ticks: {
							color: '#7b8ca5',
							maxTicksLimit: 4,
							font: {
								size: 10,
								family: 'Montserrat'
							}
						}
					}
				}
			}
		});

		return true;
	}

	function fetchPayload(url) {
		return window.fetch(url, {
			method: 'GET',
			headers: {
				Accept: 'application/json'
			}
		}).then(function (response) {
			return response.json().then(function (payload) {
				if (!response.ok || !payload.success) {
					throw new Error(payload.message || 'Dashboard preview request failed.');
				}

				return payload;
			});
		});
	}

	function boot() {
		var previews = document.querySelectorAll('[data-dashboard-preview]');

		if (!previews.length || !window.Chart) {
			return;
		}

		previews.forEach(function (preview) {
			var url = preview.getAttribute('data-api-url');
			var canvas = preview.querySelector('[data-dashboard-preview-canvas]');
			var loading = preview.querySelector('[data-dashboard-preview-loading]');
			var error = preview.querySelector('[data-dashboard-preview-error]');

			if (!url || !canvas) {
				return;
			}

			fetchPayload(url)
				.then(function (payload) {
					if (loading) {
						loading.classList.add('hidden');
					}

					if (!renderPreview(canvas, payload) && error) {
						error.classList.remove('hidden');
					}
				})
				.catch(function () {
					if (loading) {
						loading.classList.add('hidden');
					}

					if (error) {
						error.classList.remove('hidden');
					}
				});
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot, { once: true });
	} else {
		boot();
	}
})();
