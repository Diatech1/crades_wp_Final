<?php
/**
 * Elementor compatibility layer.
 *
 * @package CRADES_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers Elementor theme locations when Elementor is active.
 *
 * @param \Elementor\Core\DocumentTypes\ThemeDocument $elementor_theme_manager Theme manager instance.
 * @return void
 */
function crades_register_elementor_locations( $elementor_theme_manager ) {
	if ( ! $elementor_theme_manager ) {
		return;
	}

	$elementor_theme_manager->register_all_core_location();
}
add_action( 'elementor/theme/register_locations', 'crades_register_elementor_locations' );

/**
 * Flags Elementor-built pages in the body classes.
 *
 * @param string[] $classes Existing classes.
 * @return string[]
 */
function crades_elementor_body_classes( $classes ) {
	if ( is_singular() && crades_is_built_with_elementor( get_the_ID() ) ) {
		$classes[] = 'crades-elementor-page';
	}

	return $classes;
}
add_filter( 'body_class', 'crades_elementor_body_classes' );

/**
 * Returns whether the current post is built with Elementor.
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function crades_is_built_with_elementor( $post_id ) {
	if ( ! $post_id || ! did_action( 'elementor/loaded' ) ) {
		return false;
	}

	if ( ! class_exists( '\Elementor\Plugin' ) ) {
		return false;
	}

	$document = \Elementor\Plugin::$instance->documents->get( $post_id );

	if ( ! $document ) {
		return false;
	}

	return $document->is_built_with_elementor();
}

/**
 * Prevents Elementor from loading unexpected Google Fonts on the front end.
 *
 * @return bool
 */
function crades_disable_elementor_google_fonts() {
	return false;
}
add_filter( 'elementor/frontend/print_google_fonts', 'crades_disable_elementor_google_fonts' );
