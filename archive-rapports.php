<?php
/**
 * Rapports archive template.
 *
 * @package CRADES_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wp_query;

$rapport_terms = get_terms(
	array(
		'taxonomy'   => 'rapport_type',
		'hide_empty' => true,
	)
);

get_header();
?>
<div class="pdf-library-page" data-pdf-library>
	<section class="bg-brand-navy py-16 lg:py-20">
		<div class="mx-auto max-w-6xl px-4 sm:px-6">
			<nav class="mb-4 text-xs text-gray-400">
				<a class="hover:text-white" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php esc_html_e( 'Accueil', 'crades-theme' ); ?>
				</a>
				<span class="mx-2">/</span>
				<span class="text-gray-300"><?php post_type_archive_title(); ?></span>
			</nav>

			<div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
				<div class="max-w-2xl">
					<p class="text-xs font-semibold uppercase tracking-[0.24em] text-brand-gold">
						<?php esc_html_e( 'Bibliothèque documentaire', 'crades-theme' ); ?>
					</p>
					<h1 class="mt-4 font-display text-2xl text-white lg:text-3xl"><?php post_type_archive_title(); ?></h1>
					<p class="mt-2 text-sm leading-relaxed text-gray-400">
						<?php
						echo esc_html(
							wp_strip_all_tags(
								get_the_archive_description()
									? get_the_archive_description()
									: __( 'Consultez les rapports du CRADES, filtrez-les par type et ouvrez-les dans le lecteur PDF intégré.', 'crades-theme' )
							)
						);
						?>
					</p>
				</div>

				<div class="inline-flex items-center gap-3 rounded-full border border-white/10 bg-white/5 px-4 py-2 text-white/80 backdrop-blur-sm">
					<span class="text-[10px] font-semibold uppercase tracking-[0.18em] text-brand-gold"><?php esc_html_e( 'Collection', 'crades-theme' ); ?></span>
					<span class="text-sm font-semibold text-white" data-pdf-count aria-live="polite"><?php echo esc_html( (string) $wp_query->found_posts ); ?></span>
				</div>
			</div>
		</div>
	</section>

	<section class="bg-gray-50 py-12">
		<div class="mx-auto max-w-6xl px-4 sm:px-6">
			<?php
			get_template_part(
				'template-parts/pdf/pdf',
				'filters',
				array(
					'terms' => is_wp_error( $rapport_terms ) ? array() : $rapport_terms,
				)
			);
			?>

			<?php if ( have_posts() ) : ?>
				<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4" data-pdf-grid>
					<?php
					while ( have_posts() ) :
						the_post();
						get_template_part( 'template-parts/pdf/pdf', 'card' );
					endwhile;
					?>
				</div>

				<div class="mt-6 hidden rounded-xl border border-dashed border-gray-200 bg-white px-6 py-10 text-center text-gray-500" data-pdf-empty>
					<p class="text-lg font-semibold text-gray-900">
						<?php esc_html_e( 'Aucun résultat pour ce filtre', 'crades-theme' ); ?>
					</p>
					<p class="mt-3 text-sm">
						<?php esc_html_e( 'Essayez un autre type de document ou revenez à la vue complète.', 'crades-theme' ); ?>
					</p>
				</div>

				<div class="mt-10">
					<?php the_posts_pagination(); ?>
				</div>
			<?php else : ?>
				<div class="rounded-xl border border-dashed border-gray-200 bg-white px-6 py-12 text-center text-gray-500">
					<p class="text-lg font-semibold text-gray-900">
						<?php esc_html_e( 'Aucun rapport disponible', 'crades-theme' ); ?>
					</p>
					<p class="mt-3 text-sm">
						<?php esc_html_e( 'Ajoutez des contenus dans le type de contenu Rapports pour alimenter cette archive.', 'crades-theme' ); ?>
					</p>
				</div>
			<?php endif; ?>
		</div>
	</section>

	<div class="fixed inset-0 z-[80] hidden items-center justify-center bg-transparent p-4" data-pdf-modal aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="crades-pdf-reader-title">
		<div class="absolute inset-0 bg-white/15 backdrop-blur-sm"></div>
		<div class="relative flex h-[82vh] w-[min(95vw,1100px)] flex-col overflow-hidden border border-gray-200 bg-white shadow-2xl">
			<div class="flex items-center justify-between gap-4 border-b border-gray-200 px-4 py-4 sm:px-6">
				<div class="min-w-0">
					<p class="text-xs font-semibold uppercase tracking-[0.24em] text-brand-gold"><?php esc_html_e( 'Lecteur PDF', 'crades-theme' ); ?></p>
					<h2 id="crades-pdf-reader-title" class="truncate text-base font-semibold text-slate-900 sm:text-lg" data-pdf-reader-title><?php esc_html_e( 'Document', 'crades-theme' ); ?></h2>
				</div>
				<div class="flex items-center gap-2">
					<a class="inline-flex items-center rounded-full border border-gray-200 px-4 py-2 text-sm font-semibold text-slate-600 transition-colors hover:border-brand-blue hover:text-brand-blue" href="#" data-pdf-reader-download target="_blank" rel="noopener"><?php esc_html_e( 'Télécharger', 'crades-theme' ); ?></a>
					<button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-gray-200 text-slate-500 transition-colors hover:border-brand-blue hover:text-brand-blue" data-pdf-close aria-label="<?php esc_attr_e( 'Fermer le lecteur', 'crades-theme' ); ?>"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
				</div>
			</div>

			<div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 bg-slate-50 px-4 py-3 sm:px-6">
				<div class="flex items-center gap-2">
					<button type="button" class="inline-flex items-center rounded-full border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-600 transition-colors hover:border-brand-blue hover:text-brand-blue disabled:cursor-not-allowed disabled:opacity-40" data-pdf-prev><?php esc_html_e( 'Précédent', 'crades-theme' ); ?></button>
					<button type="button" class="inline-flex items-center rounded-full border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-600 transition-colors hover:border-brand-blue hover:text-brand-blue disabled:cursor-not-allowed disabled:opacity-40" data-pdf-next><?php esc_html_e( 'Suivant', 'crades-theme' ); ?></button>
				</div>
				<p class="text-sm font-medium text-slate-500" data-pdf-page-indicator><?php esc_html_e( 'Page 1 sur 1', 'crades-theme' ); ?></p>
			</div>

			<div class="relative flex-1 overflow-auto bg-slate-100 p-3 sm:p-6" data-pdf-reader-stage>
				<div class="absolute inset-0 flex items-center justify-center bg-slate-100/90 text-center text-slate-500" data-pdf-reader-loading>
					<div>
						<div class="mx-auto mb-4 h-12 w-12 animate-pulse rounded-full bg-brand-ice"></div>
						<p class="text-sm font-medium"><?php esc_html_e( 'Chargement du document...', 'crades-theme' ); ?></p>
					</div>
				</div>

				<div class="hidden absolute inset-0 items-center justify-center bg-slate-100 px-6 text-center" data-pdf-reader-error>
					<div>
						<div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-red-50 text-red-500"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i></div>
						<p class="mt-4 text-base font-semibold text-slate-900"><?php esc_html_e( 'Lecture impossible', 'crades-theme' ); ?></p>
						<p class="mt-2 text-sm text-slate-500" data-pdf-reader-error-message><?php esc_html_e( 'Impossible de charger ce PDF.', 'crades-theme' ); ?></p>
					</div>
				</div>

				<div class="mx-auto flex min-h-full w-full max-w-4xl items-start justify-center">
					<canvas class="rounded-2xl bg-white shadow-soft" data-pdf-reader-canvas></canvas>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
get_footer();
