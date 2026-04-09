<?php
/**
 * Industrie page.
 *
 * @package CRADES_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$kpi_cards = array(
	array(
		'label'  => __( 'IHPI – Production', 'crades-theme' ),
		'note'   => __( 'Cumul 2025', 'crades-theme' ),
		'badge'  => __( 'ANSD', 'crades-theme' ),
		'accent' => 'text-emerald-600',
	),
	array(
		'label'  => __( "ICAI – Chiffre d'Affaires", 'crades-theme' ),
		'note'   => __( 'Var. annuelle T3 2025', 'crades-theme' ),
		'badge'  => __( 'ANSD', 'crades-theme' ),
		'accent' => 'text-emerald-600',
	),
	array(
		'label'  => __( 'IPPI – Prix Production', 'crades-theme' ),
		'note'   => __( 'Var. annuelle Jan. 2026', 'crades-theme' ),
		'badge'  => __( 'ANSD', 'crades-theme' ),
		'accent' => 'text-emerald-600',
	),
	array(
		'label'  => __( 'CIP – Compétitivité', 'crades-theme' ),
		'note'   => __( 'Score et rang mondial', 'crades-theme' ),
		'badge'  => __( 'UNIDO', 'crades-theme' ),
		'accent' => 'text-red-600',
	),
	array(
		'label'  => __( 'TUCP – Capacités', 'crades-theme' ),
		'note'   => __( '2025-T3', 'crades-theme' ),
		'badge'  => __( 'BCEAO', 'crades-theme' ),
		'accent' => 'text-brand-gold',
	),
);

$chart_sections = array(
	array(
		array(
			'id'          => 'industry-ihpi',
			'title'       => __( 'Indice de Production Industrielle (IHPI)', 'crades-theme' ),
			'description' => __( 'Ensemble et branches desagregees sur les 12 dernieres periodes.', 'crades-theme' ),
			'period'      => __( '12 dernieres periodes', 'crades-theme' ),
			'source'      => __( 'Source : ANSD - IHPI mensuel', 'crades-theme' ),
		),
		array(
			'id'          => 'industry-icai',
			'title'       => __( "Indice du Chiffre d'Affaires Industriel (ICAI)", 'crades-theme' ),
			'description' => __( 'Ensemble et branches principales sur les 12 dernieres periodes.', 'crades-theme' ),
			'period'      => __( '12 dernieres periodes', 'crades-theme' ),
			'source'      => __( 'Source : ANSD - ICAI trimestriel', 'crades-theme' ),
		),
	),
	array(
		array(
			'id'          => 'industry-ippi',
			'title'       => __( 'Indice des Prix a la Production (IPPI)', 'crades-theme' ),
			'description' => __( "Lecture sur les 24 derniers mois pour l'ensemble.", 'crades-theme' ),
			'period'      => __( '24 derniers mois', 'crades-theme' ),
			'source'      => __( "Source : ANSD - IPPI mensuel (Ensemble hors egrenage coton)", 'crades-theme' ),
		),
		array(
			'id'          => 'industry-capacity',
			'title'       => __( "Taux d'Utilisation des Capacites Productives", 'crades-theme' ),
			'description' => __( 'Serie BCEAO de suivi de la capacite productive.', 'crades-theme' ),
			'period'      => __( 'Serie BCEAO', 'crades-theme' ),
			'source'      => __( 'Source : BCEAO - Bulletin Trimestriel des Statistiques', 'crades-theme' ),
		),
	),
	array(
		array(
			'id'          => 'industry-pci',
			'title'       => __( 'Capacites Productives (PCI - UNCTAD)', 'crades-theme' ),
			'description' => __( 'Comparaison 2000 vs derniere annee disponible sur les dimensions structurelles.', 'crades-theme' ),
			'period'      => __( '2000 vs recent', 'crades-theme' ),
			'source'      => __( 'Source : UNCTAD - Indice des Capacites Productives (0-100)', 'crades-theme' ),
		),
		array(
			'id'          => 'industry-pib',
			'title'       => __( 'Indice de Competitivite Industrielle (CIP)', 'crades-theme' ),
			'description' => __( 'Score et rang mondial dans un meme graphique.', 'crades-theme' ),
			'period'      => __( 'Serie historique', 'crades-theme' ),
			'source'      => __( 'Source : UNIDO CIP Index. Score plus eleve = meilleure competitivite.', 'crades-theme' ),
		),
	),
);
?>
<div class="dashboard-page" data-dashboard-page data-dashboard-key="industrie">
	<section class="bg-brand-navy py-16 lg:py-20">
		<div class="max-w-6xl mx-auto px-4 sm:px-6">
			<nav class="text-xs text-gray-400 mb-4">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hover:text-white"><?php esc_html_e( 'Accueil', 'crades-theme' ); ?></a>
				<span class="mx-2 text-gray-600">/</span>
				<span class="text-gray-300"><?php esc_html_e( 'Industrie', 'crades-theme' ); ?></span>
			</nav>
			<h1 class="font-display text-2xl lg:text-3xl text-white"><?php esc_html_e( 'Tableau de bord Industrie', 'crades-theme' ); ?></h1>
			<p class="text-gray-400 mt-2 max-w-3xl text-sm leading-relaxed">
				<?php esc_html_e( "Production, prix, chiffre d'affaires, competitivite et transformation structurelle du secteur industriel senegalais. Cette page reprend la mise en page du tableau de bord Hono avec ses grandes sections.", 'crades-theme' ); ?>
			</p>
		</div>
	</section>

	<section class="bg-white border-b border-gray-100">
		<div class="max-w-6xl mx-auto px-4 sm:px-6 py-6">
			<div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
				<?php foreach ( $kpi_cards as $card ) : ?>
					<article class="bg-brand-frost rounded-lg p-4 text-center shadow-sm" data-dashboard-kpi>
						<div class="text-lg md:text-xl leading-none font-bold <?php echo esc_attr( $card['accent'] ); ?>" data-dashboard-kpi-value>--</div>
						<p class="text-[11px] text-gray-500 mt-1 leading-tight" data-kpi-label><?php echo esc_html( $card['label'] ); ?></p>
						<p class="text-[10px] text-gray-400 mt-1 leading-tight" data-kpi-note><?php echo esc_html( $card['note'] ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="py-10 bg-gray-50">
		<div class="max-w-6xl mx-auto px-4 sm:px-6">
			<?php foreach ( $chart_sections as $section ) : ?>
				<div class="grid lg:grid-cols-2 gap-6 mb-6 last:mb-0">
					<?php foreach ( $section as $chart ) : ?>
						<?php
						$is_primary_index_chart = in_array( $chart['id'], array( 'industry-ihpi', 'industry-icai' ), true );
						$is_operational_chart   = in_array( $chart['id'], array( 'industry-ippi', 'industry-capacity' ), true );
						$is_structural_chart    = in_array( $chart['id'], array( 'industry-pci', 'industry-pib' ), true );
						$chart_height           = ( $is_primary_index_chart || $is_operational_chart || $is_structural_chart ) ? '280px' : '320px';
						$source_margin_class    = 'industry-pib' === $chart['id'] ? 'mt-3' : 'mt-2';
						?>
						<article class="bg-white border border-gray-100 rounded-lg p-5 shadow-sm" data-dashboard-chart data-chart-id="<?php echo esc_attr( $chart['id'] ); ?>">
							<div class="flex flex-wrap items-center justify-between gap-2 <?php echo $is_primary_index_chart ? 'mb-2' : 'mb-4'; ?>">
								<div>
									<h2 class="text-sm font-semibold text-gray-800" data-chart-title><?php echo esc_html( $chart['title'] ); ?></h2>
									<?php if ( $is_primary_index_chart ) : ?>
										<p class="text-[10px] text-gray-500 mt-0.5" data-chart-description><?php echo esc_html( $chart['description'] ); ?></p>
									<?php endif; ?>
								</div>
								<span class="text-[10px] text-gray-400" data-chart-period><?php echo esc_html( $chart['period'] ); ?></span>
							</div>
							<div class="relative bg-gray-50 rounded-md p-3" style="height: <?php echo esc_attr( $chart_height ); ?>;">
								<div class="absolute inset-0 flex items-center justify-center text-xs text-gray-400" data-chart-loading><?php esc_html_e( 'Chargement du graphique...', 'crades-theme' ); ?></div>
								<div class="hidden absolute inset-0 flex items-center justify-center px-6 text-center text-xs text-gray-400" data-chart-empty><p data-chart-empty-copy><?php esc_html_e( 'Aucune donnee exploitable pour ce graphique.', 'crades-theme' ); ?></p></div>
								<div class="hidden absolute inset-0 flex items-center justify-center px-6 text-center text-xs text-red-500" data-chart-error><p data-chart-error-copy><?php esc_html_e( 'Impossible de charger ce graphique.', 'crades-theme' ); ?></p></div>
								<div class="h-full">
									<canvas class="h-full w-full hidden" data-chart-canvas></canvas>
								</div>
							</div>
							<?php if ( $is_primary_index_chart ) : ?>
								<div class="mt-2" data-industry-chart-legend></div>
								<div class="mt-2 flex items-center justify-between gap-3">
									<div class="text-xs text-gray-500">
										<?php esc_html_e( 'Derniere valeur Ensemble :', 'crades-theme' ); ?>
										<strong class="<?php echo esc_attr( 'industry-ihpi' === $chart['id'] ? 'text-brand-blue' : 'text-brand-navy' ); ?>" data-industry-chart-latest>&mdash;</strong>
									</div>
									<div class="text-[10px] text-gray-400" data-industry-chart-base>
										<?php echo esc_html( 'industry-ihpi' === $chart['id'] ? __( 'Base 100 = 2006', 'crades-theme' ) : __( 'Base 100 = 2016', 'crades-theme' ) ); ?>
									</div>
								</div>
							<?php elseif ( 'industry-ippi' === $chart['id'] ) : ?>
								<div class="mt-2 flex items-center justify-between gap-3">
									<span class="text-xs text-gray-500">
										<?php esc_html_e( 'Derniere valeur :', 'crades-theme' ); ?>
										<strong class="text-rose-600" data-industry-chart-latest>&mdash;</strong>
									</span>
									<span class="text-[10px] text-gray-400" data-industry-chart-base><?php esc_html_e( 'Base 100 = 2015', 'crades-theme' ); ?></span>
								</div>
							<?php elseif ( 'industry-capacity' === $chart['id'] ) : ?>
								<div class="mt-2 flex items-center justify-between gap-3">
									<span class="text-xs text-gray-500">
										<?php esc_html_e( 'Derniere valeur :', 'crades-theme' ); ?>
										<strong class="text-emerald-600" data-industry-chart-latest>&mdash;</strong>
									</span>
									<span class="text-[10px] text-gray-400" data-industry-chart-base><?php esc_html_e( 'Unite : %', 'crades-theme' ); ?></span>
								</div>
							<?php elseif ( 'industry-pci' === $chart['id'] ) : ?>
								<div class="mt-3 grid grid-cols-2 gap-2" data-industry-pci-grid></div>
							<?php endif; ?>
							<p class="text-[9px] text-gray-400 <?php echo esc_attr( $source_margin_class ); ?> text-center italic" data-chart-source-copy><?php echo esc_html( $chart['source'] ); ?></p>
						</article>
					<?php endforeach; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="py-10">
		<div class="max-w-6xl mx-auto px-4 sm:px-6">
			<article class="bg-white border border-gray-100 rounded-lg overflow-hidden shadow-sm" data-dashboard-table data-table-id="production-dpee" data-table-style="industry-dpee">
				<div class="bg-brand-frost px-4 py-3 border-b border-brand-ice/50">
					<div class="flex items-center justify-between gap-3">
						<h3 class="text-sm font-semibold text-gray-800 inline-flex items-center gap-2" data-dashboard-table-title>
							<i class="fas fa-table text-brand-navy" aria-hidden="true"></i>
							<span><?php esc_html_e( 'Production Industrielle', 'crades-theme' ); ?></span>
						</h3>
						<span class="text-[10px] text-gray-400"><?php esc_html_e( '12 dernieres periodes', 'crades-theme' ); ?></span>
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
					<p class="text-[9px] text-gray-400 text-center italic"><?php esc_html_e( 'Source : DPEE - Production Industrielle. ▲ hausse / ▼ baisse vs periode precedente.', 'crades-theme' ); ?></p>
				</div>
			</article>
		</div>
	</section>

	<section class="bg-brand-frost border-t border-brand-ice/50 py-8">
		<div class="max-w-6xl mx-auto px-4 sm:px-6">
			<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
				<div>
					<p class="text-xs text-gray-500"><i class="fas fa-info-circle mr-1" aria-hidden="true"></i><?php esc_html_e( 'Sources : ANSD, DPEE, BCEAO, UNCTAD, UNIDO', 'crades-theme' ); ?></p>
					<p class="text-[10px] text-gray-400 mt-1"><?php esc_html_e( 'Donnees Industrie consolidees depuis les flux officiels et alignees sur la structure du tableau de bord principal.', 'crades-theme' ); ?></p>
				</div>
				<div class="flex items-center gap-3">
					<a href="<?php echo esc_url( rest_url( 'ministere/v1/industrie' ) ); ?>" target="_blank" rel="noopener" class="text-[10px] bg-white border border-gray-200 px-3 py-1.5 rounded hover:border-gray-300 text-gray-500 transition-colors">
						<i class="fas fa-code mr-1" aria-hidden="true"></i> <?php esc_html_e( 'API JSON', 'crades-theme' ); ?>
					</a>
				</div>
			</div>
		</div>
	</section>
</div>
<?php
get_footer();
