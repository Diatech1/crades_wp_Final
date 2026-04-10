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
		'key'      => 'commerce-exterieur',
		'title'    => 'Commerce extérieur',
		'href'     => crades_get_page_url( 'commerce-exterieur' ),
		'chart_id' => 'trade-evolution',
		'canvas'   => 'dashboard-chart-commerce-exterieur',
		'api_url'  => rest_url( 'ministere/v1/commerce-exterieur' ),
		'chart'    => array(
			'title'       => 'Évolution du commerce (Mds FCFA)',
			'description' => 'Exportations et importations 2016-2025.',
			'period'      => '2016-2025',
			'source'      => 'Source : ANSD',
		),
	),
	array(
		'key'      => 'commerce-interieur',
		'title'    => 'Commerce intérieur',
		'href'     => crades_get_page_url( 'commerce-interieur' ),
		'chart_id' => 'ihpc-desagrege',
		'canvas'   => 'dashboard-chart-commerce-interieur',
		'api_url'  => rest_url( 'ministere/v1/commerce-interieur' ),
		'chart'    => array(
			'title'       => 'IHPC désagrégé — variations mensuelles (%)',
			'description' => '10 dernières périodes · cliquer pour masquer / afficher',
			'period'      => '10 dernières périodes',
			'source'      => 'Source: ANSD — IHPC COICOP. Var. = (Indice[t] / Indice[t-1] - 1) × 100',
		),
	),
	array(
		'key'      => 'industrie',
		'title'    => 'Industrie',
		'href'     => crades_get_page_url( 'industrie' ),
		'chart_id' => 'industry-ippi',
		'canvas'   => 'dashboard-chart-industrie',
		'api_url'  => rest_url( 'ministere/v1/industrie' ),
		'chart'    => array(
			'title'       => 'Indice des Prix à la Production (IPPI)',
			'description' => "Lecture sur les 24 derniers mois pour l'ensemble.",
			'period'      => '24 derniers mois',
			'source'      => 'Source : ANSD - IPPI mensuel (Ensemble hors égrenage coton)',
		),
	),
	array(
		'key'      => 'pme-pmi',
		'title'    => 'PME / PMI',
		'href'     => crades_get_page_url( 'pme-pmi' ),
		'chart_id' => 'pme-immatriculations',
		'canvas'   => 'dashboard-chart-pme',
		'api_url'  => rest_url( 'ministere/v1/pme-pmi' ),
		'chart'    => array(
			'title'       => "Immatriculations par secteur d'activité",
			'description' => 'Entreprises individuelles — 2019–2024',
			'period'      => 'Entreprises individuelles — 2019–2024',
			'source'      => 'Source : ANSD/RNEA — BANIN 2024',
		),
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
			<?php esc_html_e( 'Chaque carte reprend le graphique principal de son dashboard WordPress et ouvre la page complète au clic.', 'crades-theme' ); ?>
		</p>
	</div>
</section>

<section class="py-14 bg-gray-50">
	<div class="max-w-6xl mx-auto px-4 sm:px-6">
		<div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-7">
			<?php foreach ( $dashboards as $dashboard ) : ?>
				<?php get_template_part( 'template-parts/dashboard/preview-card', null, array( 'dashboard' => $dashboard ) ); ?>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php
get_footer();
