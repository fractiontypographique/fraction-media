<?php
/**
 * Fraction Média — thème éditorial (inspiration StreetPress + CNAP).
 *
 * @package fraction-media
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FRACTION_MEDIA_VERSION', '1.0.0' );

/**
 * Réglages du thème.
 */
function fraction_media_setup() {
	load_theme_textdomain( 'fraction-media', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 60,
			'width'       => 240,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	register_nav_menus(
		array(
			'principale'    => __( 'Menu principal', 'fraction-media' ),
			'pied_de_page'  => __( 'Menu pied de page', 'fraction-media' ),
		)
	);

	add_image_size( 'fraction-une', 1280, 960, true );
	add_image_size( 'fraction-carte', 800, 450, true );
	add_image_size( 'fraction-carre', 600, 600, true );
	add_image_size( 'fraction-portrait', 600, 750, true );

	add_editor_style( 'assets/css/editor.css' );
}
add_action( 'after_setup_theme', 'fraction_media_setup' );

/**
 * Styles et scripts.
 */
function fraction_media_assets() {
	wp_enqueue_style( 'fraction-media', get_stylesheet_uri(), array(), FRACTION_MEDIA_VERSION );
	wp_enqueue_script( 'fraction-media-nav', get_template_directory_uri() . '/assets/js/nav.js', array(), FRACTION_MEDIA_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'fraction_media_assets' );

/**
 * Longueur des extraits.
 *
 * @param int $length Longueur par défaut.
 * @return int
 */
function fraction_media_extrait_longueur( $length ) {
	return 28;
}
add_filter( 'excerpt_length', 'fraction_media_extrait_longueur' );

/**
 * Suffixe des extraits.
 *
 * @param string $more Suffixe par défaut.
 * @return string
 */
function fraction_media_extrait_suite( $more ) {
	return '…';
}
add_filter( 'excerpt_more', 'fraction_media_extrait_suite' );

/**
 * Types de contenus : Galeries photo et Œuvres d'art.
 */
function fraction_media_register_types() {
	register_post_type(
		'galerie',
		array(
			'labels'        => array(
				'name'               => __( 'Galeries photo', 'fraction-media' ),
				'singular_name'      => __( 'Galerie', 'fraction-media' ),
				'add_new_item'       => __( 'Ajouter une galerie', 'fraction-media' ),
				'edit_item'          => __( 'Modifier la galerie', 'fraction-media' ),
				'new_item'           => __( 'Nouvelle galerie', 'fraction-media' ),
				'view_item'          => __( 'Voir la galerie', 'fraction-media' ),
				'search_items'       => __( 'Rechercher des galeries', 'fraction-media' ),
				'not_found'          => __( 'Aucune galerie trouvée', 'fraction-media' ),
				'menu_name'          => __( 'Photographies', 'fraction-media' ),
			),
			'public'        => true,
			'has_archive'   => 'photographies',
			'menu_position' => 5,
			'menu_icon'     => 'dashicons-format-gallery',
			'rewrite'       => array( 'slug' => 'photographie' ),
			'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'author' ),
			'show_in_rest'  => true,
		)
	);

	register_post_type(
		'oeuvre',
		array(
			'labels'        => array(
				'name'               => __( 'Œuvres d’art', 'fraction-media' ),
				'singular_name'      => __( 'Œuvre', 'fraction-media' ),
				'add_new_item'       => __( 'Ajouter une œuvre', 'fraction-media' ),
				'edit_item'          => __( 'Modifier l’œuvre', 'fraction-media' ),
				'new_item'           => __( 'Nouvelle œuvre', 'fraction-media' ),
				'view_item'          => __( 'Voir l’œuvre', 'fraction-media' ),
				'search_items'       => __( 'Rechercher des œuvres', 'fraction-media' ),
				'not_found'          => __( 'Aucune œuvre trouvée', 'fraction-media' ),
				'menu_name'          => __( 'Œuvres d’art', 'fraction-media' ),
			),
			'public'        => true,
			'has_archive'   => 'oeuvres',
			'menu_position' => 6,
			'menu_icon'     => 'dashicons-art',
			'rewrite'       => array( 'slug' => 'oeuvre' ),
			'supports'      => array( 'title', 'editor', 'thumbnail' ),
			'show_in_rest'  => true,
		)
	);
}
add_action( 'init', 'fraction_media_register_types' );

/**
 * Boîtes à méta : chapô et crédit image (articles), notices d’œuvre.
 */
function fraction_media_meta_boxes() {
	add_meta_box(
		'fraction_media_article',
		__( 'Infos éditoriales — Fraction Média', 'fraction-media' ),
		'fraction_media_article_meta_box',
		'post',
		'normal',
		'high'
	);

	add_meta_box(
		'fraction_media_oeuvre',
		__( 'Notice de l’œuvre', 'fraction-media' ),
		'fraction_media_oeuvre_meta_box',
		'oeuvre',
		'side'
	);
}
add_action( 'add_meta_boxes', 'fraction_media_meta_boxes' );

/**
 * Formulaire méta des articles.
 *
 * @param WP_Post $post Article courant.
 */
function fraction_media_article_meta_box( $post ) {
	wp_nonce_field( 'fraction_media_meta', 'fraction_media_nonce' );
	$chapo  = get_post_meta( $post->ID, '_fraction_chapo', true );
	$credit = get_post_meta( $post->ID, '_fraction_credit', true );
	?>
	<p>
		<label for="fraction_chapo"><strong><?php esc_html_e( 'Chapô — texte d’introduction affiché sous le titre', 'fraction-media' ); ?></strong></label><br />
		<textarea id="fraction_chapo" name="fraction_chapo" rows="4" style="width:100%;"><?php echo esc_textarea( $chapo ); ?></textarea>
	</p>
	<p>
		<label for="fraction_credit"><strong><?php esc_html_e( 'Crédit de l’image de couverture', 'fraction-media' ); ?></strong></label><br />
		<input type="text" id="fraction_credit" name="fraction_credit" value="<?php echo esc_attr( $credit ); ?>" style="width:100%;" />
	</p>
	<?php
}

/**
 * Formulaire méta des œuvres.
 *
 * @param WP_Post $post Œuvre courante.
 */
function fraction_media_oeuvre_meta_box( $post ) {
	wp_nonce_field( 'fraction_media_meta', 'fraction_media_nonce' );
	$artiste   = get_post_meta( $post->ID, '_fraction_artiste', true );
	$annee     = get_post_meta( $post->ID, '_fraction_annee', true );
	$technique = get_post_meta( $post->ID, '_fraction_technique', true );
	?>
	<p>
		<label for="fraction_artiste"><strong><?php esc_html_e( 'Artiste', 'fraction-media' ); ?></strong></label><br />
		<input type="text" id="fraction_artiste" name="fraction_artiste" value="<?php echo esc_attr( $artiste ); ?>" style="width:100%;" />
	</p>
	<p>
		<label for="fraction_annee"><strong><?php esc_html_e( 'Année', 'fraction-media' ); ?></strong></label><br />
		<input type="text" id="fraction_annee" name="fraction_annee" value="<?php echo esc_attr( $annee ); ?>" style="width:100%;" />
	</p>
	<p>
		<label for="fraction_technique"><strong><?php esc_html_e( 'Technique', 'fraction-media' ); ?></strong></label><br />
		<input type="text" id="fraction_technique" name="fraction_technique" value="<?php echo esc_attr( $technique ); ?>" style="width:100%;" />
	</p>
	<p>
		<?php esc_html_e( 'Utilisez l’image mise en avant comme photo de l’œuvre, et le contenu principal pour sa description.', 'fraction-media' ); ?>
	</p>
	<?php
}

/**
 * Enregistre les méta.
 *
 * @param int $post_id Identifiant du contenu.
 */
function fraction_media_save_meta( $post_id ) {
	if ( ! isset( $_POST['fraction_media_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['fraction_media_nonce'] ) ), 'fraction_media_meta' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$champs = array(
		'fraction_chapo'     => '_fraction_chapo',
		'fraction_credit'    => '_fraction_credit',
		'fraction_artiste'   => '_fraction_artiste',
		'fraction_annee'     => '_fraction_annee',
		'fraction_technique' => '_fraction_technique',
	);

	foreach ( $champs as $champ => $meta ) {
		if ( isset( $_POST[ $champ ] ) ) {
			update_post_meta( $post_id, $meta, sanitize_textarea_field( wp_unslash( $_POST[ $champ ] ) ) );
		}
	}
}
add_action( 'save_post', 'fraction_media_save_meta' );

/**
 * Chapô : méta dédiée, sinon extrait.
 *
 * @param WP_Post|int|null $post Article.
 * @return string
 */
function fraction_media_chapo( $post = null ) {
	$post  = get_post( $post );
	$chapo = $post ? get_post_meta( $post->ID, '_fraction_chapo', true ) : '';
	if ( '' === $chapo && $post ) {
		$chapo = $post->post_excerpt;
	}
	return $chapo;
}

/**
 * Temps de lecture en minutes (200 mots / minute).
 *
 * @param WP_Post|int|null $post Article.
 * @return int
 */
function fraction_media_temps_lecture( $post = null ) {
	$post  = get_post( $post );
	$temps = 1;
	if ( $post ) {
		preg_match_all( '/[\p{L}\p{N}]+/u', wp_strip_all_tags( $post->post_content ), $occurrences );
		$mots  = is_countable( $occurrences[0] ) ? count( $occurrences[0] ) : 0;
		$temps = max( 1, (int) ceil( $mots / 200 ) );
	}
	return $temps;
}

/**
 * Date en français.
 *
 * @param WP_Post|int|null $post Article.
 * @return string
 */
function fraction_media_date_fr( $post = null ) {
	return get_the_date( 'j F Y', $post );
}

/**
 * URL de la marque rouge (SVG fourni).
 *
 * @return string
 */
function fraction_media_marque_url() {
	return get_template_directory_uri() . '/assets/img/marque-fraction.svg';
}

/**
 * Bloc de repli pour les contenus sans image mise en avant.
 */
function fraction_media_fallback() {
	printf(
		'<div class="media-fallback" aria-hidden="true"><img class="media-marque" src="%1$s" alt="" /></div>',
		esc_url( fraction_media_marque_url() )
	);
}

/**
 * Favicon de secours : la marque, si aucune icône de site n'est définie.
 */
function fraction_media_icone() {
	if ( function_exists( 'has_site_icon' ) && has_site_icon() ) {
		return;
	}
	printf( '<link rel="icon" href="%s" />', esc_url( fraction_media_marque_url() ) );
}
add_action( 'wp_head', 'fraction_media_icone' );

/**
 * Remplace la vignette manquante par un bloc sombre avec la marque.
 *
 * @param int|null $post_id Identifiant du contenu (facultatif).
 */
function fraction_media_image( $post_id = null ) {
	if ( has_post_thumbnail( $post_id ) ) {
		echo get_the_post_thumbnail( $post_id, 'fraction-carte', array( 'class' => 'carte-img' ) );
		return;
	}
	fraction_media_fallback();
}

/**
 * Contenu de secours pour le menu principal : rubriques + archives.
 */
function fraction_media_nav_defaut() {
	$liens = array();
	$categories = get_categories( array( 'number' => 6 ) );
	foreach ( $categories as $categorie ) {
		$liens[] = '<a href="' . esc_url( get_category_link( $categorie ) ) . '">' . esc_html( $categorie->name ) . '</a>';
	}
	$liens[] = '<a href="' . esc_url( get_post_type_archive_link( 'galerie' ) ) . '">' . esc_html__( 'Photographies', 'fraction-media' ) . '</a>';
	$liens[] = '<a href="' . esc_url( get_post_type_archive_link( 'oeuvre' ) ) . '">' . esc_html__( 'Œuvres', 'fraction-media' ) . '</a>';
	echo implode( "\n", $liens ); // phpcs:ignore WordPress.Security.EscapeOutput
}
