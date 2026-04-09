<?php
/**
 * Dashboard filter chips.
 *
 * @package CRADES_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$dashboard = isset( $args['dashboard'] ) ? $args['dashboard'] : array();
$filters   = isset( $dashboard['filters'] ) ? $dashboard['filters'] : array();

if ( empty( $filters ) ) {
	return;
}
?>
<div class="mb-8 flex flex-wrap gap-3" data-dashboard-filters>
	<?php foreach ( $filters as $filter ) : ?>
		<?php
		$is_active = ! empty( $filter['active'] );
		?>
		<button
			type="button"
			class="dashboard-filter-chip <?php echo $is_active ? 'is-active bg-brand-blue text-white border-brand-blue' : 'border-slate-200 bg-white text-slate-500 hover:border-brand-blue hover:text-brand-blue'; ?>"
			data-dashboard-filter
			aria-pressed="<?php echo $is_active ? 'true' : 'false'; ?>"
		>
			<?php echo esc_html( $filter['label'] ); ?>
		</button>
	<?php endforeach; ?>
</div>
