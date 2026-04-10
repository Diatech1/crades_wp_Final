<?php
/**
 * Enqueue handlers.
 *
 * @package CRADES_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers and enqueues theme assets.
 *
 * @return void
 */
function crades_enqueue_assets() {
	$theme_version = wp_get_theme()->get( 'Version' );

	wp_enqueue_style(
		'crades-style',
		get_stylesheet_uri(),
		array(),
		$theme_version
	);

	wp_enqueue_style(
		'crades-fonts',
		'https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap',
		array(),
		null
	);

	wp_enqueue_style(
		'crades-font-awesome',
		'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.0/css/all.min.css',
		array(),
		'6.5.0'
	);

	wp_enqueue_style(
		'crades-app',
		get_template_directory_uri() . '/assets/css/app.css',
		array( 'crades-style', 'crades-fonts', 'crades-font-awesome' ),
		crades_asset_version( 'assets/css/app.css' )
	);

	wp_enqueue_script(
		'crades-tailwind',
		'https://cdn.tailwindcss.com',
		array(),
		null,
		false
	);

	wp_add_inline_script( 'crades-tailwind', crades_get_tailwind_config(), 'before' );

	wp_register_script(
		'crades-chartjs',
		'https://cdn.jsdelivr.net/npm/chart.js',
		array(),
		null,
		true
	);

	wp_register_script(
		'crades-pdfjs',
		'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js',
		array(),
		'3.11.174',
		true
	);

	wp_register_style(
		'crades-dashboard',
		get_template_directory_uri() . '/assets/css/dashboard.css',
		array( 'crades-app' ),
		crades_asset_version( 'assets/css/dashboard.css' )
	);

	wp_enqueue_script(
		'crades-main',
		get_template_directory_uri() . '/assets/js/main.js',
		array(),
		crades_asset_version( 'assets/js/main.js' ),
		true
	);
	wp_script_add_data( 'crades-main', 'strategy', 'defer' );

	wp_localize_script(
		'crades-main',
		'cradesTheme',
		array(
			'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
			'homeUrl'      => home_url( '/' ),
			'templateUrl'  => get_template_directory_uri(),
			'chartEnabled' => wp_script_is( 'crades-chartjs', 'registered' ),
			'pdfEnabled'   => wp_script_is( 'crades-pdfjs', 'registered' ),
		)
	);

	wp_register_script(
		'crades-dashboard',
		get_template_directory_uri() . '/assets/js/dashboard.js',
		array( 'crades-main', 'crades-chartjs' ),
		crades_asset_version( 'assets/js/dashboard.js' ),
		true
	);
	wp_script_add_data( 'crades-dashboard', 'strategy', 'defer' );

	wp_register_script(
		'crades-dashboard-hub',
		get_template_directory_uri() . '/assets/js/dashboard-hub.js',
		array( 'crades-main', 'crades-chartjs' ),
		crades_asset_version( 'assets/js/dashboard-hub.js' ),
		true
	);
	wp_script_add_data( 'crades-dashboard-hub', 'strategy', 'defer' );

	wp_register_script(
		'crades-pdf-reader',
		get_template_directory_uri() . '/assets/js/pdf-reader.js',
		array( 'crades-main', 'crades-pdfjs' ),
		crades_asset_version( 'assets/js/pdf-reader.js' ),
		true
	);
	wp_script_add_data( 'crades-pdf-reader', 'strategy', 'defer' );

	wp_register_script(
		'crades-home',
		get_template_directory_uri() . '/assets/js/home.js',
		array( 'crades-main', 'crades-pdfjs' ),
		crades_asset_version( 'assets/js/home.js' ),
		true
	);
	wp_script_add_data( 'crades-home', 'strategy', 'defer' );

	wp_register_script(
		'crades-publications',
		get_template_directory_uri() . '/assets/js/publications.js',
		array( 'crades-main', 'crades-pdfjs' ),
		crades_asset_version( 'assets/js/publications.js' ),
		true
	);
	wp_script_add_data( 'crades-publications', 'strategy', 'defer' );

	$dashboard_key        = crades_get_current_dashboard_key();
	$current_request_slug = function_exists( 'crades_get_current_request_slug' ) ? crades_get_current_request_slug() : '';
	$is_dashboard_hub     = is_page( 'tableaux-de-bord' ) || 'tableaux-de-bord' === $current_request_slug;

	if ( $dashboard_key ) {
		$dashboard = $dashboard_key ? crades_get_dashboard_blueprint( $dashboard_key ) : array();

		wp_enqueue_style( 'crades-dashboard' );
		wp_enqueue_script( 'crades-chartjs' );
		wp_enqueue_script( 'crades-dashboard' );

		wp_localize_script(
			'crades-dashboard',
			'cradesDashboard',
			array(
				'pageKey'  => $dashboard_key,
				'hasData'  => false,
				'state'    => 'placeholder',
				'messages' => array(
					'loading' => __( 'Chargement du tableau de bord...', 'crades-theme' ),
					'empty'   => __( 'La source de données sera branchée dans une prochaine étape.', 'crades-theme' ),
					'error'   => __( 'Le moteur de visualisation n est pas disponible.', 'crades-theme' ),
				),
				'filters'  => isset( $dashboard['filters'] ) ? $dashboard['filters'] : array(),
				'apiBase'  => esc_url_raw( rest_url( 'ministere/v1/' ) ),
				'apiUrl'   => $dashboard_key ? esc_url_raw( rest_url( 'ministere/v1/' . $dashboard_key ) ) : '',
			)
		);
	}

	if ( $is_dashboard_hub ) {
		wp_enqueue_style( 'crades-dashboard' );
		wp_enqueue_script( 'crades-chartjs' );
		wp_enqueue_script( 'crades-dashboard-hub' );

		wp_localize_script(
			'crades-dashboard-hub',
			'cradesDashboardHub',
			array(
				'endpoints' => array(
					'commerceExterieur' => esc_url_raw( rest_url( 'ministere/v1/commerce-exterieur' ) ),
					'commerceInterieur' => esc_url_raw( rest_url( 'ministere/v1/commerce-interieur' ) ),
					'industrie'         => esc_url_raw( rest_url( 'ministere/v1/industrie' ) ),
					'pmePmi'            => esc_url_raw( rest_url( 'ministere/v1/pme-pmi' ) ),
				),
			)
		);
	}

	if ( crades_is_pdf_library_view() ) {
		crades_enqueue_pdf_js();
		wp_enqueue_script( 'crades-pdf-reader' );

		wp_localize_script(
			'crades-pdf-reader',
			'cradesPdfReader',
			array(
				'workerSrc' => 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js',
				'messages'  => array(
					'loading'        => __( 'Chargement du document...', 'crades-theme' ),
					'thumbnail'      => __( 'Chargement de l aperçu...', 'crades-theme' ),
					'thumbnailError' => __( 'Aperçu indisponible', 'crades-theme' ),
					'readerError'    => __( 'Impossible de charger ce PDF.', 'crades-theme' ),
					'noDocument'     => __( 'Aucun document PDF n est attaché à cette fiche.', 'crades-theme' ),
					'pageLabel'      => __( 'Page', 'crades-theme' ),
					'ofLabel'        => __( 'sur', 'crades-theme' ),
					'filterAll'      => __( 'Tous', 'crades-theme' ),
				),
			)
		);
	}

	if ( is_front_page() ) {
		wp_enqueue_style( 'crades-dashboard' );
		wp_enqueue_script( 'crades-chartjs' );
		wp_enqueue_script( 'crades-dashboard-hub' );

		wp_localize_script(
			'crades-dashboard-hub',
			'cradesDashboardHub',
			array(
				'endpoints' => array(
					'commerceExterieur' => esc_url_raw( rest_url( 'ministere/v1/commerce-exterieur' ) ),
					'commerceInterieur' => esc_url_raw( rest_url( 'ministere/v1/commerce-interieur' ) ),
					'industrie'         => esc_url_raw( rest_url( 'ministere/v1/industrie' ) ),
					'pmePmi'            => esc_url_raw( rest_url( 'ministere/v1/pme-pmi' ) ),
				),
			)
		);

		crades_enqueue_pdf_js();
		wp_enqueue_script( 'crades-home' );

		wp_localize_script(
			'crades-home',
			'cradesHome',
			array(
				'workerSrc' => 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js',
				'messages'  => array(
					'loading' => __( 'Chargement du document...', 'crades-theme' ),
					'error'   => __( 'Impossible de charger le PDF.', 'crades-theme' ),
				),
			)
		);
	}

	if ( is_page( 'publications' ) ) {
		crades_enqueue_pdf_js();
		wp_enqueue_script( 'crades-publications' );

		wp_localize_script(
			'crades-publications',
			'cradesPublications',
			array(
				'pdfJsSrc'  => 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js',
				'workerSrc' => 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js',
				'messages'  => array(
					'loading' => __( 'Chargement du document...', 'crades-theme' ),
					'error'   => __( 'Impossible de charger le PDF.', 'crades-theme' ),
				),
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'crades_enqueue_assets' );

/**
 * Enqueues Chart.js only when needed.
 *
 * @return void
 */
function crades_enqueue_chart_js() {
	wp_enqueue_script( 'crades-chartjs' );
}

/**
 * Enqueues PDF.js only when needed.
 *
 * @return void
 */
function crades_enqueue_pdf_js() {
	wp_enqueue_script( 'crades-pdfjs' );
	wp_add_inline_script(
		'crades-pdfjs',
		'window.CRADES_PDF = window.CRADES_PDF || {}; window.CRADES_PDF.workerSrc = "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js";',
		'after'
	);
}

/**
 * Adds performance resource hints for third-party assets.
 *
 * @param string[] $urls          Existing URLs.
 * @param string   $relation_type Relation type.
 * @return string[]
 */
function crades_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = 'https://fonts.googleapis.com';
		$urls[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		);
		$urls[] = 'https://cdn.jsdelivr.net';
		$urls[] = 'https://cdnjs.cloudflare.com';
	}

	return $urls;
}
add_filter( 'wp_resource_hints', 'crades_resource_hints', 10, 2 );
