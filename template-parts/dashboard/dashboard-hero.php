<?php
/**
 * Dashboard hero partial.
 *
 * @package CRADES_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$dashboard = isset( $args['dashboard'] ) ? $args['dashboard'] : array();

if ( empty( $dashboard ) ) {
	return;
}
?>
<section class="bg-brand-navy py-16 lg:py-20 text-white">
	<div class="mx-auto max-w-7xl px-4 sm:px-6">
		<nav class="mb-4 text-xs text-white/60">
			<a class="hover:text-white" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php esc_html_e( 'Accueil', 'crades-theme' ); ?>
			</a>
			<span class="mx-2">/</span>
			<a class="hover:text-white" href="<?php echo esc_url( crades_get_page_url( 'tableaux-de-bord' ) ); ?>">
				<?php esc_html_e( 'Tableaux de bord', 'crades-theme' ); ?>
			</a>
			<span class="mx-2">/</span>
			<span class="text-white/80"><?php echo esc_html( $dashboard['title'] ); ?></span>
		</nav>

		<div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_320px] lg:items-end">
			<div>
				<p class="text-xs font-semibold uppercase tracking-[0.24em] text-brand-gold">
					<?php echo esc_html( $dashboard['eyebrow'] ); ?>
				</p>
				<h1 class="mt-4 font-display text-3xl font-bold lg:text-5xl">
					<?php echo esc_html( $dashboard['title'] ); ?>
				</h1>
				<p class="mt-4 max-w-3xl text-sm leading-relaxed text-white/75 sm:text-base">
					<?php echo esc_html( $dashboard['description'] ); ?>
				</p>
			</div>

			<div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-sm">
				<p class="text-xs font-semibold uppercase tracking-[0.24em] text-white/60">
					<?php esc_html_e( 'Etat du module', 'crades-theme' ); ?>
				</p>
				<p class="mt-3 text-lg font-semibold text-white">
					<?php echo esc_html( $dashboard['status'] ); ?>
				</p>
				<p class="mt-2 text-sm leading-relaxed text-white/65">
					<?php esc_html_e( 'Les conteneurs, cartes et états de visualisation sont prêts. La liaison à la source de données viendra à l’étape suivante.', 'crades-theme' ); ?>
				</p>
			</div>
		</div>
	</div>
</section>
