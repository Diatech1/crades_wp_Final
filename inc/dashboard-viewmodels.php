<?php
/**
 * Dashboard-specific view model builders that mirror the Hono business logic.
 *
 * @package CRADES_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns a table payload by source key.
 *
 * @param array<string, mixed> $tables      Dashboard tables.
 * @param string               $source_key  Source key.
 * @return array<string, mixed>
 */
function crades_vm_get_table( $tables, $source_key ) {
	return isset( $tables[ $source_key ] ) && is_array( $tables[ $source_key ] ) ? $tables[ $source_key ] : array();
}

/**
 * Returns display rows from a source table.
 *
 * @param array<string, mixed> $tables     Dashboard tables.
 * @param string               $source_key Source key.
 * @return array<int, array<int, string>>
 */
function crades_vm_get_display_rows( $tables, $source_key ) {
	$table = crades_vm_get_table( $tables, $source_key );

	return ! empty( $table['display_rows'] ) && is_array( $table['display_rows'] ) ? $table['display_rows'] : array();
}

/**
 * Returns display headers from a source table.
 *
 * @param array<string, mixed> $tables     Dashboard tables.
 * @param string               $source_key Source key.
 * @return array<int, string>
 */
function crades_vm_get_display_headers( $tables, $source_key ) {
	$table = crades_vm_get_table( $tables, $source_key );

	return ! empty( $table['display_headers'] ) && is_array( $table['display_headers'] ) ? $table['display_headers'] : array();
}

/**
 * Returns a matrix-like representation of a source table with headers first.
 *
 * @param array<string, mixed> $tables     Dashboard tables.
 * @param string               $source_key Source key.
 * @return array<int, array<int, string>>
 */
function crades_vm_get_sheet_matrix( $tables, $source_key ) {
	$headers = crades_vm_get_display_headers( $tables, $source_key );
	$rows    = crades_vm_get_display_rows( $tables, $source_key );

	if ( empty( $headers ) ) {
		return $rows;
	}

	return array_merge( array( $headers ), $rows );
}

/**
 * Returns normalized rows from a source table.
 *
 * @param array<string, mixed> $tables     Dashboard tables.
 * @param string               $source_key Source key.
 * @return array<int, array<string, mixed>>
 */
function crades_vm_get_rows( $tables, $source_key ) {
	$table = crades_vm_get_table( $tables, $source_key );

	return ! empty( $table['rows'] ) && is_array( $table['rows'] ) ? $table['rows'] : array();
}

/**
 * Parses a number from mixed content.
 *
 * @param mixed $value Raw value.
 * @return float
 */
function crades_vm_parse_number( $value ) {
	if ( is_int( $value ) || is_float( $value ) ) {
		return (float) $value;
	}

	$string = trim( (string) $value );

	if ( '' === $string ) {
		return 0.0;
	}

	$string = str_replace( array( ' ', "\xc2\xa0", "\xe2\x80\xaf", '%' ), '', $string );

	if ( false !== strpos( $string, ',' ) && false !== strpos( $string, '.' ) ) {
		if ( strrpos( $string, ',' ) > strrpos( $string, '.' ) ) {
			$string = str_replace( '.', '', $string );
			$string = str_replace( ',', '.', $string );
		} else {
			$string = str_replace( ',', '', $string );
		}
	} elseif ( false !== strpos( $string, ',' ) ) {
		if ( 1 === preg_match( '/^-?\d{1,3}(,\d{3})+$/', $string ) ) {
			$string = str_replace( ',', '', $string );
		} else {
			$string = str_replace( ',', '.', $string );
		}
	} elseif ( false !== strpos( $string, '.' ) && 1 === preg_match( '/^-?\d{1,3}(\.\d{3})+$/', $string ) ) {
		$string = str_replace( '.', '', $string );
	}

	$string = preg_replace( '/[^0-9.\-]/', '', $string );

	return is_numeric( $string ) ? (float) $string : 0.0;
}

/**
 * Parses a percent-like value.
 *
 * @param mixed $value Raw value.
 * @return float
 */
function crades_vm_parse_percent( $value ) {
	$string = trim( str_replace( '~', '', (string) $value ) );

	return crades_vm_parse_number( $string );
}

/**
 * Formats a number for display.
 *
 * @param float $value  Numeric value.
 * @param int   $digits Decimal precision.
 * @return string
 */
function crades_vm_format_number( $value, $digits = 1 ) {
	return number_format_i18n( (float) $value, (int) $digits );
}

/**
 * Builds a year-month label into French short format.
 *
 * @param string $value Raw period value.
 * @return string
 */
function crades_vm_format_ym_label( $value ) {
	$months = array(
		'01' => 'Jan',
		'02' => 'Fev',
		'03' => 'Mar',
		'04' => 'Avr',
		'05' => 'Mai',
		'06' => 'Juin',
		'07' => 'Juil',
		'08' => 'Aou',
		'09' => 'Sep',
		'10' => 'Oct',
		'11' => 'Nov',
		'12' => 'Dec',
	);

	if ( preg_match( '/^(\d{4})-(\d{2})/', (string) $value, $matches ) ) {
		return ( isset( $months[ $matches[2] ] ) ? $months[ $matches[2] ] : $matches[2] ) . ' ' . $matches[1];
	}

	return (string) $value;
}

/**
 * Returns exact fallback sectors for commerce exterieur.
 *
 * @return array<int, array<string, mixed>>
 */
function crades_vm_commerce_exterieur_sectors() {
	return array(
		array( 'name' => 'Combustibles', 'exports' => 1200, 'imports' => 3800 ),
		array( 'name' => 'Produits vegetaux', 'exports' => 450, 'imports' => 950 ),
		array( 'name' => 'Machines et electronique', 'exports' => 180, 'imports' => 1800 ),
		array( 'name' => 'Produits chimiques', 'exports' => 350, 'imports' => 850 ),
		array( 'name' => 'Metaux', 'exports' => 220, 'imports' => 650 ),
		array( 'name' => 'Transport', 'exports' => 80, 'imports' => 720 ),
		array( 'name' => 'Produits alimentaires', 'exports' => 420, 'imports' => 580 ),
		array( 'name' => 'Pierre et verre', 'exports' => 950, 'imports' => 120 ),
		array( 'name' => 'Mineraux', 'exports' => 650, 'imports' => 95 ),
		array( 'name' => 'Produits animaux', 'exports' => 380, 'imports' => 65 ),
		array( 'name' => 'Plastique / Caoutchouc', 'exports' => 75, 'imports' => 280 ),
		array( 'name' => 'Divers', 'exports' => 90, 'imports' => 180 ),
		array( 'name' => 'Bois', 'exports' => 55, 'imports' => 150 ),
		array( 'name' => 'Textiles et habillement', 'exports' => 120, 'imports' => 140 ),
		array( 'name' => 'Chaussures', 'exports' => 45, 'imports' => 85 ),
		array( 'name' => 'Cuirs et peaux', 'exports' => 35, 'imports' => 25 ),
	);
}

/**
 * Returns the default commerce exterieur data.
 *
 * @return array<string, mixed>
 */
function crades_vm_get_default_commerce_exterieur() {
	return array(
		'year'               => 2025,
		'overview'           => array(
			'totalExports'   => 4180.0,
			'totalImports'   => 7320.0,
			'tradeBalance'   => -3140.0,
			'tradeVolume'    => 11500.0,
			'exportGrowth'   => 0.069,
			'importGrowth'   => 0.022,
			'exportShareGDP' => 0.52,
			'importShareGDP' => 0.36,
		),
		'timeSeries'         => array(
			array( 'year' => 2016, 'exports' => 2280.0, 'imports' => 5450.0, 'balance' => -3170.0 ),
			array( 'year' => 2017, 'exports' => 2420.0, 'imports' => 5680.0, 'balance' => -3260.0 ),
			array( 'year' => 2018, 'exports' => 2580.0, 'imports' => 5920.0, 'balance' => -3340.0 ),
			array( 'year' => 2019, 'exports' => 2750.0, 'imports' => 6180.0, 'balance' => -3430.0 ),
			array( 'year' => 2020, 'exports' => 2480.0, 'imports' => 5850.0, 'balance' => -3370.0 ),
			array( 'year' => 2021, 'exports' => 3020.0, 'imports' => 6850.0, 'balance' => -3830.0 ),
			array( 'year' => 2022, 'exports' => 3563.4, 'imports' => 7549.4, 'balance' => -3986.0 ),
			array( 'year' => 2023, 'exports' => 3223.9, 'imports' => 7207.8, 'balance' => -3983.9 ),
			array( 'year' => 2024, 'exports' => 3909.1, 'imports' => 7161.4, 'balance' => -3252.3 ),
			array( 'year' => 2025, 'exports' => 4180.0, 'imports' => 7320.0, 'balance' => -3140.0 ),
		),
		'topExportPartners'  => array(
			array( 'country' => 'Mali', 'value' => 802.8, 'share' => 0.205 ),
			array( 'country' => 'Suisse', 'value' => 472.9, 'share' => 0.121 ),
			array( 'country' => 'Inde', 'value' => 353.7, 'share' => 0.090 ),
			array( 'country' => 'Espagne', 'value' => 154.1, 'share' => 0.039 ),
			array( 'country' => 'Etats-Unis', 'value' => 133.9, 'share' => 0.034 ),
			array( 'country' => 'Italie', 'value' => 118.5, 'share' => 0.030 ),
			array( 'country' => 'Gambie', 'value' => 95.2, 'share' => 0.024 ),
			array( 'country' => 'Guinee', 'value' => 87.6, 'share' => 0.022 ),
			array( 'country' => 'Pays-Bas', 'value' => 76.3, 'share' => 0.019 ),
			array( 'country' => 'Cote d Ivoire', 'value' => 68.4, 'share' => 0.017 ),
		),
		'topImportPartners'  => array(
			array( 'country' => 'Chine', 'value' => 1450.2, 'share' => 0.202 ),
			array( 'country' => 'France', 'value' => 920.5, 'share' => 0.129 ),
			array( 'country' => 'Inde', 'value' => 680.3, 'share' => 0.095 ),
			array( 'country' => 'Espagne', 'value' => 520.8, 'share' => 0.073 ),
			array( 'country' => 'Nigeria', 'value' => 410.5, 'share' => 0.057 ),
			array( 'country' => 'Belgique', 'value' => 385.7, 'share' => 0.054 ),
			array( 'country' => 'Pays-Bas', 'value' => 342.1, 'share' => 0.048 ),
			array( 'country' => 'Turquie', 'value' => 298.6, 'share' => 0.042 ),
			array( 'country' => 'Emirats Arabes Unis', 'value' => 265.4, 'share' => 0.037 ),
			array( 'country' => 'Russie', 'value' => 243.8, 'share' => 0.034 ),
		),
		'sources'            => array(
			'primary'   => 'ANSD',
			'secondary' => array( 'ODP Senegal', 'WTO', 'UN Comtrade' ),
		),
		'sectors'            => crades_vm_commerce_exterieur_sectors(),
	);
}

/**
 * Builds the commerce exterieur data object.
 *
 * @param array<string, mixed> $tables Dashboard tables.
 * @return array<string, mixed>
 */
function crades_vm_build_commerce_exterieur_data( $tables ) {
	$defaults     = crades_vm_get_default_commerce_exterieur();
	$summary_rows = crades_vm_get_display_rows( $tables, 'summary' );

	if ( empty( $summary_rows ) ) {
		return $defaults;
	}

	$exports_2024     = ! empty( $summary_rows[0][3] ) ? crades_vm_parse_number( $summary_rows[0][3] ) : 3909.1;
	$imports_2024     = ! empty( $summary_rows[1][3] ) ? crades_vm_parse_number( $summary_rows[1][3] ) : 7161.4;
	$balance_2024     = ! empty( $summary_rows[2][3] ) ? crades_vm_parse_number( $summary_rows[2][3] ) : -3252.3;
	$volume_2024      = ! empty( $summary_rows[3][3] ) ? crades_vm_parse_number( $summary_rows[3][3] ) : 11070.5;
	$exports_2023     = ! empty( $summary_rows[0][2] ) ? crades_vm_parse_number( $summary_rows[0][2] ) : 3223.9;
	$imports_2023     = ! empty( $summary_rows[1][2] ) ? crades_vm_parse_number( $summary_rows[1][2] ) : 7207.8;
	$export_growth    = ! empty( $summary_rows[0][4] ) ? crades_vm_parse_number( $summary_rows[0][4] ) : 0.213;
	$import_growth    = ! empty( $summary_rows[1][4] ) ? crades_vm_parse_number( $summary_rows[1][4] ) : -0.006;
	$export_share_gdp = ! empty( $summary_rows[4][3] ) ? crades_vm_parse_number( $summary_rows[4][3] ) : 0.546;
	$import_share_gdp = ! empty( $summary_rows[5][3] ) ? crades_vm_parse_number( $summary_rows[5][3] ) : 0.358;

	$export_partners = array();
	$import_partners = array();

	for ( $index = 9; $index < min( count( $summary_rows ), 20 ); $index++ ) {
		$row = $summary_rows[ $index ];

		if ( empty( $row[1] ) ) {
			continue;
		}

		$partner = array(
			'country' => trim( (string) $row[1] ),
			'value'   => isset( $row[3] ) ? crades_vm_parse_number( $row[3] ) : 0,
			'share'   => isset( $row[4] ) ? crades_vm_parse_number( $row[4] ) : 0,
		);

		if ( $index < 15 ) {
			$export_partners[] = $partner;
		} else {
			$import_partners[] = $partner;
		}
	}

	$time_series = array(
		array( 'year' => 2016, 'exports' => 2280.0, 'imports' => 5450.0, 'balance' => -3170.0 ),
		array( 'year' => 2017, 'exports' => 2420.0, 'imports' => 5680.0, 'balance' => -3260.0 ),
		array( 'year' => 2018, 'exports' => 2580.0, 'imports' => 5920.0, 'balance' => -3340.0 ),
		array( 'year' => 2019, 'exports' => 2750.0, 'imports' => 6180.0, 'balance' => -3430.0 ),
		array( 'year' => 2020, 'exports' => 2480.0, 'imports' => 5850.0, 'balance' => -3370.0 ),
		array( 'year' => 2021, 'exports' => 3020.0, 'imports' => 6850.0, 'balance' => -3830.0 ),
		array(
			'year'    => 2022,
			'exports' => isset( $summary_rows[0][1] ) ? crades_vm_parse_number( $summary_rows[0][1] ) : 3563.4,
			'imports' => isset( $summary_rows[1][1] ) ? crades_vm_parse_number( $summary_rows[1][1] ) : 7549.4,
			'balance' => isset( $summary_rows[2][1] ) ? crades_vm_parse_number( $summary_rows[2][1] ) : -3986.0,
		),
		array(
			'year'    => 2023,
			'exports' => $exports_2023,
			'imports' => $imports_2023,
			'balance' => $exports_2023 - $imports_2023,
		),
		array(
			'year'    => 2024,
			'exports' => $exports_2024,
			'imports' => $imports_2024,
			'balance' => $balance_2024,
		),
		array( 'year' => 2025, 'exports' => 4180.0, 'imports' => 7320.0, 'balance' => -3140.0 ),
	);

	return array(
		'year'              => 2025,
		'overview'          => array(
			'totalExports'   => 4180.0,
			'totalImports'   => 7320.0,
			'tradeBalance'   => -3140.0,
			'tradeVolume'    => 11500.0,
			'exportGrowth'   => 0.069,
			'importGrowth'   => 0.022,
			'exportShareGDP' => 0.52,
			'importShareGDP' => 0.36,
		),
		'timeSeries'        => $time_series,
		'topExportPartners' => count( $export_partners ) >= 10 ? array_slice( $export_partners, 0, 10 ) : $defaults['topExportPartners'],
		'topImportPartners' => count( $import_partners ) >= 10 ? array_slice( $import_partners, 0, 10 ) : $defaults['topImportPartners'],
		'sources'           => $defaults['sources'],
		'sectors'           => $defaults['sectors'],
		'rawOverview'       => array(
			'totalExports'   => $exports_2024,
			'totalImports'   => $imports_2024,
			'tradeBalance'   => $balance_2024,
			'tradeVolume'    => $volume_2024,
			'exportGrowth'   => $export_growth,
			'importGrowth'   => $import_growth,
			'exportShareGDP' => $export_share_gdp,
			'importShareGDP' => $import_share_gdp,
		),
	);
}

/**
 * Returns default commerce interieur data.
 *
 * @return array<string, mixed>
 */
function crades_vm_get_default_commerce_interieur() {
	return array(
		'year'             => '2026',
		'indicators'       => array(),
		'ihpcData'         => array(),
		'ihpcSeries'       => array(
			array( 'date' => 'Jan 2024', 'value' => 100.5 ),
			array( 'date' => 'Fev 2024', 'value' => 100.7 ),
			array( 'date' => 'Mar 2024', 'value' => 100.9 ),
			array( 'date' => 'Avr 2024', 'value' => 101.1 ),
			array( 'date' => 'Mai 2024', 'value' => 101.3 ),
			array( 'date' => 'Jun 2024', 'value' => 101.5 ),
			array( 'date' => 'Jul 2024', 'value' => 101.6 ),
			array( 'date' => 'Aou 2024', 'value' => 101.7 ),
			array( 'date' => 'Sep 2024', 'value' => 101.8 ),
			array( 'date' => 'Oct 2024', 'value' => 101.9 ),
			array( 'date' => 'Nov 2024', 'value' => 102.0 ),
			array( 'date' => 'Dec 2024', 'value' => 102.0 ),
			array( 'date' => 'Jan 2025', 'value' => 102.0 ),
			array( 'date' => 'Fev 2025', 'value' => 101.8 ),
		),
		'ihpcDesagrege'    => array(),
		'icaiSeries'       => array(
			array( 'date' => '2023 T3', 'gros' => 104.9, 'detail' => 116.7, 'autoMoto' => 119.3 ),
			array( 'date' => '2023 T4', 'gros' => 108.2, 'detail' => 120.4, 'autoMoto' => 123.1 ),
			array( 'date' => '2024 T1', 'gros' => 106.5, 'detail' => 118.3, 'autoMoto' => 121.0 ),
			array( 'date' => '2024 T2', 'gros' => 109.6, 'detail' => 122.0, 'autoMoto' => 124.6 ),
			array( 'date' => '2024 T3', 'gros' => 112.0, 'detail' => 124.4, 'autoMoto' => 127.1 ),
			array( 'date' => '2024 T4', 'gros' => 114.8, 'detail' => 127.5, 'autoMoto' => 130.3 ),
			array( 'date' => '2025 T1', 'gros' => 117.9, 'detail' => 131.0, 'autoMoto' => 133.8 ),
			array( 'date' => '2025 T2', 'gros' => 121.0, 'detail' => 134.5, 'autoMoto' => 137.3 ),
			array( 'date' => '2025 T3', 'gros' => 124.2, 'detail' => 138.1, 'autoMoto' => 140.9 ),
			array( 'date' => '2025 T4', 'gros' => 127.5, 'detail' => 141.7, 'autoMoto' => 144.6 ),
		),
		'icaiBreakdown'    => array(
			array( 'category' => 'Commerce de gros', 'value' => 2500, 'share' => 35.0 ),
			array( 'category' => 'Commerce de détail', 'value' => 1800, 'share' => 25.2 ),
			array( 'category' => 'Distribution alimentaire', 'value' => 1200, 'share' => 16.8 ),
			array( 'category' => 'Transport et logistique', 'value' => 900, 'share' => 12.6 ),
			array( 'category' => 'Services associés', 'value' => 750, 'share' => 10.4 ),
		),
		'inflationData'    => array(
			array( 'year' => '2023', 'rate' => 5.9 ),
			array( 'year' => '2024', 'rate' => 0.8 ),
			array( 'year' => '2025', 'rate' => 1.4 ),
		),
		'emploiCommerce'   => null,
		'denreesDeBase'    => array(),
		'denreesDeBaseSeries' => array(),
	);
}

/**
 * Parses the commerce interieur overview indicators sheet.
 *
 * @param array<int, array<int, string>> $rows Raw display rows.
 * @return array<int, array<string, mixed>>
 */
function crades_vm_parse_commerce_indicators( $rows ) {
	$indicators = array();

	foreach ( $rows as $row ) {
		if ( count( $row ) < 5 ) {
			continue;
		}

		$name = trim( (string) $row[0] );

		if ( '' === $name ) {
			continue;
		}

		$value = null;

		if ( '' !== trim( (string) $row[1] ) && false === strpos( (string) $row[1], 'ERROR' ) ) {
			$parsed = crades_vm_parse_number( $row[1] );
			$value  = 0.0 === $parsed && ! preg_match( '/0/', (string) $row[1] ) ? null : $parsed;
		}

		$indicators[] = array(
			'name'      => $name,
			'value'     => $value,
			'reference' => isset( $row[2] ) ? trim( (string) $row[2] ) : '',
			'note'      => isset( $row[3] ) ? trim( (string) $row[3] ) : '',
			'source'    => ! empty( $row[4] ) ? trim( (string) $row[4] ) : 'ANSD',
			'formatted' => isset( $row[1] ) ? trim( (string) $row[1] ) : 'n/a',
		);
	}

	return $indicators;
}

/**
 * Builds yearly inflation from the IHPC global sheet.
 *
 * @param array<int, array<int, string>> $rows Raw display rows.
 * @return array<int, array<string, mixed>>
 */
function crades_vm_build_inflation_from_ihpc( $rows ) {
	$annual_ihpc   = array();
	$current_year  = (int) gmdate( 'Y' );
	$current_month = (int) gmdate( 'n' );

	foreach ( $rows as $row ) {
		$date_label = isset( $row[0] ) ? strtolower( trim( (string) $row[0] ) ) : '';
		$value      = isset( $row[1] ) ? crades_vm_parse_number( $row[1] ) : 0;

		if ( false === strpos( $date_label, 'annuel' ) || $value <= 0 ) {
			continue;
		}

		if ( ! preg_match( '/(\d{4})/', $date_label, $matches ) ) {
			continue;
		}

		$year = (int) $matches[1];

		if ( $year > $current_year || ( $year === $current_year && 12 !== $current_month ) ) {
			continue;
		}

		$annual_ihpc[] = array(
			'year' => $year,
			'ihpc' => $value,
		);
	}

	usort(
		$annual_ihpc,
		function ( $left, $right ) {
			return $left['year'] <=> $right['year'];
		}
	);

	$inflation = array();

	for ( $index = 1; $index < count( $annual_ihpc ); $index++ ) {
		$current = $annual_ihpc[ $index ];
		$previous = $annual_ihpc[ $index - 1 ];
		$raw_change = 0 !== $previous['ihpc'] ? ( ( $current['ihpc'] / $previous['ihpc'] ) - 1 ) * 100 : 0;

		if ( 100.0 === (float) $current['ihpc'] && abs( $raw_change ) > 10 ) {
			continue;
		}

		$inflation[] = array(
			'year' => (string) $current['year'],
			'rate' => round( $raw_change, 1 ),
		);
	}

	return $inflation;
}

/**
 * Parses IHPC desagrege rows.
 *
 * @param array<int, array<int, string>> $rows Raw display rows.
 * @return array<int, array<string, mixed>>
 */
function crades_vm_parse_ihpc_desagrege_rows( $rows ) {
	$result = array();

	foreach ( $rows as $row ) {
		$date = isset( $row[0] ) ? trim( (string) $row[0] ) : '';

		if ( '' === $date ) {
			continue;
		}

		$result[] = array(
			'date'                     => $date,
			'global'                   => isset( $row[1] ) ? crades_vm_parse_number( $row[1] ) : 0,
			'alimentaire'              => isset( $row[2] ) ? crades_vm_parse_number( $row[2] ) : 0,
			'boissonsTabac'            => isset( $row[3] ) ? crades_vm_parse_number( $row[3] ) : 0,
			'vetementsChaussures'      => isset( $row[4] ) ? crades_vm_parse_number( $row[4] ) : 0,
			'logementEau'              => isset( $row[5] ) ? crades_vm_parse_number( $row[5] ) : 0,
			'soinsPersonnels'          => isset( $row[6] ) ? crades_vm_parse_number( $row[6] ) : 0,
			'assurancesFinances'       => isset( $row[7] ) ? crades_vm_parse_number( $row[7] ) : 0,
			'restaurantsHebergement'   => isset( $row[8] ) ? crades_vm_parse_number( $row[8] ) : 0,
			'enseignement'             => isset( $row[9] ) ? crades_vm_parse_number( $row[9] ) : 0,
			'loisirsCulture'           => isset( $row[10] ) ? crades_vm_parse_number( $row[10] ) : 0,
			'informationCommunication' => isset( $row[11] ) ? crades_vm_parse_number( $row[11] ) : 0,
			'transport'                => isset( $row[12] ) ? crades_vm_parse_number( $row[12] ) : 0,
			'ameublementMenager'       => isset( $row[13] ) ? crades_vm_parse_number( $row[13] ) : 0,
			'sante'                    => isset( $row[14] ) ? crades_vm_parse_number( $row[14] ) : 0,
		);
	}

	return $result;
}

/**
 * Parses the emploi commerce KPI.
 *
 * @param array<int, string>            $headers Display headers.
 * @param array<int, array<int, string>> $rows    Raw display rows.
 * @return array<string, mixed>|null
 */
function crades_vm_parse_emploi_commerce( $headers, $rows ) {
	foreach ( $rows as $row ) {
		$label = isset( $row[0] ) ? strtoupper( trim( (string) $row[0] ) ) : '';

		if ( false === strpos( $label, 'COMMERCE DE GROS' ) || false === strpos( $label, 'REPAR' ) ) {
			continue;
		}

		$value = isset( $row[6] ) ? crades_vm_parse_number( $row[6] ) : 0;

		return array(
			'label' => trim( (string) $row[0] ),
			'value' => 0.0 === $value && '' === trim( (string) ( $row[6] ?? '' ) ) ? null : $value,
			'year'  => isset( $headers[6] ) ? trim( (string) $headers[6] ) : '',
		);
	}

	return null;
}

/**
 * Parses denrees de base latest rows and timeseries.
 *
 * @param array<int, string>             $headers Display headers.
 * @param array<int, array<int, string>> $rows    Raw display rows.
 * @return array<string, mixed>
 */
function crades_vm_parse_denrees_data( $headers, $rows ) {
	$parsed = array();

	foreach ( $rows as $row ) {
		$date_value = isset( $row[0] ) ? trim( (string) $row[0] ) : '';
		$timestamp  = strtotime( $date_value );

		if ( '' === $date_value || false === $timestamp ) {
			continue;
		}

		$parsed[] = array(
			'timestamp' => $timestamp,
			'date'      => gmdate( 'Y-m-d', $timestamp ),
			'row'       => $row,
		);
	}

	usort(
		$parsed,
		function ( $left, $right ) {
			return $left['timestamp'] <=> $right['timestamp'];
		}
	);

	if ( count( $parsed ) < 2 ) {
		return crades_vm_parse_denrees_matrix_data( $headers, $rows );
	}

	$latest = $parsed[ count( $parsed ) - 1 ];
	$prev   = $parsed[ count( $parsed ) - 2 ];
	$latest_rows = array();

	for ( $index = 3; $index < count( $headers ); $index++ ) {
		$product = trim( (string) $headers[ $index ] );

		if ( '' === $product ) {
			continue;
		}

		$price = isset( $latest['row'][ $index ] ) ? crades_vm_parse_number( $latest['row'][ $index ] ) : 0;
		$prev_value = isset( $prev['row'][ $index ] ) ? crades_vm_parse_number( $prev['row'][ $index ] ) : 0;
		$variation = $prev_value > 0 ? ( ( $price / $prev_value ) - 1 ) * 100 : null;
		preg_match( '/\(([^)]+)\)/', $product, $matches );

		$latest_rows[] = array(
			'date'         => $latest['date'],
			'produit'      => $product,
			'prix'         => $price > 0 ? $price : null,
			'unite'        => ! empty( $matches[1] ) ? $matches[1] : 'FCFA/kg',
			'prev'         => $prev_value > 0 ? $prev_value : null,
			'variationPct' => null !== $variation ? round( $variation, 1 ) : null,
		);
	}

	$series = array();
	$recent = array_slice( $parsed, -12 );

	foreach ( $recent as $item ) {
		$values = array();

		for ( $index = 3; $index < count( $headers ); $index++ ) {
			$product = trim( (string) $headers[ $index ] );

			if ( '' === $product ) {
				continue;
			}

			$value = isset( $item['row'][ $index ] ) ? crades_vm_parse_number( $item['row'][ $index ] ) : 0;
			$values[ $product ] = $value > 0 ? $value : null;
		}

		$series[] = array(
			'date'   => $item['date'],
			'values' => $values,
		);
	}

	return array(
		'latest' => $latest_rows,
		'series' => $series,
	);
}

/**
 * Parses denrees data delivered as a matrix with products in rows and years in columns.
 *
 * @param array<int, string>             $headers Display headers.
 * @param array<int, array<int, string>> $rows    Raw display rows.
 * @return array<string, mixed>
 */
function crades_vm_parse_denrees_matrix_data( $headers, $rows ) {
	$year_columns = array();

	foreach ( $headers as $index => $header ) {
		$label = trim( (string) $header );

		if ( preg_match( '/^\d{4}$/', $label ) ) {
			$year_columns[] = array(
				'index' => $index,
				'year'  => $label,
			);
		}
	}

	// Keep only columns that actually behave like price columns.
	$year_columns = array_values(
		array_filter(
			$year_columns,
			function ( $column ) use ( $rows ) {
				$numeric_samples = array();

				foreach ( $rows as $row ) {
					$value = isset( $row[ $column['index'] ] ) ? crades_vm_parse_number( $row[ $column['index'] ] ) : 0;

					if ( $value > 0 ) {
						$numeric_samples[] = $value;
					}
				}

				if ( count( $numeric_samples ) < 3 ) {
					return false;
				}

				sort( $numeric_samples );
				$median = $numeric_samples[ (int) floor( count( $numeric_samples ) / 2 ) ];

				// Price columns are large FCFA values; percentage columns in this sheet are tiny.
				return $median >= 50;
			}
		)
	);

	if ( count( $year_columns ) < 2 ) {
		return array(
			'latest' => array(),
			'series' => array(),
		);
	}

	$product_rows = array();
	$inside_denrees_section = false;

	foreach ( $rows as $row ) {
		$product = isset( $row[0] ) ? trim( (string) $row[0] ) : '';

		if ( '' === $product ) {
			continue;
		}

		$upper_product = strtoupper( $product );

		if ( false !== strpos( $upper_product, 'PRIX DENR' ) ) {
			$inside_denrees_section = true;
			continue;
		}

		if ( ! $inside_denrees_section ) {
			continue;
		}

		if ( 'PRODUIT' === $upper_product ) {
			continue;
		}

		if (
			false !== strpos( $upper_product, 'EMPLOI' ) ||
			'INDICATEUR' === $upper_product ||
			0 === strpos( $upper_product, 'SOURCES:' )
		) {
			break;
		}

		if ( preg_match( '/^\d+[,.]?\d*%?$/', $product ) ) {
			continue;
		}

		$has_price = false;

		foreach ( $year_columns as $column ) {
			$value = isset( $row[ $column['index'] ] ) ? crades_vm_parse_number( $row[ $column['index'] ] ) : 0;

			if ( $value > 0 ) {
				$has_price = true;
				break;
			}
		}

		if ( ! $has_price ) {
			continue;
		}

		$product_rows[] = array(
			'product' => $product,
			'row'     => $row,
		);
	}

	if ( empty( $product_rows ) ) {
		return array(
			'latest' => array(),
			'series' => array(),
		);
	}

	$latest_column = $year_columns[ count( $year_columns ) - 1 ];
	$prev_column   = $year_columns[ count( $year_columns ) - 2 ];
	$latest_rows   = array();

	foreach ( $product_rows as $product_row ) {
		$product    = $product_row['product'];
		$row        = $product_row['row'];
		$price      = isset( $row[ $latest_column['index'] ] ) ? crades_vm_parse_number( $row[ $latest_column['index'] ] ) : 0;
		$prev_value = isset( $row[ $prev_column['index'] ] ) ? crades_vm_parse_number( $row[ $prev_column['index'] ] ) : 0;
		$variation  = 0 !== $prev_value ? ( ( $price / $prev_value ) - 1 ) * 100 : null;
		$unit       = false !== strpos( strtoupper( $product ), '/L' ) ? 'FCFA/L' : 'FCFA/kg';

		$latest_rows[] = array(
			'date'         => $latest_column['year'],
			'produit'      => $product,
			'prix'         => $price > 0 ? $price : null,
			'unite'        => $unit,
			'prev'         => $prev_value > 0 ? $prev_value : null,
			'variationPct' => null !== $variation ? round( $variation, 1 ) : null,
		);
	}

	$series = array();

	foreach ( $year_columns as $column ) {
		$values = array();

		foreach ( $product_rows as $product_row ) {
			$product           = $product_row['product'];
			$row               = $product_row['row'];
			$value             = isset( $row[ $column['index'] ] ) ? crades_vm_parse_number( $row[ $column['index'] ] ) : 0;
			$values[ $product ] = $value > 0 ? $value : null;
		}

		$series[] = array(
			'date'   => $column['year'],
			'values' => $values,
		);
	}

	return array(
		'latest' => $latest_rows,
		'series' => $series,
	);
}

/**
 * Builds the commerce interieur data object.
 *
 * @param array<string, mixed> $tables Dashboard tables.
 * @return array<string, mixed>
 */
function crades_vm_build_commerce_interieur_data( $tables ) {
	$defaults       = crades_vm_get_default_commerce_interieur();
	$indicators     = crades_vm_parse_commerce_indicators( crades_vm_get_display_rows( $tables, 'overview' ) );
	$ihpc_rows      = crades_vm_get_display_rows( $tables, 'ihpc_global' );
	$ihpc_headers   = crades_vm_get_display_headers( $tables, 'ihpc_global' );
	$ihpc_desagrege = crades_vm_parse_ihpc_desagrege_rows( crades_vm_get_display_rows( $tables, 'ihpc_desagrege' ) );
	$emploi         = crades_vm_parse_emploi_commerce( crades_vm_get_display_headers( $tables, 'emploi_commerce' ), crades_vm_get_display_rows( $tables, 'emploi_commerce' ) );
	$denrees        = crades_vm_parse_denrees_data( crades_vm_get_display_headers( $tables, 'denrees_base' ), crades_vm_get_display_rows( $tables, 'denrees_base' ) );
	$inflation      = crades_vm_build_inflation_from_ihpc( $ihpc_rows );
	$ihpc_series    = array();
	$dashboard_year = (string) gmdate( 'Y' );

	foreach ( $ihpc_rows as $row ) {
		$period = isset( $row[0] ) ? trim( (string) $row[0] ) : '';
		$value  = isset( $row[1] ) ? crades_vm_parse_number( $row[1] ) : 0;

		if ( '' === $period || $value <= 0 || false !== strpos( strtolower( $period ), 'annuel' ) ) {
			continue;
		}

		$ihpc_series[] = array(
			'date'  => $period,
			'value' => $value,
		);
	}

	if ( ! empty( $indicators ) ) {
		foreach ( $indicators as $indicator ) {
			if ( preg_match( '/(\d{4})/', (string) $indicator['name'], $matches ) ) {
				$dashboard_year = $matches[1];
				break;
			}
		}
	} elseif ( ! empty( $ihpc_desagrege ) && preg_match( '/(\d{4})/', (string) $ihpc_desagrege[ count( $ihpc_desagrege ) - 1 ]['date'], $matches ) ) {
		$dashboard_year = $matches[1];
	}

	return array(
		'year'               => $dashboard_year ? $dashboard_year : $defaults['year'],
		'indicators'         => ! empty( $indicators ) ? $indicators : $defaults['indicators'],
		'ihpcData'           => array(),
		'ihpcSeries'         => count( $ihpc_series ) >= 6 ? array_slice( $ihpc_series, -14 ) : $defaults['ihpcSeries'],
		'ihpcDesagrege'      => count( $ihpc_desagrege ) >= 6 ? $ihpc_desagrege : $defaults['ihpcDesagrege'],
		'icaiSeries'         => $defaults['icaiSeries'],
		'icaiBreakdown'      => $defaults['icaiBreakdown'],
		'inflationData'      => ! empty( $inflation ) ? $inflation : $defaults['inflationData'],
		'emploiCommerce'     => $emploi ? $emploi : $defaults['emploiCommerce'],
		'denreesDeBase'      => ! empty( $denrees['latest'] ) ? $denrees['latest'] : $defaults['denreesDeBase'],
		'denreesDeBaseSeries'=> ! empty( $denrees['series'] ) ? $denrees['series'] : $defaults['denreesDeBaseSeries'],
	);
}

/**
 * Builds the commerce interieur view model.
 *
 * @param array<string, mixed> $tables Dashboard tables.
 * @return array<string, mixed>
 */
function crades_vm_build_commerce_interieur_view_model( $tables ) {
	$data            = crades_vm_build_commerce_interieur_data( $tables );
	$ihpc_recent     = array_slice( $data['ihpcDesagrege'], -11 );
	$variation_labels = array();
	$variation_sets   = array();
	$categories       = array(
		array( 'key' => 'global', 'label' => 'Global', 'color' => '#1e293b' ),
		array( 'key' => 'alimentaire', 'label' => 'Alim. & boissons', 'color' => '#e74c3c' ),
		array( 'key' => 'boissonsTabac', 'label' => 'Boissons & tabac', 'color' => '#8e44ad' ),
		array( 'key' => 'vetementsChaussures', 'label' => 'Vetements', 'color' => '#3498db' ),
		array( 'key' => 'logementEau', 'label' => 'Logement & eau', 'color' => '#2ecc71' ),
		array( 'key' => 'soinsPersonnels', 'label' => 'Soins perso.', 'color' => '#f39c12' ),
		array( 'key' => 'assurancesFinances', 'label' => 'Assurances', 'color' => '#1abc9c' ),
		array( 'key' => 'restaurantsHebergement', 'label' => 'Restaurants', 'color' => '#e67e22' ),
		array( 'key' => 'enseignement', 'label' => 'Enseignement', 'color' => '#9b59b6' ),
		array( 'key' => 'loisirsCulture', 'label' => 'Loisirs', 'color' => '#16a085' ),
		array( 'key' => 'informationCommunication', 'label' => 'Info. & com.', 'color' => '#2980b9' ),
		array( 'key' => 'transport', 'label' => 'Transport', 'color' => '#c0392b' ),
		array( 'key' => 'ameublementMenager', 'label' => 'Ameublement', 'color' => '#d35400' ),
		array( 'key' => 'sante', 'label' => 'Sante', 'color' => '#27ae60' ),
	);

	for ( $index = 1; $index < count( $ihpc_recent ); $index++ ) {
		$variation_labels[] = $ihpc_recent[ $index ]['date'];
	}

	foreach ( $categories as $category ) {
		$points = array();

		for ( $index = 1; $index < count( $ihpc_recent ); $index++ ) {
			$prev = isset( $ihpc_recent[ $index - 1 ][ $category['key'] ] ) ? (float) $ihpc_recent[ $index - 1 ][ $category['key'] ] : 0;
			$curr = isset( $ihpc_recent[ $index ][ $category['key'] ] ) ? (float) $ihpc_recent[ $index ][ $category['key'] ] : 0;
			$points[] = $prev > 0 && $curr > 0 ? round( ( ( $curr / $prev ) - 1 ) * 100, 2 ) : null;
		}

		$variation_sets[] = array(
			'label'           => $category['label'],
			'data'            => $points,
			'borderColor'     => $category['color'],
			'backgroundColor' => $category['color'] . 'CC',
			'borderWidth'     => 'global' === $category['key'] ? 2.5 : 1.5,
			'pointRadius'     => 3,
			'pointHoverRadius'=> 5,
			'tension'         => 0.3,
			'fill'            => false,
		);
	}

	$icai_recent = array_slice( $data['icaiSeries'], -10 );

	$ihpc_indicator = null;

	foreach ( $data['indicators'] as $indicator ) {
		if ( false !== strpos( $indicator['name'], 'IHPC Global Fev' ) || false !== strpos( $indicator['name'], 'IHPC Global Jan' ) || false !== strpos( $indicator['name'], 'IHPC Global F' ) ) {
			$ihpc_indicator = $indicator;
			break;
		}
	}

	$latest_inflation = ! empty( $data['inflationData'] ) ? $data['inflationData'][ count( $data['inflationData'] ) - 1 ] : null;
	$latest_icai      = ! empty( $icai_recent ) ? $icai_recent[ count( $icai_recent ) - 1 ] : null;
	$variation_period = '';
	$inflation_period = '';

	if ( count( $variation_labels ) >= 2 ) {
		$variation_period = $variation_labels[0] . ' - ' . $variation_labels[ count( $variation_labels ) - 1 ];
	} elseif ( 1 === count( $variation_labels ) ) {
		$variation_period = $variation_labels[0];
	}

	if ( ! empty( $data['inflationData'] ) ) {
		$inflation_period = $data['inflationData'][0]['year'] . '-' . $data['inflationData'][ count( $data['inflationData'] ) - 1 ]['year'] . ' (années complètes)';
	}

	return array(
		'meta'   => array(
			'year'       => $data['year'],
			'yearLabel'  => 'Mars ' . $data['year'],
			'footerNote' => 'Données Commerce Intérieur actualisées Mars ' . $data['year'] . ' - IHPC Base 100=2023',
		),
		'kpis'   => array(
			array(
				'label'   => 'IHPC Global',
				'display' => $ihpc_indicator ? $ihpc_indicator['formatted'] : 'n/a',
				'note'    => $ihpc_indicator ? $ihpc_indicator['note'] : 'Base 100=2023',
				'badge'   => 'ANSD',
			),
			array(
				'label'   => 'Inflation annuelle',
				'display' => $latest_inflation ? str_replace( '.', ',', number_format( (float) $latest_inflation['rate'], 1, '.', '' ) ) . '%' : 'n/a',
				'note'    => $latest_inflation ? $latest_inflation['year'] : 'ANSD',
				'badge'   => 'ANSD',
			),
			array(
				'label'   => 'ICAI Commerce de gros',
				'display' => $latest_icai ? str_replace( '.', ',', number_format( (float) $latest_icai['gros'], 1, '.', '' ) ) : 'n/a',
				'note'    => $latest_icai ? $latest_icai['date'] : 'DPEE',
				'badge'   => 'DPEE',
			),
			array(
				'label'   => 'ICAI Commerce de détail',
				'display' => $latest_icai ? str_replace( '.', ',', number_format( (float) $latest_icai['detail'], 1, '.', '' ) ) : 'n/a',
				'note'    => $latest_icai ? $latest_icai['date'] : 'DPEE',
				'badge'   => 'DPEE',
			),
		),
		'charts' => array(
			array(
				'id'          => 'ihpc-desagrege',
				'title'       => 'IHPC désagrégé — variations mensuelles (%)',
				'description' => '10 dernières périodes · cliquer pour masquer/afficher',
				'period'      => $variation_period,
				'source'      => 'Source: ANSD — IHPC COICOP. Var. = (Indice[t] / Indice[t-1] - 1) × 100',
				'type'        => 'line',
				'data'        => array(
					'labels'   => $variation_labels,
					'datasets' => $variation_sets,
				),
			),
			array(
				'id'          => 'inflation-threshold',
				'title'       => 'Inflation Annuelle vs Seuil UEMOA (3%)',
				'description' => '',
				'period'      => $inflation_period,
				'source'      => 'Source: ANSD (calculé depuis IHPC annuel). 2023 exclu (rebasement).',
				'type'        => 'bar',
				'data'        => array(
					'labels'   => array_map(
						function ( $item ) {
							return $item['year'];
						},
						$data['inflationData']
					),
					'datasets' => array(
						array(
							'type'            => 'line',
							'label'           => 'Seuil UEMOA (3%)',
							'data'            => array_fill( 0, count( $data['inflationData'] ), 3 ),
							'borderColor'     => '#dc2626',
							'backgroundColor' => '#dc2626',
							'borderDash'      => array( 5, 5 ),
							'pointRadius'     => 0,
							'borderWidth'     => 2,
							'fill'            => false,
						),
						array(
							'type'            => 'bar',
							'label'           => "Taux d'inflation",
							'data'            => array_map(
								function ( $item ) {
									return $item['rate'];
								},
								$data['inflationData']
							),
							'backgroundColor' => '#044badcc',
							'borderColor'     => '#044bad',
							'borderWidth'     => 1,
							'borderRadius'    => 4,
						),
					),
				),
			),
			array(
				'id'          => 'icai-series',
				'title'       => 'ICAI Commerce',
				'description' => '',
				'period'      => '10 dernières périodes',
				'source'      => 'Source: DPEE',
				'type'        => 'line',
				'data'        => array(
					'labels'   => array_map(
						function ( $item ) {
							return $item['date'];
						},
						$icai_recent
					),
					'datasets' => array(
						array(
							'label'           => 'Commerce de Gros',
							'data'            => array_map(
								function ( $item ) {
									return $item['gros'];
								},
								$icai_recent
							),
							'borderColor'     => '#044bad',
							'backgroundColor' => '#044bad20',
							'borderWidth'     => 2,
							'pointRadius'     => 3,
							'tension'         => 0.3,
							'fill'            => false,
						),
						array(
							'label'           => 'Commerce de Détail',
							'data'            => array_map(
								function ( $item ) {
									return $item['detail'];
								},
								$icai_recent
							),
							'borderColor'     => '#10b981',
							'backgroundColor' => '#10b98120',
							'borderWidth'     => 2,
							'pointRadius'     => 3,
							'tension'         => 0.3,
							'fill'            => false,
						),
						array(
							'label'           => 'Auto & Moto',
							'data'            => array_map(
								function ( $item ) {
									return $item['autoMoto'];
								},
								$icai_recent
							),
							'borderColor'     => '#b8943e',
							'backgroundColor' => '#b8943e20',
							'borderWidth'     => 2,
							'pointRadius'     => 3,
							'tension'         => 0.3,
							'fill'            => false,
						),
					),
				),
			),
			array(
				'id'          => 'icai-breakdown',
				'title'       => 'Répartition ICAI par Catégorie',
				'description' => '',
				'period'      => $data['year'],
				'source'      => 'Source: DPEE',
				'type'        => 'doughnut',
				'data'        => array(
					'labels'   => array_map(
						function ( $item ) {
							return $item['category'];
						},
						$data['icaiBreakdown']
					),
					'datasets' => array(
						array(
							'label'           => 'ICAI',
							'data'            => array_map(
								function ( $item ) {
									return $item['share'];
								},
								$data['icaiBreakdown']
							),
							'backgroundColor' => array( '#044bad85', '#032d6b85', '#b8943e85', '#3a7fd485', '#10b98185' ),
							'borderColor'     => array( '#044bad', '#032d6b', '#b8943e', '#3a7fd4', '#10b981' ),
							'borderWidth'     => 2,
						),
					),
				),
			),
		),
		'tables'  => array(
			array(
				'id'      => 'denrees-base',
				'title'   => 'Prix des denrées de base',
				'headers' => array( 'Date', 'Produit', 'Prix', 'Unite', 'Prec.', 'Var. %' ),
				'rows'    => array_map(
					function ( $row ) {
						return array(
							$row['date'],
							$row['produit'],
							null !== $row['prix'] ? crades_vm_format_number( $row['prix'], 0 ) : '',
							$row['unite'],
							null !== $row['prev'] ? crades_vm_format_number( $row['prev'], 0 ) : '',
							null !== $row['variationPct'] ? crades_vm_format_number( $row['variationPct'], 1 ) . '%' : '',
						);
					},
					$data['denreesDeBase']
				),
				'products' => array_values(
					array_map(
						function ( $row ) {
							return $row['produit'];
						},
						$data['denreesDeBase']
					)
				),
				'series'   => $data['denreesDeBaseSeries'],
			),
		),
	);
}

/**
 * Parses month labels used by industry sheets.
 *
 * @param string $value Header label.
 * @return string|null
 */
function crades_vm_parse_industry_month_header( $value ) {
	if ( ! preg_match( '/(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)\s+(\d{4})/', (string) $value, $matches ) ) {
		return null;
	}

	$month_map = array(
		'Jan' => '01',
		'Feb' => '02',
		'Mar' => '03',
		'Apr' => '04',
		'May' => '05',
		'Jun' => '06',
		'Jul' => '07',
		'Aug' => '08',
		'Sep' => '09',
		'Oct' => '10',
		'Nov' => '11',
		'Dec' => '12',
	);

	return isset( $month_map[ $matches[1] ] ) ? $matches[2] . '-' . $month_map[ $matches[1] ] : null;
}

/**
 * Parses IHPI sheet rows.
 *
 * @param array<int, array<int, string>> $matrix Header row + data rows.
 * @return array<string, mixed>
 */
function crades_vm_parse_industry_ihpi( $matrix ) {
	if ( count( $matrix ) < 2 ) {
		return array( 'ensemble' => array(), 'branches' => array() );
	}

	$headers = $matrix[0];
	$periods = array();

	for ( $index = 1; $index < count( $headers ); $index++ ) {
		$periods[] = crades_vm_parse_industry_month_header( $headers[ $index ] );
	}

	$ensemble = array();
	$branches = array();

	for ( $row_index = 1; $row_index < count( $matrix ); $row_index++ ) {
		$row         = $matrix[ $row_index ];
		$branch_name = isset( $row[0] ) ? trim( (string) $row[0] ) : '';

		if ( strlen( $branch_name ) < 3 ) {
			continue;
		}

		$is_ensemble = false !== strpos( strtoupper( $branch_name ), 'ENSEMBLE' );
		$branch_data = array();

		for ( $col_index = 1; $col_index < count( $row ) && $col_index - 1 < count( $periods ); $col_index++ ) {
			$period = $periods[ $col_index - 1 ];
			$value  = crades_vm_parse_number( $row[ $col_index ] );

			if ( ! $period || $value <= 0 ) {
				continue;
			}

			if ( $is_ensemble ) {
				$ensemble[] = array( 'period' => $period, 'value' => $value );
			}

			$branch_data[] = array( 'period' => $period, 'value' => $value );
		}

		if ( ! empty( $branch_data ) && false === strpos( strtoupper( $branch_name ), 'EGRENAGE' ) ) {
			$branches[] = array(
				'branch' => $branch_name,
				'data'   => $branch_data,
			);
		}
	}

	return array(
		'ensemble' => $ensemble,
		'branches' => $branches,
	);
}

/**
 * Parses IPPI rows.
 *
 * @param array<int, array<int, string>> $matrix Header row + data rows.
 * @return array<string, mixed>
 */
function crades_vm_parse_industry_ippi( $matrix ) {
	if ( count( $matrix ) < 2 ) {
		return array( 'ensemble' => array(), 'branches' => array() );
	}

	$headers    = $matrix[0];
	$periods    = array();
	$data_start = 2;

	for ( $index = $data_start; $index < count( $headers ); $index++ ) {
		$periods[] = crades_vm_parse_industry_month_header( $headers[ $index ] );
	}

	$targets = array(
		'PRODUITS DES INDUSTRIES EXTRACTIVES',
		'PRODUITS ALIMENTAIRES',
		'PRODUITS CHIMIQUES',
		'MATERIAUX MINERAUX',
		'PRODUITS METALLURGIQUES',
		'ELECTRICITE',
		'ENSEMBLE HORS EGRENAGE',
	);

	$ensemble = array();
	$branches = array();

	for ( $row_index = 1; $row_index < count( $matrix ); $row_index++ ) {
		$row         = $matrix[ $row_index ];
		$branch_name = trim( (string) ( $row[1] ?? $row[0] ?? '' ) );

		if ( strlen( $branch_name ) < 3 ) {
			continue;
		}

		$is_ensemble = false !== strpos( strtoupper( $branch_name ), 'ENSEMBLE HORS' );
		$is_target   = false;

		foreach ( $targets as $target ) {
			if ( false !== strpos( strtoupper( remove_accents( $branch_name ) ), strtoupper( remove_accents( $target ) ) ) ) {
				$is_target = true;
				break;
			}
		}

		$raw_values = array();

		for ( $col_index = $data_start; $col_index < count( $row ) && $col_index - $data_start < count( $periods ); $col_index++ ) {
			$period = $periods[ $col_index - $data_start ];
			$value  = crades_vm_parse_number( $row[ $col_index ] );

			if ( ! $period || $value <= 0 ) {
				continue;
			}

			$raw_values[] = array( 'period' => $period, 'value' => $value );
		}

		$branch_data = array();

		for ( $index = 0; $index < count( $raw_values ); $index++ ) {
			$point = $raw_values[ $index ];
			$yoy   = null;

			if ( $index >= 12 && ! empty( $raw_values[ $index - 12 ]['value'] ) ) {
				$yoy = ( ( $point['value'] - $raw_values[ $index - 12 ]['value'] ) / $raw_values[ $index - 12 ]['value'] ) * 100;
			}

			$branch_data[] = array(
				'period' => $point['period'],
				'value'  => $point['value'],
				'yoy'    => null !== $yoy ? round( $yoy, 1 ) : null,
			);

			if ( $is_ensemble ) {
				$ensemble[] = array( 'period' => $point['period'], 'value' => $point['value'] );
			}
		}

		if ( ! empty( $branch_data ) && ( $is_target || $is_ensemble ) ) {
			$branches[] = array(
				'branch' => trim( str_replace( 'dont…', '', str_replace( 'dont...', '', $branch_name ) ) ),
				'data'   => $branch_data,
			);
		}
	}

	return array(
		'ensemble' => $ensemble,
		'branches' => $branches,
	);
}

/**
 * Parses ICAI rows.
 *
 * @param array<int, array<int, string>> $matrix Header row + data rows.
 * @return array<string, mixed>
 */
function crades_vm_parse_industry_icai( $matrix ) {
	if ( count( $matrix ) < 2 ) {
		return array( 'ensemble' => array(), 'branches' => array() );
	}

	$headers    = $matrix[0];
	$periods    = array();
	$data_start = 2;

	for ( $index = $data_start; $index < count( $headers ); $index++ ) {
		$header = trim( (string) $headers[ $index ] );

		if ( preg_match( '/(\d{4})Q(\d)/', $header, $matches ) ) {
			$periods[] = $matches[1] . '-Q' . $matches[2];
		} else {
			$periods[] = $header;
		}
	}

	$ensemble = array();
	$branches = array();

	for ( $row_index = 1; $row_index < count( $matrix ); $row_index++ ) {
		$row         = $matrix[ $row_index ];
		$branch_name = trim( (string) ( $row[1] ?? $row[0] ?? '' ) );

		if ( strlen( $branch_name ) < 3 ) {
			continue;
		}

		$is_ensemble      = false !== strpos( strtoupper( $branch_name ), 'ENSEMBLE HORS' );
		$is_main_aggregate = false !== strpos( $branch_name, 'dont…' ) || false !== strpos( $branch_name, 'dont...' );
		$is_environmental  = false !== strpos( strtoupper( $branch_name ), 'INDUSTRIES ENVIRONNEMENTALES' );
		$branch_data      = array();

		for ( $col_index = $data_start; $col_index < count( $row ) && $col_index - $data_start < count( $periods ); $col_index++ ) {
			$period = $periods[ $col_index - $data_start ];
			$value  = crades_vm_parse_number( $row[ $col_index ] );

			if ( '' === $period || $value <= 0 ) {
				continue;
			}

			if ( $is_ensemble ) {
				$ensemble[] = array( 'period' => $period, 'value' => $value );
			}

			$branch_data[] = array( 'period' => $period, 'value' => $value );
		}

		if ( ! empty( $branch_data ) && ( $is_main_aggregate || $is_ensemble || $is_environmental ) ) {
			$branches[] = array(
				'branch' => trim( str_replace( 'dont…', '', str_replace( 'dont...', '', $branch_name ) ) ),
				'data'   => $branch_data,
			);
		}
	}

	return array(
		'ensemble' => $ensemble,
		'branches' => $branches,
	);
}

/**
 * Parses PIB branches.
 *
 * @param array<int, array<int, string>> $matrix Header row + data rows.
 * @return array<int, array<string, mixed>>
 */
function crades_vm_parse_industry_pib_branches( $matrix ) {
	if ( count( $matrix ) < 3 ) {
		return array();
	}

	$headers      = $matrix[0];
	$year_columns = array();

	for ( $index = 2; $index < count( $headers ); $index++ ) {
		$year = preg_replace( '/\s+/', '', trim( (string) $headers[ $index ] ) );

		if ( preg_match( '/^\d{4}$/', $year ) ) {
			$year_columns[] = array( 'index' => $index, 'year' => $year );
		}
	}

	$branches = array();

	for ( $row_index = 1; $row_index < count( $matrix ); $row_index++ ) {
		$row         = $matrix[ $row_index ];
		$code        = trim( (string) ( $row[0] ?? '' ) );
		$branch_name = trim( (string) ( $row[1] ?? '' ) );

		if ( ! preg_match( '/^[A-Z]\d{2}$/', $code ) || '' === $branch_name || false !== strpos( strtoupper( $branch_name ), 'NOTE' ) || false !== strpos( strtoupper( $branch_name ), 'VALEUR AJOUTEE' ) ) {
			continue;
		}

		$values = array();
		$latest = 0;

		foreach ( $year_columns as $column ) {
			$value = isset( $row[ $column['index'] ] ) ? crades_vm_parse_number( $row[ $column['index'] ] ) : 0;

			if ( $value > 0 ) {
				$values[ $column['year'] ] = $value;
				$latest                    = $value;
			}
		}

		if ( empty( $values ) ) {
			continue;
		}

		$years  = array_keys( $values );
		sort( $years );
		$growth = 0;

		if ( count( $years ) >= 2 ) {
			$last_year = $years[ count( $years ) - 1 ];
			$prev_year = $years[ count( $years ) - 2 ];
			$growth    = 0 !== $values[ $prev_year ] ? ( ( $values[ $last_year ] - $values[ $prev_year ] ) / $values[ $prev_year ] ) * 100 : 0;
		}

		$branches[] = array(
			'name'   => $branch_name,
			'code'   => $code,
			'values' => $values,
			'latest' => $latest,
			'growth' => round( $growth, 1 ),
		);
	}

	return $branches;
}

/**
 * Parses CIP rows.
 *
 * @param array<int, array<int, string>> $matrix Header row + data rows.
 * @return array<string, mixed>
 */
function crades_vm_parse_industry_cip( $matrix ) {
	$all = array();

	for ( $index = 1; $index < count( $matrix ); $index++ ) {
		$row   = $matrix[ $index ];
		$year  = isset( $row[0] ) ? (int) $row[0] : 0;
		$score = isset( $row[1] ) ? crades_vm_parse_number( $row[1] ) : 0;
		$rank  = isset( $row[2] ) ? (int) crades_vm_parse_number( $row[2] ) : 0;
		$total = isset( $row[3] ) ? (int) crades_vm_parse_number( $row[3] ) : 152;

		if ( $year >= 2010 && $score > 0 && $rank > 0 ) {
			$all[] = array(
				'year'           => $year,
				'score'          => $score,
				'rank'           => $rank,
				'totalCountries' => $total,
			);
		}
	}

	usort(
		$all,
		function ( $left, $right ) {
			return $left['year'] <=> $right['year'];
		}
	);

	return array(
		'latest' => ! empty( $all ) ? $all[ count( $all ) - 1 ] : null,
		'all'    => $all,
	);
}

/**
 * Parses PCI rows.
 *
 * @param array<int, array<int, string>> $matrix Header row + data rows.
 * @return array<int, array<string, mixed>>
 */
function crades_vm_parse_industry_pci( $matrix ) {
	$data = array();

	for ( $index = 1; $index < count( $matrix ); $index++ ) {
		$row  = $matrix[ $index ];
		$year = isset( $row[0] ) ? (int) $row[0] : 0;

		if ( $year < 2000 ) {
			continue;
		}

		$data[] = array(
			'year'             => $year,
			'global'           => isset( $row[1] ) ? crades_vm_parse_number( $row[1] ) : 0,
			'humanCapital'     => isset( $row[2] ) ? crades_vm_parse_number( $row[2] ) : 0,
			'naturalCapital'   => isset( $row[3] ) ? crades_vm_parse_number( $row[3] ) : 0,
			'energy'           => isset( $row[4] ) ? crades_vm_parse_number( $row[4] ) : 0,
			'transport'        => isset( $row[5] ) ? crades_vm_parse_number( $row[5] ) : 0,
			'ict'              => isset( $row[6] ) ? crades_vm_parse_number( $row[6] ) : 0,
			'institutions'     => isset( $row[7] ) ? crades_vm_parse_number( $row[7] ) : 0,
			'privateSector'    => isset( $row[8] ) ? crades_vm_parse_number( $row[8] ) : 0,
			'structuralChange' => isset( $row[9] ) ? crades_vm_parse_number( $row[9] ) : 0,
		);
	}

	return $data;
}

/**
 * Parses TUCP rows.
 *
 * @param array<int, array<int, string>> $rows Raw display rows.
 * @return array<int, array<string, mixed>>
 */
function crades_vm_parse_industry_tucp( $rows ) {
	$data = array();

	foreach ( $rows as $row ) {
		$period = isset( $row[0] ) ? trim( (string) $row[0] ) : '';

		if ( ! preg_match( '/(\d{4})\s*T(\d)/', $period, $matches ) ) {
			continue;
		}

		$value = isset( $row[1] ) ? crades_vm_parse_number( $row[1] ) : 0;

		if ( $value <= 0 ) {
			continue;
		}

		$data[] = array(
			'period' => $matches[1] . '-T' . $matches[2],
			'value'  => $value,
		);
	}

	return $data;
}

/**
 * Parses production DPEE rows.
 *
 * @param array<int, string>             $headers Display headers.
 * @param array<int, array<int, string>> $rows    Raw display rows.
 * @return array<string, mixed>
 */
function crades_vm_parse_industry_dpee( $headers, $rows ) {
	$products = array_slice( $headers, 1 );
	$series   = array();

	foreach ( $rows as $row ) {
		$date = isset( $row[0] ) ? trim( (string) $row[0] ) : '';

		if ( preg_match( '/Date\((\d{4}),(\d{1,2}),/', $date, $matches ) ) {
			$date = $matches[1] . '-' . str_pad( (string) ( (int) $matches[2] + 1 ), 2, '0', STR_PAD_LEFT );
		}

		if ( strlen( $date ) < 6 ) {
			continue;
		}

		$values = array();

		for ( $index = 1; $index < count( $row ) && $index - 1 < count( $products ); $index++ ) {
			$product = trim( (string) $products[ $index - 1 ] );
			$value   = crades_vm_parse_industry_dpee_value( $row[ $index ] );
			$values[ $product ] = $value > 0 ? $value : null;
		}

		$series[] = array(
			'date'   => $date,
			'values' => $values,
		);
	}

	return array(
		'products' => $products,
		'series'   => $series,
	);
}

/**
 * Parses DPEE production values using the sheet's decimal conventions.
 *
 * The DPEE sheet uses dotted decimals for many production columns
 * like "176.729", "930.911", or "10.31". The generic parser treats
 * some of those as grouped thousands, which creates mixed scales in
 * the same column. For this sheet we keep dots as decimals.
 *
 * @param mixed $value Raw cell value.
 * @return float
 */
function crades_vm_parse_industry_dpee_value( $value ) {
	if ( is_int( $value ) || is_float( $value ) ) {
		return (float) $value;
	}

	$string = trim( (string) $value );

	if ( '' === $string ) {
		return 0.0;
	}

	$string = str_replace( array( ' ', "\xc2\xa0", "\xe2\x80\xaf", '%' ), '', $string );

	if ( false !== strpos( $string, ',' ) && false !== strpos( $string, '.' ) ) {
		if ( strrpos( $string, ',' ) > strrpos( $string, '.' ) ) {
			$string = str_replace( '.', '', $string );
			$string = str_replace( ',', '.', $string );
		} else {
			$string = str_replace( ',', '', $string );
		}
	} elseif ( false !== strpos( $string, ',' ) ) {
		$string = str_replace( ',', '.', $string );
	}

	$string = preg_replace( '/[^0-9.\-]/', '', $string );

	return is_numeric( $string ) ? (float) $string : 0.0;
}

/**
 * Parses industry summary indicators.
 *
 * @param array<int, array<int, string>> $rows Raw display rows.
 * @return array<int, array<string, mixed>>
 */
function crades_vm_parse_industry_indicators( $rows ) {
	$indicators = array();
	$in_section = false;

	foreach ( $rows as $row ) {
		$label = isset( $row[0] ) ? trim( (string) $row[0] ) : '';

		if ( false !== strpos( strtoupper( $label ), 'DERNIERES VALEURS' ) || false !== strpos( strtoupper( $label ), 'DERNIÈRES VALEURS' ) ) {
			$in_section = true;
			continue;
		}

		if ( 'Indicateur' === $label || ! $in_section || strlen( $label ) <= 3 ) {
			continue;
		}

		$value = isset( $row[2] ) ? trim( (string) $row[2] ) : '';

		if ( '' === $value || '—' === $value || '–' === $value ) {
			continue;
		}

		$source = false !== strpos( strtoupper( $label ), 'CIP' ) ? 'UNIDO' : 'ANSD';

		$indicators[] = array(
			'name'        => $label,
			'lastPeriod'  => isset( $row[1] ) ? trim( (string) $row[1] ) : '',
			'value'       => $value,
			'description' => $label . ' - ' . $value,
			'source'      => $source,
		);
	}

	return $indicators;
}

/**
 * Returns the default industry data object.
 *
 * @return array<string, mixed>
 */
function crades_vm_get_default_industry() {
	return array(
		'year'          => 2024,
		'indicators'    => array(
			array( 'name' => 'IHPI Ensemble', 'lastPeriod' => 'Dec. 2024', 'value' => '+24,9%', 'description' => 'Production Industrielle', 'source' => 'ANSD' ),
			array( 'name' => 'IPPI Ensemble', 'lastPeriod' => 'Jan. 2026', 'value' => '-1,7%', 'description' => 'Prix Production', 'source' => 'ANSD' ),
			array( 'name' => 'ICAI Ensemble', 'lastPeriod' => 'T3 2025', 'value' => '+16,5%', 'description' => 'Chiffre d affaires', 'source' => 'ANSD' ),
			array( 'name' => 'CIP Score', 'lastPeriod' => '2023', 'value' => '0,061 / Rang 50', 'description' => 'Competitivite UNIDO', 'source' => 'UNIDO' ),
		),
		'ihpiData'      => array(),
		'ihpiBranches'  => array(),
		'ippiData'      => array(),
		'ippiBranches'  => array(),
		'icaiData'      => array(),
		'icaiBranches'  => array(),
		'pibBranches'   => array(),
		'cipData'       => array(),
		'cipScore'      => null,
		'pciData'       => array(),
		'productionDPEE'=> array( 'products' => array(), 'series' => array() ),
		'tucpData'      => array(),
	);
}

/**
 * Builds the industry data object.
 *
 * @param array<string, mixed> $tables Dashboard tables.
 * @return array<string, mixed>
 */
function crades_vm_build_industry_data( $tables ) {
	$defaults   = crades_vm_get_default_industry();
	$ihpi       = crades_vm_parse_industry_ihpi( crades_vm_get_sheet_matrix( $tables, 'ihpi' ) );
	$ippi       = crades_vm_parse_industry_ippi( crades_vm_get_sheet_matrix( $tables, 'ippi' ) );
	$icai       = crades_vm_parse_industry_icai( crades_vm_get_sheet_matrix( $tables, 'icai' ) );
	$pib        = crades_vm_parse_industry_pib_branches( crades_vm_get_sheet_matrix( $tables, 'pib_branches' ) );
	$cip        = crades_vm_parse_industry_cip( crades_vm_get_sheet_matrix( $tables, 'cip_competitivite' ) );
	$pci        = crades_vm_parse_industry_pci( crades_vm_get_sheet_matrix( $tables, 'pci_unctad' ) );
	$tucp       = crades_vm_parse_industry_tucp( crades_vm_get_display_rows( $tables, 'taux_utilisation' ) );
	$production = crades_vm_parse_industry_dpee( crades_vm_get_display_headers( $tables, 'production_dpee' ), crades_vm_get_display_rows( $tables, 'production_dpee' ) );
	$indicators = crades_vm_parse_industry_indicators( crades_vm_get_display_rows( $tables, 'overview' ) );

	return array(
		'year'           => ! empty( $cip['latest']['year'] ) ? $cip['latest']['year'] : $defaults['year'],
		'indicators'     => ! empty( $indicators ) ? $indicators : $defaults['indicators'],
		'ihpiData'       => ! empty( $ihpi['ensemble'] ) ? $ihpi['ensemble'] : $defaults['ihpiData'],
		'ihpiBranches'   => ! empty( $ihpi['branches'] ) ? $ihpi['branches'] : $defaults['ihpiBranches'],
		'ippiData'       => ! empty( $ippi['ensemble'] ) ? $ippi['ensemble'] : $defaults['ippiData'],
		'ippiBranches'   => ! empty( $ippi['branches'] ) ? $ippi['branches'] : $defaults['ippiBranches'],
		'icaiData'       => ! empty( $icai['ensemble'] ) ? $icai['ensemble'] : $defaults['icaiData'],
		'icaiBranches'   => ! empty( $icai['branches'] ) ? $icai['branches'] : $defaults['icaiBranches'],
		'pibBranches'    => ! empty( $pib ) ? $pib : $defaults['pibBranches'],
		'cipData'        => ! empty( $cip['all'] ) ? $cip['all'] : $defaults['cipData'],
		'cipScore'       => ! empty( $cip['latest'] ) ? $cip['latest'] : $defaults['cipScore'],
		'pciData'        => ! empty( $pci ) ? $pci : $defaults['pciData'],
		'productionDPEE' => ! empty( $production['series'] ) ? $production : $defaults['productionDPEE'],
		'tucpData'       => ! empty( $tucp ) ? $tucp : $defaults['tucpData'],
	);
}

/**
 * Builds the industry view model.
 *
 * @param array<string, mixed> $tables Dashboard tables.
 * @return array<string, mixed>
 */
function crades_vm_build_industry_view_model( $tables ) {
	$data         = crades_vm_build_industry_data( $tables );
	$ihpi_recent  = array_slice( $data['ihpiData'], -12 );
	$icai_recent  = array_slice( $data['icaiData'], -12 );
	$ippi_recent  = array_slice( $data['ippiData'], -24 );
	$tucp_data    = $data['tucpData'];
	$cip_data     = $data['cipData'];
	$cip_score    = $data['cipScore'];
	$pci_sorted   = $data['pciData'];
	$dpee_series  = ! empty( $data['productionDPEE']['series'] ) ? array_slice( $data['productionDPEE']['series'], -12 ) : array();
	$dpee_products = ! empty( $data['productionDPEE']['products'] ) ? $data['productionDPEE']['products'] : array();
	$ihpi_colors  = array( '#044bad', '#059669', '#b8943e', '#dc2626', '#7c3aed', '#0891b2', '#ea580c', '#e11d48', '#16a085', '#8e44ad', '#f39c12', '#2980b9', '#c0392b' );
	$icai_colors  = array( '#044bad', '#059669', '#b8943e', '#dc2626', '#7c3aed', '#0891b2', '#ea580c', '#2563eb' );
	$latest_tucp  = ! empty( $tucp_data ) ? $tucp_data[ count( $tucp_data ) - 1 ] : null;

	usort(
		$pci_sorted,
		function ( $left, $right ) {
			return $left['year'] <=> $right['year'];
		}
	);

	$pci_2000   = null;
	$pci_latest = ! empty( $pci_sorted ) ? $pci_sorted[ count( $pci_sorted ) - 1 ] : null;

	foreach ( $pci_sorted as $entry ) {
		if ( 2000 === (int) $entry['year'] ) {
			$pci_2000 = $entry;
			break;
		}
	}

	$ihpi_branch_short_names = array(
		'INDUSTRIES EXTRACTIVES' => 'Extractives',
		'INDUSTRIES AGRO-ALIMENTAIRES' => 'Agro-alimentaires',
		'INDUSTRIES TEXTILES' => 'Textiles',
		'INDUSTRIE DE CUIR' => 'Cuir & Chaussures',
		'INDUSTRIES DU PAPIER' => 'Papier & Carton',
		'INDUSTRIES DE TRANSFORMATION DE PRODUITS PETROLIERS' => 'Raffinage Pétrole',
		'INDUSTRIES CHIMIQUES' => 'Chimie/Plastique',
		'INDUSTRIES DE MATERIAUX' => 'Mat. Minéraux',
		'INDUSTRIES METALLIQUES' => 'Métallurgie',
		'INDUSTRIES ELECTRONIQUES' => 'Électronique/Machines',
		'AUTRES INDUSTRIES MANUFACTURIERES' => 'Autres Manuf.',
		'INDUSTRIES DE PRODUCTION ET DE DISTRIBUTION' => 'Électricité/Gaz/Eau',
		'INDUSTRIES ENVIRONNEMENTALES' => 'Environnement',
	);
	$icai_branch_short_names = array(
		'PRODUITS DES INDUSTRIES EXTRACTIVES' => 'Extractives',
		'PRODUITS MANUFACTURIERS' => 'Manufacturiers',
		'ELECTRICITE, GAZ ET EAU' => 'Électricité/Gaz/Eau',
		'PRODUITS DES INDUSTRIES ENVIRONNEMENTALES' => 'Environnement',
		'PRODUITS CHIMIQUES' => 'Chimie/Plastique',
		'PRODUITS METALLURGIQUES' => 'Métallurgie',
		'PRODUITS ELECTRONIQUES' => 'Électronique/Machines',
		'AUTRES INDUSTRIES MANUFACTURIERES' => 'Autres Manuf.',
	);

	$ihpi_datasets = array(
		array(
			'label'           => 'ENSEMBLE',
			'data'            => array_map( fn( $point ) => $point['value'], $ihpi_recent ),
			'borderColor'     => '#1e293b',
			'backgroundColor' => '#1e293b15',
			'fill'            => true,
			'tension'         => 0.3,
			'pointRadius'     => 3,
			'borderWidth'     => 2.5,
		),
	);

	$ihpi_branches = array_values(
		array_filter(
			$data['ihpiBranches'],
			fn( $branch ) => false === strpos( strtoupper( $branch['branch'] ), 'ENSEMBLE' ) && false === strpos( strtoupper( $branch['branch'] ), 'EGRENAGE' )
		)
	);

	foreach ( $ihpi_branches as $index => $branch ) {
		$label = $branch['branch'];

		foreach ( $ihpi_branch_short_names as $search => $replace ) {
			if ( false !== strpos( strtoupper( remove_accents( $label ) ), strtoupper( remove_accents( $search ) ) ) ) {
				$label = $replace;
				break;
			}
		}

		$ihpi_datasets[] = array(
			'label'           => $label,
			'data'            => array_map(
				function ( $point ) use ( $branch ) {
					foreach ( $branch['data'] as $branch_point ) {
						if ( $branch_point['period'] === $point['period'] ) {
							return $branch_point['value'];
						}
					}

					return null;
				},
				$ihpi_recent
			),
			'borderColor'     => $ihpi_colors[ $index % count( $ihpi_colors ) ],
			'backgroundColor' => $ihpi_colors[ $index % count( $ihpi_colors ) ] . '10',
			'fill'            => false,
			'tension'         => 0.3,
			'pointRadius'     => 2,
			'borderWidth'     => 1.5,
		);
	}

	$icai_datasets = array(
		array(
			'label'           => 'ENSEMBLE',
			'data'            => array_map( fn( $point ) => $point['value'], $icai_recent ),
			'borderColor'     => '#1e293b',
			'backgroundColor' => '#1e293b15',
			'fill'            => true,
			'tension'         => 0.3,
			'pointRadius'     => 3,
			'borderWidth'     => 2.5,
		),
	);

	$icai_branches = array_values(
		array_filter(
			$data['icaiBranches'],
			fn( $branch ) => false === strpos( strtoupper( $branch['branch'] ), 'ENSEMBLE' )
		)
	);

	foreach ( $icai_branches as $index => $branch ) {
		$label = $branch['branch'];

		foreach ( $icai_branch_short_names as $search => $replace ) {
			if ( false !== strpos( strtoupper( remove_accents( $label ) ), strtoupper( remove_accents( $search ) ) ) ) {
				$label = $replace;
				break;
			}
		}

		$icai_datasets[] = array(
			'label'           => $label,
			'data'            => array_map(
				function ( $point ) use ( $branch ) {
					foreach ( $branch['data'] as $branch_point ) {
						if ( $branch_point['period'] === $point['period'] ) {
							return $branch_point['value'];
						}
					}

					return null;
				},
				$icai_recent
			),
			'borderColor'     => $icai_colors[ $index % count( $icai_colors ) ],
			'backgroundColor' => $icai_colors[ $index % count( $icai_colors ) ] . '10',
			'fill'            => false,
			'tension'         => 0.3,
			'pointRadius'     => 2,
			'borderWidth'     => 1.5,
		);
	}

	$pci_dimensions = array(
		array( 'key' => 'humanCapital', 'label' => 'Capital Humain' ),
		array( 'key' => 'naturalCapital', 'label' => 'Capital Naturel' ),
		array( 'key' => 'energy', 'label' => 'Energie' ),
		array( 'key' => 'transport', 'label' => 'Transport' ),
		array( 'key' => 'ict', 'label' => 'TIC' ),
		array( 'key' => 'institutions', 'label' => 'Institutions' ),
		array( 'key' => 'privateSector', 'label' => 'Secteur Prive' ),
		array( 'key' => 'structuralChange', 'label' => 'Changement Structurel' ),
	);

	return array(
		'kpis'   => array(
			array( 'label' => 'IHPI – Production', 'display' => '+24,9%', 'note' => ! empty( $data['indicators'][0]['lastPeriod'] ) ? $data['indicators'][0]['lastPeriod'] : 'Cumul 2025', 'badge' => 'ANSD' ),
			array( 'label' => "ICAI – Chiffre d'Affaires", 'display' => '+16,5%', 'note' => ! empty( $data['indicators'][2]['lastPeriod'] ) ? 'Var. annuelle ' . $data['indicators'][2]['lastPeriod'] : 'Var. annuelle T3 2025', 'badge' => 'ANSD' ),
			array( 'label' => 'IPPI – Prix Production', 'display' => '-1,7%', 'note' => ! empty( $data['indicators'][1]['lastPeriod'] ) ? 'Var. annuelle ' . $data['indicators'][1]['lastPeriod'] : 'Var. annuelle Jan. 2026', 'badge' => 'ANSD' ),
			array( 'label' => 'CIP – Compétitivité', 'display' => $cip_score ? 'Rang ' . $cip_score['rank'] : 'Rang 50', 'note' => $cip_score ? 'Score : ' . str_replace( '.', ',', number_format( (float) $cip_score['score'], 3, '.', '' ) ) . ' - ' . $cip_score['year'] : 'Score : 0,061 - 2023', 'badge' => 'UNIDO' ),
			array( 'label' => 'TUCP – Capacités', 'display' => $latest_tucp ? str_replace( '.', ',', number_format( (float) $latest_tucp['value'], 1, '.', '' ) ) . ' %' : '—', 'note' => $latest_tucp ? $latest_tucp['period'] : 'Derniere periode BCEAO', 'badge' => 'BCEAO' ),
		),
		'charts' => array(
			array( 'id' => 'industry-ihpi', 'title' => 'Indice de Production Industrielle (IHPI)', 'description' => count( $ihpi_branches ) . ' sous-secteurs · cliquer pour isoler une ligne', 'type' => 'line', 'data' => array( 'labels' => array_map( fn( $p ) => $p['period'], $ihpi_recent ), 'datasets' => $ihpi_datasets ) ),
			array( 'id' => 'industry-icai', 'title' => 'Indice du Chiffre d Affaires Industriel (ICAI)', 'description' => count( $icai_branches ) . ' sous-secteurs · cliquer pour isoler une ligne', 'type' => 'line', 'data' => array( 'labels' => array_map( fn( $p ) => $p['period'], $icai_recent ), 'datasets' => $icai_datasets ) ),
			array( 'id' => 'industry-ippi', 'title' => 'Indice des Prix à la Production (IPPI)', 'description' => 'Ensemble sur les 24 derniers mois.', 'type' => 'line', 'data' => array( 'labels' => array_map( fn( $p ) => $p['period'], $ippi_recent ), 'datasets' => array( array( 'label' => 'IPPI Ensemble', 'data' => array_map( fn( $p ) => $p['value'], $ippi_recent ), 'borderColor' => '#e11d48', 'backgroundColor' => '#e11d4815', 'fill' => true, 'tension' => 0.3, 'pointRadius' => 1.5, 'borderWidth' => 2 ) ) ) ),
			array( 'id' => 'industry-capacity', 'title' => 'Taux d Utilisation des Capacités Productives', 'description' => 'Série TUCP BCEAO.', 'type' => 'bar', 'data' => array( 'labels' => array_map( fn( $p ) => $p['period'], $tucp_data ), 'datasets' => array( array( 'label' => 'Taux d utilisation (%)', 'data' => array_map( fn( $p ) => $p['value'], $tucp_data ), 'backgroundColor' => array_map( fn( $p ) => $p['value'] >= 80 ? '#05966990' : ( $p['value'] >= 60 ? '#b8943e90' : '#dc262690' ), $tucp_data ), 'borderColor' => array_map( fn( $p ) => $p['value'] >= 80 ? '#059669' : ( $p['value'] >= 60 ? '#b8943e' : '#dc2626' ), $tucp_data ), 'borderWidth' => 1, 'borderRadius' => 3 ) ) ) ),
			array( 'id' => 'industry-pci', 'title' => 'Capacités Productives (PCI – UNCTAD)', 'description' => 'Radar 2000 vs dernière année disponible.', 'type' => 'radar', 'data' => array( 'labels' => array_map( fn( $d ) => $d['label'], $pci_dimensions ), 'datasets' => array( array( 'label' => $pci_2000 ? (string) $pci_2000['year'] : '2000', 'data' => array_map( fn( $d ) => $pci_2000 ? $pci_2000[ $d['key'] ] : 0, $pci_dimensions ), 'borderColor' => '#e11d48', 'backgroundColor' => '#e11d4818', 'pointBackgroundColor' => '#e11d48', 'pointBorderColor' => '#e11d48', 'borderWidth' => 2, 'pointRadius' => 3 ), array( 'label' => $pci_latest ? (string) $pci_latest['year'] : '2024', 'data' => array_map( fn( $d ) => $pci_latest ? $pci_latest[ $d['key'] ] : 0, $pci_dimensions ), 'borderColor' => '#059669', 'backgroundColor' => '#05966920', 'pointBackgroundColor' => '#059669', 'pointBorderColor' => '#059669', 'borderWidth' => 2.5, 'pointRadius' => 3 ) ) ) ),
			array( 'id' => 'industry-pib', 'title' => 'Indice de Compétitivité Industrielle (CIP)', 'description' => 'Double axe score / rang mondial.', 'type' => 'bar', 'data' => array( 'labels' => array_map( fn( $d ) => $d['year'], $cip_data ), 'datasets' => array( array( 'type' => 'bar', 'label' => 'Score CIP', 'data' => array_map( fn( $d ) => $d['score'], $cip_data ), 'backgroundColor' => '#044badcc', 'borderColor' => '#044bad', 'borderWidth' => 1, 'borderRadius' => 4, 'yAxisID' => 'y', 'order' => 2 ), array( 'type' => 'line', 'label' => 'Rang mondial', 'data' => array_map( fn( $d ) => $d['rank'], $cip_data ), 'borderColor' => '#b8943e', 'backgroundColor' => '#b8943e', 'tension' => 0.3, 'pointRadius' => 5, 'borderWidth' => 2.5, 'yAxisID' => 'y1', 'order' => 1 ) ) ) ),
		),
		'tables'  => array(
			array(
				'id'      => 'production-dpee',
				'title'   => 'Production industrielle',
				'headers' => array_merge( array( 'Date' ), array_map( fn( $p ) => strlen( $p ) > 18 ? substr( $p, 0, 16 ) . '…' : $p, $dpee_products ) ),
				'rows'    => array_map(
					function ( $row ) use ( $dpee_products ) {
						$cells = array( $row['date'] );
						foreach ( $dpee_products as $product ) {
							$cells[] = null !== $row['values'][ $product ] ? crades_vm_format_number( $row['values'][ $product ], 1 ) : '';
						}
						return $cells;
					},
					$dpee_series
				),
				'products' => $dpee_products,
				'series'   => $dpee_series,
			),
			array(
				'id'      => 'pci-table',
				'title'   => 'Branches industrielles',
				'headers' => array( 'Branche', 'Code', 'Derniere valeur', 'Croissance %' ),
				'rows'    => array_map(
					function ( $row ) {
						return array(
							$row['name'],
							$row['code'],
							crades_vm_format_number( $row['latest'], 1 ),
							crades_vm_format_number( $row['growth'], 1 ) . '%',
						);
					},
					array_slice( $data['pibBranches'], 0, 12 )
				),
			),
		),
	);
}

/**
 * Builds the commerce exterieur view model.
 *
 * @param array<string, mixed> $tables Dashboard tables.
 * @return array<string, mixed>
 */
function crades_vm_build_commerce_exterieur_view_model( $tables ) {
	$data          = crades_vm_build_commerce_exterieur_data( $tables );
	$overview      = $data['overview'];
	$coverage_rate = $overview['totalImports'] > 0 ? ( $overview['totalExports'] / $overview['totalImports'] ) * 100 : 0;
	$ipce_export   = 108.5;
	$ipce_import   = 112.3;
	$terms         = ( $ipce_export / $ipce_import ) * 100;
	$sectors       = $data['sectors'];

	return array(
		'kpis'   => array(
			array( 'label' => 'Exportations', 'display' => str_replace( '.', ',', number_format( $overview['totalExports'], 1, '.', '' ) ) . ' Mds', 'note' => '+' . str_replace( '.', ',', number_format( $overview['exportGrowth'] * 100, 1, '.', '' ) ) . '% vs 2024', 'badge' => 'ANSD' ),
			array( 'label' => 'Importations', 'display' => str_replace( '.', ',', number_format( $overview['totalImports'], 1, '.', '' ) ) . ' Mds', 'note' => '+' . str_replace( '.', ',', number_format( $overview['importGrowth'] * 100, 1, '.', '' ) ) . '% vs 2024', 'badge' => 'ANSD' ),
			array( 'label' => 'Balance commerciale', 'display' => str_replace( '.', ',', number_format( $overview['tradeBalance'], 1, '.', '' ) ) . ' Mds', 'note' => $overview['tradeBalance'] >= 0 ? 'Excédent' : 'Déficit', 'badge' => 'ANSD' ),
			array( 'label' => 'Volume commercial', 'display' => str_replace( '.', ',', number_format( $overview['tradeVolume'], 1, '.', '' ) ) . ' Mds', 'note' => 'Export + import', 'badge' => 'ANSD' ),
			array( 'label' => 'IPCE Export', 'display' => str_replace( '.', ',', number_format( $ipce_export, 1, '.', '' ) ), 'note' => 'Base 100 = 2020', 'badge' => 'Indice' ),
			array( 'label' => 'IPCE Import', 'display' => str_replace( '.', ',', number_format( $ipce_import, 1, '.', '' ) ), 'note' => 'Base 100 = 2020', 'badge' => 'Indice' ),
			array( 'label' => 'Termes de l échange', 'display' => str_replace( '.', ',', number_format( $terms, 1, '.', '' ) ), 'note' => $terms >= 100 ? 'Favorable' : 'Défavorable', 'badge' => 'Calculé' ),
			array( 'label' => 'Taux de couverture', 'display' => str_replace( '.', ',', number_format( $coverage_rate, 1, '.', '' ) ) . '%', 'note' => 'Export / import', 'badge' => 'Calculé' ),
		),
		'charts' => array(
			array( 'id' => 'trade-evolution', 'title' => 'Évolution du commerce (Mds FCFA)', 'description' => 'Exportations et importations 2016–2025.', 'type' => 'line', 'data' => array( 'labels' => array_map( fn( $p ) => $p['year'], $data['timeSeries'] ), 'datasets' => array( array( 'label' => 'Exportations', 'data' => array_map( fn( $p ) => $p['exports'], $data['timeSeries'] ), 'borderColor' => '#059669', 'backgroundColor' => '#05966915', 'fill' => true, 'tension' => 0.3, 'pointRadius' => 4, 'borderWidth' => 2 ), array( 'label' => 'Importations', 'data' => array_map( fn( $p ) => $p['imports'], $data['timeSeries'] ), 'borderColor' => '#dc2626', 'backgroundColor' => '#dc262610', 'fill' => true, 'tension' => 0.3, 'pointRadius' => 4, 'borderWidth' => 2 ) ) ) ),
			array( 'id' => 'trade-balance', 'title' => 'Balance commerciale (Mds FCFA)', 'description' => 'Solde annuel des échanges.', 'type' => 'bar', 'data' => array( 'labels' => array_map( fn( $p ) => $p['year'], $data['timeSeries'] ), 'datasets' => array( array( 'label' => 'Balance', 'data' => array_map( fn( $p ) => $p['balance'], $data['timeSeries'] ), 'backgroundColor' => array_map( fn( $p ) => $p['balance'] >= 0 ? '#059669cc' : '#dc2626cc', $data['timeSeries'] ), 'borderColor' => array_map( fn( $p ) => $p['balance'] >= 0 ? '#059669' : '#dc2626', $data['timeSeries'] ), 'borderWidth' => 1, 'borderRadius' => 4 ) ) ) ),
			array( 'id' => 'export-partners', 'title' => 'Top destinations d exportation', 'description' => 'Principales destinations 2025.', 'type' => 'bar', 'options' => array( 'indexAxis' => 'y' ), 'data' => array( 'labels' => array_map( fn( $p ) => $p['country'], array_slice( $data['topExportPartners'], 0, 10 ) ), 'datasets' => array( array( 'label' => 'Exportations', 'data' => array_map( fn( $p ) => $p['value'], array_slice( $data['topExportPartners'], 0, 10 ) ), 'backgroundColor' => '#044badcc', 'borderColor' => '#044bad', 'borderWidth' => 1 ) ) ) ),
			array( 'id' => 'import-partners', 'title' => 'Top fournisseurs (importations)', 'description' => 'Principaux fournisseurs 2025.', 'type' => 'bar', 'options' => array( 'indexAxis' => 'y' ), 'data' => array( 'labels' => array_map( fn( $p ) => $p['country'], array_slice( $data['topImportPartners'], 0, 10 ) ), 'datasets' => array( array( 'label' => 'Importations', 'data' => array_map( fn( $p ) => $p['value'], array_slice( $data['topImportPartners'], 0, 10 ) ), 'backgroundColor' => '#dc2626cc', 'borderColor' => '#dc2626', 'borderWidth' => 1 ) ) ) ),
			array( 'id' => 'export-sectors', 'title' => 'Exportations par secteur (M USD)', 'description' => 'Ventilation sectorielle des exportations.', 'type' => 'bar', 'options' => array( 'indexAxis' => 'y' ), 'data' => array( 'labels' => array_map( fn( $s ) => $s['name'], $sectors ), 'datasets' => array( array( 'label' => 'Exportations', 'data' => array_map( fn( $s ) => $s['exports'], $sectors ), 'backgroundColor' => array( '#044badcc', '#059669cc', '#b8943ecc', '#dc2626cc', '#7c3aedcc', '#0891b2cc', '#ea580ccc', '#e11d48cc', '#16a34acc', '#2563ebcc', '#0d9488cc', '#ca8a04cc', '#475569cc', '#be123ccc', '#9333eacc', '#0369a1cc' ) ) ) ) ),
			array( 'id' => 'import-sectors', 'title' => 'Importations par secteur (M USD)', 'description' => 'Ventilation sectorielle des importations.', 'type' => 'bar', 'options' => array( 'indexAxis' => 'y' ), 'data' => array( 'labels' => array_map( fn( $s ) => $s['name'], $sectors ), 'datasets' => array( array( 'label' => 'Importations', 'data' => array_map( fn( $s ) => $s['imports'], $sectors ), 'backgroundColor' => array( '#044badcc', '#059669cc', '#b8943ecc', '#dc2626cc', '#7c3aedcc', '#0891b2cc', '#ea580ccc', '#e11d48cc', '#16a34acc', '#2563ebcc', '#0d9488cc', '#ca8a04cc', '#475569cc', '#be123ccc', '#9333eacc', '#0369a1cc' ) ) ) ) ),
		),
		'tables'  => array(
			array( 'id' => 'export-partners', 'title' => 'Principales destinations', 'headers' => array( '#', 'Pays', 'Valeur (Mds FCFA)', 'Part (%)' ), 'rows' => array_map( fn( $p, $i ) => array( (string) ( $i + 1 ), $p['country'], str_replace( '.', ',', number_format( $p['value'], 1, '.', '' ) ), str_replace( '.', ',', number_format( $p['share'] * 100, 1, '.', '' ) ) . '%' ), array_slice( $data['topExportPartners'], 0, 10 ), array_keys( array_slice( $data['topExportPartners'], 0, 10 ) ) ) ),
			array( 'id' => 'import-partners', 'title' => 'Principaux fournisseurs', 'headers' => array( '#', 'Pays', 'Valeur (Mds FCFA)', 'Part (%)' ), 'rows' => array_map( fn( $p, $i ) => array( (string) ( $i + 1 ), $p['country'], str_replace( '.', ',', number_format( $p['value'], 1, '.', '' ) ), str_replace( '.', ',', number_format( $p['share'] * 100, 1, '.', '' ) ) . '%' ), array_slice( $data['topImportPartners'], 0, 10 ), array_keys( array_slice( $data['topImportPartners'], 0, 10 ) ) ) ),
		),
	);
}

/**
 * Parses PME NINEA rows.
 *
 * @param array<int, array<int, string>> $rows Raw display rows.
 * @return array<string, mixed>
 */
function crades_vm_parse_pme_ninea( $rows ) {
	$years  = array( 2019, 2020, 2021, 2022, 2023, 2024 );
	$totals = array();

	foreach ( $years as $index => $year ) {
		$totals[] = array( 'year' => $year, 'total' => isset( $rows[0][ $index + 1 ] ) ? (int) crades_vm_parse_number( $rows[0][ $index + 1 ] ) : 0 );
	}

	$secteur_rows = array(
		array( 'row' => 20, 'label' => 'Commerce' ),
		array( 'row' => 21, 'label' => 'Services personnels' ),
		array( 'row' => 22, 'label' => 'Services aux entreprises' ),
		array( 'row' => 23, 'label' => 'Agriculture & Pêche' ),
		array( 'row' => 24, 'label' => 'BTP' ),
		array( 'row' => 25, 'label' => 'Hôtels & Restaurants' ),
		array( 'row' => 26, 'label' => 'Autres industries' ),
	);
	$secteur_by_year = array();

	foreach ( $secteur_rows as $entry ) {
		$values = array();
		foreach ( $years as $index => $year ) {
			$values[ $year ] = isset( $rows[ $entry['row'] ][ $index + 1 ] ) ? crades_vm_parse_percent( $rows[ $entry['row'] ][ $index + 1 ] ) : 0;
		}
		$secteur_by_year[] = array( 'label' => $entry['label'], 'values' => $values );
	}

	$autres_index = 6;
	foreach ( $years as $year ) {
		if ( empty( $secteur_by_year[ $autres_index ]['values'][ $year ] ) ) {
			$sum = 0;
			foreach ( $secteur_by_year as $index => $entry ) {
				if ( $index === $autres_index ) {
					continue;
				}
				$sum += $entry['values'][ $year ];
			}
			$secteur_by_year[ $autres_index ]['values'][ $year ] = max( 0, round( 100 - $sum, 1 ) );
		}
	}

	$region_rows = array(
		'Dakar' => 37, 'Thiès' => 38, 'Diourbel' => 39, 'Kaolack' => 40, 'Ziguinchor' => 41, 'Saint-Louis' => 42, 'Louga' => 43, 'Fatick' => 44, 'Tambacounda' => 45, 'Kolda' => 46, 'Matam' => 47, 'Kaffrine' => 48, 'Sédhiou' => 49, 'Kédougou' => 50,
	);
	$regions = array();
	foreach ( $region_rows as $label => $row_index ) {
		$regions[] = array( 'region' => $label, 'pct' => isset( $rows[ $row_index ][6] ) ? crades_vm_parse_percent( $rows[ $row_index ][6] ) : 0 );
	}

	return array(
		'immatriculations' => ! empty( $totals ) ? $totals[ count( $totals ) - 1 ]['total'] : 0,
		'immatricVariation'=> isset( $rows[0][7] ) ? trim( (string) $rows[0][7] ) : '',
		'immatricSeries'   => $totals,
		'secteurByYear'    => $secteur_by_year,
		'regionBreakdown'  => $regions,
		'ageBreakdown'     => array(
			array( 'label' => '< 25 ans', 'pct' => isset( $rows[57][6] ) ? crades_vm_parse_percent( $rows[57][6] ) : 0 ),
			array( 'label' => '25–34 ans', 'pct' => isset( $rows[58][6] ) ? crades_vm_parse_percent( $rows[58][6] ) : 0 ),
			array( 'label' => '35–54 ans', 'pct' => isset( $rows[59][6] ) ? crades_vm_parse_percent( $rows[59][6] ) : 0 ),
			array( 'label' => '55+ ans', 'pct' => isset( $rows[60][6] ) ? crades_vm_parse_percent( $rows[60][6] ) : 0 ),
		),
		'regimeBreakdown'  => array_values( array_filter( array( array( 'label' => 'Entreprises Individuelles', 'pct' => isset( $rows[63][2] ) ? crades_vm_parse_percent( $rows[63][2] ) : 0 ), array( 'label' => 'GIE', 'pct' => isset( $rows[65][2] ) ? crades_vm_parse_percent( $rows[65][2] ) : 0 ), array( 'label' => 'SARL', 'pct' => isset( $rows[66][2] ) ? crades_vm_parse_percent( $rows[66][2] ) : 0 ), array( 'label' => 'SUARL', 'pct' => isset( $rows[67][2] ) ? crades_vm_parse_percent( $rows[67][2] ) : 0 ), array( 'label' => 'Propriétaires Fonciers', 'pct' => isset( $rows[68][2] ) ? crades_vm_parse_percent( $rows[68][2] ) : 0 ), array( 'label' => 'Opérateurs Occasionnels', 'pct' => isset( $rows[69][2] ) ? crades_vm_parse_percent( $rows[69][2] ) : 0 ) ), fn( $entry ) => $entry['pct'] > 0 ) ),
	);
}

/**
 * Parses PME WB rows.
 *
 * @param array<int, array<int, string>> $rows Raw display rows.
 * @return array<string, mixed>
 */
function crades_vm_parse_pme_wb( $rows ) {
	if ( empty( $rows ) || crades_vm_is_pme_wb_index_sheet( $rows ) ) {
		return array();
	}

	$taille_breakdown = array(
		array( 'label' => 'Petites (5-19 emp.)', 'pct' => isset( $rows[5][1] ) ? crades_vm_parse_percent( $rows[5][1] ) : 0 ),
		array( 'label' => 'Moyennes (20-99 emp.)', 'pct' => isset( $rows[6][1] ) ? crades_vm_parse_percent( $rows[6][1] ) : 0 ),
		array( 'label' => 'Grandes (100+ emp.)', 'pct' => isset( $rows[7][1] ) ? crades_vm_parse_percent( $rows[7][1] ) : 0 ),
	);
	$taille_total = array_reduce(
		$taille_breakdown,
		function ( $carry, $item ) {
			return $carry + (float) $item['pct'];
		},
		0
	);
	$exportatrices = ! empty( $rows[8][1] ) ? trim( (string) $rows[8][1] ) : '';
	$femmes        = ! empty( $rows[10][1] ) ? trim( (string) $rows[10][1] ) : '';
	$credit        = ! empty( $rows[20][1] ) ? trim( (string) $rows[20][1] ) : '';
	$emploi        = ! empty( $rows[47][1] ) ? trim( (string) $rows[47][1] ) : '';

	if ( $taille_total <= 0 || $taille_total > 100.5 || ! crades_vm_is_valid_pme_scalar( $exportatrices ) || ! crades_vm_is_valid_pme_scalar( $credit ) || ! crades_vm_is_valid_pme_scalar( $emploi ) ) {
		return array();
	}

	return array(
		'tailleBreakdown'   => $taille_breakdown,
		'exportatrices'     => $exportatrices,
		'femmesDirigeantes' => crades_vm_is_valid_pme_scalar( $femmes ) ? $femmes : '~25%',
		'creditAccess'      => $credit,
		'croissanceEmploi'  => $emploi,
		'obstacles'         => array_map( fn( $item ) => array( 'label' => $item['label'], 'pct' => isset( $rows[ $item['row'] ][1] ) ? crades_vm_parse_percent( $rows[ $item['row'] ][1] ) : 0, 'note' => ! empty( $rows[ $item['row'] ][3] ) ? trim( (string) $rows[ $item['row'] ][3] ) : '' ), array( array( 'row' => 12, 'label' => 'Accès au financement' ), array( 'row' => 13, 'label' => 'Secteur informel' ), array( 'row' => 14, 'label' => 'Corruption' ), array( 'row' => 15, 'label' => 'Fiscalité' ), array( 'row' => 16, 'label' => 'Électricité' ), array( 'row' => 17, 'label' => 'Accès au foncier' ), array( 'row' => 18, 'label' => 'Réglementation douanière' ) ) ),
	);
}

/**
 * Returns the default PME / PMI data object.
 *
 * @return array<string, mixed>
 */
function crades_vm_get_default_pme() {
	return array(
		'immatriculations' => 91936,
		'immatricVariation' => '+72,1%',
		'exportatrices' => '~16%',
		'creditAccess' => '~25%',
		'femmesDirigeantes' => '~25%',
		'croissanceEmploi' => '~+5%',
		'immatricSeries' => array(),
		'tailleBreakdown' => array(),
		'regimeBreakdown' => array(),
		'secteurByYear' => array(),
		'regionBreakdown' => array(),
		'ageBreakdown' => array(),
		'obstacles' => array(),
	);
}

/**
 * Returns whether the PME WB rows are actually the workbook index.
 *
 * @param array<int, array<int, string>> $rows Raw display rows.
 * @return bool
 */
function crades_vm_is_pme_wb_index_sheet( $rows ) {
	$first_row  = strtoupper( remove_accents( trim( (string) ( $rows[0][0] ?? '' ) ) ) );
	$header_one = strtoupper( remove_accents( trim( (string) ( $rows[2][0] ?? '' ) ) ) );
	$header_two = strtoupper( remove_accents( trim( (string) ( $rows[2][1] ?? '' ) ) ) );

	if ( '' === $first_row ) {
		return true;
	}

	return false !== strpos( $first_row, 'TABLEAU DE BORD INTEGRE' ) || ( 'FEUILLE' === $header_one && 'CONTENU' === $header_two );
}

/**
 * Returns whether a PME scalar is usable in the survey view model.
 *
 * @param string $value Raw scalar value.
 * @return bool
 */
function crades_vm_is_valid_pme_scalar( $value ) {
	$value = trim( (string) $value );

	if ( '' === $value ) {
		return false;
	}

	if ( false !== stripos( $value, 'http' ) ) {
		return false;
	}

	return 1 === preg_match( '/[0-9]/', $value );
}

/**
 * Builds the PME / PMI data object.
 *
 * @param array<string, mixed> $tables Dashboard tables.
 * @return array<string, mixed>
 */
function crades_vm_build_pme_data( $tables ) {
	$defaults = crades_vm_get_default_pme();
	$ninea    = crades_vm_parse_pme_ninea( crades_vm_get_display_rows( $tables, 'ninea_immatriculations' ) );
	$wb       = crades_vm_parse_pme_wb( crades_vm_get_display_rows( $tables, 'enquete_wb' ) );

	return array_merge( $defaults, $ninea, $wb );
}

/**
 * Builds the PME / PMI view model.
 *
 * @param array<string, mixed> $tables Dashboard tables.
 * @return array<string, mixed>
 */
function crades_vm_build_pme_view_model( $tables ) {
	$data        = crades_vm_build_pme_data( $tables );
	$years       = array( 2019, 2020, 2021, 2022, 2023, 2024 );
	$totals_by_year = array();
	foreach ( $data['immatricSeries'] as $item ) {
		$totals_by_year[ $item['year'] ] = $item['total'];
	}

	$stack_colors = array( '#044badcc', '#059669cc', '#b8943ecc', '#dc2626cc', '#7c3aedcc', '#0891b2cc', '#ea580ccc' );
	$sector_datasets = array();
	foreach ( $data['secteurByYear'] as $index => $entry ) {
		$sector_datasets[] = array(
			'label' => $entry['label'],
			'data'  => array_map( fn( $year ) => round( ( ( $entry['values'][ $year ] ?? 0 ) / 100 ) * ( $totals_by_year[ $year ] ?? 0 ) ), $years ),
			'backgroundColor' => $stack_colors[ $index % count( $stack_colors ) ],
			'borderColor' => str_replace( 'cc', '', $stack_colors[ $index % count( $stack_colors ) ] ),
			'borderWidth' => 1,
			'borderRadius' => 2,
		);
	}

	return array(
		'kpis' => array(
			array( 'label' => 'Immatriculations 2024', 'display' => str_replace( ',', ' ', number_format( (int) $data['immatriculations'], 0, '.', ',' ) ), 'note' => $data['immatricVariation'] . ' vs 2019', 'badge' => 'ANSD' ),
			array( 'label' => 'Accès au crédit', 'display' => $data['creditAccess'], 'note' => 'Ligne de crédit – BM 2024', 'badge' => 'Banque mondiale' ),
			array( 'label' => 'Croissance emploi', 'display' => $data['croissanceEmploi'], 'note' => '3 dernières années – BM 2024', 'badge' => 'Banque mondiale' ),
			array( 'label' => 'Entreprises exportatrices', 'display' => $data['exportatrices'], 'note' => 'Enquête BM 2024', 'badge' => 'Banque mondiale' ),
		),
		'charts' => array(
			array( 'id' => 'pme-immatriculations', 'title' => 'Immatriculations par secteur d activité', 'description' => 'Entreprises individuelles 2019–2024.', 'type' => 'bar', 'options' => array( 'scales' => array( 'x' => array( 'stacked' => true ), 'y' => array( 'stacked' => true ) ) ), 'data' => array( 'labels' => $years, 'datasets' => $sector_datasets ) ),
			array( 'id' => 'pme-structure', 'title' => 'Répartition par taille', 'description' => 'Enquête BM 2024.', 'type' => 'doughnut', 'data' => array( 'labels' => array_map( fn( $i ) => $i['label'], $data['tailleBreakdown'] ), 'datasets' => array( array( 'label' => 'Taille', 'data' => array_map( fn( $i ) => $i['pct'], $data['tailleBreakdown'] ), 'backgroundColor' => array( '#044bad', '#059669', '#b8943e' ), 'borderColor' => '#ffffff', 'borderWidth' => 2 ) ) ) ),
			array( 'id' => 'pme-enquete', 'title' => 'Répartition par tranche d âge', 'description' => 'Entrepreneurs individuels 2024.', 'type' => 'bar', 'data' => array( 'labels' => array_map( fn( $i ) => $i['label'], $data['ageBreakdown'] ), 'datasets' => array( array( 'label' => 'Part (%)', 'data' => array_map( fn( $i ) => $i['pct'], $data['ageBreakdown'] ), 'backgroundColor' => '#044badcc', 'borderColor' => '#044bad', 'borderWidth' => 1, 'borderRadius' => 4 ) ) ) ),
			array( 'id' => 'pme-macro', 'title' => 'Répartition par régime juridique', 'description' => 'Ventilation 2024.', 'type' => 'doughnut', 'data' => array( 'labels' => array_map( fn( $i ) => $i['label'], $data['regimeBreakdown'] ), 'datasets' => array( array( 'label' => 'Régime', 'data' => array_map( fn( $i ) => $i['pct'], $data['regimeBreakdown'] ), 'backgroundColor' => array( '#044bad', '#059669', '#b8943e', '#dc2626', '#7c3aed', '#0891b2' ), 'borderColor' => '#ffffff', 'borderWidth' => 2 ) ) ) ),
		),
		'tables' => array(
			array( 'id' => 'ninea-immatriculations', 'title' => 'Répartition géographique', 'headers' => array( 'Région', 'Part (%)' ), 'rows' => array_map( fn( $item ) => array( $item['region'], crades_vm_format_number( $item['pct'], 1 ) . '%' ), $data['regionBreakdown'] ) ),
			array( 'id' => 'enquete-wb', 'title' => 'Obstacles à l activité', 'headers' => array( 'Obstacle', 'Part (%)', 'Note' ), 'rows' => array_map( fn( $item ) => array( $item['label'], crades_vm_format_number( $item['pct'], 1 ) . '%', $item['note'] ), $data['obstacles'] ) ),
		),
	);
}

/**
 * Builds the final dashboard view model payload.
 *
 * @param string               $dashboard_key Dashboard key.
 * @param array<string, mixed> $tables        Dashboard tables.
 * @return array<string, mixed>
 */
function crades_build_dashboard_view_model( $dashboard_key, $tables ) {
	switch ( $dashboard_key ) {
		case 'commerce-exterieur':
			return crades_vm_build_commerce_exterieur_view_model( $tables );
		case 'commerce-interieur':
			return crades_vm_build_commerce_interieur_view_model( $tables );
		case 'industrie':
			return crades_vm_build_industry_view_model( $tables );
		case 'pme-pmi':
			return crades_vm_build_pme_view_model( $tables );
		default:
			return array();
	}
}
