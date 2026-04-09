<?php
/**
 * Single post content template.
 *
 * @package CRADES_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<section class="bg-brand-navy py-16 lg:py-20 text-white">
		<div class="mx-auto max-w-6xl px-4 sm:px-6">
			<nav class="mb-4 text-xs text-white/60">
				<a class="hover:text-white" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php esc_html_e( 'Accueil', 'crades-theme' ); ?>
				</a>
				<span class="mx-2">/</span>
				<a class="hover:text-white" href="<?php echo esc_url( get_post_type_archive_link( get_post_type() ) ?: home_url( '/' ) ); ?>">
					<?php echo esc_html( post_type_archive_title( '', false ) ?: __( 'Actualites', 'crades-theme' ) ); ?>
				</a>
				<span class="mx-2">/</span>
				<span class="text-white/80"><?php the_title(); ?></span>
			</nav>

			<p class="text-xs font-semibold uppercase tracking-[0.24em] text-white/60">
				<?php echo esc_html( get_the_date() ); ?>
			</p>
			<h1 class="mt-4 font-display text-3xl font-bold lg:text-5xl">
				<?php the_title(); ?>
			</h1>
			<?php if ( has_excerpt() ) : ?>
				<p class="mt-3 max-w-2xl text-sm leading-relaxed text-white/75">
					<?php echo esc_html( get_the_excerpt() ); ?>
				</p>
			<?php endif; ?>
		</div>
	</section>

	<section class="py-12">
		<div class="mx-auto max-w-4xl px-4 sm:px-6">
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="mb-8 overflow-hidden rounded-3xl">
					<?php the_post_thumbnail( 'large', array( 'class' => 'h-auto w-full' ) ); ?>
				</div>
			<?php endif; ?>

			<div class="entry-content text-sm leading-7 text-slate-600">
				<?php the_content(); ?>
				<?php
				wp_link_pages(
					array(
						'before' => '<div class="mt-6 text-sm font-medium text-slate-500">' . esc_html__( 'Pages :', 'crades-theme' ),
						'after'  => '</div>',
					)
				);
				?>
			</div>

			<?php edit_post_link( __( 'Modifier cet article', 'crades-theme' ), '<p class="mt-6 text-sm font-medium text-brand-blue">', '</p>' ); ?>
		</div>
	</section>
</article>
