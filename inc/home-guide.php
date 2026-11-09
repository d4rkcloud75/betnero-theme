<?php
/**
 * Titolo degli accordion (blocco Dettagli) come vero titolo H2, H3 o H4.
 *
 * Nell'editor, barra laterale del blocco Dettagli: "Titolo come"
 * (Predefinito, Testo semplice, H2, H3, H4). Nella pagina il testo del
 * sommario viene messo dentro il titolo scelto, cosi' Google vede la
 * struttura dei titoli anche dentro gli accordion.
 * Predefinito: H3 per le voci della "Guida Betnero" in home (classe
 * bn-guide-item), testo semplice per tutti gli altri accordion.
 *
 * @package betnero
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Attributo "bnHeading" sul blocco core/details (salvato nel commento del blocco).
add_filter( 'register_block_type_args', function ( $args, $name ) {
	if ( 'core/details' === $name ) {
		$args['attributes']              = isset( $args['attributes'] ) ? $args['attributes'] : array();
		$args['attributes']['bnHeading'] = array( 'type' => 'string', 'default' => '' );
	}
	return $args;
}, 10, 2 );

function betnero_details_heading_level( $block ) {
	$a   = isset( $block['attrs'] ) ? $block['attrs'] : array();
	$lvl = isset( $a['bnHeading'] ) ? (string) $a['bnHeading'] : '';
	if ( in_array( $lvl, array( 'h2', 'h3', 'h4' ), true ) ) {
		return $lvl;
	}
	if ( 'p' === $lvl ) {
		return '';
	}
	$cls = isset( $a['className'] ) ? (string) $a['className'] : '';
	return preg_match( '/(^|\s)bn-guide-item(\s|$)/', $cls ) ? 'h3' : '';
}

add_filter( 'render_block_core/details', function ( $html, $block ) {
	$tag = betnero_details_heading_level( $block );
	if ( ! $tag ) {
		return $html;
	}
	$cls = 'bn-sum-h' . ( false !== strpos( (string) ( $block['attrs']['className'] ?? '' ), 'bn-guide-item' ) ? ' bn-guide-title' : '' );
	return preg_replace( '#<summary>(.*?)</summary>#s', '<summary><' . $tag . ' class="' . $cls . '">$1</' . $tag . '></summary>', $html, 1 );
}, 10, 2 );

// Controllo "Titolo come" nell'editor.
add_action( 'enqueue_block_editor_assets', function () {
	wp_enqueue_script(
		'betnero-details-heading',
		get_theme_file_uri( 'assets/js/details-heading.js' ),
		array( 'wp-hooks', 'wp-element', 'wp-compose', 'wp-block-editor', 'wp-components' ),
		BETNERO_THEME_VERSION,
		true
	);
} );
