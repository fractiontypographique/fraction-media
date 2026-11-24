<?php
/**
 * Article.
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
			<?php
			$rubriques = get_the_category();
			if ( $rubriques ) :
				?>
				<a class="kicker" href="<?php echo esc_url( get_category_link( $rubriques[0] ) ); ?>"><?php echo esc_html( $rubriques[0]->name ); ?></a>
			<?php endif; ?>
			<h1><?php the_title(); ?></h1>
			<?php if ( fraction_media_chapo() ) : ?>
				<p class="article-chapo"><?php echo esc_html( fraction_media_chapo() ); ?></p>
			<?php endif; ?>
			<p class="byline">
				<?php the_author(); ?> <span class="byline-sep">·</span> <?php echo esc_html( fraction_media_date_fr() ); ?> <span class="byline-sep">·</span> <?php echo esc_html( fraction_media_temps_lecture() ); ?> min <?php esc_html_e( 'de lecture', 'fraction-media' ); ?>
			</p>
		</header>

		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="article-couverture">
				<?php the_post_thumbnail( 'fraction-une' ); ?>
				<?php
				$legende = get_the_post_thumbnail_caption();
				$credit  = get_post_meta( get_the_ID(), '_fraction_credit', true );
				if ( $legende || $credit ) :
					?>
					<figcaption>
						<?php echo esc_html( $legende ); ?>
						<?php if ( $credit ) : ?>
							<?php echo $legende ? ' — ' : ''; ?><?php echo esc_html( $credit ); ?>
						<?php endif; ?>
					</figcaption>
				<?php endif; ?>
			</figure>
		<?php endif; ?>

		<div class="article-corps">
			<?php the_content(); ?>
		</div>

		<?php
		$etiquettes = get_the_tags();
		if ( $etiquettes ) :
			?>
			<ul class="article-tags">
				<?php foreach ( $etiquettes as $etiquette ) : ?>
					<li><a class="tag" href="<?php echo esc_url( get_tag_link( $etiquette ) ); ?>">#<?php echo esc_html( $etiquette->name ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<?php
		$section = $rubriques ? $rubriques[0] : null;
		$lies    = $section
			? get_posts(
				array(
					'category__in' => array( $section->term_id ),
					'post__not_in' => array( get_the_ID() ),
					'numberposts'  => 3,
					'post_status'  => 'publish',
				)
			)
			: array();
		if ( $lies ) :
			?>
			<section class="section-lecture">
				<h2 class="section-titre"><?php esc_html_e( 'À lire aussi', 'fraction-media' ); ?></h2>
				<div class="grid-articles">
					<?php
					global $post;
					foreach ( $lies as $post ) :
						setup_postdata( $post );
						get_template_part( 'template-parts/carte' );
					endforeach;
					wp_reset_postdata();
					?>
				</div>
			</section>
		<?php endif; ?>
	</article>
	<?php
endwhile;

get_footer();
