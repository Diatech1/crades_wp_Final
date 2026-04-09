<?php
/**
 * Theme bootstrap file.
 *
 * @package CRADES_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'CRADES_THEME_BUILD' ) ) {
	define( 'CRADES_THEME_BUILD', '2026-04-09-7618381' );
}

require get_template_directory() . '/inc/helpers.php';
require get_template_directory() . '/inc/setup.php';
require get_template_directory() . '/inc/caching.php';
require get_template_directory() . '/inc/dashboard-viewmodels.php';
require get_template_directory() . '/inc/sheets.php';
require get_template_directory() . '/inc/api-routes.php';
require get_template_directory() . '/inc/enqueue.php';
require get_template_directory() . '/inc/elementor.php';
