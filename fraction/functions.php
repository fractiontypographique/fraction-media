<?php
/**
 * Fraction - fonctions du theme.
 * Theme FSE minimal : tout passe par theme.json et les templates HTML.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

add_action( 'after_setup_theme', function () {
  load_theme_textdomain( 'fraction', get_template_directory() . '/languages' );
  add_theme_support( 'responsive-embeds' );
  add_theme_support( 'editor-styles' );
  add_editor_style( 'style.css' );
} );