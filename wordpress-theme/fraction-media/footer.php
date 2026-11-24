<?php
/**
 * Pied de page.
 *
 * @package fraction-media
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
</main>

<footer class="footer">
	<div class="container footer-inner">
		<div>
			<p class="footer-logo"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></p>
			<p class="footer-tagline"><?php echo esc_html( get_bloginfo( 'description' ) ); ?></p>
		</div>

		<div>
			<?php
			if ( has_nav_menu( 'pied_de_page' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'pied_de_page',
						'container'      => '',
						'menu_class'     => 'footer-nav',
						'depth'          => 1,
					)
				);
			} else {
				echo '<ul class="footer-nav">';
				wp_list_categories( array( 'title_li' => '', 'number' => 6 ) );
				echo '</ul>';
			}
			?>
		</div>

		<div>
			<p class="footer-contribute">
				<a href="<?php echo esc_url( get_bloginfo( 'rss2_url' ) ); ?>"><?php esc_html_e( 'Flux RSS', 'fraction-media' ); ?></a>
			</p>
		</div>
	</div>

	<div class="container footer-legal">
		© <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( get_bloginfo( 'name' ) ); ?> — <?php esc_html_e( 'Art, photographie et articles de fond', 'fraction-media' ); ?>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
