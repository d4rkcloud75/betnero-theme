<?php
/**
 * SEO: un solo H1 per articolo.
 * Il titolo dell'articolo e' gia' l'H1 del tema: gli H1 scritti dentro il testo
 * (rimasti dalle pagine Elementor) vengono mostrati come H2, senza toccare i contenuti.
 *
 * @package betnero
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'the_content', function ( $content ) {
	if ( ! is_singular( 'post' ) || false === stripos( $content, '<h1' ) ) {
		return $content;
	}
	return preg_replace( '#<h1(\s[^>]*)?>(.*?)</h1>#is', '<h2$1>$2</h2>', $content );
}, 20 );
