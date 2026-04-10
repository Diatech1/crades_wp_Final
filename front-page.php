<?php
/**
 * Front page template ported from the Hono homepage.
 *
 * @package CRADES_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$extract_tables = static function ( $payload ) {
	return ! is_wp_error( $payload ) && ! empty( $payload['data']['tables'] ) && is_array( $payload['data']['tables'] )
		? $payload['data']['tables']
		: array();
};

$format_number = static function ( $value, $decimals = 1 ) {
	return str_replace( '.', ',', number_format( (float) $value, $decimals, '.', '' ) );
};

$format_billions = static function ( $value ) use ( $format_number ) {
	$number = (float) $value;

	if ( abs( $number ) >= 1000 ) {
		$number /= 1000;
	}

	return $format_number( $number, 1 );
};

$format_integer = static function ( $value ) {
	return number_format( (int) $value, 0, ',', ' ' );
};

$compact_metric = static function ( $value ) {
	if ( null === $value || '' === trim( (string) $value ) ) {
		return 'n/a';
	}

	$text = preg_replace( '/\s+/', ' ', trim( (string) $value ) );

	if ( preg_match( '/[+-]?\d+(?:[.,]\d+)?(?:\s*%|)/', $text, $matches ) ) {
		return trim( preg_replace( '/\s+/', ' ', $matches[0] ) );
	}

	return $text;
};

$find_indicator = static function ( $indicators, $needles ) {
	foreach ( $indicators as $indicator ) {
		$name = isset( $indicator['name'] ) ? (string) $indicator['name'] : '';

		foreach ( $needles as $needle ) {
			if ( false !== stripos( $name, $needle ) ) {
				return $indicator;
			}
		}
	}

	return null;
};

$commerce_exterieur = crades_vm_build_commerce_exterieur_data( $extract_tables( crades_get_dashboard_sheet_payload( 'commerce-exterieur' ) ) );
$commerce_interieur = crades_vm_build_commerce_interieur_data( $extract_tables( crades_get_dashboard_sheet_payload( 'commerce-interieur' ) ) );
$industrie_data     = crades_vm_build_industry_data( $extract_tables( crades_get_dashboard_sheet_payload( 'industrie' ) ) );
$pme_data           = crades_vm_build_pme_data( $extract_tables( crades_get_dashboard_sheet_payload( 'pme-pmi' ) ) );
$overview           = $commerce_exterieur['overview'];
$coverage_rate      = $overview['totalImports'] > 0 ? ( $overview['totalExports'] / $overview['totalImports'] ) * 100 : 0;
$ihpc_indicator     = $find_indicator( $commerce_interieur['indicators'], array( 'IHPC Global F', 'IHPC Global Jan', 'IHPC Global' ) );
$latest_inflation   = ! empty( $commerce_interieur['inflationData'] ) ? $commerce_interieur['inflationData'][ count( $commerce_interieur['inflationData'] ) - 1 ] : null;
$commerce_pib       = $find_indicator( $commerce_interieur['indicators'], array( 'Commerce/PIB' ) );
$ihpi_indicator     = $find_indicator( $industrie_data['indicators'], array( 'IHPI' ) );
$icai_indicator     = $find_indicator( $industrie_data['indicators'], array( 'ICAI' ) );
$ippi_indicator     = $find_indicator( $industrie_data['indicators'], array( 'IPPI' ) );
$cip_indicator      = $find_indicator( $industrie_data['indicators'], array( 'CIP' ) );
$latest_tucp        = ! empty( $industrie_data['tucpData'] ) ? $format_number( $industrie_data['tucpData'][ count( $industrie_data['tucpData'] ) - 1 ]['value'] ) . ' %' : 'n/a';
$dashboards_page_url = crades_get_page_url( 'tableaux-de-bord' );
$publications_page_url = crades_get_page_url( 'publications' );

$dashboards = array(
	array(
		'title'     => 'Commerce extérieur',
		'desc'      => 'Exportations, importations, balance commerciale et partenaires du Sénégal.',
		'icon'      => 'fa-globe',
		'icon_bg'   => 'bg-brand-blue/10',
		'icon_text' => 'text-brand-blue',
		'href'      => crades_get_page_url( 'commerce-exterieur' ),
	),
	array(
		'title'     => 'Commerce intérieur',
		'desc'      => 'Prix à la consommation, inflation, IHPC et marchés intérieurs.',
		'icon'      => 'fa-store',
		'icon_bg'   => 'bg-brand-gold/10',
		'icon_text' => 'text-brand-gold',
		'href'      => crades_get_page_url( 'commerce-interieur' ),
	),
	array(
		'title'     => 'Industrie',
		'desc'      => 'Production industrielle, IHPI, valeur ajoutée et emploi du secteur.',
		'icon'      => 'fa-industry',
		'icon_bg'   => 'bg-brand-sky/10',
		'icon_text' => 'text-sky-500',
		'href'      => crades_get_page_url( 'industrie' ),
	),
	array(
		'title'     => 'PME / PMI',
		'desc'      => 'Immatriculations, secteurs, taille, répartition géographique et obstacles.',
		'icon'      => 'fa-building',
		'icon_bg'   => 'bg-brand-navy/10',
		'icon_text' => 'text-brand-navy',
		'href'      => crades_get_page_url( 'pme-pmi' ),
	),
);

$dashboard_previews = array(
	array(
		'key'      => 'commerce-exterieur',
		'title'    => 'Commerce extérieur',
		'href'     => crades_get_page_url( 'commerce-exterieur' ),
		'chart_id' => 'trade-evolution',
		'canvas'   => 'home-dashboard-chart-commerce-exterieur',
		'api_url'  => rest_url( 'ministere/v1/commerce-exterieur' ),
	),
	array(
		'key'      => 'commerce-interieur',
		'title'    => 'Commerce intérieur',
		'href'     => crades_get_page_url( 'commerce-interieur' ),
		'chart_id' => 'ihpc-desagrege',
		'canvas'   => 'home-dashboard-chart-commerce-interieur',
		'api_url'  => rest_url( 'ministere/v1/commerce-interieur' ),
	),
	array(
		'key'      => 'industrie',
		'title'    => 'Industrie',
		'href'     => crades_get_page_url( 'industrie' ),
		'chart_id' => 'industry-ippi',
		'canvas'   => 'home-dashboard-chart-industrie',
		'api_url'  => rest_url( 'ministere/v1/industrie' ),
	),
	array(
		'key'      => 'pme-pmi',
		'title'    => 'PME / PMI',
		'href'     => crades_get_page_url( 'pme-pmi' ),
		'chart_id' => 'pme-immatriculations',
		'canvas'   => 'home-dashboard-chart-pme',
		'api_url'  => rest_url( 'ministere/v1/pme-pmi' ),
	),
);

$home_dashboard_previews = $dashboard_previews;
$home_dashboard_carousel_slides = $home_dashboard_previews;

foreach ( array_slice( $home_dashboard_previews, 0, 2 ) as $clone_index => $dashboard_clone ) {
	$dashboard_clone['canvas'] = $dashboard_clone['canvas'] . '-clone-' . (string) ( $clone_index + 1 );
	$home_dashboard_carousel_slides[] = $dashboard_clone;
}

$kpis = array(
	array( 'value' => $format_billions( $overview['totalExports'] ), 'unit' => 'Mds FCFA', 'label' => 'Exportations', 'href' => crades_get_page_url( 'commerce-exterieur' ) ),
	array( 'value' => $format_billions( $overview['totalImports'] ), 'unit' => 'Mds FCFA', 'label' => 'Importations', 'href' => crades_get_page_url( 'commerce-exterieur' ) ),
	array( 'value' => $format_billions( $overview['tradeBalance'] ), 'unit' => 'Mds FCFA', 'label' => 'Balance commerciale', 'href' => crades_get_page_url( 'commerce-exterieur' ) ),
	array( 'value' => $format_number( $coverage_rate ), 'unit' => '%', 'label' => 'Taux de couverture', 'href' => crades_get_page_url( 'commerce-exterieur' ) ),
	array( 'value' => ! empty( $ihpc_indicator['formatted'] ) ? $ihpc_indicator['formatted'] : 'n/a', 'unit' => '', 'label' => 'Indice Prix Consommation', 'href' => crades_get_page_url( 'commerce-interieur' ) ),
	array( 'value' => $latest_inflation ? $format_number( $latest_inflation['rate'] ) . ' %' : 'n/a', 'unit' => '', 'label' => 'Inflation annuelle', 'href' => crades_get_page_url( 'commerce-interieur' ) ),
	array( 'value' => isset( $pme_data['exportatrices'] ) ? $pme_data['exportatrices'] : 'n/a', 'unit' => '', 'label' => 'Entreprises exportatrices', 'href' => crades_get_page_url( 'pme-pmi' ) ),
	array( 'value' => ! empty( $commerce_pib['formatted'] ) ? $commerce_pib['formatted'] : 'n/a', 'unit' => '', 'label' => 'Commerce/PIB', 'href' => crades_get_page_url( 'commerce-interieur' ) ),
	array( 'value' => $compact_metric( isset( $ihpi_indicator['value'] ) ? $ihpi_indicator['value'] : null ), 'unit' => '', 'label' => 'IHPI - Production', 'href' => crades_get_page_url( 'industrie' ) ),
	array( 'value' => $compact_metric( isset( $icai_indicator['value'] ) ? $icai_indicator['value'] : null ), 'unit' => '', 'label' => 'ICAI - Industrie', 'href' => crades_get_page_url( 'industrie' ) ),
	array( 'value' => $compact_metric( isset( $ippi_indicator['value'] ) ? $ippi_indicator['value'] : null ), 'unit' => '', 'label' => 'IPPI - Prix Production', 'href' => crades_get_page_url( 'industrie' ) ),
	array( 'value' => $compact_metric( isset( $cip_indicator['value'] ) ? $cip_indicator['value'] : null ), 'unit' => '', 'label' => 'CIP - Compétitivité', 'href' => crades_get_page_url( 'industrie' ) ),
	array( 'value' => $latest_tucp, 'unit' => '', 'label' => "Taux d'utilisation des capacités", 'href' => crades_get_page_url( 'industrie' ) ),
	array( 'value' => $format_integer( $pme_data['immatriculations'] ), 'unit' => '', 'label' => 'Immatriculations 2024', 'href' => crades_get_page_url( 'pme-pmi' ) ),
	array( 'value' => isset( $pme_data['creditAccess'] ) ? $pme_data['creditAccess'] : 'n/a', 'unit' => '', 'label' => 'Accès au crédit', 'href' => crades_get_page_url( 'pme-pmi' ) ),
	array( 'value' => isset( $pme_data['croissanceEmploi'] ) ? $pme_data['croissanceEmploi'] : 'n/a', 'unit' => '', 'label' => "Croissance de l'emploi", 'href' => crades_get_page_url( 'pme-pmi' ) ),
);

$publications = array(
	array(
		'title'    => 'Bulletin mensuel - produits de base - Mai 2025',
		'type'     => 'Bulletin mensuel',
		'taxonomy' => 'Bulletins mensuels',
		'year'     => '2025',
		'desc'     => 'Suivi mensuel des produits de base, avec les tendances de marché et les principaux indicateurs disponibles pour mai 2025.',
		'href'     => crades_get_theme_asset_uri( 'assets/docs/publications/bulletin-mensuel-produits-de-base-mai-2025.pdf' ),
	),
	array(
		'title'    => 'CRADES_bulletin annuel 2025 - marchés des produits de base',
		'type'     => 'Bulletin annuel',
		'taxonomy' => 'Bulletins annuels',
		'year'     => '2025',
		'desc'     => 'Vue annuelle 2025 sur les marchés des produits de base, avec les analyses et tableaux de synthèse du CRADES.',
		'href'     => crades_get_theme_asset_uri( 'assets/docs/publications/crades-bulletin-annuel-2025-marches-produits-de-base.pdf' ),
	),
);

$palette = array(
	'Bulletins mensuels'   => array(
		'badge' => 'bg-white/15 text-white',
		'text'  => 'text-white',
	),
	'Bulletins annuels'    => array(
		'badge' => 'bg-white/15 text-white',
		'text'  => 'text-white',
	),
	'Fiches synoptiques'   => array(
		'badge' => 'bg-white/15 text-white',
		'text'  => 'text-white',
	),
	'Études monographiques' => array(
		'badge' => 'bg-white/15 text-white',
		'text'  => 'text-white',
	),
);
?>
<section class="relative overflow-hidden bg-brand-frost">
	<div class="max-w-6xl mx-auto px-4 sm:px-6 py-16 lg:py-24">
		<div class="max-w-xl">
			<h1 class="font-display font-bold text-2xl sm:text-3xl md:text-4xl lg:text-5xl text-brand-navy leading-tight">
				Centre de Recherche,<br>d'Analyse&nbsp;des&nbsp;Échanges et&nbsp;Statistiques
			</h1>
			<p class="text-gray-600 mt-5 text-sm leading-relaxed">
				Le CRADES produit et diffuse les statistiques, études et analyses stratégiques sur l'industrie et le commerce du Sénégal.
			</p>
			<div class="flex flex-wrap gap-4 mt-10">
				<a href="<?php echo esc_url( $dashboards_page_url ); ?>" class="inline-flex items-center text-sm font-medium bg-brand-blue text-white px-5 py-2.5 rounded-lg hover:bg-brand-navy transition-colors shadow-sm">
					<?php esc_html_e( 'Explorer les tableaux de bord', 'crades-theme' ); ?>
				</a>
				<a href="<?php echo esc_url( $publications_page_url ); ?>" class="inline-flex items-center text-sm font-medium bg-white text-brand-navy px-5 py-2.5 rounded-lg hover:bg-white/80 transition-colors border border-brand-ice shadow-sm">
					<?php esc_html_e( 'Publications', 'crades-theme' ); ?>
				</a>
			</div>
		</div>
	</div>
</section>

<section class="bg-brand-navy border-t border-brand-blue/20 border-b border-brand-blue/20 overflow-hidden">
	<style>
		@keyframes cradesKpiMarquee {
			from { transform: translateX(0); }
			to { transform: translateX(-50%); }
		}
		.crades-kpi-track {
			width: max-content;
			animation: cradesKpiMarquee 36s linear infinite;
		}
		.crades-kpi-track:hover {
			animation-play-state: paused;
		}
	</style>
	<div class="crades-kpi-track flex">
		<?php foreach ( array_merge( $kpis, $kpis ) as $kpi ) : ?>
			<a href="<?php echo esc_url( $kpi['href'] ); ?>" class="group flex min-w-[220px] sm:min-w-[250px] items-center justify-center border-r border-white/10 px-4 py-4 text-center transition-colors hover:bg-brand-blue/30">
				<div>
					<div class="text-white text-lg sm:text-xl font-bold">
						<?php echo esc_html( $kpi['value'] ); ?>
						<?php if ( ! empty( $kpi['unit'] ) ) : ?>
							<span class="text-brand-ice text-sm sm:text-base font-medium"><?php echo esc_html( $kpi['unit'] ); ?></span>
						<?php endif; ?>
					</div>
					<div class="mt-1 text-brand-ice text-[11px] sm:text-xs group-hover:text-white transition-colors"><?php echo esc_html( $kpi['label'] ); ?></div>
				</div>
			</a>
		<?php endforeach; ?>
	</div>
</section>

<section class="py-14 bg-white border-b border-gray-100">
	<div class="max-w-6xl mx-auto px-4 sm:px-6">
		<div class="grid gap-6 lg:grid-cols-[220px_minmax(0,1fr)] lg:items-start">
			<div>
				<p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-brand-gold"><?php esc_html_e( 'À propos', 'crades-theme' ); ?></p>
				<h2 class="mt-3 font-display text-2xl lg:text-[2rem] leading-tight text-brand-navy"><?php esc_html_e( 'Notre Mission', 'crades-theme' ); ?></h2>
			</div>
			<div class="max-w-3xl">
				<p class="text-sm lg:text-[15px] leading-7 text-gray-500">
					<?php esc_html_e( "Centre de Recherche et d'Analyse des Échanges et Statistiques, structure technique spécialisée dans la recherche, le traitement et l'analyse des informations commerciales et industrielles.", 'crades-theme' ); ?>
				</p>
				<a href="<?php echo esc_url( crades_get_page_url( 'a-propos' ) ); ?>" class="inline-flex items-center gap-2 mt-5 text-sm font-medium text-brand-blue hover:text-brand-navy transition-colors">
					<?php esc_html_e( 'En savoir plus', 'crades-theme' ); ?>
					<i class="fas fa-arrow-right text-[11px]" aria-hidden="true"></i>
				</a>
			</div>
		</div>
	</div>
</section>

<section class="py-16 bg-white">
	<div class="max-w-6xl mx-auto px-4 sm:px-6">
		<div class="flex items-center justify-between mb-8">
			<h2 class="font-display text-xl text-gray-800"><?php esc_html_e( 'Tableaux de bord sectoriels', 'crades-theme' ); ?></h2>
			<a href="<?php echo esc_url( $dashboards_page_url ); ?>" class="text-xs text-brand-blue font-medium hover:underline"><?php esc_html_e( 'Voir tous', 'crades-theme' ); ?> &rarr;</a>
		</div>
		<div class="relative" data-home-dashboard-carousel data-carousel-original-count="<?php echo esc_attr( (string) count( $home_dashboard_previews ) ); ?>" data-carousel-clone-count="2">
			<style>
				.crades-home-dashboard-track::-webkit-scrollbar {
					display: none;
				}
			</style>
			<div class="overflow-hidden">
				<div class="crades-home-dashboard-track flex gap-6 overflow-x-auto scroll-smooth snap-x snap-mandatory pb-2" style="-ms-overflow-style:none;scrollbar-width:none;" data-dashboard-carousel-track>
					<?php foreach ( $home_dashboard_carousel_slides as $dashboard ) : ?>
						<div class="snap-start shrink-0 w-full md:w-[calc(50%-12px)]" data-dashboard-carousel-slide>
							<?php get_template_part( 'template-parts/dashboard/preview-card', null, array( 'dashboard' => $dashboard ) ); ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="py-16 bg-gray-50 border-t border-gray-100" data-home-pdf-gallery>
	<div class="max-w-6xl mx-auto px-4 sm:px-6">
		<div class="flex items-center justify-between mb-10">
			<h2 class="font-display text-xl text-gray-800"><?php esc_html_e( 'Dernières publications', 'crades-theme' ); ?></h2>
			<a href="<?php echo esc_url( $publications_page_url ); ?>" class="text-xs text-brand-blue font-medium hover:underline"><?php esc_html_e( 'Voir toutes', 'crades-theme' ); ?> &rarr;</a>
		</div>
		<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">
			<?php foreach ( $publications as $publication ) : ?>
				<?php
				$style = isset( $palette[ $publication['taxonomy'] ] ) ? $palette[ $publication['taxonomy'] ] : array(
					'badge' => 'bg-white text-brand-blue',
					'text'  => 'text-brand-navy',
				);
				?>
				<article class="group bg-white rounded-xl border border-gray-100 overflow-hidden hover:shadow-md transition-shadow" data-publication-card data-taxonomy="<?php echo esc_attr( $publication['taxonomy'] ); ?>">
					<button type="button" class="relative block w-full aspect-[4/3] overflow-hidden bg-white flex items-center justify-center cursor-pointer" data-home-pdf-open="<?php echo esc_url( $publication['href'] ); ?>" data-home-pdf-title="<?php echo esc_attr( $publication['title'] ); ?>">
						<canvas class="block h-full w-auto max-w-none" data-home-pdf-thumb data-pdf-url="<?php echo esc_url( $publication['href'] ); ?>" data-pdf-title="<?php echo esc_attr( $publication['title'] ); ?>"></canvas>
						<div class="absolute inset-0 pointer-events-none bg-gradient-to-t from-black/10 via-transparent to-transparent"></div>
						<div class="absolute left-3 top-3 inline-flex items-center gap-2 rounded-full px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wider <?php echo esc_attr( $style['badge'] ); ?>">
							<i class="fas fa-file-pdf text-[9px]" aria-hidden="true"></i>
							PDF
						</div>
						<div class="absolute right-3 top-3 rounded-full border border-white/20 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wider <?php echo esc_attr( $style['text'] ); ?> bg-black/20 backdrop-blur-sm">
							<?php echo esc_html( $publication['year'] ); ?>
						</div>
					</button>
					<div class="p-4">
						<div class="border-t-2 border-brand-gold pt-3" style="border-top-color:#b8943e;">
							<h3 class="text-sm font-semibold text-brand-navy line-clamp-2 group-hover:text-brand-blue transition-colors"><?php echo esc_html( $publication['title'] ); ?></h3>
						</div>
						<div class="mt-2 space-y-1">
							<div class="text-[10px] font-semibold uppercase tracking-[0.18em] text-brand-gold"><?php echo esc_html( $publication['type'] ); ?></div>
							<div class="text-[10px] font-medium uppercase tracking-[0.16em] text-brand-blue opacity-0 select-none"><?php echo esc_html( $publication['taxonomy'] ); ?></div>
						</div>
						<div class="flex items-center justify-between mt-3 text-[10px] text-gray-300 gap-3">
							<span><?php echo esc_html( $publication['year'] ); ?></span>
							<div class="flex items-center gap-3">
								<button type="button" data-home-pdf-open="<?php echo esc_url( $publication['href'] ); ?>" data-home-pdf-title="<?php echo esc_attr( $publication['title'] ); ?>" class="text-brand-blue font-medium hover:underline">
									<?php esc_html_e( 'Ouvrir', 'crades-theme' ); ?>
								</button>
								<a href="<?php echo esc_url( $publication['href'] ); ?>" download class="text-brand-blue font-medium hover:underline">
									<?php esc_html_e( 'Télécharger', 'crades-theme' ); ?>
								</a>
							</div>
						</div>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</div>

	<div id="homePdfModal" class="fixed inset-0 z-[80] hidden flex items-center justify-center bg-transparent p-4" data-home-pdf-modal>
		<div class="absolute inset-0 bg-white/15 backdrop-blur-sm" data-home-pdf-backdrop></div>
		<div class="relative w-[min(95vw,1100px)] h-[82vh] overflow-hidden border border-gray-200 bg-white shadow-2xl">
			<button type="button" class="absolute right-3 top-3 z-20 inline-flex h-9 w-9 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 shadow-sm hover:border-gray-300 hover:text-gray-700 transition-colors" data-home-pdf-close aria-label="<?php esc_attr_e( 'Fermer', 'crades-theme' ); ?>">
				<i class="fas fa-times" aria-hidden="true"></i>
			</button>
			<div id="homePdfModalViewer" class="h-full w-full overflow-auto bg-white px-4 py-14" data-home-pdf-viewer></div>
		</div>
	</div>
</section>

<?php
get_footer();
