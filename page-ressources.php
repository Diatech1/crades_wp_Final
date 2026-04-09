<?php
/**
 * Exact Ressources page ported from the Hono app.
 *
 * @package CRADES_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<section class="bg-brand-navy py-16 lg:py-20">
	<div class="max-w-6xl mx-auto px-4 sm:px-6">
		<nav class="text-xs text-gray-400 mb-4">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hover:text-white"><?php esc_html_e( 'Accueil', 'crades-theme' ); ?></a>
			<span class="mx-2">/</span>
			<span class="text-gray-300"><?php esc_html_e( 'Ressources', 'crades-theme' ); ?></span>
		</nav>
		<h1 class="font-display text-2xl lg:text-3xl text-white"><?php esc_html_e( 'Ressources', 'crades-theme' ); ?></h1>
		<p class="text-gray-400 mt-2 max-w-2xl text-sm leading-relaxed"><?php esc_html_e( 'Section en cours de preparation. Les ressources seront bientot disponibles ici.', 'crades-theme' ); ?></p>
	</div>
</section>

<section class="py-12 bg-gray-50">
	<div class="max-w-6xl mx-auto px-4 sm:px-6">
		<div class="bg-white rounded-2xl border border-gray-100 px-6 py-14 sm:px-10 sm:py-16 text-center shadow-sm">
			<div class="mx-auto w-16 h-16 rounded-2xl bg-brand-frost flex items-center justify-center mb-5">
				<i class="fas fa-tools text-2xl text-brand-blue"></i>
			</div>
			<span class="text-xs font-semibold uppercase tracking-[0.22em] text-brand-gold"><?php esc_html_e( 'Bientôt disponible', 'crades-theme' ); ?></span>
			<h2 class="mt-3 text-2xl sm:text-3xl font-display text-gray-900"><?php esc_html_e( 'Ressources en construction', 'crades-theme' ); ?></h2>
			<p class="mt-3 text-sm sm:text-base text-gray-500 max-w-2xl mx-auto leading-relaxed">
				<?php esc_html_e( "Nous préparons une nouvelle bibliothèque de documents, de liens utiles et d'accès rapides pour centraliser les contenus CRADES.", 'crades-theme' ); ?>
			</p>
			<div class="mt-8 flex flex-wrap justify-center gap-3">
				<a href="<?php echo esc_url( crades_get_page_url( 'publications' ) ); ?>" class="inline-flex items-center rounded-full bg-brand-blue px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-navy transition-colors">
					<?php esc_html_e( 'Aller aux publications', 'crades-theme' ); ?>
				</a>
				<a href="<?php echo esc_url( crades_get_page_url( 'tableaux-de-bord' ) ); ?>" class="inline-flex items-center rounded-full border border-gray-200 px-5 py-2.5 text-sm font-semibold text-gray-700 hover:border-brand-blue hover:text-brand-blue transition-colors">
					<?php esc_html_e( 'Voir les tableaux de bord', 'crades-theme' ); ?>
				</a>
			</div>
		</div>
	</div>
</section>
<?php
get_footer();
