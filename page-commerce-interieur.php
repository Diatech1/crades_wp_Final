<?php
/**
 * Commerce intérieur page.
 *
 * @package CRADES_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$kpi_cards = array(
	array(
		'label'  => __( 'IHPC Global', 'crades-theme' ),
		'note'   => __( 'Base 100 = 2023', 'crades-theme' ),
		'badge'  => __( 'ANSD', 'crades-theme' ),
		'accent' => 'text-brand-blue',
	),
	array(
		'label'  => __( 'Inflation annuelle', 'crades-theme' ),
		'note'   => __( 'Seuil UEMOA : 3%', 'crades-theme' ),
		'badge'  => __( 'ANSD', 'crades-theme' ),
		'accent' => 'text-brand-navy',
	),
	array(
		'label'  => __( 'ICAI Commerce de gros', 'crades-theme' ),
		'note'   => __( 'DPEE', 'crades-theme' ),
		'badge'  => __( 'DPEE', 'crades-theme' ),
		'accent' => 'text-emerald-600',
	),
	array(
		'label'  => __( 'ICAI Commerce de détail', 'crades-theme' ),
		'note'   => __( 'DPEE', 'crades-theme' ),
		'badge'  => __( 'DPEE', 'crades-theme' ),
		'accent' => 'text-brand-gold',
	),
);

$chart_rows = array(
	array(
		array(
			'id'          => 'ihpc-desagrege',
			'title'       => __( 'IHPC désagrégé — variations mensuelles (%)', 'crades-theme' ),
			'description' => __( '10 dernières périodes · cliquer pour masquer / afficher', 'crades-theme' ),
			'period'      => __( '10 dernières périodes', 'crades-theme' ),
			'source'      => __( 'Source: ANSD — IHPC COICOP. Var. = (Indice[t] / Indice[t-1] - 1) × 100', 'crades-theme' ),
		),
		array(
			'id'          => 'inflation-threshold',
			'title'       => __( 'Inflation Annuelle vs Seuil UEMOA (3%)', 'crades-theme' ),
			'description' => '',
			'period'      => __( 'Années complètes', 'crades-theme' ),
			'source'      => __( 'Source: ANSD (calculé depuis IHPC annuel). 2023 exclu (rebasement).', 'crades-theme' ),
		),
	),
	array(
		array(
			'id'          => 'icai-series',
			'title'       => __( 'ICAI Commerce', 'crades-theme' ),
			'description' => '',
			'period'      => __( '10 dernières périodes', 'crades-theme' ),
			'source'      => __( 'Source: DPEE', 'crades-theme' ),
		),
		array(
			'id'          => 'icai-breakdown',
			'title'       => __( 'Répartition ICAI par Catégorie', 'crades-theme' ),
			'description' => '',
			'period'      => __( 'Dernière lecture', 'crades-theme' ),
			'source'      => __( 'Source: DPEE', 'crades-theme' ),
		),
	),
);
?>
<div class="dashboard-page" data-dashboard-page data-dashboard-key="commerce-interieur">
	<section class="bg-brand-navy py-16 lg:py-20">
		<div class="max-w-6xl mx-auto px-4 sm:px-6">
			<nav class="text-xs text-gray-400 mb-4">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hover:text-white"><?php esc_html_e( 'Accueil', 'crades-theme' ); ?></a>
				<span class="mx-2 text-gray-600">/</span>
				<span class="text-gray-300"><?php esc_html_e( 'Commerce intérieur', 'crades-theme' ); ?></span>
			</nav>
			<h1 class="font-display text-2xl lg:text-3xl text-white"><?php esc_html_e( 'Commerce intérieur du Sénégal', 'crades-theme' ); ?></h1>
			<p class="text-gray-400 mt-2 max-w-2xl text-sm leading-relaxed">
				<?php esc_html_e( "Indices des prix (IHPC), indicateur conjoncturel d'activité (ICAI) et dynamique du commerce intérieur.", 'crades-theme' ); ?>
				<?php esc_html_e( 'Mise à jour :', 'crades-theme' ); ?>
				<span class="text-brand-gold font-medium" data-dashboard-year-label><?php esc_html_e( 'Mars 2026', 'crades-theme' ); ?></span>.
			</p>
		</div>
	</section>

	<section class="bg-white border-b border-gray-100">
		<div class="max-w-6xl mx-auto px-4 sm:px-6 py-6">
			<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
				<?php foreach ( $kpi_cards as $card ) : ?>
					<article class="bg-brand-frost rounded-lg p-4 text-center" data-dashboard-kpi>
						<div class="text-lg md:text-xl font-bold <?php echo esc_attr( $card['accent'] ); ?>" data-dashboard-kpi-value>--</div>
						<p class="text-[11px] text-gray-500 mt-1" data-kpi-label><?php echo esc_html( $card['label'] ); ?></p>
						<p class="text-[10px] text-gray-400 mt-1" data-kpi-note><?php echo esc_html( $card['note'] ); ?></p>
						<div class="text-[9px] text-gray-400 mt-2 italic" data-kpi-badge><?php echo esc_html( $card['badge'] ); ?></div>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="py-10 bg-gray-50">
		<div class="max-w-6xl mx-auto px-4 sm:px-6">
			<?php foreach ( $chart_rows as $row ) : ?>
				<div class="grid lg:grid-cols-2 gap-6 mb-6 last:mb-0">
					<?php foreach ( $row as $chart ) : ?>
						<article class="bg-white border border-gray-100 rounded-lg p-5" data-dashboard-chart data-chart-id="<?php echo esc_attr( $chart['id'] ); ?>">
							<div class="flex flex-wrap items-center justify-between gap-2 mb-4">
								<div>
									<h2 class="text-sm font-semibold text-gray-800" data-chart-title><?php echo esc_html( $chart['title'] ); ?></h2>
									<p class="text-[10px] text-gray-500 mt-1" data-chart-description><?php echo esc_html( $chart['description'] ); ?></p>
								</div>
								<span class="text-[10px] text-gray-400" data-chart-period><?php echo esc_html( $chart['period'] ); ?></span>
							</div>
							<div class="relative bg-gray-50 rounded-md p-3" style="height: 280px;">
								<div class="absolute inset-0 flex items-center justify-center text-xs text-gray-400" data-chart-loading><?php esc_html_e( 'Chargement du graphique...', 'crades-theme' ); ?></div>
								<div class="hidden absolute inset-0 flex items-center justify-center px-6 text-center text-xs text-gray-400" data-chart-empty><p data-chart-empty-copy><?php esc_html_e( 'Aucune donnée exploitable pour ce graphique.', 'crades-theme' ); ?></p></div>
								<div class="hidden absolute inset-0 flex items-center justify-center px-6 text-center text-xs text-red-500" data-chart-error><p data-chart-error-copy><?php esc_html_e( 'Impossible de charger ce graphique.', 'crades-theme' ); ?></p></div>
								<canvas class="h-full w-full hidden" data-chart-canvas></canvas>
							</div>
							<p class="text-[9px] text-gray-400 mt-3 text-center italic" data-chart-source-copy><?php echo esc_html( $chart['source'] ); ?></p>
						</article>
					<?php endforeach; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="py-10">
		<div class="max-w-6xl mx-auto px-4 sm:px-6">
			<article class="bg-white border border-gray-100 rounded-lg overflow-hidden" data-dashboard-table data-table-id="denrees-base" data-table-style="commerce-interieur-denrees">
				<div class="bg-brand-frost px-4 py-3 border-b border-brand-ice/50">
					<div class="flex items-center justify-between gap-3">
						<h3 class="text-sm font-semibold text-gray-800 inline-flex items-center gap-2" data-dashboard-table-title>
							<span class="inline-flex items-center gap-1">
								<svg class="w-4 h-4" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
									<path d="M24 8 L36 20 H28 V40 H20 V20 H12 Z" fill="#00B66A"/>
								</svg>
								<span data-dashboard-table-title-copy><?php esc_html_e( 'Prix des denrées de base (FCFA/kg)', 'crades-theme' ); ?></span>
							</span>
						</h3>
						<span class="text-[10px] text-gray-400" data-dashboard-table-period><?php esc_html_e( 'Chargement...', 'crades-theme' ); ?></span>
					</div>
				</div>
				<div class="px-4 py-10 text-center text-xs text-gray-400" data-table-loading><?php esc_html_e( 'Chargement du tableau...', 'crades-theme' ); ?></div>
				<div class="hidden px-4 py-10 text-center text-xs text-gray-400" data-table-empty><span data-table-empty-copy><?php esc_html_e( 'Aucune ligne disponible.', 'crades-theme' ); ?></span></div>
				<div class="hidden px-4 py-10 text-center text-xs text-red-500" data-table-error><span data-table-error-copy><?php esc_html_e( 'Impossible de charger le tableau.', 'crades-theme' ); ?></span></div>
				<div class="hidden overflow-x-auto" data-table-wrap>
					<table class="w-full text-xs">
						<thead data-dashboard-table-head></thead>
						<tbody data-dashboard-table-body></tbody>
					</table>
				</div>
				<div class="px-4 py-3 border-t border-gray-100">
					<p class="text-[9px] text-gray-400 text-center italic" data-dashboard-table-source-copy><?php esc_html_e( 'Chargement de la source...', 'crades-theme' ); ?></p>
				</div>
			</article>
		</div>
	</section>

	<section class="bg-white py-8 border-t border-gray-100">
		<div class="max-w-6xl mx-auto px-4 sm:px-6">
			<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
				<div>
					<p class="text-xs text-gray-500"><i class="fas fa-info-circle mr-1" aria-hidden="true"></i><?php esc_html_e( 'Sources : ANSD, DPEE, World Bank, UEMOA', 'crades-theme' ); ?></p>
					<p class="text-[10px] text-gray-400 mt-1" data-dashboard-footer-note><?php esc_html_e( 'Données Commerce Intérieur actualisées Mars 2026 - IHPC Base 100=2023', 'crades-theme' ); ?></p>
				</div>
				<div class="flex items-center gap-3">
					<a href="<?php echo esc_url( rest_url( 'ministere/v1/commerce-interieur' ) ); ?>" target="_blank" rel="noopener" class="text-[10px] bg-brand-frost border border-brand-ice px-3 py-1.5 rounded hover:bg-brand-ice/50 text-gray-700 transition-colors">
						<i class="fas fa-code mr-1" aria-hidden="true"></i> <?php esc_html_e( 'API JSON', 'crades-theme' ); ?>
					</a>
				</div>
			</div>
		</div>
	</section>
</div>
<?php
get_footer();
