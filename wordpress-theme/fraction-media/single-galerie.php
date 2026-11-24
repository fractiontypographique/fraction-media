<?php
/**
 * Galerie photo.
 *
 * @package fraction-media
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article class="container">
		<header class="article-head">
			<span class="kicker"><?php esc_html_e( 'Photographie', 'fraction-media' ); ?></span>
			<h1><?php the_title(); ?></h1>
			<?php if ( fraction_media_chapo() ) : ?>
				<p class="article-chapo"><?php echo esc_html( fraction_media_chapo() ); ?></p>
			<?php endif; ?>
			<p class="byline">
				<?php the_author(); ?> <span class="byline-sep">·</span> <?php echo esc_html( fraction_media_date_fr() ); ?>
			</p>
		</header>

		<?php
		$photos = get_posts(
			array(
				'post_type'      => 'attachment',
				'post_mime_type' => 'image',
				'post_status'    => 'inherit',
				'post_parent'    => get_the_ID(),
				'numberposts'    => 60,
				'orderby'        => 'menu_order',
				'order'          => 'ASC',
			)
		);
		if ( $photos ) :
			?>
			<div class="galerie-grille">
				<?php foreach ( $photos as $photo ) : ?>
					<figure class="photo-item">
						<?php echo wp_get_attachment_image( $photo->ID, 'large' ); ?>
						<figcaption>
							<?php echo esc_html( $photo->post_excerpt ); ?>
							<?php
							$credit = get_post_meta( $photo->ID, '_fraction_credit', true );
							if ( $credit ) {
								echo ' — ' . esc_html( $credit );
							}
							?>
						</figcaption>
					</figure>
				<?php endforeach; ?>
			</div>
		<?php elseif ( has_post_thumbnail() ) : ?>
			<figure class="photo-item galerie-grille">
				<?php the_post_thumbnail( 'large' ); ?>
			</figure>
		<?php else : ?>
			<p class="empty-note"><?php esc_html_e( 'Ajoutez des photos à cette galerie via « Ajouter un média » dans l’éditeur.', 'fraction-media' ); ?></p>
		<?php endif; ?>

		<div class="article-corps">
			<?php the_content(); ?>
		</div>
	</article>
	<?php
endwhile;

get_footer();
