<?php
/**
 * Shared dashboard preview card used on the hub and homepage.
 *
 * @package CRADES_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$dashboard = isset( $args['dashboard'] ) && is_array( $args['dashboard'] ) ? $args['dashboard'] : array();
$compact   = ! empty( $args['compact'] );

if ( empty( $dashboard ) ) {
	return;
}

$wrapper_classes = $compact
	? 'group block h-full w-full rounded-xl focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-blue focus-visible:ring-offset-2'
	: 'group block h-full w-full max-w-[35rem] mx-auto rounded-xl focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-blue focus-visible:ring-offset-2';

$article_classes = $compact
	? 'h-full bg-white border border-gray-100 rounded-xl p-4 shadow-sm transition-all duration-200 group-hover:border-brand-ice group-hover:shadow-md flex flex-col'
	: 'h-full bg-white border border-gray-100 rounded-xl p-6 shadow-sm transition-all duration-200 group-hover:border-brand-ice group-hover:shadow-md flex flex-col';

$title_classes = $compact
	? 'text-sm font-semibold leading-snug text-gray-800 mb-4'
	: 'text-[15px] font-semibold leading-tight text-gray-800 mb-5';

$chart_shell_classes = $compact
	? 'relative rounded-lg border border-gray-100 bg-gray-50 p-3 overflow-hidden'
	: 'relative rounded-lg border border-gray-100 bg-gray-50 p-4 overflow-hidden';

$chart_height = $compact ? '220px' : '300px';

$cta_classes = $compact
	? 'mt-4 pt-3 border-t border-gray-100'
	: 'mt-5 pt-4 border-t border-gray-100';

$cta_text_classes = $compact
	? 'inline-flex items-center gap-2 text-xs font-semibold text-brand-blue transition-colors group-hover:text-brand-navy'
	: 'inline-flex items-center gap-2 text-[13px] font-semibold text-brand-blue transition-colors group-hover:text-brand-navy';
?>
<a href="<?php echo esc_url( $dashboard['href'] ); ?>" class="<?php echo esc_attr( $wrapper_classes ); ?>">
	<article class="<?php echo esc_attr( $article_classes ); ?>" data-dashboard-preview-card data-dashboard-key="<?php echo esc_attr( $dashboard['key'] ); ?>" data-chart-id="<?php echo esc_attr( $dashboard['chart_id'] ); ?>">
		<h3 class="<?php echo esc_attr( $title_classes ); ?>"><?php echo esc_html( $dashboard['title'] ); ?></h3>
		<div class="<?php echo esc_attr( $chart_shell_classes ); ?>" style="height: <?php echo esc_attr( $chart_height ); ?>;">
			<div class="absolute inset-0 flex items-center justify-center text-xs text-gray-400" data-chart-loading><?php esc_html_e( 'Chargement du graphique...', 'crades-theme' ); ?></div>
			<div class="hidden absolute inset-0 flex items-center justify-center px-6 text-center text-xs text-gray-400" data-chart-empty><p data-chart-empty-copy><?php esc_html_e( 'Aucune donnee exploitable pour ce graphique.', 'crades-theme' ); ?></p></div>
			<div class="hidden absolute inset-0 flex items-center justify-center px-6 text-center text-xs text-red-500" data-chart-error><p data-chart-error-copy><?php esc_html_e( 'Impossible de charger ce graphique.', 'crades-theme' ); ?></p></div>
			<div class="relative h-full w-full" data-dashboard-preview data-dashboard-key="<?php echo esc_attr( $dashboard['key'] ); ?>" data-chart-id="<?php echo esc_attr( $dashboard['chart_id'] ); ?>" data-api-url="<?php echo esc_url( $dashboard['api_url'] ); ?>">
				<canvas id="<?php echo esc_attr( $dashboard['canvas'] ); ?>" class="h-full w-full hidden" data-chart-canvas></canvas>
			</div>
		</div>
		<div class="<?php echo esc_attr( $cta_classes ); ?>">
			<span class="<?php echo esc_attr( $cta_text_classes ); ?>">
				<?php esc_html_e( 'Ouvrir le Tableau de Bord...', 'crades-theme' ); ?>
				<i class="fas fa-arrow-right text-[11px]" aria-hidden="true"></i>
			</span>
		</div>
	</article>
</a>
