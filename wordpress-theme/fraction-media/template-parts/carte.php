<?php
/**
 * Carte d'article, utilisée dans les boucles.
 *
 * @package fraction-media
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article class="carte">
	<a class="carte-rubrique" href="<?php echo esc_url( get_permalink() ); ?>">
		<?php
		$rubriques = get_the_category();
		if ( $rubriques ) {
			echo esc_html( $rubriques[0]->name );
		}
		?>
	</a>
	<?php fraction_media_image(); ?>
	<h3 class="carte-titre"><a href="<?php echo esc_url( get_permalink() ); ?>"><?php the_title(); ?></a></h3>
	<?php if ( fraction_media_chapo() ) : ?>
		<p class="carte-chapo"><?php echo esc_html( wp_trim_words( fraction_media_chapo(), 22 ) ); ?></p>
	<?php endif; ?>
	<p class="byline">
		<?php the_author(); ?> <span class="byline-sep">·</span> <?php echo esc_html( fraction_media_date_fr() ); ?> <span class="byline-sep">·</span> <?php echo esc_html( fraction_media_temps_lecture() ); ?> min
	</p>
</article>
