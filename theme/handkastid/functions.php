<?php
/**
 * Handkastið – grunnstillingar þema.
 */

defined( 'ABSPATH' ) || exit;

define( 'HK_VERSION', '0.1.2' );

require get_template_directory() . '/inc/helpers.php';
require get_template_directory() . '/inc/ads.php';
require get_template_directory() . '/inc/games.php';
require get_template_directory() . '/inc/customizer.php';

add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', [ 'search-form', 'gallery', 'caption', 'style', 'script' ] );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );

	add_image_size( 'hk-hero', 1340, 754, true );
	add_image_size( 'hk-card', 600, 400, true );

	register_nav_menus( [
		'primary'    => 'Aðalvalmynd',
		'categories' => 'Flokkar (blá rönd)',
		'footer'     => 'Fótur',
	] );
} );

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style(
		'hk-fonts',
		'https://fonts.googleapis.com/css2?family=Big+Shoulders+Display:wght@600;800;900&family=Libre+Franklin:ital,wght@0,400;0,500;0,600;0,700;1,400&family=JetBrains+Mono:wght@500;700&display=swap',
		[],
		null
	);
	wp_enqueue_style( 'hk-main', get_template_directory_uri() . '/assets/css/main.css', [ 'hk-fonts' ], HK_VERSION );
	wp_enqueue_script( 'hk-main', get_template_directory_uri() . '/assets/js/main.js', [], HK_VERSION, [ 'strategy' => 'defer', 'in_footer' => true ] );
} );

add_filter( 'excerpt_length', fn() => 28 );
add_filter( 'excerpt_more', fn() => '…' );
