<?php
/**
 * REST API routes for dashboard data.
 *
 * @package CRADES_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers dashboard REST API routes.
 *
 * Namespace: /wp-json/ministere/v1/*
 *
 * @return void
 */
function crades_register_rest_routes() {
	$route_args = array(
		'methods'             => WP_REST_Server::READABLE,
		'permission_callback' => '__return_true',
		'args'                => array(
			'refresh' => array(
				'description'       => __( 'Force refresh and bypass cached transients. Requires a logged-in administrator.', 'crades-theme' ),
				'type'              => 'boolean',
				'required'          => false,
				'sanitize_callback' => 'rest_sanitize_boolean',
			),
		),
	);

	register_rest_route(
		'ministere/v1',
		'/commerce-exterieur',
		array_merge(
			$route_args,
			array(
				'callback' => 'crades_rest_get_commerce_exterieur',
			)
		)
	);

	register_rest_route(
		'ministere/v1',
		'/commerce-interieur',
		array_merge(
			$route_args,
			array(
				'callback' => 'crades_rest_get_commerce_interieur',
			)
		)
	);

	register_rest_route(
		'ministere/v1',
		'/industrie',
		array_merge(
			$route_args,
			array(
				'callback' => 'crades_rest_get_industrie',
			)
		)
	);

	register_rest_route(
		'ministere/v1',
		'/pme-pmi',
		array_merge(
			$route_args,
			array(
				'callback' => 'crades_rest_get_pme_pmi',
			)
		)
	);
}
add_action( 'rest_api_init', 'crades_register_rest_routes' );

/**
 * Sanitizes REST error details for public responses.
 *
 * @param mixed $details Error details.
 * @return mixed
 */
function crades_sanitize_rest_error_details( $details ) {
	if ( ! is_array( $details ) ) {
		return $details;
	}

	unset( $details['url'], $details['spreadsheet_id'] );

	foreach ( $details as $key => $value ) {
		if ( is_array( $value ) ) {
			$details[ $key ] = crades_sanitize_rest_error_details( $value );
		}
	}

	return $details;
}

/**
 * Applies cache headers to REST responses.
 *
 * @param WP_REST_Response $response Response object.
 * @param array<string, mixed> $payload  Response payload.
 * @return WP_REST_Response
 */
function crades_apply_rest_cache_headers( WP_REST_Response $response, $payload ) {
	$ttl   = isset( $payload['cache']['ttl'] ) ? (int) $payload['cache']['ttl'] : crades_get_default_cache_ttl();
	$stale = ! empty( $payload['cache']['stale'] );

	$response->header( 'Cache-Control', 'public, max-age=' . $ttl . ', stale-while-revalidate=' . $ttl );
	$response->header( 'X-CRADES-Cache-Stale', $stale ? '1' : '0' );

	return $response;
}

/**
 * Returns the Commerce exterieur payload.
 *
 * @param WP_REST_Request $request REST request.
 * @return WP_REST_Response
 */
function crades_rest_get_commerce_exterieur( WP_REST_Request $request ) {
	return crades_rest_dashboard_response( 'commerce-exterieur', $request );
}

/**
 * Returns the Commerce interieur payload.
 *
 * @param WP_REST_Request $request REST request.
 * @return WP_REST_Response
 */
function crades_rest_get_commerce_interieur( WP_REST_Request $request ) {
	return crades_rest_dashboard_response( 'commerce-interieur', $request );
}

/**
 * Returns the Industrie payload.
 *
 * @param WP_REST_Request $request REST request.
 * @return WP_REST_Response
 */
function crades_rest_get_industrie( WP_REST_Request $request ) {
	return crades_rest_dashboard_response( 'industrie', $request );
}

/**
 * Returns the PME / PMI payload.
 *
 * @param WP_REST_Request $request REST request.
 * @return WP_REST_Response
 */
function crades_rest_get_pme_pmi( WP_REST_Request $request ) {
	return crades_rest_dashboard_response( 'pme-pmi', $request );
}

/**
 * Shared REST callback builder for dashboard routes.
 *
 * @param string          $dashboard_key Dashboard key.
 * @param WP_REST_Request $request       REST request.
 * @return WP_REST_Response
 */
function crades_rest_dashboard_response( $dashboard_key, WP_REST_Request $request ) {
	$refresh_requested = rest_sanitize_boolean( $request->get_param( 'refresh' ) );
	$force_refresh     = $refresh_requested && current_user_can( 'manage_options' );
	$payload           = crades_get_dashboard_sheet_payload( $dashboard_key, $force_refresh );

	if ( is_wp_error( $payload ) ) {
		$error_data = $payload->get_error_data();
		$status     = 500;
		$details    = array();

		if ( is_array( $error_data ) ) {
			if ( ! empty( $error_data['status'] ) ) {
				$status = (int) $error_data['status'];
			}

			if ( ! empty( $error_data['errors'] ) ) {
				$details = crades_sanitize_rest_error_details( $error_data['errors'] );
			}
		}

		return new WP_REST_Response(
			array(
				'success'   => false,
				'dashboard' => $dashboard_key,
				'code'      => $payload->get_error_code(),
				'message'   => $payload->get_error_message(),
				'errors'    => $details,
			),
			$status
		);
	}

	return crades_apply_rest_cache_headers( new WP_REST_Response( $payload, 200 ), $payload );
}
