<?php
/**
 * Betnero: tema a blocchi.
 *
 * @package betnero
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BETNERO_THEME_VERSION', '0.5.3' );

require_once get_theme_file_path( 'inc/blocks.php' );
require_once get_theme_file_path( 'inc/academy.php' );

/**
 * Supporti del tema.
 */
add_action( 'after_setup_theme', function () {
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'post-thumbnails' );
	add_editor_style( 'assets/css/theme.css' );
} );

/**
 * Font Sen: il file e' gia' nella libreria media del sito (caricato nel 2025).
 * Lo dichiariamo da qui perche' l'indirizzo cambia tra staging e sito vero.
 */
function betnero_font_face_css() {
	$url = content_url( 'uploads/2025/04/Sen-VariableFont_wght.ttf' );
	return '@font-face{font-family:"Sen";src:url("' . esc_url( $url ) . '") format("truetype");font-weight:400 800;font-style:normal;font-display:swap;}';
}

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style(
		'betnero-theme',
		get_theme_file_uri( 'assets/css/theme.css' ),
		array(),
		BETNERO_THEME_VERSION
	);
	wp_add_inline_style( 'betnero-theme', betnero_font_face_css() );
} );

add_action( 'enqueue_block_editor_assets', function () {
	wp_register_style( 'betnero-editor-font', false, array(), BETNERO_THEME_VERSION );
	wp_enqueue_style( 'betnero-editor-font' );
	wp_add_inline_style( 'betnero-editor-font', betnero_font_face_css() );
} );

add_action( 'wp_head', function () {
	echo '<link rel="preload" href="' . esc_url( content_url( 'uploads/2025/04/Sen-VariableFont_wght.ttf' ) ) . '" as="font" type="font/ttf" crossorigin>' . "\n";
}, 1 );

/**
 * Categoria di pattern del tema.
 */
add_action( 'init', function () {
	register_block_pattern_category( 'betnero', array( 'label' => 'Betnero' ) );
} );

/**
 * Shortcode di ripiego per la sidebar: se lo snippet della toplist non c'e',
 * la sidebar resta vuota invece di mostrare "[bn_toplist_sidebar]".
 */
add_action( 'init', function () {
	if ( ! shortcode_exists( 'bn_toplist_sidebar' ) ) {
		add_shortcode( 'bn_toplist_sidebar', '__return_empty_string' );
	}
}, 99 );

/* --------------------------------------------------------------------------
 * TRANSIZIONE DA ELEMENTOR (temporaneo, da rimuovere a migrazione finita)
 * ----------------------------------------------------------------------- */

/**
 * Il Theme Builder di Elementor non deve sostituire i template di articoli,
 * archivi e 404: li gestisce questo tema. Le pagine fatte con Elementor
 * continuano a funzionare come contenuto.
 */
add_filter( 'elementor/theme/need_override_location', function ( $need, $location ) {
	if ( in_array( $location, array( 'single', 'archive' ), true ) ) {
		return false;
	}
	return $need;
}, 10, 2 );

/**
 * CSS aggiuntivo del vecchio tema (Hello Elementor): serve alle pagine
 * Elementor e ai campi JetEngine (bottoni "Vai al sito", "Recensione", ecc.).
 */
add_action( 'wp_head', function () {
	if ( ! function_exists( 'wp_get_custom_css' ) ) {
		return;
	}
	$css = wp_get_custom_css( 'hello-elementor' );
	if ( $css ) {
		echo '<style id="betnero-legacy-css">' . wp_strip_all_tags( $css ) . '</style>' . "\n";
	}
}, 101 );

/**
 * Reset di base di Hello Elementor, se il vecchio tema e' ancora installato.
 */
add_action( 'wp_enqueue_scripts', function () {
	$reset = get_theme_root() . '/hello-elementor/assets/css/reset.css';
	if ( file_exists( $reset ) ) {
		wp_enqueue_style( 'betnero-legacy-reset', get_theme_root_uri() . '/hello-elementor/assets/css/reset.css', array(), BETNERO_THEME_VERSION );
	}
}, 5 );
