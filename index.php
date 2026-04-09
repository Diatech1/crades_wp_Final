<?php
/**
 * Main fallback template.
 *
 * @package CRADES_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<section class="bg-brand-navy py-16 lg:py-20 text-white">
	<div class="mx-auto max-w-6xl px-4 sm:px-6">
		<p class="text-xs font-semibold uppercase tracking-[0.24em] text-white/60">
			<?php esc_html_e( 'CRADES', 'crades-theme' ); ?>
		</p>
		<h1 class="mt-4 font-display text-3xl font-bold lg:text-5xl">
			<?php bloginfo( 'name' ); ?>
		</h1>
		<p class="mt-3 max-w-2xl text-sm leading-relaxed text-white/75">
			<?php esc_html_e( 'Theme fallback template for generic content rendering.', 'crades-theme' ); ?>
		</p>
	</div>
</section>

<section class="py-12">
	<div class="mx-auto max-w-6xl px-4 sm:px-6">
		<?php if ( have_posts() ) : ?>
			<div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/content', 'archive' );
				endwhile;
				?>
			</div>

			<div class="mt-10">
				<?php the_posts_pagination(); ?>
			</div>
		<?php else : ?>
			<p class="text-sm text-slate-500">
				<?php esc_html_e( 'Aucun contenu disponible pour le moment.', 'crades-theme' ); ?>
			</p>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
