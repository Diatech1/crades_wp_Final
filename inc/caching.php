<?php
/**
 * Transient caching helpers for dashboard data.
 *
 * @package CRADES_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns the default cache TTL in seconds.
 *
 * @return int
 */
function crades_get_default_cache_ttl() {
	return (int) apply_filters( 'crades_default_cache_ttl', 10 * MINUTE_IN_SECONDS );
}

/**
 * Returns the fallback cache TTL in seconds.
 *
 * @return int
 */
function crades_get_fallback_cache_ttl() {
	return (int) apply_filters( 'crades_fallback_cache_ttl', WEEK_IN_SECONDS );
}

/**
 * Returns the cache TTL for a given dashboard.
 *
 * @param string $dashboard_key Dashboard key.
 * @return int
 */
function crades_get_dashboard_cache_ttl( $dashboard_key ) {
	$ttl = crades_get_default_cache_ttl();

	return (int) apply_filters( 'crades_dashboard_cache_ttl', $ttl, $dashboard_key );
}

/**
 * Builds a transient cache key.
 *
 * @param string       $group      Cache group.
 * @param string|array $identifier Cache identifier.
 * @return string
 */
function crades_get_cache_key( $group, $identifier ) {
	return 'crades_' . sanitize_key( $group ) . '_' . md5( wp_json_encode( $identifier ) );
}

/**
 * Reads a transient cache value.
 *
 * @param string       $group      Cache group.
 * @param string|array $identifier Cache identifier.
 * @return mixed
 */
function crades_cache_get( $group, $identifier ) {
	return get_transient( crades_get_cache_key( $group, $identifier ) );
}

/**
 * Stores a transient cache value.
 *
 * @param string       $group      Cache group.
 * @param string|array $identifier Cache identifier.
 * @param mixed        $value      Value to store.
 * @param int          $ttl        TTL in seconds.
 * @return bool
 */
function crades_cache_set( $group, $identifier, $value, $ttl ) {
	return (bool) set_transient( crades_get_cache_key( $group, $identifier ), $value, (int) $ttl );
}

/**
 * Deletes a transient cache value.
 *
 * @param string       $group      Cache group.
 * @param string|array $identifier Cache identifier.
 * @return bool
 */
function crades_cache_delete( $group, $identifier ) {
	return (bool) delete_transient( crades_get_cache_key( $group, $identifier ) );
}

/**
 * Stores a last-known-good fallback cache value.
 *
 * @param string       $group      Cache group.
 * @param string|array $identifier Cache identifier.
 * @param mixed        $value      Value to store.
 * @return bool
 */
function crades_cache_set_fallback( $group, $identifier, $value ) {
	return crades_cache_set( $group . '_fallback', $identifier, $value, crades_get_fallback_cache_ttl() );
}

/**
 * Reads a last-known-good fallback cache value.
 *
 * @param string       $group      Cache group.
 * @param string|array $identifier Cache identifier.
 * @return mixed
 */
function crades_cache_get_fallback( $group, $identifier ) {
	return crades_cache_get( $group . '_fallback', $identifier );
}

/**
 * Clears dashboard cache entries.
 *
 * @param string $dashboard_key Optional dashboard key. Empty clears all dashboards.
 * @return bool
 */
function crades_clear_dashboard_cache( $dashboard_key = '' ) {
	$dashboards = function_exists( 'crades_get_dashboard_sheet_sources' ) ? crades_get_dashboard_sheet_sources() : array();

	if ( empty( $dashboards ) ) {
		return false;
	}

	if ( '' === $dashboard_key ) {
		foreach ( array_keys( $dashboards ) as $registered_dashboard_key ) {
			crades_clear_dashboard_cache( $registered_dashboard_key );
		}

		return true;
	}

	if ( ! isset( $dashboards[ $dashboard_key ] ) ) {
		return false;
	}

	crades_cache_delete( 'dashboard', $dashboard_key );
	crades_cache_delete( 'dashboard_fallback', $dashboard_key );

	if ( ! empty( $dashboards[ $dashboard_key ]['sources'] ) ) {
		foreach ( array_keys( $dashboards[ $dashboard_key ]['sources'] ) as $source_key ) {
			crades_cache_delete( 'sheet', array( $dashboard_key, $source_key ) );
			crades_cache_delete( 'sheet_fallback', array( $dashboard_key, $source_key ) );
		}
	}

	return true;
}
