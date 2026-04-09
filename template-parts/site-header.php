<?php
/**
 * Shared site header matching the CRADES Hono interface.
 *
 * @package CRADES_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$organization     = crades_get_organization_details();
$is_home          = is_front_page() || is_home();
$is_about         = is_page( 'a-propos' );
$is_publications  = is_page( 'publications' ) || crades_is_pdf_library_view();
$is_dashboard_hub = is_page( 'tableaux-de-bord' );
$is_commerce_ext  = is_page( 'commerce-exterieur' ) || is_page_template( 'templates/page-dashboard-commerce-exterieur.php' );
$is_commerce_int  = is_page( 'commerce-interieur' ) || is_page_template( 'templates/page-dashboard-commerce-interieur.php' );
$is_industrie     = is_page( 'industrie' ) || is_page_template( 'templates/page-dashboard-industrie.php' );
$is_pme_pmi       = is_page( 'pme-pmi' ) || is_page_template( 'templates/page-dashboard-pme-pmi.php' );
$is_dashboards    = $is_dashboard_hub || $is_commerce_ext || $is_commerce_int || $is_industrie || $is_pme_pmi;
$is_ressources    = is_page( 'ressources' );
$is_contact       = is_page( 'contact' );
$french_url       = esc_url( add_query_arg( 'lang', 'fr' ) );
$english_url      = esc_url( add_query_arg( 'lang', 'en' ) );
?>
<div class="crades-utility-strip bg-white border-b border-gray-100">
	<div class="max-w-6xl mx-auto px-4 sm:px-6 py-1 flex justify-center">
		<img src="<?php echo esc_url( $organization['ministry_logo_uri'] ); ?>" alt="<?php echo esc_attr( $organization['ministry_name'] ); ?>" class="crades-utility-strip__logo h-14 sm:h-16 w-auto">
	</div>
</div>

<header class="crades-site-header bg-white border-b border-gray-100 sticky top-0 z-50">
	<div class="max-w-6xl mx-auto px-4 sm:px-6">
		<div class="crades-site-header__inner flex items-center justify-between h-20">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="crades-site-brand flex items-center gap-3" aria-label="<?php echo esc_attr( $organization['short_name'] ); ?>">
				<img src="<?php echo esc_url( $organization['logo_uri'] ); ?>" alt="<?php echo esc_attr( $organization['short_name'] ); ?>" class="crades-site-brand__logo h-16 w-auto">
			</a>

			<nav class="crades-site-nav hidden lg:flex items-center gap-5 xl:gap-6 text-[13px] font-medium text-gray-500" aria-label="<?php esc_attr_e( 'Navigation principale', 'crades-theme' ); ?>">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hover:text-brand-blue transition-colors whitespace-nowrap <?php echo $is_home ? 'text-brand-blue' : ''; ?>">
					<?php esc_html_e( 'Accueil', 'crades-theme' ); ?>
				</a>
				<a href="<?php echo esc_url( crades_get_page_url( 'a-propos' ) ); ?>" class="hover:text-brand-blue transition-colors whitespace-nowrap <?php echo $is_about ? 'text-brand-blue' : ''; ?>">
					<?php esc_html_e( 'À propos', 'crades-theme' ); ?>
				</a>
				<a href="<?php echo esc_url( crades_get_page_url( 'publications' ) ); ?>" class="hover:text-brand-blue transition-colors whitespace-nowrap <?php echo $is_publications ? 'text-brand-blue' : ''; ?>">
					<?php esc_html_e( 'Publications', 'crades-theme' ); ?>
				</a>
				<div class="crades-dashboard-menu relative group">
					<a href="<?php echo esc_url( crades_get_page_url( 'tableaux-de-bord' ) ); ?>" class="hover:text-brand-blue transition-colors flex items-center gap-1 whitespace-nowrap">
						<span class="<?php echo $is_dashboards ? 'text-brand-blue' : ''; ?>"><?php esc_html_e( 'Tableaux de bord', 'crades-theme' ); ?></span>
						<i class="fas fa-chevron-down text-[8px] text-gray-400 group-hover:text-brand-blue transition-colors" aria-hidden="true"></i>
					</a>
					<div class="crades-dashboard-dropdown absolute top-full left-0 pt-2 hidden group-hover:block">
						<div class="bg-white border border-gray-100 rounded-lg shadow-lg py-2 min-w-[220px]">
							<a href="<?php echo esc_url( crades_get_page_url( 'commerce-exterieur' ) ); ?>" class="block px-4 py-2 text-[13px] text-gray-500 hover:text-brand-blue hover:bg-gray-50 transition-colors <?php echo $is_commerce_ext ? 'text-brand-blue font-semibold' : ''; ?>">
								<?php esc_html_e( 'Commerce extérieur', 'crades-theme' ); ?>
							</a>
							<a href="<?php echo esc_url( crades_get_page_url( 'commerce-interieur' ) ); ?>" class="block px-4 py-2 text-[13px] text-gray-500 hover:text-brand-blue hover:bg-gray-50 transition-colors <?php echo $is_commerce_int ? 'text-brand-blue font-semibold' : ''; ?>">
								<?php esc_html_e( 'Commerce intérieur', 'crades-theme' ); ?>
							</a>
							<a href="<?php echo esc_url( crades_get_page_url( 'industrie' ) ); ?>" class="block px-4 py-2 text-[13px] text-gray-500 hover:text-brand-blue hover:bg-gray-50 transition-colors <?php echo $is_industrie ? 'text-brand-blue font-semibold' : ''; ?>">
								<?php esc_html_e( 'Industrie', 'crades-theme' ); ?>
							</a>
							<a href="<?php echo esc_url( crades_get_page_url( 'pme-pmi' ) ); ?>" class="block px-4 py-2 text-[13px] text-gray-500 hover:text-brand-blue hover:bg-gray-50 transition-colors <?php echo $is_pme_pmi ? 'text-brand-blue font-semibold' : ''; ?>">
								<?php esc_html_e( 'PME/PMI', 'crades-theme' ); ?>
							</a>
						</div>
					</div>
				</div>
				<a href="<?php echo esc_url( crades_get_page_url( 'ressources' ) ); ?>" class="hover:text-brand-blue transition-colors whitespace-nowrap <?php echo $is_ressources ? 'text-brand-blue' : ''; ?>">
					<?php esc_html_e( 'Ressources', 'crades-theme' ); ?>
				</a>
				<a href="<?php echo esc_url( crades_get_page_url( 'contact' ) ); ?>" class="hover:text-brand-blue transition-colors whitespace-nowrap <?php echo $is_contact ? 'text-brand-blue' : ''; ?>">
					<?php esc_html_e( 'Contact', 'crades-theme' ); ?>
				</a>
			</nav>

			<div class="crades-header-actions flex items-center gap-3">
				<div class="crades-language-switch hidden sm:flex items-center gap-1 text-xs text-gray-400 border border-gray-200 rounded-full px-2 py-1">
					<a href="<?php echo $french_url; ?>" class="text-brand-blue font-semibold">FR</a>
					<span class="text-gray-300">|</span>
					<a href="<?php echo $english_url; ?>" class="hover:text-gray-600">EN</a>
				</div>
				<button type="button" class="lg:hidden w-8 h-8 flex items-center justify-center text-gray-500" aria-controls="mobile-navigation" aria-expanded="false" data-nav-toggle>
					<span class="screen-reader-text" data-nav-label><?php esc_html_e( 'Ouvrir la navigation', 'crades-theme' ); ?></span>
					<i class="fas fa-bars" data-nav-icon aria-hidden="true"></i>
				</button>
			</div>
		</div>
	</div>
</header>

<div id="mobile-navigation" class="crades-mobile-panel hidden fixed inset-0 z-[60] bg-white lg:hidden" data-mobile-panel aria-hidden="true">
	<div class="crades-mobile-panel__header flex items-center justify-between p-4 border-b border-gray-100">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="crades-mobile-panel__brand flex items-center gap-2" aria-label="<?php echo esc_attr( $organization['short_name'] ); ?>">
			<img src="<?php echo esc_url( $organization['logo_uri'] ); ?>" alt="<?php echo esc_attr( $organization['short_name'] ); ?>" class="crades-mobile-panel__logo h-20 w-auto">
		</a>
		<button type="button" class="crades-mobile-panel__close w-8 h-8 flex items-center justify-center text-gray-400" data-nav-toggle>
			<span class="screen-reader-text"><?php esc_html_e( 'Fermer la navigation', 'crades-theme' ); ?></span>
			<i class="fas fa-times" aria-hidden="true"></i>
		</button>
	</div>
	<nav class="crades-mobile-panel__nav p-4 space-y-1" aria-label="<?php esc_attr_e( 'Navigation mobile', 'crades-theme' ); ?>">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="crades-mobile-panel__link block px-4 py-3 rounded-lg text-gray-600 hover:bg-gray-50 font-medium text-sm" data-mobile-nav-link><?php esc_html_e( 'Accueil', 'crades-theme' ); ?></a>
		<a href="<?php echo esc_url( crades_get_page_url( 'a-propos' ) ); ?>" class="crades-mobile-panel__link block px-4 py-3 rounded-lg text-gray-600 hover:bg-gray-50 font-medium text-sm" data-mobile-nav-link><?php esc_html_e( 'À propos', 'crades-theme' ); ?></a>
		<a href="<?php echo esc_url( crades_get_page_url( 'publications' ) ); ?>" class="crades-mobile-panel__link block px-4 py-3 rounded-lg text-gray-600 hover:bg-gray-50 font-medium text-sm" data-mobile-nav-link><?php esc_html_e( 'Publications', 'crades-theme' ); ?></a>
		<div class="crades-mobile-panel__group pt-2">
			<a href="<?php echo esc_url( crades_get_page_url( 'tableaux-de-bord' ) ); ?>" class="crades-mobile-panel__link block px-4 py-3 rounded-lg text-gray-600 hover:bg-gray-50 font-medium text-sm" data-mobile-nav-link><?php esc_html_e( 'Tableaux de bord', 'crades-theme' ); ?></a>
			<a href="<?php echo esc_url( crades_get_page_url( 'commerce-exterieur' ) ); ?>" class="crades-mobile-panel__sublink block pl-8 pr-4 py-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-50 font-medium text-sm" data-mobile-nav-link><i class="fas fa-chevron-right text-[8px] mr-2" aria-hidden="true"></i><?php esc_html_e( 'Commerce extérieur', 'crades-theme' ); ?></a>
			<a href="<?php echo esc_url( crades_get_page_url( 'commerce-interieur' ) ); ?>" class="crades-mobile-panel__sublink block pl-8 pr-4 py-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-50 font-medium text-sm" data-mobile-nav-link><i class="fas fa-chevron-right text-[8px] mr-2" aria-hidden="true"></i><?php esc_html_e( 'Commerce intérieur', 'crades-theme' ); ?></a>
			<a href="<?php echo esc_url( crades_get_page_url( 'industrie' ) ); ?>" class="crades-mobile-panel__sublink block pl-8 pr-4 py-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-50 font-medium text-sm" data-mobile-nav-link><i class="fas fa-chevron-right text-[8px] mr-2" aria-hidden="true"></i><?php esc_html_e( 'Industrie', 'crades-theme' ); ?></a>
			<a href="<?php echo esc_url( crades_get_page_url( 'pme-pmi' ) ); ?>" class="crades-mobile-panel__sublink block pl-8 pr-4 py-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-50 font-medium text-sm" data-mobile-nav-link><i class="fas fa-chevron-right text-[8px] mr-2" aria-hidden="true"></i><?php esc_html_e( 'PME/PMI', 'crades-theme' ); ?></a>
		</div>
		<a href="<?php echo esc_url( crades_get_page_url( 'ressources' ) ); ?>" class="crades-mobile-panel__link block px-4 py-3 rounded-lg text-gray-600 hover:bg-gray-50 font-medium text-sm" data-mobile-nav-link><?php esc_html_e( 'Ressources', 'crades-theme' ); ?></a>
		<a href="<?php echo esc_url( crades_get_page_url( 'contact' ) ); ?>" class="crades-mobile-panel__link block px-4 py-3 rounded-lg text-gray-600 hover:bg-gray-50 font-medium text-sm" data-mobile-nav-link><?php esc_html_e( 'Contact', 'crades-theme' ); ?></a>
		<div class="crades-mobile-panel__language pt-3 border-t border-gray-100 mt-3 flex gap-3 px-4 text-sm">
			<a href="<?php echo $french_url; ?>" class="text-brand-blue font-semibold"><?php esc_html_e( 'Français', 'crades-theme' ); ?></a>
			<a href="<?php echo $english_url; ?>" class="text-gray-400"><?php esc_html_e( 'English', 'crades-theme' ); ?></a>
		</div>
	</nav>
</div>
