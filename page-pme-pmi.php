<?php
/**
 * PME / PMI page.
 *
 * @package CRADES_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$kpi_cards = array(
	array(
		'label'  => __( 'Immatriculations 2024', 'crades-theme' ),
		'note'   => __( 'Variation vs 2019', 'crades-theme' ),
		'accent' => 'text-emerald-600',
	),
	array(
		'label'  => __( 'Accès au crédit', 'crades-theme' ),
		'note'   => __( 'Ligne de crédit — BM 2024', 'crades-theme' ),
		'accent' => 'text-brand-gold',
	),
	array(
		'label'  => __( 'Croissance emploi', 'crades-theme' ),
		'note'   => __( '3 dernières années — BM 2024', 'crades-theme' ),
		'accent' => 'text-emerald-600',
	),
	array(
		'label'  => __( 'Entreprises exportatrices', 'crades-theme' ),
		'note'   => __( 'Enquête BM 2024', 'crades-theme' ),
		'accent' => 'text-red-600',
	),
);
?>
<div class="dashboard-page" data-dashboard-page data-dashboard-key="pme-pmi">
	<section class="bg-brand-navy py-16 lg:py-20">
		<div class="max-w-6xl mx-auto px-4 sm:px-6">
			<nav class="text-xs text-gray-400 mb-4">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hover:text-white"><?php esc_html_e( 'Accueil', 'crades-theme' ); ?></a>
				<span class="mx-2 text-gray-600">/</span>
				<span class="text-gray-300"><?php esc_html_e( 'PME / PMI', 'crades-theme' ); ?></span>
			</nav>
			<h1 class="font-display text-2xl lg:text-3xl text-white"><?php esc_html_e( 'Tableau de bord PME / PMI', 'crades-theme' ); ?></h1>
			<p class="text-gray-400 mt-2 max-w-2xl text-sm leading-relaxed">
				<?php esc_html_e( "Immatriculations, structure des entreprises, démographie entrepreneuriale et obstacles à l'activité au Sénégal. Données : 2019–2024.", 'crades-theme' ); ?>
			</p>
		</div>
	</section>

	<section class="bg-white border-b border-gray-100">
		<div class="max-w-6xl mx-auto px-4 sm:px-6 py-6">
			<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
				<?php foreach ( $kpi_cards as $card ) : ?>
					<article class="bg-brand-frost rounded-lg p-4 text-center shadow-sm" data-dashboard-kpi>
						<div class="text-lg md:text-xl font-bold <?php echo esc_attr( $card['accent'] ); ?>" data-dashboard-kpi-value>--</div>
						<p class="text-[11px] text-gray-500 mt-1" data-kpi-label><?php echo esc_html( $card['label'] ); ?></p>
						<p class="text-[10px] text-gray-400 mt-1" data-kpi-note><?php echo esc_html( $card['note'] ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="py-10 bg-gray-50">
		<div class="max-w-6xl mx-auto px-4 sm:px-6">
			<div id="pme-section-2-grid" class="grid lg:grid-cols-2 gap-6">
				<article id="pme-sector-card" class="bg-white border border-gray-100 rounded-lg p-5 shadow-sm" data-dashboard-chart data-chart-id="pme-immatriculations">
					<div class="flex items-center justify-between mb-4 gap-3">
						<h2 class="text-sm font-semibold text-gray-800" data-chart-title><?php esc_html_e( "Immatriculations par secteur d'activité", 'crades-theme' ); ?></h2>
						<span class="text-[10px] text-gray-400" data-chart-period><?php esc_html_e( 'Entreprises individuelles — 2019–2024', 'crades-theme' ); ?></span>
					</div>
					<div class="relative bg-gray-50 rounded-md p-3" style="height: 280px;">
						<div class="absolute inset-0 flex items-center justify-center text-xs text-gray-400" data-chart-loading><?php esc_html_e( 'Chargement du graphique...', 'crades-theme' ); ?></div>
						<div class="hidden absolute inset-0 flex items-center justify-center px-6 text-center text-xs text-gray-400" data-chart-empty><p data-chart-empty-copy><?php esc_html_e( 'Aucune donnée exploitable pour ce graphique.', 'crades-theme' ); ?></p></div>
						<div class="hidden absolute inset-0 flex items-center justify-center px-6 text-center text-xs text-red-500" data-chart-error><p data-chart-error-copy><?php esc_html_e( 'Impossible de charger ce graphique.', 'crades-theme' ); ?></p></div>
						<canvas class="h-full w-full hidden" data-chart-canvas></canvas>
					</div>
					<p class="text-[9px] text-gray-400 mt-3 text-center italic" data-chart-source-copy><?php esc_html_e( 'Source : ANSD/RNEA — BANIN 2024', 'crades-theme' ); ?></p>
				</article>

				<article id="pme-size-card" class="bg-white border border-gray-100 rounded-lg p-5 shadow-sm" data-dashboard-chart data-chart-id="pme-structure">
					<div class="flex items-center justify-between mb-4 gap-3">
						<h2 class="text-sm font-semibold text-gray-800" data-chart-title><?php esc_html_e( 'Répartition par Taille', 'crades-theme' ); ?></h2>
						<span class="text-[10px] text-gray-400" data-chart-period><?php esc_html_e( 'Enquête BM 2024', 'crades-theme' ); ?></span>
					</div>
					<div class="relative bg-gray-50 rounded-md p-3" style="height: 280px;">
						<div class="absolute inset-0 flex items-center justify-center text-xs text-gray-400" data-chart-loading><?php esc_html_e( 'Chargement du graphique...', 'crades-theme' ); ?></div>
						<div class="hidden absolute inset-0 flex items-center justify-center px-6 text-center text-xs text-gray-400" data-chart-empty><p data-chart-empty-copy><?php esc_html_e( 'Aucune donnée exploitable pour ce graphique.', 'crades-theme' ); ?></p></div>
						<div class="hidden absolute inset-0 flex items-center justify-center px-6 text-center text-xs text-red-500" data-chart-error><p data-chart-error-copy><?php esc_html_e( 'Impossible de charger ce graphique.', 'crades-theme' ); ?></p></div>
						<canvas class="h-full w-full hidden" data-chart-canvas></canvas>
					</div>
					<p class="text-[9px] text-gray-400 mt-3 text-center italic" data-chart-source-copy><?php esc_html_e( 'Source : Banque Mondiale — Enterprise Surveys 2024', 'crades-theme' ); ?></p>
				</article>
			</div>
		</div>
	</section>

	<section class="py-10">
		<div class="max-w-6xl mx-auto px-4 sm:px-6">
			<div id="pme-section-3-grid" class="grid lg:grid-cols-2 gap-6">
				<article id="pme-geo-card" class="bg-white border border-gray-100 rounded-lg p-5 shadow-sm">
					<div class="flex items-center justify-between mb-4 gap-3">
						<h2 class="text-sm font-semibold text-gray-800"><?php esc_html_e( 'Répartition Géographique des Immatriculations', 'crades-theme' ); ?></h2>
						<span class="text-[10px] text-gray-400"><?php esc_html_e( '% immatriculations 2024', 'crades-theme' ); ?></span>
					</div>
					<div id="pme-region-map" class="bg-gray-50 rounded-md p-3 relative" style="height: 280px;"></div>
					<div class="flex items-center justify-center gap-1 mt-2">
						<span class="text-[9px] text-gray-400 mr-1"><?php esc_html_e( 'Forte', 'crades-theme' ); ?></span>
						<div class="w-4 h-2.5 rounded-sm" style="background:#1A05A2;"></div>
						<div class="w-4 h-2.5 rounded-sm" style="background:#8F0177;"></div>
						<div class="w-4 h-2.5 rounded-sm" style="background:#DE1A58;"></div>
						<div class="w-4 h-2.5 rounded-sm" style="background:#FBC4A0;"></div>
						<span class="text-[9px] text-gray-400 ml-1"><?php esc_html_e( 'Faible', 'crades-theme' ); ?></span>
					</div>
					<p class="text-[9px] text-gray-400 mt-1 text-center italic"><?php esc_html_e( 'Source : ANSD/RNEA — BANIN 2024 | Fond : GADM 4.1', 'crades-theme' ); ?></p>
				</article>

				<article id="pme-age-card" class="bg-white border border-gray-100 rounded-lg p-5 shadow-sm" data-dashboard-chart data-chart-id="pme-enquete">
					<div class="flex items-center justify-between mb-4 gap-3">
						<h2 class="text-sm font-semibold text-gray-800" data-chart-title><?php esc_html_e( "Répartition par Tranche d'Age", 'crades-theme' ); ?></h2>
						<span class="text-[10px] text-gray-400" data-chart-period><?php esc_html_e( 'Entrepreneurs individuels 2024', 'crades-theme' ); ?></span>
					</div>
					<div class="relative bg-gray-50 rounded-md p-3" style="height: 280px;">
						<div class="absolute inset-0 flex items-center justify-center text-xs text-gray-400" data-chart-loading><?php esc_html_e( 'Chargement du graphique...', 'crades-theme' ); ?></div>
						<div class="hidden absolute inset-0 flex items-center justify-center px-6 text-center text-xs text-gray-400" data-chart-empty><p data-chart-empty-copy><?php esc_html_e( 'Aucune donnée exploitable pour ce graphique.', 'crades-theme' ); ?></p></div>
						<div class="hidden absolute inset-0 flex items-center justify-center px-6 text-center text-xs text-red-500" data-chart-error><p data-chart-error-copy><?php esc_html_e( 'Impossible de charger ce graphique.', 'crades-theme' ); ?></p></div>
						<canvas class="h-full w-full hidden" data-chart-canvas></canvas>
					</div>
					<p class="text-[9px] text-gray-400 mt-3 text-center italic" data-chart-source-copy><?php esc_html_e( 'Source : ANSD/RNEA — BANIN 2024', 'crades-theme' ); ?></p>
				</article>
			</div>
		</div>
	</section>

	<section class="py-10 bg-gray-50">
		<div class="max-w-6xl mx-auto px-4 sm:px-6">
			<div class="grid lg:grid-cols-2 gap-6">
				<article class="bg-white border border-gray-100 rounded-lg p-5 shadow-sm" data-dashboard-chart data-chart-id="pme-macro">
					<div class="flex items-center justify-between mb-4 gap-3">
						<h2 class="text-sm font-semibold text-gray-800" data-chart-title><?php esc_html_e( 'Répartition par Régime Juridique', 'crades-theme' ); ?></h2>
						<span class="text-[10px] text-gray-400" data-chart-period><?php esc_html_e( '2024', 'crades-theme' ); ?></span>
					</div>
					<div class="relative bg-gray-50 rounded-md p-3" style="height: 280px;">
						<div class="absolute inset-0 flex items-center justify-center text-xs text-gray-400" data-chart-loading><?php esc_html_e( 'Chargement du graphique...', 'crades-theme' ); ?></div>
						<div class="hidden absolute inset-0 flex items-center justify-center px-6 text-center text-xs text-gray-400" data-chart-empty><p data-chart-empty-copy><?php esc_html_e( 'Aucune donnée exploitable pour ce graphique.', 'crades-theme' ); ?></p></div>
						<div class="hidden absolute inset-0 flex items-center justify-center px-6 text-center text-xs text-red-500" data-chart-error><p data-chart-error-copy><?php esc_html_e( 'Impossible de charger ce graphique.', 'crades-theme' ); ?></p></div>
						<canvas class="h-full w-full hidden" data-chart-canvas></canvas>
					</div>
					<p class="text-[9px] text-gray-400 mt-3 text-center italic" data-chart-source-copy><?php esc_html_e( 'Source : ANSD/RNEA — BANIN 2024', 'crades-theme' ); ?></p>
				</article>

				<article class="bg-white border border-gray-100 rounded-lg p-5 shadow-sm">
					<div class="flex items-center justify-between mb-4 gap-3">
						<h2 class="text-sm font-semibold text-gray-800"><?php esc_html_e( "Obstacles à l'Activité", 'crades-theme' ); ?></h2>
						<span class="text-[10px] text-gray-400"><?php esc_html_e( 'Enquête BM 2024', 'crades-theme' ); ?></span>
					</div>
					<div class="bg-gray-50 rounded-md p-2" style="height: 280px;">
						<div id="obstacles-treemap" class="w-full h-full relative"></div>
					</div>
					<p class="text-[9px] text-gray-400 mt-3 text-center italic"><?php esc_html_e( 'Source : Banque Mondiale — Enterprise Surveys 2024', 'crades-theme' ); ?></p>
				</article>
			</div>
		</div>
	</section>

	<section class="bg-brand-frost border-t border-brand-ice/50 py-8">
		<div class="max-w-6xl mx-auto px-4 sm:px-6">
			<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
				<div>
					<p class="text-xs text-gray-500"><i class="fas fa-info-circle mr-1" aria-hidden="true"></i><?php esc_html_e( 'Sources : ANSD (NINEA / BANIN / BDEF) et Banque mondiale (Enterprise Surveys).', 'crades-theme' ); ?></p>
					<p class="text-[10px] text-gray-400 mt-1"><?php esc_html_e( 'Données mises à jour automatiquement depuis les sources officielles.', 'crades-theme' ); ?></p>
				</div>
				<div class="flex items-center gap-3">
					<a href="<?php echo esc_url( rest_url( 'ministere/v1/pme-pmi' ) ); ?>" target="_blank" rel="noopener" class="text-[10px] bg-white border border-gray-200 px-3 py-1.5 rounded hover:border-gray-300 text-gray-500 transition-colors">
						<i class="fas fa-code mr-1" aria-hidden="true"></i> <?php esc_html_e( 'API JSON', 'crades-theme' ); ?>
					</a>
				</div>
			</div>
		</div>
	</section>
</div>
<?php
get_footer();

