<?php
/**
 * Commerce extérieur page.
 *
 * @package CRADES_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$latest_year = 2025;
$kpi_cards   = array(
	array(
		'label'  => __( 'Exportations', 'crades-theme' ),
		'note'   => __( '+6,9% vs 2024', 'crades-theme' ),
		'badge'  => __( 'ANSD', 'crades-theme' ),
		'accent' => 'text-brand-blue',
	),
	array(
		'label'  => __( 'Importations', 'crades-theme' ),
		'note'   => __( '+2,2% vs 2024', 'crades-theme' ),
		'badge'  => __( 'ANSD', 'crades-theme' ),
		'accent' => 'text-brand-navy',
	),
	array(
		'label'  => __( 'Balance commerciale', 'crades-theme' ),
		'note'   => __( 'Déficit', 'crades-theme' ),
		'badge'  => __( 'ANSD', 'crades-theme' ),
		'accent' => 'text-emerald-600',
	),
	array(
		'label'  => __( 'Volume commercial', 'crades-theme' ),
		'note'   => __( 'Export + import', 'crades-theme' ),
		'badge'  => __( 'ANSD', 'crades-theme' ),
		'accent' => 'text-brand-gold',
	),
	array(
		'label'  => __( 'IPCE Export', 'crades-theme' ),
		'note'   => __( 'Base 100 = 2020', 'crades-theme' ),
		'badge'  => __( 'Indice', 'crades-theme' ),
		'accent' => 'text-brand-blue',
	),
	array(
		'label'  => __( 'IPCE Import', 'crades-theme' ),
		'note'   => __( 'Base 100 = 2020', 'crades-theme' ),
		'badge'  => __( 'Indice', 'crades-theme' ),
		'accent' => 'text-brand-navy',
	),
	array(
		'label'  => __( 'Termes de l’échange', 'crades-theme' ),
		'note'   => __( 'Défavorable', 'crades-theme' ),
		'badge'  => __( 'Calculé', 'crades-theme' ),
		'accent' => 'text-emerald-600',
	),
	array(
		'label'  => __( 'Taux de couverture', 'crades-theme' ),
		'note'   => __( 'Export / import', 'crades-theme' ),
		'badge'  => __( 'Calculé', 'crades-theme' ),
		'accent' => 'text-brand-gold',
	),
);
$chart_rows  = array(
	array(
		array(
			'id'          => 'trade-evolution',
			'title'       => __( 'Évolution du commerce (Mds FCFA)', 'crades-theme' ),
			'description' => __( 'Exportations et importations 2016-2025.', 'crades-theme' ),
			'period'      => '2016-2025',
		),
		array(
			'id'          => 'trade-balance',
			'title'       => __( 'Balance commerciale (Mds FCFA)', 'crades-theme' ),
			'description' => __( 'Solde annuel des échanges.', 'crades-theme' ),
			'period'      => '2016-2025',
		),
	),
	array(
		array(
			'id'          => 'export-partners',
			'title'       => __( 'Top destinations d’exportation', 'crades-theme' ),
			'description' => __( 'Principales destinations 2025.', 'crades-theme' ),
			'period'      => (string) $latest_year,
		),
		array(
			'id'          => 'import-partners',
			'title'       => __( 'Top fournisseurs (importations)', 'crades-theme' ),
			'description' => __( 'Principaux fournisseurs 2025.', 'crades-theme' ),
			'period'      => (string) $latest_year,
		),
	),
	array(
		array(
			'id'          => 'export-sectors',
			'title'       => __( 'Exportations par secteur (M USD)', 'crades-theme' ),
			'description' => __( 'Ventilation sectorielle des exportations.', 'crades-theme' ),
			'period'      => (string) $latest_year,
		),
		array(
			'id'          => 'import-sectors',
			'title'       => __( 'Importations par secteur (M USD)', 'crades-theme' ),
			'description' => __( 'Ventilation sectorielle des importations.', 'crades-theme' ),
			'period'      => (string) $latest_year,
		),
	),
);
?>
<div class="dashboard-page" data-dashboard-page data-dashboard-key="commerce-exterieur">
	<section class="bg-brand-navy py-16 lg:py-20">
		<div class="max-w-6xl mx-auto px-4 sm:px-6">
			<nav class="text-xs text-gray-400 mb-4">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hover:text-white"><?php esc_html_e( 'Accueil', 'crades-theme' ); ?></a>
				<span class="mx-2 text-gray-600">/</span>
				<span class="text-gray-300"><?php esc_html_e( 'Commerce extérieur', 'crades-theme' ); ?></span>
			</nav>
			<h1 class="font-display text-2xl lg:text-3xl text-white"><?php esc_html_e( 'Commerce extérieur du Sénégal', 'crades-theme' ); ?></h1>
			<p class="text-gray-400 mt-2 max-w-3xl text-sm leading-relaxed">
				<?php esc_html_e( 'Données d’importation et d’exportation issues de l’ANSD (Note d’Analyse du Commerce Extérieur 2024). Dernières données disponibles :', 'crades-theme' ); ?>
				<span class="text-brand-gold font-medium"><?php echo esc_html( (string) $latest_year ); ?></span>.
			</p>
		</div>
	</section>

	<section class="bg-white border-b border-gray-100">
		<div class="max-w-6xl mx-auto px-4 sm:px-6 py-6">
			<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
				<?php foreach ( array_slice( $kpi_cards, 0, 4 ) as $card ) : ?>
					<article class="bg-brand-frost rounded-lg p-4 text-center shadow-sm" data-dashboard-kpi>
						<div class="text-2xl font-bold <?php echo esc_attr( $card['accent'] ); ?>" data-dashboard-kpi-value>--</div>
						<p class="text-[11px] text-gray-500 mt-1" data-kpi-label><?php echo esc_html( $card['label'] ); ?></p>
						<p class="text-[10px] text-gray-400 mt-1" data-kpi-note><?php echo esc_html( $card['note'] ); ?></p>
						<div class="text-[9px] text-gray-400 mt-2 italic" data-kpi-badge><?php echo esc_html( $card['badge'] ); ?></div>
					</article>
				<?php endforeach; ?>
			</div>
			<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
				<?php foreach ( array_slice( $kpi_cards, 4, 4 ) as $card ) : ?>
					<article class="bg-brand-frost rounded-lg p-4 text-center shadow-sm" data-dashboard-kpi>
						<div class="text-2xl font-bold <?php echo esc_attr( $card['accent'] ); ?>" data-dashboard-kpi-value>--</div>
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
				<div class="grid lg:grid-cols-2 gap-6 mb-6">
					<?php foreach ( $row as $chart ) : ?>
						<article class="bg-white border border-gray-100 rounded-lg p-5 shadow-sm" data-dashboard-chart data-chart-id="<?php echo esc_attr( $chart['id'] ); ?>">
							<div class="flex items-center justify-between mb-4 gap-3">
								<div>
									<h2 class="text-sm font-semibold text-gray-800" data-chart-title><?php echo esc_html( $chart['title'] ); ?></h2>
									<p class="text-[10px] text-gray-500 mt-1" data-chart-description><?php echo esc_html( $chart['description'] ); ?></p>
								</div>
								<span class="shrink-0 text-[10px] text-gray-400"><?php echo esc_html( $chart['period'] ); ?></span>
							</div>
							<div class="relative bg-gray-50 rounded-md p-3" style="height: 280px;">
								<div class="absolute inset-0 flex items-center justify-center text-xs text-gray-400" data-chart-loading><?php esc_html_e( 'Chargement du graphique...', 'crades-theme' ); ?></div>
								<div class="hidden absolute inset-0 flex items-center justify-center px-6 text-center text-xs text-gray-400" data-chart-empty><p data-chart-empty-copy><?php esc_html_e( 'Aucune donnée exploitable pour ce graphique.', 'crades-theme' ); ?></p></div>
								<div class="hidden absolute inset-0 flex items-center justify-center px-6 text-center text-xs text-red-500" data-chart-error><p data-chart-error-copy><?php esc_html_e( 'Impossible de charger ce graphique.', 'crades-theme' ); ?></p></div>
								<canvas class="h-full w-full hidden" data-chart-canvas></canvas>
							</div>
							<p class="text-[9px] text-gray-400 mt-3 text-center italic"><?php esc_html_e( 'Source : ANSD', 'crades-theme' ); ?></p>
						</article>
					<?php endforeach; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="py-10">
		<div class="max-w-6xl mx-auto px-4 sm:px-6">
			<h2 class="font-display text-xl text-gray-800 mb-6"><?php esc_html_e( 'Données détaillées', 'crades-theme' ); ?></h2>

			<div class="grid lg:grid-cols-2 gap-6">
				<article class="bg-white border border-gray-100 rounded-lg overflow-hidden shadow-sm" data-dashboard-table data-table-id="export-partners">
					<div class="bg-brand-frost px-4 py-3 border-b border-brand-ice/50">
						<h3 class="text-sm font-semibold text-gray-800">
							<i class="fas fa-arrow-up text-emerald-500 mr-1" aria-hidden="true"></i>
							<span data-dashboard-table-title><?php esc_html_e( 'Principales destinations', 'crades-theme' ); ?></span>
							<span class="text-[10px] text-gray-400 font-normal ml-1">(<?php echo esc_html( (string) $latest_year ); ?>)</span>
						</h3>
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
				</article>

				<article class="bg-white border border-gray-100 rounded-lg overflow-hidden shadow-sm" data-dashboard-table data-table-id="import-partners">
					<div class="bg-brand-frost px-4 py-3 border-b border-brand-ice/50">
						<h3 class="text-sm font-semibold text-gray-800">
							<i class="fas fa-arrow-down text-red-400 mr-1" aria-hidden="true"></i>
							<span data-dashboard-table-title><?php esc_html_e( 'Principaux fournisseurs', 'crades-theme' ); ?></span>
							<span class="text-[10px] text-gray-400 font-normal ml-1">(<?php echo esc_html( (string) $latest_year ); ?>)</span>
						</h3>
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
				</article>
			</div>
		</div>
	</section>

	<section class="bg-brand-frost border-t border-brand-ice/50 py-8">
		<div class="max-w-6xl mx-auto px-4 sm:px-6">
			<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
				<div>
					<p class="text-xs text-gray-500">
						<i class="fas fa-info-circle mr-1" aria-hidden="true"></i>
						<?php esc_html_e( 'Source : ANSD - Note d’Analyse du Commerce Extérieur 2024.', 'crades-theme' ); ?>
					</p>
					<p class="text-[10px] text-gray-400 mt-1"><?php esc_html_e( 'Les valeurs sont en milliards de FCFA. Données officielles mises à jour via Google Sheets.', 'crades-theme' ); ?></p>
				</div>
				<div class="flex items-center gap-3">
					<a href="<?php echo esc_url( rest_url( 'ministere/v1/commerce-exterieur' ) ); ?>" target="_blank" rel="noopener" class="text-[10px] bg-white border border-gray-200 px-3 py-1.5 rounded hover:border-gray-300 text-gray-500 transition-colors">
						<i class="fas fa-code mr-1" aria-hidden="true"></i>
						<?php esc_html_e( 'API JSON', 'crades-theme' ); ?>
					</a>
				</div>
			</div>
		</div>
	</section>
</div>
<?php
get_footer();
