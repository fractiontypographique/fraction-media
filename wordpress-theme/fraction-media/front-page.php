<?php
/**
 * Page d'accueil éditoriale : à la une, articles, photographies, œuvres.
 *
 * @package fraction-media
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$une_id = get_option( 'sticky_posts' );
$une    = array();
if ( ! empty( $une_id ) ) {
	$une = get_posts(
		array(
			'post__in'            => array( $une_id[0] ),
			'numberposts'         => 1,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
		)
	);
}
if ( empty( $une ) ) {
	$une = get_posts( array( 'numberposts' => 1, 'post_status' => 'publish' ) );
}
$une = $une ? $une[0] : null;

$exclus = $une ? array( $une->ID ) : array( 0 );
$derniers = get_posts(
	array(
		'numberposts' => 9,
		'post_status' => 'publish',
		'post__not_in' => $exclus,
	)
);

$galeries = get_posts(
	array(
		'numberposts' => 4,
		'post_type'   => 'galerie',
		'post_status' => 'publish',
	)
);

$oeuvres = get_posts(
	array(
		'numberposts' => 6,
		'post_type'   => 'oeuvre',
		'post_status' => 'publish',
	)
);
?>
<div class="container">

	<?php if ( $une ) : ?>
	<section class="une">
		<div class="une-inner">
			<div>
				<?php
				$rubriques = get_the_category( $une->ID );
				if ( $rubriques ) :
					?>
					<a class="kicker kicker-une" href="<?php echo esc_url( get_category_link( $rubriques[0] ) ); ?>"><?php echo esc_html( $rubriques[0]->name ); ?></a>
				<?php endif; ?>
				<h1 class="une-titre"><a href="<?php echo esc_url( get_permalink( $une ) ); ?>"><?php echo esc_html( get_the_title( $une ) ); ?></a></h1>
				<?php if ( fraction_media_chapo( $une ) ) : ?>
					<p class="une-chapo"><?php echo esc_html( fraction_media_chapo( $une ) ); ?></p>
				<?php endif; ?>
				<p class="byline">
					<?php echo esc_html( get_the_author_meta( 'display_name', $une->post_author ) ); ?>
					<span class="byline-sep">·</span> <?php echo esc_html( fraction_media_date_fr( $une ) ); ?>
					<span class="byline-sep">·</span> <?php echo esc_html( fraction_media_temps_lecture( $une ) ); ?> min
				</p>
			</div>
			<a class="une-media" href="<?php echo esc_url( get_permalink( $une ) ); ?>">
				<?php
				if ( has_post_thumbnail( $une ) ) {
					echo get_the_post_thumbnail( $une, 'fraction-une' );
				} else {
					fraction_media_fallback();
				}
				?>
			</a>
		</div>
	</section>
	<?php endif; ?>

	<section>
		<h2 class="section-titre"><?php esc_html_e( 'Derniers articles', 'fraction-media' ); ?></h2>
		<div class="grid-articles">
			<?php
			if ( $derniers ) :
				global $post;
				foreach ( $derniers as $post ) :
					setup_postdata( $post );
					get_template_part( 'template-parts/carte' );
				endforeach;
				wp_reset_postdata();
			else :
				?>
				<p class="empty-note"><?php esc_html_e( 'Aucun article pour le moment.', 'fraction-media' ); ?></p>
			<?php endif; ?>
		</div>
		<p class="section-lien"><a class="bouton" href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ? get_permalink( get_option( 'page_for_posts' ) ) : home_url( '/?post_type=post' ) ); ?>"><?php esc_html_e( 'Tous les articles', 'fraction-media' ); ?></a></p>
	</section>
</div>

<?php if ( $galeries ) : ?>
<section class="section-photos">
	<div class="container">
		<h2 class="section-titre"><?php esc_html_e( 'Photographie', 'fraction-media' ); ?></h2>
		<div class="grid-photos">
			<?php
			global $post;
			foreach ( $galeries as $post ) :
				setup_postdata( $post );
				?>
				<a class="carte-photo" href="<?php echo esc_url( get_permalink() ); ?>">
					<?php
					if ( has_post_thumbnail() ) {
						echo get_the_post_thumbnail( get_the_ID(), 'fraction-carre' );
					} else {
						fraction_media_fallback();
					}
					?>
					<div class="carte-photo-meta">
						<h3><?php echo esc_html( get_the_title() ); ?></h3>
						<p><?php echo esc_html( get_the_author_meta( 'display_name', $post->post_author ) ); ?></p>
					</div>
				</a>
			<?php endforeach; wp_reset_postdata(); ?>
		</div>
		<p class="section-lien"><a class="bouton" href="<?php echo esc_url( get_post_type_archive_link( 'galerie' ) ); ?>"><?php esc_html_e( 'Toutes les photographies', 'fraction-media' ); ?></a></p>
	</div>
</section>
<?php endif; ?>

<?php if ( $oeuvres ) : ?>
<div class="container">
	<section>
		<h2 class="section-titre"><?php esc_html_e( 'Œuvres d’art', 'fraction-media' ); ?></h2>
		<div class="grid-oeuvres">
			<?php
			global $post;
			foreach ( $oeuvres as $post ) :
				setup_postdata( $post );
				?>
				<figure class="carte-oeuvre">
					<a href="<?php echo esc_url( get_permalink() ); ?>">
						<?php
						if ( has_post_thumbnail() ) {
							echo get_the_post_thumbnail( get_the_ID(), 'fraction-portrait' );
						} else {
							fraction_media_fallback();
						}
						?>
					</a>
					<figcaption>
						<strong><a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a></strong>
						<span><?php echo esc_html( get_post_meta( get_the_ID(), '_fraction_artiste', true ) ); ?></span>
					</figcaption>
				</figure>
			<?php endforeach; wp_reset_postdata(); ?>
		</div>
		<p class="section-lien"><a class="bouton" href="<?php echo esc_url( get_post_type_archive_link( 'oeuvre' ) ); ?>"><?php esc_html_e( 'Toutes les œuvres', 'fraction-media' ); ?></a></p>
	</section>
</div>
<?php endif; ?>

<?php
get_footer();
