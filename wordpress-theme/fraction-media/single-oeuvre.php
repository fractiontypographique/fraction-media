<?php
/**
 * Œuvre d'art — présentation muséale.
 *
 * @package fraction-media
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();
	$artiste   = get_post_meta( get_the_ID(), '_fraction_artiste', true );
	$annee     = get_post_meta( get_the_ID(), '_fraction_annee', true );
	$technique = get_post_meta( get_the_ID(), '_fraction_technique', true );
	?>
	<article class="container oeuvre-layout">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'full' ); ?>
		<?php else : ?>
			<div class="media-fallback" aria-hidden="true"><span>F</span></div>
		<?php endif; ?>

		<div>
			<dl class="oeuvre-notices">
				<dt><?php esc_html_e( 'Titre', 'fraction-media' ); ?></dt>
				<dd><?php the_title(); ?></dd>
				<?php if ( $artiste ) : ?>
					<dt><?php esc_html_e( 'Artiste', 'fraction-media' ); ?></dt>
					<dd><?php echo esc_html( $artiste ); ?></dd>
				<?php endif; ?>
				<?php if ( $annee ) : ?>
					<dt><?php esc_html_e( 'Année', 'fraction-media' ); ?></dt>
					<dd><?php echo esc_html( $annee ); ?></dd>
				<?php endif; ?>
				<?php if ( $technique ) : ?>
					<dt><?php esc_html_e( 'Technique', 'fraction-media' ); ?></dt>
					<dd><?php echo esc_html( $technique ); ?></dd>
				<?php endif; ?>
			</dl>

			<div class="article-corps">
				<?php the_content(); ?>
			</div>
		</div>
	</article>
	<?php
endwhile;

get_footer();
