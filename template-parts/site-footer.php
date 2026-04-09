<?php
/**
 * Shared site footer matching the CRADES Hono interface.
 *
 * @package CRADES_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$organization = crades_get_organization_details();
?>
<footer class="crades-site-footer border-t border-gray-100 mt-20">
	<div class="max-w-6xl mx-auto px-4 sm:px-6 py-12">
		<div class="crades-site-footer__grid grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-10 md:gap-12 items-start">
			<div class="md:col-span-1 self-start">
				<div class="flex items-start">
					<img src="<?php echo esc_url( $organization['logo_uri'] ); ?>" alt="<?php echo esc_attr( $organization['short_name'] ); ?>" class="crades-site-footer__logo h-20 sm:h-24 w-auto">
				</div>
			</div>
			<div>
				<h4 class="text-xs font-semibold text-gray-800 uppercase tracking-wider mb-3"><?php esc_html_e( 'Navigation', 'crades-theme' ); ?></h4>
				<ul class="space-y-2 text-xs text-gray-400">
					<li><a href="<?php echo esc_url( crades_get_page_url( 'a-propos' ) ); ?>" class="hover:text-gray-600"><?php esc_html_e( 'Ã€ propos', 'crades-theme' ); ?></a></li>
					<li><a href="<?php echo esc_url( crades_get_page_url( 'publications' ) ); ?>" class="hover:text-gray-600"><?php esc_html_e( 'Publications', 'crades-theme' ); ?></a></li>
				</ul>
			</div>
			<div>
				<h4 class="text-xs font-semibold text-gray-800 uppercase tracking-wider mb-3"><?php esc_html_e( 'Tableaux de bord', 'crades-theme' ); ?></h4>
				<ul class="space-y-2 text-xs text-gray-400">
					<li><a href="<?php echo esc_url( crades_get_page_url( 'commerce-exterieur' ) ); ?>" class="hover:text-gray-600"><?php esc_html_e( 'Commerce extÃ©rieur', 'crades-theme' ); ?></a></li>
					<li><a href="<?php echo esc_url( crades_get_page_url( 'commerce-interieur' ) ); ?>" class="hover:text-gray-600"><?php esc_html_e( 'Commerce intÃ©rieur', 'crades-theme' ); ?></a></li>
					<li><a href="<?php echo esc_url( crades_get_page_url( 'industrie' ) ); ?>" class="hover:text-gray-600"><?php esc_html_e( 'Industrie', 'crades-theme' ); ?></a></li>
					<li><a href="<?php echo esc_url( crades_get_page_url( 'pme-pmi' ) ); ?>" class="hover:text-gray-600"><?php esc_html_e( 'PME/PMI', 'crades-theme' ); ?></a></li>
				</ul>
			</div>
			<div>
				<h4 class="text-xs font-semibold text-gray-800 uppercase tracking-wider mb-3"><?php esc_html_e( 'Contact', 'crades-theme' ); ?></h4>
				<div class="space-y-2 text-xs text-gray-400">
					<p><?php esc_html_e( 'SphÃ¨re MinistÃ©rielle Habib THIAM,', 'crades-theme' ); ?><br><?php esc_html_e( 'BÃ¢timent C, Diamniadio', 'crades-theme' ); ?></p>
					<p><?php echo esc_html( $organization['phones'] ); ?></p>
					<p><?php echo esc_html( $organization['email'] ); ?></p>
					<p><?php echo esc_html( wp_parse_url( $organization['website'], PHP_URL_HOST ) ); ?></p>
				</div>
			</div>
		</div>
	</div>
	<div class="crades-site-footer__bottom border-t border-gray-50">
		<div class="max-w-6xl mx-auto px-4 sm:px-6 py-4 flex flex-col sm:flex-row items-center justify-between text-[11px] text-gray-300">
			<p>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php echo esc_html( $organization['short_name'] ); ?> - <?php esc_html_e( 'Tous droits rÃ©servÃ©s', 'crades-theme' ); ?></p>
			<p><?php esc_html_e( 'RÃ©publique du SÃ©nÃ©gal', 'crades-theme' ); ?></p>
		</div>
	</div>
</footer>

