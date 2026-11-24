<?php
/**
 * Page statique (à propos, mentions, etc.).
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
	<article class="page-contenu">
		<span class="kicker"><?php esc_html_e( 'Fraction Média', 'fraction-media' ); ?></span>
		<h1 class="une-titre"><?php the_title(); ?></h1>
		<div class="article-corps">
			<?php the_content(); ?>
		</div>
	</article>
	<?php
endwhile;

get_footer();
