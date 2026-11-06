<?php
/**
 * Home: sezione "Guida Betnero" con i testi della vecchia home in blocchi Dettagli
 * (classe bn-guide-item). Nella pagina il titolo di ogni voce diventa un H3,
 * cosi' resta un vero sottotitolo per Google; nell'editor resta testo semplice.
 *
 * @package betnero
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'render_block_core/details', function ( $html, $block ) {
	$cls = isset( $block['attrs']['className'] ) ? (string) $block['attrs']['className'] : '';
	if ( ! preg_match( '/(^|\s)bn-guide-item(\s|$)/', $cls ) ) {
		return $html;
	}
	return preg_replace( '#<summary>(.*?)</summary>#s', '<summary><h3 class="bn-guide-title">$1</h3></summary>', $html, 1 );
}, 10, 2 );
