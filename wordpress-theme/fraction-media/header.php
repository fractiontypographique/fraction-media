<?php
/**
 * En-tête du site.
 *
 * @package fraction-media
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="screen-reader-text" href="#contenu"><?php esc_html_e( 'Aller au contenu', 'fraction-media' ); ?></a>

<header class="masthead">
	<div class="container masthead-inner">
		<?php if ( has_custom_logo() ) : ?>
			<?php the_custom_logo(); ?>
		<?php else : ?>
			<a class="logo" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( get_bloginfo( 'name' ) ); ?> <img class="logo-marque" src="<?php echo esc_url( fraction_media_marque_url() ); ?>" alt="" /></a>
		<?php endif; ?>

		<button class="nav-toggle" aria-expanded="false" aria-controls="menu-principal">☰</button>

		<nav id="menu-principal" class="nav" aria-label="<?php esc_attr_e( 'Menu principal', 'fraction-media' ); ?>">
			<?php
			if ( has_nav_menu( 'principale' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'principale',
						'container'      => '',
						'items_wrap'     => '%3$s',
						'depth'          => 1,
					)
				);
			} else {
				fraction_media_nav_defaut();
			}
			?>
		</nav>
	</div>
</header>

<main id="contenu">
