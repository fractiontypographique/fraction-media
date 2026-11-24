<?php
/**
 * Gabarit par défaut : liste des articles.
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
		<span class="kicker"><?php esc_html_e( 'Tous les articles', 'fraction-media' ); ?></span>
		<h1><?php echo esc_html( get_bloginfo( 'name' ) ); ?></h1>
		<p class="rubrique-desc"><?php echo esc_html( get_bloginfo( 'description' ) ); ?></p>
	</header>

	<div class="grid-articles">
		<?php
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/carte' );
			endwhile;
		else :
			?>
			<p class="empty-note"><?php esc_html_e( 'Aucun article pour le moment.', 'fraction-media' ); ?></p>
		<?php endif; ?>
	</div>

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
