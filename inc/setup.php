<?php
/**
 * Theme setup hooks.
 *
 * @package CRADES_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers theme supports and menus.
 *
 * @return void
 */
function crades_theme_setup() {
	load_theme_textdomain( 'crades-theme', get_template_directory() . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 96,
			'width'       => 320,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'crades-theme' ),
			'footer'  => __( 'Footer Menu', 'crades-theme' ),
		)
	);

	add_post_type_support( 'page', 'excerpt' );
}
add_action( 'after_setup_theme', 'crades_theme_setup' );

/**
 * Registers PDF library content types.
 *
 * @return void
 */
function crades_register_rapport_content_types() {
	register_post_type(
		'rapports',
		array(
			'labels' => array(
				'name'               => __( 'Rapports', 'crades-theme' ),
				'singular_name'      => __( 'Rapport', 'crades-theme' ),
				'add_new'            => __( 'Ajouter', 'crades-theme' ),
				'add_new_item'       => __( 'Ajouter un rapport', 'crades-theme' ),
				'edit_item'          => __( 'Modifier le rapport', 'crades-theme' ),
				'new_item'           => __( 'Nouveau rapport', 'crades-theme' ),
				'view_item'          => __( 'Voir le rapport', 'crades-theme' ),
				'search_items'       => __( 'Rechercher des rapports', 'crades-theme' ),
				'not_found'          => __( 'Aucun rapport trouve.', 'crades-theme' ),
				'not_found_in_trash' => __( 'Aucun rapport dans la corbeille.', 'crades-theme' ),
				'menu_name'          => __( 'Rapports', 'crades-theme' ),
			),
			'public'             => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_rest'       => true,
			'has_archive'        => 'rapports',
			'rewrite'            => array(
				'slug'       => 'rapports',
				'with_front' => false,
			),
			'menu_icon'          => 'dashicons-media-document',
			'supports'           => array(
				'title',
				'editor',
				'excerpt',
				'thumbnail',
				'revisions',
				'custom-fields',
			),
			'publicly_queryable' => true,
			'hierarchical'       => false,
			'menu_position'      => 21,
		)
	);

	register_taxonomy(
		'rapport_type',
		array( 'rapports' ),
		array(
			'labels'            => array(
				'name'          => __( 'Types de rapports', 'crades-theme' ),
				'singular_name' => __( 'Type de rapport', 'crades-theme' ),
				'search_items'  => __( 'Rechercher des types', 'crades-theme' ),
				'all_items'     => __( 'Tous les types', 'crades-theme' ),
				'edit_item'     => __( 'Modifier le type', 'crades-theme' ),
				'update_item'   => __( 'Mettre a jour le type', 'crades-theme' ),
				'add_new_item'  => __( 'Ajouter un type', 'crades-theme' ),
				'new_item_name' => __( 'Nouveau type', 'crades-theme' ),
				'menu_name'     => __( 'Types de rapports', 'crades-theme' ),
			),
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'hierarchical'      => true,
			'rewrite'           => array(
				'slug'       => 'type-rapport',
				'with_front' => false,
			),
		)
	);

	register_post_meta(
		'rapports',
		'_crades_pdf_file_id',
		array(
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'integer',
			'auth_callback'     => 'crades_can_manage_rapport_meta',
			'sanitize_callback' => 'absint',
		)
	);

	register_post_meta(
		'rapports',
		'_crades_pdf_url',
		array(
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'string',
			'auth_callback'     => 'crades_can_manage_rapport_meta',
			'sanitize_callback' => 'esc_url_raw',
		)
	);

	register_post_meta(
		'rapports',
		'_crades_pdf_year',
		array(
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'string',
			'auth_callback'     => 'crades_can_manage_rapport_meta',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	register_post_meta(
		'rapports',
		'_crades_pdf_summary',
		array(
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'string',
			'auth_callback'     => 'crades_can_manage_rapport_meta',
			'sanitize_callback' => 'sanitize_textarea_field',
		)
	);

	register_post_meta(
		'rapports',
		'_crades_pdf_featured',
		array(
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'boolean',
			'auth_callback'     => 'crades_can_manage_rapport_meta',
			'sanitize_callback' => 'rest_sanitize_boolean',
		)
	);
}
add_action( 'init', 'crades_register_rapport_content_types' );

/**
 * Adds rapport management meta box.
 *
 * @return void
 */
function crades_register_rapport_meta_boxes() {
	add_meta_box(
		'crades-rapport-details',
		__( 'Details du PDF', 'crades-theme' ),
		'crades_render_rapport_meta_box',
		'rapports',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'crades_register_rapport_meta_boxes' );

/**
 * Renders the rapport details meta box.
 *
 * @param WP_Post $post Current post.
 * @return void
 */
function crades_render_rapport_meta_box( $post ) {
	$attachment_id = crades_get_rapport_pdf_attachment_id( $post );
	$pdf_url       = (string) get_post_meta( $post->ID, '_crades_pdf_url', true );
	$year          = (string) get_post_meta( $post->ID, '_crades_pdf_year', true );
	$summary       = (string) get_post_meta( $post->ID, '_crades_pdf_summary', true );
	$featured      = (bool) get_post_meta( $post->ID, '_crades_pdf_featured', true );

	wp_nonce_field( 'crades_save_rapport_meta', 'crades_rapport_meta_nonce' );
	?>
	<p>
		<label for="crades_pdf_file_id"><strong><?php esc_html_e( 'ID du fichier PDF', 'crades-theme' ); ?></strong></label><br>
		<input type="number" class="widefat" id="crades_pdf_file_id" name="crades_pdf_file_id" value="<?php echo esc_attr( $attachment_id ); ?>" min="0">
		<span class="description"><?php esc_html_e( 'Utilisez l ID d une piece jointe de la bibliotheque WordPress. Cette valeur est prioritaire sur l URL externe.', 'crades-theme' ); ?></span>
	</p>
	<p>
		<label for="crades_pdf_url"><strong><?php esc_html_e( 'URL PDF externe', 'crades-theme' ); ?></strong></label><br>
		<input type="url" class="widefat" id="crades_pdf_url" name="crades_pdf_url" value="<?php echo esc_attr( $pdf_url ); ?>" placeholder="https://example.com/document.pdf">
		<span class="description"><?php esc_html_e( 'Utilisee uniquement si aucun fichier PDF WordPress n est renseigne.', 'crades-theme' ); ?></span>
	</p>
	<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;">
		<p>
			<label for="crades_pdf_year"><strong><?php esc_html_e( 'Annee d affichage', 'crades-theme' ); ?></strong></label><br>
			<input type="text" class="widefat" id="crades_pdf_year" name="crades_pdf_year" value="<?php echo esc_attr( $year ); ?>" maxlength="12">
		</p>
		<p>
			<label for="crades_pdf_featured"><strong><?php esc_html_e( 'Mise en avant', 'crades-theme' ); ?></strong></label><br>
			<label>
				<input type="checkbox" id="crades_pdf_featured" name="crades_pdf_featured" value="1" <?php checked( $featured ); ?>>
				<?php esc_html_e( 'Afficher ce rapport comme contenu mis en avant lorsque le theme en a besoin.', 'crades-theme' ); ?>
			</label>
		</p>
	</div>
	<p>
		<label for="crades_pdf_summary"><strong><?php esc_html_e( 'Resume court', 'crades-theme' ); ?></strong></label><br>
		<textarea class="widefat" id="crades_pdf_summary" name="crades_pdf_summary" rows="4"><?php echo esc_textarea( $summary ); ?></textarea>
		<span class="description"><?php esc_html_e( 'Si ce champ est vide, le theme utilisera l extrait ou un resume du contenu.', 'crades-theme' ); ?></span>
	</p>
	<?php
}

/**
 * Saves rapport meta box fields.
 *
 * @param int $post_id Current post ID.
 * @return void
 */
function crades_save_rapport_meta( $post_id ) {
	if ( ! isset( $_POST['crades_rapport_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['crades_rapport_meta_nonce'] ) ), 'crades_save_rapport_meta' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$attachment_id = isset( $_POST['crades_pdf_file_id'] ) ? absint( wp_unslash( $_POST['crades_pdf_file_id'] ) ) : 0;
	$pdf_url       = isset( $_POST['crades_pdf_url'] ) ? esc_url_raw( wp_unslash( $_POST['crades_pdf_url'] ) ) : '';
	$year          = isset( $_POST['crades_pdf_year'] ) ? sanitize_text_field( wp_unslash( $_POST['crades_pdf_year'] ) ) : '';
	$summary       = isset( $_POST['crades_pdf_summary'] ) ? sanitize_textarea_field( wp_unslash( $_POST['crades_pdf_summary'] ) ) : '';
	$featured      = ! empty( $_POST['crades_pdf_featured'] );

	if ( $attachment_id ) {
		update_post_meta( $post_id, '_crades_pdf_file_id', $attachment_id );
	} else {
		delete_post_meta( $post_id, '_crades_pdf_file_id' );
	}

	if ( $pdf_url ) {
		update_post_meta( $post_id, '_crades_pdf_url', $pdf_url );
	} else {
		delete_post_meta( $post_id, '_crades_pdf_url' );
	}

	if ( $year ) {
		update_post_meta( $post_id, '_crades_pdf_year', $year );
	} else {
		delete_post_meta( $post_id, '_crades_pdf_year' );
	}

	if ( $summary ) {
		update_post_meta( $post_id, '_crades_pdf_summary', $summary );
	} else {
		delete_post_meta( $post_id, '_crades_pdf_summary' );
	}

	update_post_meta( $post_id, '_crades_pdf_featured', $featured );
}
add_action( 'save_post_rapports', 'crades_save_rapport_meta' );

/**
 * Sets the archive size for rapports views.
 *
 * @param WP_Query $query Query instance.
 * @return void
 */
function crades_filter_rapports_archive_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( $query->is_post_type_archive( 'rapports' ) || $query->is_tax( 'rapport_type' ) ) {
		$query->set( 'posts_per_page', crades_get_rapport_posts_per_page() );
	}
}
add_action( 'pre_get_posts', 'crades_filter_rapports_archive_query' );

/**
 * Registers custom columns for rapports.
 *
 * @param string[] $columns Existing columns.
 * @return string[]
 */
function crades_filter_rapport_admin_columns( $columns ) {
	$updated_columns = array();

	foreach ( $columns as $key => $label ) {
		$updated_columns[ $key ] = $label;

		if ( 'title' === $key ) {
			$updated_columns['rapport_type'] = __( 'Type', 'crades-theme' );
			$updated_columns['pdf_year']     = __( 'Annee', 'crades-theme' );
			$updated_columns['pdf_status']   = __( 'PDF', 'crades-theme' );
		}
	}

	return $updated_columns;
}
add_filter( 'manage_rapports_posts_columns', 'crades_filter_rapport_admin_columns' );

/**
 * Renders custom rapports admin columns.
 *
 * @param string $column  Column key.
 * @param int    $post_id Post ID.
 * @return void
 */
function crades_render_rapport_admin_columns( $column, $post_id ) {
	if ( 'rapport_type' === $column ) {
		$terms = get_the_term_list( $post_id, 'rapport_type', '', ', ' );
		echo $terms ? wp_kses_post( $terms ) : '<span aria-hidden="true">-</span>';
		return;
	}

	if ( 'pdf_year' === $column ) {
		$year = crades_get_rapport_year( $post_id );
		echo $year ? esc_html( $year ) : '<span aria-hidden="true">-</span>';
		return;
	}

	if ( 'pdf_status' === $column ) {
		$pdf_url = crades_get_rapport_pdf_url( $post_id );
		echo $pdf_url ? esc_html__( 'Disponible', 'crades-theme' ) : esc_html__( 'Manquant', 'crades-theme' );
	}
}
add_action( 'manage_rapports_posts_custom_column', 'crades_render_rapport_admin_columns', 10, 2 );

/**
 * Sets a consistent content width.
 *
 * @return void
 */
function crades_theme_content_width() {
	$GLOBALS['content_width'] = 1200;
}
add_action( 'after_setup_theme', 'crades_theme_content_width', 0 );

/**
 * Adds useful body classes.
 *
 * @param string[] $classes Existing classes.
 * @return string[]
 */
function crades_filter_body_classes( $classes ) {
	$classes[] = 'crades-theme';

	if ( is_front_page() ) {
		$classes[] = 'crades-front-page';
	}

	if ( is_singular() ) {
		$classes[] = 'crades-singular';
	}

	return $classes;
}
add_filter( 'body_class', 'crades_filter_body_classes' );

/**
 * Returns the required CRADES page definitions.
 *
 * @return array<string, string>
 */
function crades_get_required_pages() {
	return array(
		'a-propos'            => 'A propos',
		'publications'        => 'Publications',
		'tableaux-de-bord'    => 'Tableaux de bord',
		'commerce-exterieur'  => 'Commerce exterieur',
		'commerce-interieur'  => 'Commerce interieur',
		'industrie'           => 'Industrie',
		'pme-pmi'             => 'PME / PMI',
		'ressources'          => 'Ressources',
		'contact'             => 'Contact',
	);
}

/**
 * Returns the slug-to-template fallback map for static CRADES pages.
 *
 * @return array<string, string>
 */
function crades_get_static_page_template_map() {
	return array(
		'a-propos'           => 'page-a-propos.php',
		'publications'       => 'page-publications.php',
		'tableaux-de-bord'   => 'page-tableaux-de-bord.php',
		'commerce-exterieur' => 'page-commerce-exterieur.php',
		'commerce-interieur' => 'page-commerce-interieur.php',
		'industrie'          => 'page-industrie.php',
		'pme-pmi'            => 'page-pme-pmi.php',
		'ressources'         => 'page-ressources.php',
		'contact'            => 'page-contact.php',
	);
}

/**
 * Creates the required CRADES pages when they do not exist yet.
 *
 * @return void
 */
function crades_sync_required_pages() {
	foreach ( crades_get_required_pages() as $slug => $title ) {
		$page = get_page_by_path( $slug, OBJECT, 'page' );

		if ( $page instanceof WP_Post ) {
			continue;
		}

		wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => $title,
				'post_name'    => $slug,
				'post_content' => '',
			)
		);
	}

	update_option( 'crades_required_pages_synced', '1' );
}

/**
 * Runs one-time theme activation setup.
 *
 * @return void
 */
function crades_after_switch_theme_setup() {
	crades_sync_required_pages();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'crades_after_switch_theme_setup' );

/**
 * Ensures required pages exist after theme activation on existing installs.
 *
 * @return void
 */
function crades_maybe_sync_required_pages() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( '1' === get_option( 'crades_required_pages_synced', '0' ) ) {
		return;
	}

	crades_sync_required_pages();
	flush_rewrite_rules( false );
}
add_action( 'admin_init', 'crades_maybe_sync_required_pages' );

/**
 * Ensures required pages exist on non-admin requests too, so fresh installs
 * behave correctly before any admin page is visited.
 *
 * @return void
 */
function crades_bootstrap_required_pages() {
	static $did_bootstrap = false;

	if ( $did_bootstrap ) {
		return;
	}

	$did_bootstrap = true;

	foreach ( array_keys( crades_get_required_pages() ) as $slug ) {
		$page = get_page_by_path( $slug, OBJECT, 'page' );

		if ( ! ( $page instanceof WP_Post ) ) {
			crades_sync_required_pages();
			break;
		}
	}
}
add_action( 'init', 'crades_bootstrap_required_pages', 20 );

/**
 * Returns the current request slug when available.
 *
 * @return string
 */
function crades_get_current_request_slug() {
	global $wp;

	if ( ! isset( $wp ) || empty( $wp->request ) ) {
		return '';
	}

	return trim( (string) $wp->request, '/' );
}

/**
 * Returns a static theme template path for a known CRADES slug fallback.
 *
 * @return string
 */
function crades_get_static_page_template_fallback() {
	$slug = crades_get_current_request_slug();
	$map  = crades_get_static_page_template_map();

	if ( empty( $slug ) || empty( $map[ $slug ] ) ) {
		return '';
	}

	$template = get_template_directory() . '/' . $map[ $slug ];

	return file_exists( $template ) ? $template : '';
}

/**
 * Prevents known CRADES slugs from staying in a 404 state.
 *
 * @return void
 */
function crades_fix_static_page_fallback_status() {
	if ( ! is_404() ) {
		return;
	}

	$template = crades_get_static_page_template_fallback();

	if ( ! $template ) {
		return;
	}

	global $wp_query;

	$wp_query->is_404 = false;
	status_header( 200 );
}
add_action( 'template_redirect', 'crades_fix_static_page_fallback_status' );

/**
 * Serves the correct page file for known CRADES slugs when no WP page is resolved.
 *
 * @param string $template Resolved template path.
 * @return string
 */
function crades_template_include_static_page_fallback( $template ) {
	$fallback = crades_get_static_page_template_fallback();

	return $fallback ? $fallback : $template;
}
add_filter( 'template_include', 'crades_template_include_static_page_fallback' );

