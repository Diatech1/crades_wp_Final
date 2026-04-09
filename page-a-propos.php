<?php
/**
 * Exact À propos page ported from the Hono app.
 *
 * @package CRADES_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$missions  = array(
	array(
		'icon'  => 'fa-magnifying-glass-chart',
		'title' => 'Recherche et traitement',
		'desc'  => 'Recherche, traitement et analyse des statistiques et des informations commerciales pour mieux eclairer la decision publique.',
	),
	array(
		'icon'  => 'fa-chart-line',
		'title' => 'Intelligence economique',
		'desc'  => "Centre d'analyse pour l'orientation stratégique, le suivi et l'évaluation de l'impact de la politique commerciale.",
	),
	array(
		'icon'  => 'fa-globe',
		'title' => 'Veille commerciale',
		'desc'  => "Surveillance permanente de l'environnement extérieur, des marchés, des risques, des opportunités et des flux d'information.",
	),
	array(
		'icon'  => 'fa-handshake',
		'title' => 'Appui aux acteurs',
		'desc'  => 'Renforcement des capacités des administrations, du secteur privé et de la société civile sur les règles et pratiques commerciales.',
	),
);
$divisions = array(
	array(
		'title' => 'DRAE',
		'label' => "Division de la recherche, de l'analyse et des études",
		'desc'  => "Conduit les études, l'analyse sectorielle et la production d'éléments d'aide à la décision.",
	),
	array(
		'title' => 'DIEES',
		'label' => "Division de l'intelligence economique et de l'exploitation des statistiques",
		'desc'  => "Exploite les données, suit les indicateurs et alimente l'orientation stratégique.",
	),
	array(
		'title' => 'Documentation',
		'label' => 'Division de la documentation commerciale et de la communication',
		'desc'  => "Organise l'information, la diffusion des productions et la communication institutionnelle.",
	),
);
$markets   = array( 'OMC', 'UEMOA', 'CEDEAO', 'OCI', 'UA' );
$pdf_url   = crades_get_theme_asset_uri( 'assets/docs/arrete-crades-clean.pdf' );
?>
<section class="bg-brand-navy py-16 lg:py-20">
	<div class="max-w-6xl mx-auto px-4 sm:px-6">
		<nav class="text-xs text-gray-400 mb-4">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hover:text-white"><?php esc_html_e( 'Accueil', 'crades-theme' ); ?></a>
			<span class="mx-2 text-gray-600">/</span>
			<span class="text-gray-300"><?php esc_html_e( 'À propos', 'crades-theme' ); ?></span>
		</nav>
		<h1 class="font-display text-2xl lg:text-3xl text-white"><?php esc_html_e( 'À propos du CRADES', 'crades-theme' ); ?></h1>
		<p class="text-gray-400 mt-2 max-w-2xl text-sm leading-relaxed">
			<?php esc_html_e( "Centre de Recherche et d'Analyse des Échanges et Statistiques, structure technique spécialisée dans la recherche, le traitement et l'analyse des informations commerciales et industrielles.", 'crades-theme' ); ?>
		</p>
	</div>
</section>

<section class="py-16">
	<div class="max-w-6xl mx-auto px-4 sm:px-6">
		<div class="grid lg:grid-cols-2 gap-8 items-start">
			<div>
				<span class="text-xs font-semibold text-brand-gold uppercase tracking-widest"><?php esc_html_e( 'Historique', 'crades-theme' ); ?></span>
				<h2 class="font-display text-xl text-gray-800 mt-3"><?php esc_html_e( "Une structure nee d'un besoin de decision publique mieux informee", 'crades-theme' ); ?></h2>
				<div class="mt-5 space-y-4 text-sm text-gray-500 leading-relaxed">
					<p><?php esc_html_e( "Le CRADES est issu d'une recommandation formulée à l'issue d'une étude commanditée par le Ministère du Commerce. Cette étude visait à recenser les principales sources d'information et à examiner la possibilité d'en assurer l'exploitation pour mieux orienter la décision publique.", 'crades-theme' ); ?></p>
					<p><?php esc_html_e( "Le contexte économique justifiait un suivi plus régulier des flux d'information afin de soutenir une politique commerciale efficace dans une économie très ouverte et fortement exposée aux ajustements liés à la libéralisation des échanges.", 'crades-theme' ); ?></p>
				</div>
			</div>
			<div class="bg-brand-frost rounded-2xl p-6 border border-brand-ice/50">
				<h3 class="text-sm font-semibold text-gray-800 mb-4"><?php esc_html_e( 'Elements de justification', 'crades-theme' ); ?></h3>
				<ul class="space-y-3 text-sm text-gray-600">
					<li class="flex items-start gap-3"><i class="fas fa-circle text-brand-blue text-[7px] mt-2"></i><?php esc_html_e( "Taux d'ouverture élevé de l'économie et forte sensibilité aux échanges extérieurs.", 'crades-theme' ); ?></li>
					<li class="flex items-start gap-3"><i class="fas fa-circle text-brand-blue text-[7px] mt-2"></i><?php esc_html_e( 'Besoin de suivre les normes, les risques, les prix, les stocks et les positions concurrentielles.', 'crades-theme' ); ?></li>
					<li class="flex items-start gap-3"><i class="fas fa-circle text-brand-blue text-[7px] mt-2"></i><?php esc_html_e( "Nécessité d'un outil d'analyse pour les engagements régionaux et multilatéraux.", 'crades-theme' ); ?></li>
				</ul>
			</div>
		</div>
	</div>
</section>

<section class="bg-brand-frost border-y border-brand-ice/50 py-16">
	<div class="max-w-6xl mx-auto px-4 sm:px-6">
		<div class="flex items-center justify-between gap-4 mb-8">
			<div>
				<span class="text-xs font-semibold text-brand-gold uppercase tracking-widest"><?php esc_html_e( 'Missions generales', 'crades-theme' ); ?></span>
				<h2 class="font-display text-xl text-gray-800 mt-3"><?php esc_html_e( "Le CRADES au service de l'intelligence economique", 'crades-theme' ); ?></h2>
			</div>
		</div>
		<div class="grid sm:grid-cols-2 gap-4">
			<?php foreach ( $missions as $mission ) : ?>
				<div class="bg-white rounded-xl p-5 border border-brand-ice/50">
					<div class="w-10 h-10 rounded-lg bg-brand-blue/10 flex items-center justify-center mb-3">
						<i class="fas <?php echo esc_attr( $mission['icon'] ); ?> text-brand-blue"></i>
					</div>
					<h3 class="text-sm font-semibold text-gray-800"><?php echo esc_html( $mission['title'] ); ?></h3>
					<p class="text-xs text-gray-500 mt-1 leading-relaxed"><?php echo esc_html( $mission['desc'] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="py-16">
	<div class="max-w-6xl mx-auto px-4 sm:px-6">
		<div class="grid lg:grid-cols-2 gap-8 items-start">
			<div>
				<span class="text-xs font-semibold text-brand-gold uppercase tracking-widest"><?php esc_html_e( "Champ d'action", 'crades-theme' ); ?></span>
				<h2 class="font-display text-xl text-gray-800 mt-3"><?php esc_html_e( 'Les grandes questions suivies par le CRADES', 'crades-theme' ); ?></h2>
				<div class="mt-5 space-y-4 text-sm text-gray-500 leading-relaxed">
					<p><?php esc_html_e( "Le centre assure une veille sur l'environnement commercial national et international, l'évolution des marchés, les contraintes réglementaires et les opportunités liées aux accords commerciaux.", 'crades-theme' ); ?></p>
					<p><?php esc_html_e( "Il contribue également au suivi des performances commerciales de l'économie et à l'analyse du système commercial au sein des espaces régionaux et multilatéraux.", 'crades-theme' ); ?></p>
				</div>
			</div>
			<div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
				<h3 class="text-sm font-semibold text-gray-800 mb-4"><?php esc_html_e( 'Espaces de suivi', 'crades-theme' ); ?></h3>
				<div class="flex flex-wrap gap-3">
					<?php foreach ( $markets as $market ) : ?>
						<span class="px-4 py-2 rounded-full bg-brand-frost text-brand-blue text-xs font-semibold border border-brand-ice/50"><?php echo esc_html( $market ); ?></span>
					<?php endforeach; ?>
				</div>
				<p class="text-xs text-gray-500 mt-4 leading-relaxed"><?php esc_html_e( 'La veille couvre notamment les données sur les concurrents effectifs et potentiels, la demande, l’offre, les stocks, les prix et les risques de marché.', 'crades-theme' ); ?></p>
			</div>
		</div>
	</div>
</section>

<section class="bg-white py-16">
	<div class="max-w-6xl mx-auto px-4 sm:px-6">
		<div class="text-center mb-10">
			<span class="text-xs font-semibold text-brand-gold uppercase tracking-widest"><?php esc_html_e( 'Organisation', 'crades-theme' ); ?></span>
			<h2 class="font-display text-xl text-gray-800 mt-3"><?php esc_html_e( "Une organisation simple au service de l'analyse", 'crades-theme' ); ?></h2>
		</div>
		<div class="grid md:grid-cols-3 gap-4">
			<?php foreach ( $divisions as $division ) : ?>
				<div class="rounded-xl border border-brand-ice/50 p-5 bg-brand-frost/40">
					<div class="text-xs font-bold text-brand-blue uppercase tracking-wider mb-2"><?php echo esc_html( $division['title'] ); ?></div>
					<h3 class="text-sm font-semibold text-gray-800 leading-snug"><?php echo esc_html( $division['label'] ); ?></h3>
					<p class="text-xs text-gray-500 mt-2 leading-relaxed"><?php echo esc_html( $division['desc'] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="bg-white py-16">
	<div class="max-w-6xl mx-auto px-4 sm:px-6">
		<div class="mb-10 text-center" style="font-family: 'Times New Roman', Times, serif;">
			<h2 class="mt-3 text-2xl font-semibold text-black leading-tight">
				<?php esc_html_e( 'ARRÊTÉ PORTANT CRÉATION ET ORGANISATION', 'crades-theme' ); ?><br>
				<?php esc_html_e( 'DU CENTRE DE RECHERCHES, D’ANALYSES DES', 'crades-theme' ); ?><br>
				<?php esc_html_e( 'ÉCHANGES ET STATISTIQUES (CRADES)', 'crades-theme' ); ?>
			</h2>
		</div>
		<div class="w-full">
			<object data="<?php echo esc_url( $pdf_url ); ?>" type="application/pdf" class="block w-full h-[92vh]">
				<div class="text-center text-sm text-gray-600 py-10" style="font-family: 'Times New Roman', Times, serif;">
					<p class="mb-3"><?php esc_html_e( 'Votre navigateur ne peut pas afficher le PDF intégré.', 'crades-theme' ); ?></p>
					<a href="<?php echo esc_url( $pdf_url ); ?>" class="inline-flex items-center rounded-full bg-brand-blue px-4 py-2 text-white text-xs font-semibold hover:bg-brand-navy transition-colors">
						<?php esc_html_e( 'Ouvrir le PDF', 'crades-theme' ); ?>
					</a>
				</div>
			</object>
		</div>
	</div>
</section>
<?php
get_footer();
