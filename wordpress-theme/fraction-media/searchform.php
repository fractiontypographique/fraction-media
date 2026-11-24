<?php
/**
 * Formulaire de recherche.
 *
 * @package fraction-media
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="recherche">
	<label class="screen-reader-text" for="s"><?php esc_html_e( 'Rechercher', 'fraction-media' ); ?></label>
	<input type="search" id="s" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Rechercher…', 'fraction-media' ); ?>" />
	<button class="bouton" type="submit"><?php esc_html_e( 'Rechercher', 'fraction-media' ); ?></button>
</form>
