<?php
/**
 * Page introuvable.
 *
 * @package fraction-media
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<div class="page-contenu">
	<span class="kicker"><?php esc_html_e( 'Erreur 404', 'fraction-media' ); ?></span>
	<h1 class="une-titre"><?php esc_html_e( 'Cette page a disparu de la circulation', 'fraction-media' ); ?></h1>
	<p class="rubrique-desc"><?php esc_html_e( 'Le contenu recherché n’existe pas ou a été déplacé.', 'fraction-media' ); ?></p>
	<p class="section-lien"><a class="bouton" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Retour à l’accueil', 'fraction-media' ); ?></a></p>
	<?php get_search_form(); ?>
</div>
<?php
get_footer();
