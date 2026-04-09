<?php
/**
 * Dashboard KPI card.
 *
 * @package CRADES_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$kpi = isset( $args['kpi'] ) ? $args['kpi'] : array();

if ( empty( $kpi ) ) {
	return;
}
?>
<article class="dashboard-kpi-card rounded-3xl border border-slate-100 bg-white p-5 shadow-sm transition-all" data-dashboard-kpi>
	<div class="flex items-start justify-between gap-4">
		<div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-frost text-brand-blue">
			<i class="<?php echo esc_attr( $kpi['icon'] ); ?>" aria-hidden="true"></i>
		</div>
		<span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-500">
			<?php esc_html_e( 'Placeholder', 'crades-theme' ); ?>
		</span>
	</div>
	<p class="mt-5 text-xs font-semibold uppercase tracking-[0.24em] text-brand-gold">
		<?php echo esc_html( $kpi['label'] ); ?>
	</p>
	<p class="mt-3 text-3xl font-bold text-slate-900" data-dashboard-kpi-value>
		<?php echo esc_html( $kpi['value'] ); ?>
	</p>
	<p class="mt-2 text-sm leading-relaxed text-slate-500">
		<?php echo esc_html( $kpi['note'] ); ?>
	</p>
</article>
