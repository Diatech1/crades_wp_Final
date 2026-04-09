<?php
/**
 * Exact Publications page ported from the Hono app.
 *
 * @package CRADES_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$taxonomies   = array(
	'Toutes',
	'Bulletins mensuels',
	'Bulletins annuels',
	'Fiches synoptiques',
	'Études monographiques',
);
$publications = array(
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
$palette      = array(
	'Bulletins mensuels'    => array(
		'badge' => 'bg-white/15 text-white',
		'text'  => 'text-white',
	),
	'Bulletins annuels'     => array(
		'badge' => 'bg-white/15 text-white',
		'text'  => 'text-white',
	),
	'Fiches synoptiques'    => array(
		'badge' => 'bg-white/15 text-white',
		'text'  => 'text-white',
	),
	'Études monographiques' => array(
		'badge' => 'bg-white/15 text-white',
		'text'  => 'text-white',
	),
);
?>
<div data-publications-page>
	<section class="bg-brand-navy py-16 lg:py-20">
		<div class="max-w-6xl mx-auto px-4 sm:px-6">
			<nav class="text-xs text-gray-400 mb-4">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hover:text-white"><?php esc_html_e( 'Accueil', 'crades-theme' ); ?></a>
				<span class="mx-2">/</span>
				<span class="text-gray-300"><?php esc_html_e( 'Publications', 'crades-theme' ); ?></span>
			</nav>
			<h1 class="font-display text-2xl lg:text-3xl text-white"><?php esc_html_e( 'Publications', 'crades-theme' ); ?></h1>
			<p class="text-gray-400 mt-2 max-w-2xl text-sm leading-relaxed">
				<?php esc_html_e( 'Rapports, notes de conjoncture, bulletins statistiques et études du CRADES.', 'crades-theme' ); ?>
			</p>
		</div>
	</section>

	<section class="py-12 bg-gray-50">
		<div class="max-w-6xl mx-auto px-4 sm:px-6">
			<div class="flex flex-wrap gap-3 mb-8">
				<?php foreach ( $taxonomies as $index => $taxonomy ) : ?>
					<button type="button" data-taxonomy-filter="<?php echo esc_attr( $taxonomy ); ?>" class="text-xs font-medium <?php echo 0 === $index ? 'bg-brand-blue text-white' : 'bg-white text-gray-500 border border-gray-200 hover:border-brand-blue hover:text-brand-blue'; ?> px-3 py-1.5 rounded-full cursor-pointer transition-colors">
						<?php echo esc_html( $taxonomy ); ?>
					</button>
				<?php endforeach; ?>
			</div>

			<div>
				<div class="flex items-center justify-between mb-5">
					<h2 class="font-display text-xl text-gray-800"><?php esc_html_e( 'Publications', 'crades-theme' ); ?></h2>
				</div>
				<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">
					<?php foreach ( $publications as $publication ) : ?>
						<?php
						$style = isset( $palette[ $publication['taxonomy'] ] ) ? $palette[ $publication['taxonomy'] ] : array(
							'badge' => 'bg-white text-brand-blue',
							'text'  => 'text-brand-navy',
						);
						?>
						<article class="group bg-white rounded-xl border border-gray-100 overflow-hidden hover:shadow-md transition-shadow" data-publication-card data-taxonomy="<?php echo esc_attr( $publication['taxonomy'] ); ?>">
							<button type="button" class="relative block w-full aspect-[4/3] overflow-hidden bg-white flex items-center justify-center cursor-pointer" data-pdf-open="<?php echo esc_url( $publication['href'] ); ?>" data-pdf-title="<?php echo esc_attr( $publication['title'] ); ?>">
								<canvas class="block h-full w-auto max-w-none" data-pdf-thumb data-pdf-url="<?php echo esc_url( $publication['href'] ); ?>" data-pdf-title="<?php echo esc_attr( $publication['title'] ); ?>"></canvas>
								<div class="absolute inset-0 pointer-events-none bg-gradient-to-t from-black/10 via-transparent to-transparent"></div>
								<div class="absolute left-3 top-3 inline-flex items-center gap-2 rounded-full px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wider <?php echo esc_attr( $style['badge'] ); ?>">
									<i class="fas fa-file-pdf text-[9px]" aria-hidden="true"></i>
									PDF
								</div>
								<div class="absolute right-3 top-3 rounded-full border border-white/20 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wider <?php echo esc_attr( $style['text'] ); ?> bg-black/20 backdrop-blur-sm">
									<?php echo esc_html( $publication['year'] ); ?>
								</div>
							</button>
							<div class="p-4">
								<div class="mb-5" style="border-top: 2px solid #b8943e;"></div>
								<div>
									<h3 class="text-sm font-semibold text-brand-navy line-clamp-2 group-hover:text-brand-blue transition-colors"><?php echo esc_html( $publication['title'] ); ?></h3>
								</div>
								<div class="mt-2 space-y-1 min-h-[2.3rem]">
									<div class="text-[10px] font-semibold uppercase tracking-[0.18em] text-brand-gold"><?php echo esc_html( $publication['type'] ); ?></div>
									<div class="text-[10px] font-medium uppercase tracking-[0.16em] text-brand-blue opacity-0 select-none"><?php echo esc_html( $publication['taxonomy'] ); ?></div>
								</div>
								<div class="flex items-center justify-between mt-3 text-[10px] text-gray-300 gap-3">
									<span><?php echo esc_html( $publication['year'] ); ?></span>
									<div class="flex items-center gap-3">
										<button type="button" data-pdf-open="<?php echo esc_url( $publication['href'] ); ?>" data-pdf-title="<?php echo esc_attr( $publication['title'] ); ?>" class="text-brand-blue font-medium hover:underline"><?php esc_html_e( 'Ouvrir', 'crades-theme' ); ?></button>
										<a href="<?php echo esc_url( $publication['href'] ); ?>" download class="text-brand-blue font-medium hover:underline"><?php esc_html_e( 'Télécharger', 'crades-theme' ); ?></a>
									</div>
								</div>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</section>

	<div id="pdfModal" class="fixed inset-0 z-[80] hidden flex items-center justify-center bg-transparent p-4">
		<div class="absolute inset-0 bg-white/15 backdrop-blur-sm"></div>
		<div class="relative w-[min(95vw,1100px)] h-[82vh] overflow-hidden border border-gray-200 bg-white shadow-2xl">
			<button type="button" class="absolute right-3 top-3 z-20 inline-flex h-9 w-9 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 shadow-sm hover:border-gray-300 hover:text-gray-700 transition-colors" data-pdf-modal-close aria-label="<?php esc_attr_e( 'Fermer', 'crades-theme' ); ?>">
				<i class="fas fa-times" aria-hidden="true"></i>
			</button>
			<div id="pdfModalViewer" class="h-full w-full overflow-auto bg-white px-4 py-14"></div>
		</div>
	</div>
</div>
<?php
get_footer();
