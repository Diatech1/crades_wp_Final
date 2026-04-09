<?php
/**
 * Page content template.
 *
 * @package CRADES_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$intro = crades_get_intro_text( get_the_ID() );
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if ( ! crades_is_built_with_elementor( get_the_ID() ) ) : ?>
		<section class="bg-brand-navy py-16 lg:py-20">
			<div class="mx-auto max-w-6xl px-4 sm:px-6">
				<nav class="text-xs text-gray-400 mb-4">
					<a class="hover:text-white" href="<?php echo esc_url( home_url( '/' ) ); ?>">
						<?php esc_html_e( 'Accueil', 'crades-theme' ); ?>
					</a>
					<span class="mx-2 text-gray-600">/</span>
					<span class="text-gray-300"><?php the_title(); ?></span>
				</nav>

				<h1 class="font-display text-2xl lg:text-3xl text-white">
					<?php the_title(); ?>
				</h1>

				<?php if ( $intro ) : ?>
					<p class="text-gray-400 mt-2 max-w-2xl text-sm leading-relaxed">
						<?php echo esc_html( $intro ); ?>
					</p>
				<?php endif; ?>
			</div>
		</section>

		<section class="py-12">
			<div class="mx-auto max-w-6xl px-4 sm:px-6">
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
				<?php edit_post_link( __( 'Modifier cette page', 'crades-theme' ), '<p class="mt-6 text-sm font-medium text-brand-blue">', '</p>' ); ?>
			</div>
		</section>
	<?php else : ?>
		<section class="crades-elementor-shell">
			<?php the_content(); ?>
			<div class="mx-auto max-w-6xl px-4 py-6 sm:px-6">
				<?php edit_post_link( __( 'Modifier cette page', 'crades-theme' ), '<p class="text-sm font-medium text-brand-blue">', '</p>' ); ?>
			</div>
		</section>
	<?php endif; ?>
</article>
