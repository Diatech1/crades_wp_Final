<?php
/**
 * Tableaux de bord hub page.
 *
 * @package CRADES_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$dashboards = array(
	array(
		'title'    => __( 'Commerce extérieur', 'crades-theme' ),
		'href'     => crades_get_page_url( 'commerce-exterieur' ),
		'canvas'   => 'dashboard-chart-commerce-exterieur',
		'api_url'  => rest_url( 'ministere/v1/commerce-exterieur' ),
	),
	array(
		'title'    => __( 'Commerce intérieur', 'crades-theme' ),
		'href'     => crades_get_page_url( 'commerce-interieur' ),
		'canvas'   => 'dashboard-chart-commerce-interieur',
		'api_url'  => rest_url( 'ministere/v1/commerce-interieur' ),
	),
	array(
		'title'    => __( 'Industrie', 'crades-theme' ),
		'href'     => crades_get_page_url( 'industrie' ),
		'canvas'   => 'dashboard-chart-industrie',
		'api_url'  => rest_url( 'ministere/v1/industrie' ),
	),
	array(
		'title'    => __( 'PME / PMI', 'crades-theme' ),
		'href'     => crades_get_page_url( 'pme-pmi' ),
		'canvas'   => 'dashboard-chart-pme',
		'api_url'  => rest_url( 'ministere/v1/pme-pmi' ),
	),
);
?>
<section class="bg-brand-navy py-16 lg:py-20">
	<div class="max-w-6xl mx-auto px-4 sm:px-6">
		<nav class="text-xs text-gray-400 mb-4">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hover:text-white"><?php esc_html_e( 'Accueil', 'crades-theme' ); ?></a>
			<span class="mx-2">/</span>
			<span class="text-gray-300"><?php esc_html_e( 'Tableaux de bord', 'crades-theme' ); ?></span>
		</nav>
		<h1 class="font-display text-2xl lg:text-3xl text-white"><?php esc_html_e( 'Tableaux de bord sectoriels', 'crades-theme' ); ?></h1>
		<p class="text-gray-400 mt-2 max-w-2xl text-sm leading-relaxed">
			<?php esc_html_e( 'Chaque carte utilise maintenant un vrai graphique Chart.js chargé depuis l’API WordPress avant d’ouvrir le tableau de bord complet.', 'crades-theme' ); ?>
		</p>
	</div>
</section>

<section class="py-14 bg-gray-50">
	<div class="max-w-7xl mx-auto px-4 sm:px-6">
		<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
			<?php foreach ( $dashboards as $dashboard ) : ?>
				<div class="w-full md:max-w-[75%] mx-auto">
					<h2 class="mb-2 text-sm text-center font-semibold text-gray-800"><?php echo esc_html( $dashboard['title'] ); ?></h2>
					<a href="<?php echo esc_url( $dashboard['href'] ); ?>" class="group block bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-lg hover:border-brand-ice transition-all">
						<div class="aspect-[16/8.5]">
							<div class="h-full w-full rounded-lg bg-white p-2">
								<div class="relative h-full w-full rounded-lg bg-white" data-dashboard-preview data-api-url="<?php echo esc_url( $dashboard['api_url'] ); ?>">
									<div class="absolute inset-0 flex items-center justify-center text-xs text-gray-400" data-dashboard-preview-loading><?php esc_html_e( 'Chargement du graphique...', 'crades-theme' ); ?></div>
									<div class="hidden absolute inset-0 flex items-center justify-center text-xs text-gray-400" data-dashboard-preview-error><?php esc_html_e( 'Aperçu indisponible', 'crades-theme' ); ?></div>
									<canvas id="<?php echo esc_attr( $dashboard['canvas'] ); ?>" class="w-full h-full" data-dashboard-preview-canvas></canvas>
								</div>
							</div>
						</div>
						<div class="p-3">
							<div class="mt-3 text-[13px] font-medium text-brand-blue group-hover:underline"><?php esc_html_e( 'Ouvrir le tableau de bord', 'crades-theme' ); ?> &rarr;</div>
						</div>
					</a>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php
get_footer();
