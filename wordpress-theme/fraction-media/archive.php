<?php
/**
 * Archives : rubriques, étiquettes, dates, galeries, œuvres.
 *
 * @package fraction-media
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<div class="container">
	<header class="rubrique-head">
		<span class="kicker"><?php esc_html_e( 'Archives', 'fraction-media' ); ?></span>
		<h1><?php the_archive_title(); ?></h1>
		<?php the_archive_description( '<p class="rubrique-desc">', '</p>' ); ?>
	</header>

	<?php if ( is_post_type_archive( 'galerie' ) ) : ?>
		<div class="galerie-grille">
			<?php while ( have_posts() ) : the_post(); ?>
				<a class="carte-photo" href="<?php echo esc_url( get_permalink() ); ?>">
					<?php fraction_media_image(); ?>
					<div class="carte-photo-meta">
						<h3><?php the_title(); ?></h3>
						<p><?php echo esc_html( fraction_media_date_fr() ); ?></p>
					</div>
				</a>
			<?php endwhile; ?>
		</div>
	<?php elseif ( is_post_type_archive( 'oeuvre' ) ) : ?>
		<div class="grid-oeuvres">
			<?php while ( have_posts() ) : the_post(); ?>
				<figure class="carte-oeuvre">
					<a href="<?php echo esc_url( get_permalink() ); ?>"><?php fraction_media_image(); ?></a>
					<figcaption>
						<strong><a href="<?php echo esc_url( get_permalink() ); ?>"><?php the_title(); ?></a></strong>
						<span><?php echo esc_html( get_post_meta( get_the_ID(), '_fraction_artiste', true ) ); ?></span>
					</figcaption>
				</figure>
			<?php endwhile; ?>
		</div>
	<?php else : ?>
		<div class="grid-articles">
			<?php
			if ( have_posts() ) :
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/carte' );
				endwhile;
			else :
				?>
				<p class="empty-note"><?php esc_html_e( 'Aucun contenu dans cette rubrique.', 'fraction-media' ); ?></p>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<?php
	the_posts_pagination(
		array(
			'prev_text' => '←',
			'next_text' => '→',
		)
	);
	?>
</div>
<?php
get_footer();
