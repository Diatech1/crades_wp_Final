<?php
/**
 * Theme bootstrap file.
 *
 * @package CRADES_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require get_template_directory() . '/inc/helpers.php';
require get_template_directory() . '/inc/setup.php';
require get_template_directory() . '/inc/caching.php';
require get_template_directory() . '/inc/dashboard-viewmodels.php';
require get_template_directory() . '/inc/sheets.php';
require get_template_directory() . '/inc/api-routes.php';
require get_template_directory() . '/inc/enqueue.php';
require get_template_directory() . '/inc/elementor.php';
