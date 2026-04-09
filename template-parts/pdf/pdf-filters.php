<?php
/**
 * PDF library filters.
 *
 * @package CRADES_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$terms = isset( $args['terms'] ) && is_array( $args['terms'] ) ? $args['terms'] : array();
?>
<div class="mb-8 flex flex-wrap items-center gap-3" data-pdf-filters aria-label="<?php esc_attr_e( 'Filtres des rapports', 'crades-theme' ); ?>">
	<button
		type="button"
		class="text-xs font-medium bg-brand-blue text-white px-3 py-1.5 rounded-full cursor-pointer transition-colors"
		data-pdf-filter="all"
		aria-pressed="true"
	>
		<?php esc_html_e( 'Tous', 'crades-theme' ); ?>
	</button>

	<?php foreach ( $terms as $term ) : ?>
		<button
			type="button"
			class="text-xs font-medium bg-white text-gray-500 border border-gray-200 hover:border-brand-blue hover:text-brand-blue px-3 py-1.5 rounded-full cursor-pointer transition-colors"
			data-pdf-filter="<?php echo esc_attr( $term->slug ); ?>"
			aria-pressed="false"
		>
			<?php echo esc_html( $term->name ); ?>
		</button>
	<?php endforeach; ?>
</div>
