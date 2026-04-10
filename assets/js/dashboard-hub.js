(function () {
	'use strict';

	var chartInstances = {};
	var preferredCharts = {
		'commerce-exterieur': 'trade-evolution',
		'commerce-interieur': 'ihpc-desagrege',
		'industrie': 'industry-ippi',
		'pme-pmi': 'pme-immatriculations'
	};

	function sanitizeText(value) {
		if (value === null || typeof value === 'undefined') {
			return '';
		}

		return String(value).trim();
	}

	function cloneChartConfig(value) {
		return JSON.parse(JSON.stringify(value));
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

	function mergeObjects(base, extension) {
		var output = cloneChartConfig(base || {});
		var source = extension || {};

		Object.keys(source).forEach(function (key) {
			var incoming = source[key];
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

	function showChartState(card, state, message) {
		var loading = card.querySelector('[data-chart-loading]');
		var empty = card.querySelector('[data-chart-empty]');
		var error = card.querySelector('[data-chart-error]');
		var canvas = card.querySelector('[data-chart-canvas]');
		var emptyCopy = card.querySelector('[data-chart-empty-copy]');
		var errorCopy = card.querySelector('[data-chart-error-copy]');

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
			if (emptyCopy && message) {
				emptyCopy.textContent = message;
			}

			empty.classList.remove('hidden');
		}

		if (state === 'error' && error) {
			if (errorCopy && message) {
				errorCopy.textContent = message;
			}

			error.classList.remove('hidden');
		}

		if (state === 'ready' && canvas) {
			canvas.classList.remove('hidden');
		}
	}

	function destroyChart(chartId) {
		if (chartInstances[chartId]) {
			chartInstances[chartId].destroy();
			delete chartInstances[chartId];
		}
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

	function normalizeCommerceExterieurChart(chart) {
		var nextChart = cloneChartConfig(chart);

		if (nextChart.id === 'trade-evolution') {
			nextChart.title = 'Évolution du commerce (Mds FCFA)';
			nextChart.description = 'Exportations et importations 2016-2025.';
			nextChart.period = '2016-2025';
			nextChart.source = 'Source : ANSD';
		}

		return nextChart;
	}

	function normalizeCommerceInterieurChart(chart) {
		var nextChart = cloneChartConfig(chart);

		if (nextChart.id === 'ihpc-desagrege') {
			nextChart.title = 'IHPC désagrégé — variations mensuelles (%)';
			nextChart.description = '10 dernières périodes · cliquer pour masquer / afficher';
			nextChart.period = '10 dernières périodes';
			nextChart.source = 'Source: ANSD — IHPC COICOP. Var. = (Indice[t] / Indice[t-1] - 1) × 100';
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

		return nextChart;
	}

	function normalizeIndustryChart(chart) {
		var nextChart = cloneChartConfig(chart);

		if (nextChart.id === 'industry-ippi') {
			nextChart.title = 'Indice des Prix à la Production (IPPI)';
			nextChart.description = "Lecture sur les 24 derniers mois pour l'ensemble.";
			nextChart.period = '24 derniers mois';
			nextChart.source = 'Source : ANSD - IPPI mensuel (Ensemble hors égrenage coton)';
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

		return nextChart;
	}

	function normalizePmeChart(chart) {
		var nextChart = cloneChartConfig(chart);

		if (nextChart.id === 'pme-immatriculations') {
			nextChart.title = "Immatriculations par secteur d'activité";
			nextChart.description = 'Entreprises individuelles — 2019–2024';
			nextChart.period = 'Entreprises individuelles — 2019–2024';
			nextChart.source = 'Source : ANSD/RNEA — BANIN 2024';
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

		return nextChart;
	}

	function normalizeChartForKey(key, chart) {
		if (!chart) {
			return null;
		}

		if (key === 'commerce-exterieur') {
			return normalizeCommerceExterieurChart(chart);
		}

		if (key === 'commerce-interieur') {
			return normalizeCommerceInterieurChart(chart);
		}

		if (key === 'industrie') {
			return normalizeIndustryChart(chart);
		}

		if (key === 'pme-pmi') {
			return normalizePmeChart(chart);
		}

		return cloneChartConfig(chart);
	}

	function getPreferredChart(payload, key, chartId) {
		var viewModel = payload && payload.data ? payload.data.view_model : null;
		var charts = viewModel && Array.isArray(viewModel.charts) ? viewModel.charts : [];
		var preferredId = sanitizeText(chartId) || preferredCharts[key];
		var chosen = null;

		if (!charts.length) {
			return null;
		}

		if (preferredId) {
			chosen = charts.find(function (chart) {
				return chart && sanitizeText(chart.id) === preferredId;
			}) || null;
		}

		if (!chosen) {
			chosen = charts[0];
		}

		return normalizeChartForKey(key, chosen);
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
		var cards = document.querySelectorAll('[data-dashboard-preview-card]');

		if (!cards.length || !window.Chart) {
			return;
		}

		cards.forEach(function (card) {
			var preview = card.querySelector('[data-dashboard-preview]');
			var key = sanitizeText(card.getAttribute('data-dashboard-key'));
			var chartId = sanitizeText(card.getAttribute('data-chart-id'));
			var url = preview ? preview.getAttribute('data-api-url') : '';
			var title = card.querySelector('[data-chart-title]');
			var description = card.querySelector('[data-chart-description]');
			var period = card.querySelector('[data-chart-period]');
			var source = card.querySelector('[data-chart-source-copy]');
			var canvas = card.querySelector('[data-chart-canvas]');

			if (!url || !canvas) {
				return;
			}

			destroyChart(chartId || key);

			fetchPayload(url)
				.then(function (payload) {
					var chart = getPreferredChart(payload, key, chartId);

					if (!chart) {
						showChartState(card, 'empty', 'Aucune configuration de graphique n a ete retournee.');
						return;
					}

					if (title && sanitizeText(chart.title)) {
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
					var chartInstance = new window.Chart(canvas.getContext('2d'), chartConfig);
					chartInstances[chartId || key] = chartInstance;
				})
				.catch(function () {
					showChartState(card, 'error', 'Impossible de charger ce graphique.');
				});
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot, { once: true });
	} else {
		boot();
	}
})();
