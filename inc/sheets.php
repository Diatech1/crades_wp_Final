<?php
/**
 * Google Sheets gviz configuration and fetch helpers.
 *
 * @package CRADES_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns centralized Google Sheets source configuration for dashboards.
 *
 * @return array<string, array<string, mixed>>
 */
function crades_get_dashboard_sheet_sources() {
	$dashboards = array(
		'commerce-exterieur' => array(
			'key'         => 'commerce-exterieur',
			'title'       => __( 'Commerce extérieur', 'crades-theme' ),
			'description' => __( 'Données de synthèse et tableaux du commerce extérieur via Google Sheets gviz.', 'crades-theme' ),
			'sources'     => array(
				'summary' => array(
					'label'          => __( 'Vue synthese', 'crades-theme' ),
					'spreadsheet_id' => '15La8pEkwXtfwC-0zmWcv_Oayysqc3ZCkw39iwXwrOjk',
					'gid'            => '0',
				),
			),
		),
		'commerce-interieur' => array(
			'key'         => 'commerce-interieur',
			'title'       => __( 'Commerce intérieur', 'crades-theme' ),
			'description' => __( 'Indices, denrées, inflation et feuilles auxiliaires du commerce intérieur.', 'crades-theme' ),
			'sources'     => array(
				'overview' => array(
					'label'          => __( 'Vue synthese', 'crades-theme' ),
					'spreadsheet_id' => '1FqiLKYBcWoUsJiBgMNlAD709CrYZx_3bwiJ0UT-CcDQ',
				),
				'ihpc_global' => array(
					'label'          => __( 'IHPC global', 'crades-theme' ),
					'spreadsheet_id' => '1FqiLKYBcWoUsJiBgMNlAD709CrYZx_3bwiJ0UT-CcDQ',
					'sheet'          => '1_IHPC_Global',
				),
				'ihpc_desagrege' => array(
					'label'          => __( 'IHPC désagrégé', 'crades-theme' ),
					'spreadsheet_id' => '1FqiLKYBcWoUsJiBgMNlAD709CrYZx_3bwiJ0UT-CcDQ',
					'gid'            => '351552979',
				),
				'emploi_commerce' => array(
					'label'          => __( 'Emploi commerce', 'crades-theme' ),
					'spreadsheet_id' => '1FqiLKYBcWoUsJiBgMNlAD709CrYZx_3bwiJ0UT-CcDQ',
					'sheet'          => '9_Emploi_Commerce',
				),
				'denrees_base' => array(
					'label'          => __( 'Prix denrées de base', 'crades-theme' ),
					'spreadsheet_id' => '125rwsUS2bOSYSmpoXmv3mCg28ZVM715OcYJJMIbiuDg',
					'sheet'          => '6_Prix_Denrées_Base',
				),
			),
		),
		'industrie' => array(
			'key'         => 'industrie',
			'title'       => __( 'Industrie', 'crades-theme' ),
			'description' => __( 'Production, prix, compétitivité et structures industrielles via Google Sheets gviz.', 'crades-theme' ),
			'sources'     => array(
				'overview' => array(
					'label'          => __( 'Vue synthese', 'crades-theme' ),
					'spreadsheet_id' => '1w-yPjOouHYYjoD8Y8f42_4ovpIuBv9dWsBG_7N_aTYw',
					'sheet'          => 'Feuille 1',
				),
				'ihpi' => array(
					'label'          => __( 'IHPI', 'crades-theme' ),
					'spreadsheet_id' => '1w-yPjOouHYYjoD8Y8f42_4ovpIuBv9dWsBG_7N_aTYw',
					'sheet'          => 'IHPI',
				),
				'ippi' => array(
					'label'          => __( 'IPPI', 'crades-theme' ),
					'spreadsheet_id' => '1w-yPjOouHYYjoD8Y8f42_4ovpIuBv9dWsBG_7N_aTYw',
					'sheet'          => 'IPPI',
				),
				'icai' => array(
					'label'          => __( 'ICAI', 'crades-theme' ),
					'spreadsheet_id' => '1w-yPjOouHYYjoD8Y8f42_4ovpIuBv9dWsBG_7N_aTYw',
					'sheet'          => 'ICAI',
				),
				'pib_branches' => array(
					'label'          => __( 'PIB branches', 'crades-theme' ),
					'spreadsheet_id' => '1w-yPjOouHYYjoD8Y8f42_4ovpIuBv9dWsBG_7N_aTYw',
					'sheet'          => 'PIB_Branches',
				),
				'cip_competitivite' => array(
					'label'          => __( 'CIP compétitivité', 'crades-theme' ),
					'spreadsheet_id' => '1w-yPjOouHYYjoD8Y8f42_4ovpIuBv9dWsBG_7N_aTYw',
					'sheet'          => 'CIP_Competitivite',
				),
				'pci_unctad' => array(
					'label'          => __( 'PCI granulaire UNCTAD', 'crades-theme' ),
					'spreadsheet_id' => '1w-yPjOouHYYjoD8Y8f42_4ovpIuBv9dWsBG_7N_aTYw',
					'sheet'          => 'PCI_Granulaire_UNCTAD',
				),
				'production_dpee' => array(
					'label'          => __( 'Production industrielle DPEE', 'crades-theme' ),
					'spreadsheet_id' => '1w-yPjOouHYYjoD8Y8f42_4ovpIuBv9dWsBG_7N_aTYw',
					'sheet'          => 'Production_Industrielle_DPEE',
				),
				'taux_utilisation' => array(
					'label'          => __( 'Taux utilisation', 'crades-theme' ),
					'spreadsheet_id' => '1w-yPjOouHYYjoD8Y8f42_4ovpIuBv9dWsBG_7N_aTYw',
					'sheet'          => 'Taux_utilisation',
				),
			),
		),
		'pme-pmi' => array(
			'key'         => 'pme-pmi',
			'title'       => __( 'PME / PMI', 'crades-theme' ),
			'description' => __( 'Immatriculations, structures, secteurs et contraintes des PME / PMI via Google Sheets gviz.', 'crades-theme' ),
			'sources'     => array(
				'ninea_immatriculations' => array(
					'label'          => __( 'NINEA immatriculations', 'crades-theme' ),
					'spreadsheet_id' => '1QmxcEYEwlmuFo22ukoYI3s9hbwA9cFbHw2cqWjJmBtk',
					'sheet'          => 'NINEA_Immatric',
				),
				'enquete_wb' => array(
					'label'          => __( 'Enquête Banque mondiale', 'crades-theme' ),
					'spreadsheet_id' => '1QmxcEYEwlmuFo22ukoYI3s9hbwA9cFbHw2cqWjJmBtk',
					'sheet'          => 'EnquêteWB_PME',
				),
				'macro_pib' => array(
					'label'          => __( 'Macro PIB', 'crades-theme' ),
					'spreadsheet_id' => '1QmxcEYEwlmuFo22ukoYI3s9hbwA9cFbHw2cqWjJmBtk',
					'sheet'          => 'Macro_PIB',
				),
			),
		),
	);

	return apply_filters( 'crades_dashboard_sheet_sources', $dashboards );
}

/**
 * Returns one dashboard sheet configuration.
 *
 * @param string $dashboard_key Dashboard key.
 * @return array<string, mixed>|null
 */
function crades_get_dashboard_sheet_config( $dashboard_key ) {
	$dashboards = crades_get_dashboard_sheet_sources();

	if ( isset( $dashboards['pme-pmi']['sources']['enquete_wb'] ) ) {
		$dashboards['pme-pmi']['sources']['enquete_wb']['sheet'] = 'EnquêteWB_PME';
	}

	return isset( $dashboards[ $dashboard_key ] ) ? $dashboards[ $dashboard_key ] : null;
}

/**
 * Builds a Google Sheets gviz URL from a source definition.
 *
 * @param array<string, mixed> $source Source configuration.
 * @return string
 */
function crades_build_gviz_url( $source ) {
	$base_url   = sprintf(
		'https://docs.google.com/spreadsheets/d/%s/gviz/tq',
		rawurlencode( $source['spreadsheet_id'] )
	);
	$query_args = array(
		'tqx' => 'out:json',
	);

	if ( ! empty( $source['sheet'] ) ) {
		$query_args['sheet'] = $source['sheet'];
	}

	if ( isset( $source['gid'] ) && '' !== (string) $source['gid'] ) {
		$query_args['gid'] = (string) $source['gid'];
	}

	return add_query_arg( $query_args, $base_url );
}

/**
 * Parses a gviz JSONP response body into an array.
 *
 * @param string $body Raw response body.
 * @return array<string, mixed>|\WP_Error
 */
function crades_parse_gviz_response_body( $body ) {
	if ( empty( $body ) || ! is_string( $body ) ) {
		return new WP_Error(
			'crades_empty_gviz_body',
			__( 'The Google Sheets response body is empty.', 'crades-theme' ),
			array( 'status' => 502 )
		);
	}

	$body = preg_replace( '/^\xEF\xBB\xBF/', '', $body );
	$body = preg_replace( '/^\/\*O_o\*\/\s*/', '', trim( $body ) );

	if ( preg_match( '/google\.visualization\.Query\.setResponse\((.*)\);?\s*$/s', $body, $matches ) ) {
		$body = $matches[1];
	}

	$payload = json_decode( $body, true );

	if ( JSON_ERROR_NONE !== json_last_error() || ! is_array( $payload ) ) {
		return new WP_Error(
			'crades_invalid_gviz_json',
			__( 'The Google Sheets gviz response could not be decoded.', 'crades-theme' ),
			array(
				'status'     => 502,
				'json_error' => json_last_error_msg(),
			)
		);
	}

	if ( isset( $payload['status'] ) && 'ok' !== $payload['status'] ) {
		return new WP_Error(
			'crades_invalid_gviz_status',
			__( 'The Google Sheets gviz endpoint returned a non-ok status.', 'crades-theme' ),
			array(
				'status' => 502,
				'gviz'   => $payload,
			)
		);
	}

	return $payload;
}

/**
 * Builds a stable object key from a column label.
 *
 * @param string   $label     Column label.
 * @param int      $index     Column index.
 * @param string[] $used_keys Existing keys.
 * @return string
 */
function crades_build_gviz_column_key( $label, $index, $used_keys ) {
	$base_key = sanitize_title( remove_accents( $label ) );

	if ( '' === $base_key ) {
		$base_key = 'col_' . ( $index + 1 );
	}

	$key     = $base_key;
	$counter = 2;

	while ( in_array( $key, $used_keys, true ) ) {
		$key = $base_key . '_' . $counter;
		++$counter;
	}

	return $key;
}

/**
 * Normalizes one gviz cell value.
 *
 * @param array<string, mixed>|null $cell Cell value.
 * @return bool|float|int|string|null
 */
function crades_normalize_gviz_value( $cell ) {
	if ( ! is_array( $cell ) || ! array_key_exists( 'v', $cell ) ) {
		return null;
	}

	$value = $cell['v'];

	if ( is_string( $value ) ) {
		$value = trim( $value );

		if ( preg_match( '/^Date\((\d{4}),(\d{1,2}),(\d{1,2})(?:,(\d{1,2}),(\d{1,2}),(\d{1,2}))?\)$/', $value, $matches ) ) {
			$year   = (int) $matches[1];
			$month  = (int) $matches[2] + 1;
			$day    = (int) $matches[3];
			$hour   = isset( $matches[4] ) ? (int) $matches[4] : 0;
			$minute = isset( $matches[5] ) ? (int) $matches[5] : 0;
			$second = isset( $matches[6] ) ? (int) $matches[6] : 0;

			return gmdate( 'c', gmmktime( $hour, $minute, $second, $month, $day, $year ) );
		}
	}

	if ( is_bool( $value ) || is_float( $value ) || is_int( $value ) || is_null( $value ) || is_string( $value ) ) {
		return $value;
	}

	return wp_json_encode( $value );
}

/**
 * Extracts display-oriented headers and rows from a parsed gviz table.
 *
 * @param array<string, mixed> $payload Parsed gviz payload.
 * @return array<string, mixed>
 */
function crades_extract_gviz_display_table( $payload ) {
	$headers = array();
	$rows    = array();

	if ( empty( $payload['table'] ) || ! is_array( $payload['table'] ) ) {
		return array(
			'headers' => $headers,
			'rows'    => $rows,
		);
	}

	$table = $payload['table'];

	foreach ( (array) $table['cols'] as $index => $column ) {
		$label = '';

		if ( is_array( $column ) && isset( $column['label'] ) ) {
			$label = trim( (string) $column['label'] );
		}

		if ( '' === $label ) {
			$label = sprintf( 'Column %d', $index + 1 );
		}

		$headers[] = $label;
	}

	foreach ( (array) $table['rows'] as $row ) {
		$cells     = ( isset( $row['c'] ) && is_array( $row['c'] ) ) ? $row['c'] : array();
		$row_items = array();

		foreach ( $cells as $cell ) {
			if ( null === $cell || ! is_array( $cell ) ) {
				$row_items[] = '';
				continue;
			}

			if ( isset( $cell['f'] ) ) {
				$row_items[] = trim( (string) $cell['f'] );
				continue;
			}

			if ( isset( $cell['v'] ) && null !== $cell['v'] ) {
				$row_items[] = trim( (string) $cell['v'] );
				continue;
			}

			$row_items[] = '';
		}

		$rows[] = $row_items;
	}

	return array(
		'headers' => $headers,
		'rows'    => $rows,
	);
}

/**
 * Normalizes a gviz table into clean column and row arrays.
 *
 * @param array<string, mixed> $payload Parsed gviz payload.
 * @return array<string, mixed>|\WP_Error
 */
function crades_normalize_gviz_table( $payload ) {
	if ( empty( $payload['table'] ) || ! is_array( $payload['table'] ) ) {
		return new WP_Error(
			'crades_missing_gviz_table',
			__( 'The gviz payload did not include a table.', 'crades-theme' ),
			array( 'status' => 502 )
		);
	}

	$table   = $payload['table'];
	$columns = array();
	$headers = array();
	$rows    = array();
	$used    = array();

	foreach ( (array) $table['cols'] as $index => $column ) {
		$label = '';
		$type  = 'string';

		if ( is_array( $column ) ) {
			$label = isset( $column['label'] ) ? trim( (string) $column['label'] ) : '';
			$type  = isset( $column['type'] ) ? sanitize_text_field( (string) $column['type'] ) : 'string';
		}

		if ( '' === $label ) {
			$label = sprintf( 'Column %d', $index + 1 );
		}

		$key      = crades_build_gviz_column_key( $label, $index, $used );
		$used[]   = $key;
		$headers[] = $label;
		$columns[] = array(
			'key'   => $key,
			'label' => $label,
			'type'  => $type,
		);
	}

	foreach ( (array) $table['rows'] as $row_index => $row ) {
		$cells      = ( isset( $row['c'] ) && is_array( $row['c'] ) ) ? $row['c'] : array();
		$row_object = array(
			'_row' => $row_index + 1,
		);

		foreach ( $columns as $column_index => $column ) {
			$cell_value                   = isset( $cells[ $column_index ] ) ? $cells[ $column_index ] : null;
			$row_object[ $column['key'] ] = crades_normalize_gviz_value( $cell_value );
		}

		$rows[] = $row_object;
	}

	return array(
		'headers'  => $headers,
		'columns'  => $columns,
		'rows'     => $rows,
		'row_count' => count( $rows ),
	);
}

/**
 * Returns a stale sheet fallback payload when available.
 *
 * @param string       $dashboard_key     Dashboard key.
 * @param string       $source_key        Source key.
 * @param string|array $cache_identifier  Optional cache identifier.
 * @return array<string, mixed>|null
 */
function crades_get_stale_sheet_source_fallback( $dashboard_key, $source_key, $cache_identifier = null ) {
	if ( null === $cache_identifier ) {
		$cache_identifier = array( $dashboard_key, $source_key );
	}

	$fallback = crades_cache_get_fallback( 'sheet', $cache_identifier );

	if ( ! is_array( $fallback ) ) {
		return null;
	}

	$fallback['cache_hit'] = true;
	$fallback['stale']     = true;

	return $fallback;
}

/**
 * Returns a stale dashboard fallback payload when available.
 *
 * @param string       $dashboard_key     Dashboard key.
 * @param string|array $cache_identifier  Optional cache identifier.
 * @return array<string, mixed>|null
 */
function crades_get_stale_dashboard_fallback( $dashboard_key, $cache_identifier = null ) {
	if ( null === $cache_identifier ) {
		$cache_identifier = $dashboard_key;
	}

	$fallback = crades_cache_get_fallback( 'dashboard', $cache_identifier );

	if ( ! is_array( $fallback ) ) {
		return null;
	}

	$fallback['cache']['cached'] = true;
	$fallback['cache']['stale']  = true;

	return $fallback;
}

/**
 * Fetches and parses one Google Sheets source.
 *
 * @param string $dashboard_key Dashboard key.
 * @param string $source_key    Source key.
 * @param bool   $force_refresh Optional force refresh flag.
 * @return array<string, mixed>|\WP_Error
 */
function crades_get_dashboard_sheet_source( $dashboard_key, $source_key, $force_refresh = false ) {
	$dashboard = crades_get_dashboard_sheet_config( $dashboard_key );

	if ( empty( $dashboard ) || empty( $dashboard['sources'][ $source_key ] ) ) {
		return new WP_Error(
			'crades_unknown_sheet_source',
			__( 'Unknown dashboard sheet source.', 'crades-theme' ),
			array( 'status' => 404 )
		);
	}

	$source = $dashboard['sources'][ $source_key ];
	$ttl    = crades_get_dashboard_cache_ttl( $dashboard_key );
	$cache_identifier = array(
		$dashboard_key,
		$source_key,
		isset( $source['sheet'] ) ? (string) $source['sheet'] : '',
		isset( $source['gid'] ) ? (string) $source['gid'] : '',
	);

	if ( ! $force_refresh ) {
		$cached = crades_cache_get( 'sheet', $cache_identifier );

		if ( is_array( $cached ) ) {
			$cached['cache_hit'] = true;
			$cached['stale']     = false;

			return $cached;
		}
	}

	$url      = crades_build_gviz_url( $source );
	$response = wp_remote_get(
		$url,
		array(
			'timeout'            => 20,
			'redirection'        => 5,
			'reject_unsafe_urls' => true,
			'limit_response_size' => 1024 * 1024,
			'headers'            => array(
				'Accept'     => 'application/json',
				'User-Agent' => 'CRADES-Theme/' . wp_get_theme()->get( 'Version' ),
			),
		)
	);

	if ( is_wp_error( $response ) ) {
		$fallback = crades_get_stale_sheet_source_fallback( $dashboard_key, $source_key, $cache_identifier );

		if ( $fallback ) {
			return $fallback;
		}

		return new WP_Error(
			'crades_sheet_request_failed',
			__( 'Unable to reach the Google Sheets source.', 'crades-theme' ),
			array(
				'status'     => 502,
				'dashboard'  => $dashboard_key,
				'source_key' => $source_key,
				'url'        => $url,
			)
		);
	}

	$status_code = (int) wp_remote_retrieve_response_code( $response );
	$body        = wp_remote_retrieve_body( $response );

	if ( 200 > $status_code || 299 < $status_code ) {
		$fallback = crades_get_stale_sheet_source_fallback( $dashboard_key, $source_key, $cache_identifier );

		if ( $fallback ) {
			return $fallback;
		}

		return new WP_Error(
			'crades_sheet_http_error',
			sprintf(
				/* translators: %d: HTTP response code. */
				__( 'The Google Sheets source returned HTTP %d.', 'crades-theme' ),
				$status_code
			),
			array(
				'status'     => 502,
				'dashboard'  => $dashboard_key,
				'source_key' => $source_key,
				'url'        => $url,
			)
		);
	}

	$payload = crades_parse_gviz_response_body( $body );

	if ( is_wp_error( $payload ) ) {
		$fallback = crades_get_stale_sheet_source_fallback( $dashboard_key, $source_key, $cache_identifier );

		if ( $fallback ) {
			return $fallback;
		}

		$payload->add_data(
			array(
				'dashboard'  => $dashboard_key,
				'source_key' => $source_key,
				'url'        => $url,
			)
		);

		return $payload;
	}

	$table = crades_normalize_gviz_table( $payload );
	$display_table = crades_extract_gviz_display_table( $payload );

	if ( is_wp_error( $table ) ) {
		$fallback = crades_get_stale_sheet_source_fallback( $dashboard_key, $source_key, $cache_identifier );

		if ( $fallback ) {
			return $fallback;
		}

		$table->add_data(
			array(
				'dashboard'  => $dashboard_key,
				'source_key' => $source_key,
				'url'        => $url,
			)
		);

		return $table;
	}

	$result = array(
		'source'     => array(
			'key'            => $source_key,
			'label'          => $source['label'],
			'spreadsheet_id' => $source['spreadsheet_id'],
			'sheet'          => isset( $source['sheet'] ) ? $source['sheet'] : null,
			'gid'            => isset( $source['gid'] ) ? (string) $source['gid'] : null,
			'url'            => $url,
		),
		'table'      => array_merge(
			$table,
			array(
				'display_headers' => $display_table['headers'],
				'display_rows'    => $display_table['rows'],
			)
		),
		'fetched_at' => gmdate( 'c' ),
		'cache_hit'  => false,
		'stale'      => false,
	);

	crades_cache_set( 'sheet', $cache_identifier, $result, $ttl );
	crades_cache_set_fallback( 'sheet', $cache_identifier, $result );

	return $result;
}

/**
 * Returns the normalized dashboard payload for the REST API.
 *
 * @param string $dashboard_key Dashboard key.
 * @param bool   $force_refresh Optional force refresh flag.
 * @return array<string, mixed>|\WP_Error
 */
function crades_get_dashboard_sheet_payload( $dashboard_key, $force_refresh = false ) {
	$dashboard = crades_get_dashboard_sheet_config( $dashboard_key );

	if ( empty( $dashboard ) ) {
		return new WP_Error(
			'crades_unknown_dashboard',
			__( 'Unknown dashboard key.', 'crades-theme' ),
			array( 'status' => 404 )
		);
	}

	$dashboard_cache_identifier = array(
		$dashboard_key,
		md5( wp_json_encode( $dashboard['sources'] ) ),
		file_exists( __DIR__ . '/dashboard-viewmodels.php' ) ? (string) filemtime( __DIR__ . '/dashboard-viewmodels.php' ) : '1',
	);

	if ( ! $force_refresh ) {
		$cached = crades_cache_get( 'dashboard', $dashboard_cache_identifier );

		if ( is_array( $cached ) ) {
			$cached['cache']['cached'] = true;
			$cached['cache']['stale']  = false;

			return $cached;
		}
	}

	$ttl     = crades_get_dashboard_cache_ttl( $dashboard_key );
	$tables  = array();
	$sources = array();
	$errors  = array();

	foreach ( $dashboard['sources'] as $source_key => $source ) {
		$result = crades_get_dashboard_sheet_source( $dashboard_key, $source_key, $force_refresh );

		if ( is_wp_error( $result ) ) {
			$error_data = $result->get_error_data();

			$errors[] = array(
				'code'    => $result->get_error_code(),
				'message' => $result->get_error_message(),
				'source'  => $source_key,
				'details' => is_array( $error_data ) ? $error_data : array(),
			);

			continue;
		}

		$sources[]             = $result['source'];
		$tables[ $source_key ] = $result['table'];
	}

	if ( empty( $tables ) ) {
		$fallback = crades_get_stale_dashboard_fallback( $dashboard_key, $dashboard_cache_identifier );

		if ( $fallback ) {
			return $fallback;
		}

		return new WP_Error(
			'crades_dashboard_data_unavailable',
			__( 'No Google Sheets tables could be loaded for this dashboard.', 'crades-theme' ),
			array(
				'status' => 502,
				'errors' => $errors,
			)
		);
	}

	$payload = array(
		'success'   => true,
		'dashboard' => array(
			'key'         => $dashboard['key'],
			'title'       => $dashboard['title'],
			'description' => $dashboard['description'],
		),
		'cache'     => array(
			'cached'      => false,
			'stale'       => false,
			'ttl'         => $ttl,
			'generated_at' => gmdate( 'c' ),
			'expires_at'  => gmdate( 'c', time() + $ttl ),
		),
		'source'    => array(
			'provider' => 'google-sheets-gviz',
			'count'    => count( $sources ),
			'tables'   => $sources,
		),
		'data'      => array(
			'tables'      => $tables,
			'view_model' => crades_build_dashboard_view_model( $dashboard_key, $tables ),
		),
		'errors'    => $errors,
		'partial'   => ! empty( $errors ),
	);

	crades_cache_set( 'dashboard', $dashboard_cache_identifier, $payload, $ttl );
	crades_cache_set_fallback( 'dashboard', $dashboard_cache_identifier, $payload );

	return $payload;
}
