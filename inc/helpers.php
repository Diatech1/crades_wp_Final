<?php
/**
 * Helper functions.
 *
 * @package CRADES_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns a stable asset version based on file modification time.
 *
 * @param string $relative_path Relative path from the theme root.
 * @return string
 */
function crades_asset_version( $relative_path ) {
	$absolute_path = get_template_directory() . '/' . ltrim( $relative_path, '/' );

	if ( file_exists( $absolute_path ) ) {
		return (string) filemtime( $absolute_path );
	}

	return wp_get_theme()->get( 'Version' );
}

/**
 * Returns a theme asset URI.
 *
 * @param string $relative_path Relative path from the theme root.
 * @return string
 */
function crades_get_theme_asset_uri( $relative_path ) {
	return trailingslashit( get_template_directory_uri() ) . ltrim( $relative_path, '/' );
}

/**
 * Returns the actual WordPress URL for a CRADES page slug.
 *
 * This keeps navigation working on fresh installs even when permalink rules
 * or page creation timing are different from the local dev site.
 *
 * @param string $slug Page slug.
 * @return string
 */
function crades_get_page_url( $slug ) {
	static $cache = array();

	$slug = trim( (string) $slug, '/' );

	if ( isset( $cache[ $slug ] ) ) {
		return $cache[ $slug ];
	}

	if ( '' === $slug ) {
		$cache[ $slug ] = home_url( '/' );
		return $cache[ $slug ];
	}

	$page = get_page_by_path( $slug, OBJECT, 'page' );

	if ( ! ( $page instanceof WP_Post ) ) {
		$required_pages = function_exists( 'crades_get_required_pages' ) ? crades_get_required_pages() : array();

		if ( isset( $required_pages[ $slug ] ) && function_exists( 'crades_sync_required_pages' ) ) {
			crades_sync_required_pages();
			$page = get_page_by_path( $slug, OBJECT, 'page' );
		}
	}

	if ( $page instanceof WP_Post ) {
		$cache[ $slug ] = add_query_arg( 'page_id', (int) $page->ID, home_url( '/' ) );
		return $cache[ $slug ];
	}

	$cache[ $slug ] = home_url( '/' . $slug . '/' );

	return $cache[ $slug ];
}

/**
 * Returns shared organization details.
 *
 * @return array<string, string>
 */
function crades_get_organization_details() {
	return array(
		'ministry_name'     => __( "Ministère de l'Industrie et du Commerce", 'crades-theme' ),
		'short_name'        => 'CRADES',
		'organization_name' => __( "Centre de Recherche, d'Analyse des Échanges et Statistiques", 'crades-theme' ),
		'tagline'           => __( "Structure technique spécialisée dans la recherche, le traitement et l'analyse des informations commerciales et industrielles.", 'crades-theme' ),
		'address'           => __( 'Sphère Ministérielle Habib THIAM, Bâtiment C, Diamniadio.', 'crades-theme' ),
		'short_address'     => __( 'Sphère Ministérielle Habib THIAM, Diamniadio', 'crades-theme' ),
		'phones'            => __( '33 869 21 20 / 33 824 45 12', 'crades-theme' ),
		'email'             => 'info@crades.sn',
		'website'           => 'https://www.crades.sn',
		'logo_uri'          => crades_get_theme_asset_uri( 'assets/images/logo-crades.png' ),
		'ministry_logo_uri' => crades_get_theme_asset_uri( 'assets/images/logo-mincom.png' ),
	);
}

/**
 * Returns the Tailwind CDN config string.
 *
 * @return string
 */
function crades_get_tailwind_config() {
	return <<<'JS'
window.tailwind = window.tailwind || {};
window.tailwind.config = {
	theme: {
		extend: {
			colors: {
				brand: {
					navy: '#032d6b',
					blue: '#044bad',
					sky: '#3a7fd4',
					ice: '#c7ddf5',
					frost: '#eef4fb',
					gold: '#b8943e',
					'gold-light': '#d4b262'
				}
			},
			fontFamily: {
				sans: ['Montserrat', 'system-ui', 'sans-serif'],
				display: ['Montserrat', 'system-ui', 'sans-serif']
			},
			boxShadow: {
				soft: '0 20px 45px rgba(15, 23, 42, 0.08)'
			}
		}
	}
};
JS;
}

/**
 * Returns bundled publication seeds used by the current CRADES interface.
 *
 * @return array<int, array<string, string>>
 */
function crades_get_seed_publications() {
	return array(
		array(
			'title'    => 'Bulletin mensuel - produits de base - Mai 2025',
			'type'     => 'Bulletin mensuel',
			'taxonomy' => 'Bulletins mensuels',
			'year'     => '2025',
			'desc'     => 'Suivi mensuel des produits de base, avec les tendances de marché et les principaux indicateurs disponibles pour mai 2025.',
			'href'     => crades_get_theme_asset_uri( 'assets/docs/publications/bulletin-mensuel-produits-de-base-mai-2025.pdf' ),
		),
		array(
			'title'    => 'CRADES_bulletin annuel 2025 - marchés des produits de base',
			'type'     => 'Bulletin annuel',
			'taxonomy' => 'Bulletins annuels',
			'year'     => '2025',
			'desc'     => 'Vue annuelle 2025 sur les marchés des produits de base, avec les analyses et tableaux de synthèse du CRADES.',
			'href'     => crades_get_theme_asset_uri( 'assets/docs/publications/crades-bulletin-annuel-2025-marches-produits-de-base.pdf' ),
		),
	);
}

/**
 * Returns fallback primary navigation items.
 *
 * @return array<int, array<string, string|bool>>
 */
function crades_get_fallback_menu_items() {
	return array(
		array(
			'label'   => __( 'Accueil', 'crades-theme' ),
			'url'     => home_url( '/' ),
			'current' => is_front_page() || is_home(),
		),
		array(
			'label'   => __( 'À propos', 'crades-theme' ),
			'url'     => crades_get_page_url( 'a-propos' ),
			'current' => is_page( 'a-propos' ),
		),
		array(
			'label'   => __( 'Publications', 'crades-theme' ),
			'url'     => crades_get_page_url( 'publications' ),
			'current' => is_page( 'publications' ) || is_post_type_archive( 'publication' ) || is_singular( 'publication' ) || is_post_type_archive( 'rapports' ) || is_singular( 'rapports' ) || crades_is_pdf_library_view(),
		),
		array(
			'label'   => __( 'Tableaux de bord', 'crades-theme' ),
			'url'     => crades_get_page_url( 'tableaux-de-bord' ),
			'current' => is_page( 'tableaux-de-bord' ) || crades_is_dashboard_page_template(),
		),
		array(
			'label'   => __( 'Ressources', 'crades-theme' ),
			'url'     => crades_get_page_url( 'ressources' ),
			'current' => is_page( 'ressources' ),
		),
		array(
			'label'   => __( 'Contact', 'crades-theme' ),
			'url'     => crades_get_page_url( 'contact' ),
			'current' => is_page( 'contact' ),
		),
	);
}

/**
 * Controls access to rapport meta in the REST API.
 *
 * @param bool   $allowed  Whether the user can access the meta.
 * @param string $meta_key Meta key.
 * @param int    $post_id  Post ID.
 * @param int    $user_id  User ID.
 * @return bool
 */
function crades_can_manage_rapport_meta( $allowed, $meta_key, $post_id, $user_id ) {
	unset( $allowed, $meta_key );

	return user_can( $user_id, 'edit_post', $post_id );
}

/**
 * Renders a fallback menu when no WordPress menu is assigned.
 *
 * @param string $class Optional wrapper classes.
 * @return void
 */
function crades_render_fallback_menu( $class = '' ) {
	$items = crades_get_fallback_menu_items();
	?>
	<ul class="<?php echo esc_attr( $class ); ?>">
		<?php foreach ( $items as $item ) : ?>
			<li class="menu-item<?php echo ! empty( $item['current'] ) ? ' is-current' : ''; ?>">
				<a class="transition-colors hover:text-brand-blue<?php echo ! empty( $item['current'] ) ? ' text-brand-blue' : ''; ?>" href="<?php echo esc_url( $item['url'] ); ?>">
					<?php echo esc_html( $item['label'] ); ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php
}

/**
 * Returns compact cards for the starter home page.
 *
 * @return array<int, array<string, string>>
 */
function crades_get_home_cards() {
	return array(
		array(
			'title'       => __( 'À propos', 'crades-theme' ),
			'description' => __( 'Présentation institutionnelle, mandat, missions et textes de référence du CRADES.', 'crades-theme' ),
			'url'         => crades_get_page_url( 'a-propos' ),
			'icon'        => 'fa-solid fa-landmark',
		),
		array(
			'title'       => __( 'Publications', 'crades-theme' ),
			'description' => __( 'Bibliothèque de rapports, bulletins, fiches synoptiques et documents à consulter.', 'crades-theme' ),
			'url'         => crades_get_page_url( 'publications' ),
			'icon'        => 'fa-solid fa-file-lines',
		),
		array(
			'title'       => __( 'Tableaux de bord', 'crades-theme' ),
			'description' => __( 'Accès aux tableaux de bord sectoriels et aux visualisations de données.', 'crades-theme' ),
			'url'         => crades_get_page_url( 'tableaux-de-bord' ),
			'icon'        => 'fa-solid fa-chart-line',
		),
		array(
			'title'       => __( 'Ressources', 'crades-theme' ),
			'description' => __( 'Espace réservé aux ressources, outils documentaires et accès complémentaires.', 'crades-theme' ),
			'url'         => crades_get_page_url( 'ressources' ),
			'icon'        => 'fa-solid fa-folder-open',
		),
	);
}

/**
 * Returns an intro for singular views.
 *
 * @param int|\WP_Post|null $post Optional post object or ID.
 * @return string
 */
function crades_get_intro_text( $post = null ) {
	$post = get_post( $post );

	if ( ! $post instanceof WP_Post ) {
		return '';
	}

	if ( has_excerpt( $post ) ) {
		return wp_strip_all_tags( get_the_excerpt( $post ) );
	}

	return '';
}

/**
 * Checks whether the current request uses the PDF library page template.
 *
 * @return bool
 */
function crades_is_pdf_library_page_template() {
	return is_page_template( 'templates/page-bibliotheque-pdf.php' );
}

/**
 * Checks whether the current request is a PDF library view.
 *
 * @return bool
 */
function crades_is_pdf_library_view() {
	return crades_is_pdf_library_page_template() || is_post_type_archive( 'rapports' );
}

/**
 * Returns the PDF attachment ID for a rapport.
 *
 * @param int|\WP_Post $post Post object or ID.
 * @return int
 */
function crades_get_rapport_pdf_attachment_id( $post ) {
	$post = get_post( $post );

	if ( ! $post instanceof WP_Post ) {
		return 0;
	}

	return absint( get_post_meta( $post->ID, '_crades_pdf_file_id', true ) );
}

/**
 * Returns the PDF URL for a rapport.
 *
 * @param int|\WP_Post $post Post object or ID.
 * @return string
 */
function crades_get_rapport_pdf_url( $post ) {
	$post = get_post( $post );

	if ( ! $post instanceof WP_Post ) {
		return '';
	}

	$attachment_id = crades_get_rapport_pdf_attachment_id( $post );

	if ( $attachment_id ) {
		$url = wp_get_attachment_url( $attachment_id );

		if ( $url ) {
			return esc_url_raw( $url );
		}
	}

	return esc_url_raw( (string) get_post_meta( $post->ID, '_crades_pdf_url', true ) );
}

/**
 * Returns the summary text for a rapport.
 *
 * @param int|\WP_Post $post Post object or ID.
 * @return string
 */
function crades_get_rapport_summary( $post ) {
	$post = get_post( $post );

	if ( ! $post instanceof WP_Post ) {
		return '';
	}

	$summary = (string) get_post_meta( $post->ID, '_crades_pdf_summary', true );

	if ( '' !== trim( $summary ) ) {
		return $summary;
	}

	if ( has_excerpt( $post ) ) {
		return wp_strip_all_tags( get_the_excerpt( $post ) );
	}

	return wp_strip_all_tags( wp_trim_words( wp_strip_all_tags( $post->post_content ), 28 ) );
}

/**
 * Returns the display year for a rapport.
 *
 * @param int|\WP_Post $post Post object or ID.
 * @return string
 */
function crades_get_rapport_year( $post ) {
	$post = get_post( $post );

	if ( ! $post instanceof WP_Post ) {
		return '';
	}

	$year = trim( (string) get_post_meta( $post->ID, '_crades_pdf_year', true ) );

	if ( '' !== $year ) {
		return $year;
	}

	return get_the_date( 'Y', $post );
}

/**
 * Returns the default number of rapports per archive page.
 *
 * @return int
 */
function crades_get_rapport_posts_per_page() {
	return (int) apply_filters( 'crades_rapport_posts_per_page', 12 );
}

/**
 * Returns dashboard page templates keyed by dashboard slug.
 *
 * @return array<string, string>
 */
function crades_get_dashboard_templates() {
	return array(
		'commerce-exterieur' => 'templates/page-dashboard-commerce-exterieur.php',
		'commerce-interieur' => 'templates/page-dashboard-commerce-interieur.php',
		'industrie'          => 'templates/page-dashboard-industrie.php',
		'pme-pmi'            => 'templates/page-dashboard-pme-pmi.php',
	);
}

/**
 * Returns dashboard page slugs keyed by dashboard slug.
 *
 * @return array<string, string>
 */
function crades_get_dashboard_page_slugs() {
	return array(
		'commerce-exterieur' => 'commerce-exterieur',
		'commerce-interieur' => 'commerce-interieur',
		'industrie'          => 'industrie',
		'pme-pmi'            => 'pme-pmi',
	);
}

/**
 * Returns the dashboard key from a template slug.
 *
 * @param string $template_slug Template slug.
 * @return string|null
 */
function crades_get_dashboard_key_from_template( $template_slug ) {
	$templates = crades_get_dashboard_templates();
	$key       = array_search( $template_slug, $templates, true );

	return false !== $key ? $key : null;
}

/**
 * Returns the dashboard key from a page slug.
 *
 * @param string $page_slug Page slug.
 * @return string|null
 */
function crades_get_dashboard_key_from_page_slug( $page_slug ) {
	$page_slugs = crades_get_dashboard_page_slugs();
	$key        = array_search( $page_slug, $page_slugs, true );

	return false !== $key ? $key : null;
}

/**
 * Returns the current dashboard key for a slug page or assigned template.
 *
 * @return string|null
 */
function crades_get_current_dashboard_key() {
	if ( function_exists( 'crades_get_current_request_slug' ) ) {
		$request_slug = crades_get_current_request_slug();

		if ( $request_slug ) {
			$request_key = crades_get_dashboard_key_from_page_slug( $request_slug );

			if ( $request_key ) {
				return $request_key;
			}
		}
	}

	$queried_object_id = get_queried_object_id();
	$template_slug     = $queried_object_id ? get_page_template_slug( $queried_object_id ) : '';
	$dashboard_key     = $template_slug ? crades_get_dashboard_key_from_template( $template_slug ) : null;

	if ( $dashboard_key ) {
		return $dashboard_key;
	}

	$queried_object = get_queried_object();

	if ( $queried_object instanceof WP_Post && ! empty( $queried_object->post_name ) ) {
		return crades_get_dashboard_key_from_page_slug( $queried_object->post_name );
	}

	if ( $queried_object_id ) {
		$post_name = get_post_field( 'post_name', $queried_object_id );

		if ( $post_name ) {
			return crades_get_dashboard_key_from_page_slug( $post_name );
		}
	}

	$pagename = get_query_var( 'pagename' );

	if ( is_string( $pagename ) && '' !== $pagename ) {
		$page_slug = basename( trim( $pagename, '/' ) );
		$key       = crades_get_dashboard_key_from_page_slug( $page_slug );

		if ( $key ) {
			return $key;
		}
	}

	return null;
}

/**
 * Checks whether the current request uses a dashboard template.
 *
 * @return bool
 */
function crades_is_dashboard_page_template() {
	return null !== crades_get_current_dashboard_key();
}

/**
 * Returns dashboard blueprint definitions.
 *
 * @return array<string, array<string, mixed>>
 */
function crades_get_dashboard_blueprints() {
	return array(
		'commerce-exterieur' => array(
			'key'         => 'commerce-exterieur',
			'title'       => __( 'Commerce extérieur', 'crades-theme' ),
			'eyebrow'     => __( 'Tableau de bord sectoriel', 'crades-theme' ),
			'description' => __( "Suivi des exportations, importations, partenaires, soldes et indicateurs de commerce extérieur du Sénégal.", 'crades-theme' ),
			'status'      => __( 'Connexion de données en préparation', 'crades-theme' ),
			'filters'     => array(
				array(
					'label'  => __( 'Vue générale', 'crades-theme' ),
					'active' => true,
				),
				array(
					'label' => __( 'Flux', 'crades-theme' ),
				),
				array(
					'label' => __( 'Partenaires', 'crades-theme' ),
				),
				array(
					'label' => __( 'Produits', 'crades-theme' ),
				),
			),
			'kpis'        => array(
				array(
					'label' => __( 'Exportations', 'crades-theme' ),
					'value' => __( 'À venir', 'crades-theme' ),
					'note'  => __( 'Source Google Sheets en attente', 'crades-theme' ),
					'icon'  => 'fa-solid fa-arrow-up-right-dots',
				),
				array(
					'label' => __( 'Importations', 'crades-theme' ),
					'value' => __( 'À venir', 'crades-theme' ),
					'note'  => __( 'Source Google Sheets en attente', 'crades-theme' ),
					'icon'  => 'fa-solid fa-arrow-down-wide-short',
				),
				array(
					'label' => __( 'Balance commerciale', 'crades-theme' ),
					'value' => __( 'À venir', 'crades-theme' ),
					'note'  => __( 'Calculée à partir des flux', 'crades-theme' ),
					'icon'  => 'fa-solid fa-scale-balanced',
				),
				array(
					'label' => __( 'Taux de couverture', 'crades-theme' ),
					'value' => __( 'À venir', 'crades-theme' ),
					'note'  => __( 'Exportations / importations', 'crades-theme' ),
					'icon'  => 'fa-solid fa-percent',
				),
			),
			'charts'      => array(
				array(
					'id'          => 'trade-evolution',
					'title'       => __( 'Évolution du commerce', 'crades-theme' ),
					'description' => __( 'La courbe Chart.js sera branchée ici pour afficher les exportations, importations et soldes.', 'crades-theme' ),
					'empty'       => __( 'Aucune série n’est encore connectée pour ce graphique.', 'crades-theme' ),
				),
				array(
					'id'          => 'top-partners',
					'title'       => __( 'Principaux partenaires', 'crades-theme' ),
					'description' => __( 'Zone réservée au graphique des partenaires commerciaux.', 'crades-theme' ),
					'empty'       => __( 'Les données partenaires seront affichées ici après branchement.', 'crades-theme' ),
				),
			),
		),
		'commerce-interieur' => array(
			'key'         => 'commerce-interieur',
			'title'       => __( 'Commerce intérieur', 'crades-theme' ),
			'eyebrow'     => __( 'Tableau de bord sectoriel', 'crades-theme' ),
			'description' => __( "Suivi des prix, indices, inflation, denrées de base et tendances du commerce intérieur.", 'crades-theme' ),
			'status'      => __( 'Connexion de données en préparation', 'crades-theme' ),
			'filters'     => array(
				array(
					'label'  => __( 'Vue générale', 'crades-theme' ),
					'active' => true,
				),
				array(
					'label' => __( 'IHPC', 'crades-theme' ),
				),
				array(
					'label' => __( 'Inflation', 'crades-theme' ),
				),
				array(
					'label' => __( 'Denrées', 'crades-theme' ),
				),
			),
			'kpis'        => array(
				array(
					'label' => __( 'IHPC global', 'crades-theme' ),
					'value' => __( 'À venir', 'crades-theme' ),
					'note'  => __( 'Indice harmonisé des prix', 'crades-theme' ),
					'icon'  => 'fa-solid fa-basket-shopping',
				),
				array(
					'label' => __( 'Inflation annuelle', 'crades-theme' ),
					'value' => __( 'À venir', 'crades-theme' ),
					'note'  => __( 'Calcul annuel automatisé', 'crades-theme' ),
					'icon'  => 'fa-solid fa-chart-line',
				),
				array(
					'label' => __( 'Prix denrées de base', 'crades-theme' ),
					'value' => __( 'À venir', 'crades-theme' ),
					'note'  => __( 'Série mensuelle', 'crades-theme' ),
					'icon'  => 'fa-solid fa-wheat-awn',
				),
				array(
					'label' => __( 'Commerce / PIB', 'crades-theme' ),
					'value' => __( 'À venir', 'crades-theme' ),
					'note'  => __( 'Ratio structurel', 'crades-theme' ),
					'icon'  => 'fa-solid fa-store',
				),
			),
			'charts'      => array(
				array(
					'id'          => 'ihpc-trend',
					'title'       => __( 'Tendance IHPC', 'crades-theme' ),
					'description' => __( 'Le graphique principal Chart.js sera chargé ici.', 'crades-theme' ),
					'empty'       => __( 'Les données IHPC ne sont pas encore branchées.', 'crades-theme' ),
				),
				array(
					'id'          => 'inflation-breakdown',
					'title'       => __( 'Inflation et décomposition', 'crades-theme' ),
					'description' => __( 'Emplacement réservé au graphique de décomposition.', 'crades-theme' ),
					'empty'       => __( 'La décomposition de l’inflation apparaîtra ici.', 'crades-theme' ),
				),
			),
		),
		'industrie' => array(
			'key'         => 'industrie',
			'title'       => __( 'Industrie', 'crades-theme' ),
			'eyebrow'     => __( 'Tableau de bord sectoriel', 'crades-theme' ),
			'description' => __( "Suivi de la production industrielle, des indices de prix, de compétitivité et de performance sectorielle.", 'crades-theme' ),
			'status'      => __( 'Connexion de données en préparation', 'crades-theme' ),
			'filters'     => array(
				array(
					'label'  => __( 'Vue générale', 'crades-theme' ),
					'active' => true,
				),
				array(
					'label' => __( 'Production', 'crades-theme' ),
				),
				array(
					'label' => __( 'Prix', 'crades-theme' ),
				),
				array(
					'label' => __( 'Compétitivité', 'crades-theme' ),
				),
			),
			'kpis'        => array(
				array(
					'label' => __( 'IHPI', 'crades-theme' ),
					'value' => __( 'À venir', 'crades-theme' ),
					'note'  => __( 'Indice harmonisé de production', 'crades-theme' ),
					'icon'  => 'fa-solid fa-industry',
				),
				array(
					'label' => __( 'IPPI', 'crades-theme' ),
					'value' => __( 'À venir', 'crades-theme' ),
					'note'  => __( 'Indice des prix à la production', 'crades-theme' ),
					'icon'  => 'fa-solid fa-tags',
				),
				array(
					'label' => __( 'ICAI', 'crades-theme' ),
					'value' => __( 'À venir', 'crades-theme' ),
					'note'  => __( 'Chiffre d’affaires industriel', 'crades-theme' ),
					'icon'  => 'fa-solid fa-chart-column',
				),
				array(
					'label' => __( 'CIP', 'crades-theme' ),
					'value' => __( 'À venir', 'crades-theme' ),
					'note'  => __( 'Compétitivité industrielle', 'crades-theme' ),
					'icon'  => 'fa-solid fa-trophy',
				),
			),
			'charts'      => array(
				array(
					'id'          => 'industrial-production',
					'title'       => __( 'Production industrielle', 'crades-theme' ),
					'description' => __( 'Le graphique de production sera initialisé ici via Chart.js.', 'crades-theme' ),
					'empty'       => __( 'La série de production sera visible après intégration.', 'crades-theme' ),
				),
				array(
					'id'          => 'industrial-prices',
					'title'       => __( 'Prix à la production', 'crades-theme' ),
					'description' => __( 'Zone réservée aux indices de prix et comparaisons.', 'crades-theme' ),
					'empty'       => __( 'Les indices de prix ne sont pas encore affichés.', 'crades-theme' ),
				),
			),
		),
		'pme-pmi' => array(
			'key'         => 'pme-pmi',
			'title'       => __( 'PME / PMI', 'crades-theme' ),
			'eyebrow'     => __( 'Tableau de bord sectoriel', 'crades-theme' ),
			'description' => __( "Suivi des immatriculations, structures d’entreprises, secteurs, territoires et principaux obstacles.", 'crades-theme' ),
			'status'      => __( 'Connexion de données en préparation', 'crades-theme' ),
			'filters'     => array(
				array(
					'label'  => __( 'Vue générale', 'crades-theme' ),
					'active' => true,
				),
				array(
					'label' => __( 'Immatriculations', 'crades-theme' ),
				),
				array(
					'label' => __( 'Secteurs', 'crades-theme' ),
				),
				array(
					'label' => __( 'Territoires', 'crades-theme' ),
				),
			),
			'kpis'        => array(
				array(
					'label' => __( 'Immatriculations', 'crades-theme' ),
					'value' => __( 'À venir', 'crades-theme' ),
					'note'  => __( 'Série annuelle NINEA', 'crades-theme' ),
					'icon'  => 'fa-solid fa-building-circle-check',
				),
				array(
					'label' => __( 'Accès au crédit', 'crades-theme' ),
					'value' => __( 'À venir', 'crades-theme' ),
					'note'  => __( 'Indicateur d’enquête', 'crades-theme' ),
					'icon'  => 'fa-solid fa-hand-holding-dollar',
				),
				array(
					'label' => __( 'Entreprises exportatrices', 'crades-theme' ),
					'value' => __( 'À venir', 'crades-theme' ),
					'note'  => __( 'Part des entreprises exportatrices', 'crades-theme' ),
					'icon'  => 'fa-solid fa-earth-africa',
				),
				array(
					'label' => __( 'Croissance emploi', 'crades-theme' ),
					'value' => __( 'À venir', 'crades-theme' ),
					'note'  => __( 'Croissance de l’emploi observée', 'crades-theme' ),
					'icon'  => 'fa-solid fa-users',
				),
			),
			'charts'      => array(
				array(
					'id'          => 'registrations-sector',
					'title'       => __( 'Immatriculations par secteur', 'crades-theme' ),
					'description' => __( 'Le graphique par secteur apparaîtra ici.', 'crades-theme' ),
					'empty'       => __( 'Aucune série sectorielle n’est encore branchée.', 'crades-theme' ),
				),
				array(
					'id'          => 'regional-distribution',
					'title'       => __( 'Répartition régionale', 'crades-theme' ),
					'description' => __( 'Zone réservée à la carte ou au graphique régional.', 'crades-theme' ),
					'empty'       => __( 'La répartition régionale sera chargée ici.', 'crades-theme' ),
				),
			),
		),
	);
}

/**
 * Returns one dashboard blueprint by key.
 *
 * @param string $dashboard_key Dashboard key.
 * @return array<string, mixed>
 */
function crades_get_dashboard_blueprint( $dashboard_key ) {
	$blueprints = crades_get_dashboard_blueprints();

	if ( isset( $blueprints[ $dashboard_key ] ) ) {
		return $blueprints[ $dashboard_key ];
	}

	return array();
}
