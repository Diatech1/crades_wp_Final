<?php
/**
 * Exact Contact page ported from the Hono app.
 *
 * @package CRADES_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<section class="bg-brand-navy py-16 lg:py-20">
	<div class="max-w-6xl mx-auto px-4 sm:px-6">
		<nav class="text-xs text-gray-400 mb-4">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hover:text-white"><?php esc_html_e( 'Accueil', 'crades-theme' ); ?></a>
			<span class="mx-2">/</span>
			<span class="text-gray-300"><?php esc_html_e( 'Contact', 'crades-theme' ); ?></span>
		</nav>
		<h1 class="font-display text-2xl lg:text-3xl text-white"><?php esc_html_e( 'Contact', 'crades-theme' ); ?></h1>
		<p class="text-gray-400 mt-2 max-w-2xl text-sm leading-relaxed"><?php esc_html_e( 'Une question, une demande de données ou un partenariat ? Contactez-nous.', 'crades-theme' ); ?></p>
	</div>
</section>

<section class="py-12 bg-gray-50">
	<div class="max-w-5xl mx-auto px-4 sm:px-6">
		<div class="grid lg:grid-cols-[1fr_320px] gap-8">
			<div class="bg-white rounded-xl border border-gray-100 p-8">
				<h2 class="text-lg font-semibold text-gray-800 mb-6"><?php esc_html_e( 'Envoyez-nous un message', 'crades-theme' ); ?></h2>
				<form id="contactForm" class="space-y-5" onsubmit="event.preventDefault(); document.getElementById('contactStatus').className='text-sm py-2 px-3 rounded-lg bg-emerald-50 text-emerald-700'; document.getElementById('contactStatus').textContent='Message envoye avec succes. Nous vous repondrons dans les meilleurs delais.'; document.getElementById('contactStatus').classList.remove('hidden');">
					<div class="grid sm:grid-cols-2 gap-4">
						<div>
							<label class="block text-xs font-medium text-gray-600 mb-1"><?php esc_html_e( 'Nom complet *', 'crades-theme' ); ?></label>
							<input type="text" name="name" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue/20">
						</div>
						<div>
							<label class="block text-xs font-medium text-gray-600 mb-1"><?php esc_html_e( 'Email *', 'crades-theme' ); ?></label>
							<input type="email" name="email" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue/20">
						</div>
					</div>
					<div>
						<label class="block text-xs font-medium text-gray-600 mb-1"><?php esc_html_e( 'Organisation', 'crades-theme' ); ?></label>
						<input type="text" name="organization" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue/20">
					</div>
					<div>
						<label class="block text-xs font-medium text-gray-600 mb-1"><?php esc_html_e( 'Sujet', 'crades-theme' ); ?></label>
						<select name="subject" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-brand-blue bg-white">
							<option value=""><?php esc_html_e( 'Choisir...', 'crades-theme' ); ?></option>
							<option><?php esc_html_e( 'Demande de données', 'crades-theme' ); ?></option>
							<option><?php esc_html_e( 'Partenariat', 'crades-theme' ); ?></option>
							<option><?php esc_html_e( 'Question generale', 'crades-theme' ); ?></option>
							<option><?php esc_html_e( 'Signaler un probleme', 'crades-theme' ); ?></option>
						</select>
					</div>
					<div>
						<label class="block text-xs font-medium text-gray-600 mb-1"><?php esc_html_e( 'Message *', 'crades-theme' ); ?></label>
						<textarea name="message" rows="5" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue/20 resize-none"></textarea>
					</div>
					<div id="contactStatus" class="hidden text-sm py-2 px-3 rounded-lg"></div>
					<button type="submit" class="bg-brand-blue text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-brand-navy transition-colors">
						<?php esc_html_e( 'Envoyer', 'crades-theme' ); ?>
					</button>
				</form>
			</div>

			<aside class="space-y-6">
				<div class="bg-white rounded-xl border border-gray-100 p-6">
					<h3 class="text-sm font-semibold text-gray-800 mb-4"><?php esc_html_e( 'Coordonnées', 'crades-theme' ); ?></h3>
					<div class="space-y-4 text-sm">
						<div class="flex items-start gap-3">
							<i class="fas fa-map-marker-alt text-brand-blue mt-0.5"></i>
							<div class="text-gray-600"><?php esc_html_e( 'Sphere Ministerielle Habib THIAM,', 'crades-theme' ); ?><br><?php esc_html_e( 'Batiment C, Diamniadio', 'crades-theme' ); ?></div>
						</div>
						<div class="flex items-center gap-3">
							<i class="fas fa-phone text-brand-blue"></i>
							<span class="text-gray-600"><?php esc_html_e( '33 869 21 20 / 33 824 45 12', 'crades-theme' ); ?></span>
						</div>
						<div class="flex items-center gap-3">
							<i class="fas fa-envelope text-brand-blue"></i>
							<a href="mailto:info@crades.sn" class="text-brand-blue hover:underline">info@crades.sn</a>
						</div>
						<div class="flex items-center gap-3">
							<i class="fas fa-globe text-brand-blue"></i>
							<a href="https://www.crades.sn" target="_blank" rel="noreferrer" class="text-brand-blue hover:underline">www.crades.sn</a>
						</div>
					</div>
				</div>
				<div class="bg-brand-frost rounded-xl p-6">
					<h3 class="text-sm font-semibold text-gray-800 mb-2"><?php esc_html_e( 'Horaires', 'crades-theme' ); ?></h3>
					<p class="text-xs text-gray-500"><?php esc_html_e( 'Lundi - Vendredi : 8h00 - 17h00', 'crades-theme' ); ?></p>
					<p class="text-xs text-gray-400 mt-1"><?php esc_html_e( 'Samedi, Dimanche : Ferme', 'crades-theme' ); ?></p>
				</div>
			</aside>
		</div>
	</div>
</section>
<?php
get_footer();
