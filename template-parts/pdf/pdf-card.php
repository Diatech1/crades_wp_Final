<?php
/**
 * PDF library card.
 *
 * @package CRADES_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id      = get_the_ID();
$pdf_url      = crades_get_rapport_pdf_url( $post_id );
$year         = crades_get_rapport_year( $post_id );
$terms        = get_the_terms( $post_id, 'rapport_type' );
$term_names   = array();
$term_slugs   = array();
$primary_term = '';
$term_labels  = '';

if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
	foreach ( $terms as $term ) {
		$term_names[] = $term->name;
		$term_slugs[] = $term->slug;
	}

	$primary_term = $term_names[0];
	$term_labels  = implode( '||', $term_names );
}

if ( ! $year ) {
	$year = get_the_date( 'Y', $post_id );
}
?>
<article
	<?php post_class( 'group overflow-hidden rounded-[1.15rem] border border-slate-100 bg-white shadow-[0_16px_38px_rgba(15,23,42,0.04)] transition-all duration-200 hover:-translate-y-0.5 hover:shadow-[0_22px_48px_rgba(15,23,42,0.08)]' ); ?>
	data-publication-card
	data-taxonomy="<?php echo esc_attr( $term_labels ? $term_labels : ( $primary_term ? $primary_term : __( 'Rapport', 'crades-theme' ) ) ); ?>"
	data-pdf-item
	data-pdf-url="<?php echo esc_url( $pdf_url ); ?>"
	data-pdf-title="<?php echo esc_attr( get_the_title() ); ?>"
	data-pdf-download="<?php echo esc_url( $pdf_url ); ?>"
	data-pdf-terms="<?php echo esc_attr( implode( ' ', $term_slugs ) ); ?>"
>
	<button type="button" class="relative flex w-full aspect-[4/3] cursor-pointer items-center justify-center overflow-hidden bg-white" data-pdf-open <?php disabled( empty( $pdf_url ) ); ?>>
		<canvas class="block h-full w-auto max-w-none" data-pdf-thumb-canvas aria-hidden="true"></canvas>
		<div class="absolute inset-0 pointer-events-none bg-gradient-to-t from-black/10 via-transparent to-transparent"></div>
		<div class="absolute left-3 top-3 inline-flex items-center gap-2 rounded-full px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wider bg-white/90 text-brand-blue shadow-sm">
			<i class="fas fa-file-pdf text-[9px]" aria-hidden="true"></i>
			PDF
		</div>

		<?php if ( $year ) : ?>
			<div class="absolute right-3 top-3 rounded-full border border-white/20 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wider text-white bg-black/20 backdrop-blur-sm">
				<?php echo esc_html( $year ); ?>
			</div>
		<?php endif; ?>
	</button>

	<div class="flex flex-1 flex-col p-4">
		<div class="mb-4 h-[2px] w-full bg-brand-gold" style="background-color:#b8943e;"></div>
		<div class="min-h-[4.15rem]">
			<h2 class="text-[0.95rem] font-semibold leading-[1.3] text-brand-navy line-clamp-3 transition-colors group-hover:text-brand-blue">
				<?php the_title(); ?>
			</h2>
		</div>

		<div class="mt-2.5 min-h-[2.2rem] space-y-1">
			<div class="text-[10px] font-semibold uppercase tracking-[0.22em] text-brand-gold">
				<?php echo esc_html( $primary_term ? $primary_term : __( 'Rapport', 'crades-theme' ) ); ?>
			</div>
			<div class="text-[10px] font-medium uppercase tracking-[0.18em] text-transparent select-none">
				<?php echo esc_html( $primary_term ? $primary_term : __( 'Rapport', 'crades-theme' ) ); ?>
			</div>
		</div>

		<div class="mt-auto flex items-center justify-between gap-3 pt-4 text-[10px] text-slate-300">
			<span class="font-medium"><?php echo esc_html( $year ); ?></span>
			<div class="flex items-center gap-4">
				<button
					type="button"
					data-pdf-open
					class="font-medium text-brand-blue transition-colors hover:text-brand-navy hover:underline disabled:cursor-not-allowed disabled:opacity-50"
					aria-label="<?php echo esc_attr( sprintf( __( 'Ouvrir le document %s', 'crades-theme' ), get_the_title() ) ); ?>"
					<?php disabled( empty( $pdf_url ) ); ?>
				>
					<?php esc_html_e( 'Ouvrir', 'crades-theme' ); ?>
				</button>

				<?php if ( $pdf_url ) : ?>
					<a
						class="font-medium text-brand-blue transition-colors hover:text-brand-navy hover:underline"
						href="<?php echo esc_url( $pdf_url ); ?>"
						target="_blank"
						rel="noopener"
					>
						<?php esc_html_e( 'Télécharger', 'crades-theme' ); ?>
					</a>
				<?php else : ?>
					<span class="text-slate-400">
						<?php esc_html_e( 'PDF manquant', 'crades-theme' ); ?>
					</span>
				<?php endif; ?>
			</div>
		</div>
	</div>
</article>
