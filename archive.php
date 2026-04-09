<?php
/**
 * Generic archive template.
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
		<nav class="mb-4 text-xs text-white/60">
			<a class="hover:text-white" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php esc_html_e( 'Accueil', 'crades-theme' ); ?>
			</a>
			<span class="mx-2">/</span>
			<span class="text-white/80"><?php the_archive_title(); ?></span>
		</nav>

		<h1 class="font-display text-3xl font-bold lg:text-5xl">
			<?php the_archive_title(); ?>
		</h1>

		<?php if ( get_the_archive_description() ) : ?>
			<div class="mt-3 max-w-2xl text-sm leading-relaxed text-white/75">
				<?php the_archive_description(); ?>
			</div>
		<?php endif; ?>
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
				<?php esc_html_e( 'Aucun contenu n a ete trouve pour cette archive.', 'crades-theme' ); ?>
			</p>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
