<?php
/**
 * Dashboard chart card.
 *
 * @package CRADES_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$chart = isset( $args['chart'] ) ? $args['chart'] : array();

if ( empty( $chart ) ) {
	return;
}
?>
<article class="dashboard-chart-card overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-sm" data-dashboard-chart data-chart-id="<?php echo esc_attr( $chart['id'] ); ?>">
	<div class="border-b border-slate-100 px-5 py-4">
		<h2 class="text-base font-semibold text-slate-900">
			<?php echo esc_html( $chart['title'] ); ?>
		</h2>
		<p class="mt-1 text-sm leading-relaxed text-slate-500">
			<?php echo esc_html( $chart['description'] ); ?>
		</p>
	</div>

	<div class="relative p-5">
		<div class="dashboard-chart-stage relative flex min-h-[320px] items-center justify-center overflow-hidden rounded-2xl bg-slate-50">
			<div class="dashboard-chart-loading absolute inset-0 flex flex-col items-center justify-center gap-4 bg-slate-50/95 px-6 text-center" data-chart-loading>
				<div class="dashboard-chart-shimmer h-32 w-[88%] max-w-xl rounded-2xl"></div>
				<p class="text-sm text-slate-500"><?php esc_html_e( 'Preparation du conteneur Chart.js...', 'crades-theme' ); ?></p>
			</div>

			<canvas class="h-full w-full" data-chart-canvas aria-label="<?php echo esc_attr( $chart['title'] ); ?>" role="img"></canvas>

			<div class="hidden max-w-md px-6 text-center" data-chart-empty>
				<div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-frost text-brand-blue">
					<i class="fa-solid fa-chart-line text-xl" aria-hidden="true"></i>
				</div>
				<p class="mt-4 text-sm font-semibold text-slate-900">
					<?php esc_html_e( 'Graphique pret', 'crades-theme' ); ?>
				</p>
				<p class="mt-2 text-sm leading-relaxed text-slate-500">
					<?php echo esc_html( $chart['empty'] ); ?>
				</p>
			</div>

			<div class="hidden max-w-md px-6 text-center" data-chart-error>
				<div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-red-50 text-red-500">
					<i class="fa-solid fa-triangle-exclamation text-xl" aria-hidden="true"></i>
				</div>
				<p class="mt-4 text-sm font-semibold text-slate-900">
					<?php esc_html_e( 'Impossible d initialiser le graphique', 'crades-theme' ); ?>
				</p>
				<p class="mt-2 text-sm leading-relaxed text-slate-500">
					<?php esc_html_e( 'Chart.js n est pas disponible ou le module n est pas encore branche.', 'crades-theme' ); ?>
				</p>
			</div>
		</div>
	</div>
</article>
