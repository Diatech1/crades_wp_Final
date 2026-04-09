<?php
/**
 * Archive card template.
 *
 * @package CRADES_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_type_object = get_post_type_object( get_post_type() );
$post_type_label  = $post_type_object ? $post_type_object->labels->singular_name : __( 'Article', 'crades-theme' );
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm transition-all hover:-translate-y-0.5 hover:border-brand-blue/20 hover:shadow-soft' ); ?>>
	<a class="block" href="<?php the_permalink(); ?>">
		<?php if ( has_post_thumbnail() ) : ?>
			<div class="aspect-[4/3] overflow-hidden bg-slate-100">
				<?php the_post_thumbnail( 'medium_large', array( 'class' => 'h-full w-full object-cover' ) ); ?>
			</div>
		<?php else : ?>
			<div class="flex aspect-[4/3] items-center justify-center bg-brand-frost text-brand-blue">
				<i class="fa-regular fa-file-lines text-3xl" aria-hidden="true"></i>
			</div>
		<?php endif; ?>

		<div class="p-5">
			<p class="text-xs font-semibold uppercase tracking-[0.24em] text-brand-gold">
				<?php echo esc_html( $post_type_label ); ?>
			</p>
			<h2 class="mt-3 text-lg font-semibold text-slate-900">
				<?php the_title(); ?>
			</h2>
			<div class="mt-3 text-sm leading-6 text-slate-500">
				<?php echo esc_html( wp_trim_words( get_the_excerpt(), 24 ) ); ?>
			</div>
			<span class="mt-4 inline-flex items-center text-sm font-semibold text-brand-blue">
				<?php esc_html_e( 'Lire', 'crades-theme' ); ?>
			</span>
		</div>
	</a>
</article>
