<?php
/**
 * Articolo precedente e successivo (0.9.4): due schede affiancate sotto ogni
 * articolo del blog, con miniatura, etichetta e titolo. Su mobile una sotto l'altra.
 *
 * @package betnero
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function betnero_post_nav_card( $post, $dir ) {
	if ( ! $post ) {
		return '<span class="bn-pn-empty" aria-hidden="true"></span>';
	}
	$label = 'prev' === $dir ? 'Articolo precedente' : 'Articolo successivo';
	$arrow = 'prev' === $dir ? '&larr;' : '&rarr;';
	$thumb = get_the_post_thumbnail( $post, 'thumbnail', array( 'class' => 'bn-pn-img', 'loading' => 'lazy', 'alt' => '' ) );
	return '<a class="bn-pn-card bn-pn-' . $dir . '" href="' . esc_url( get_permalink( $post ) ) . '" rel="' . ( 'prev' === $dir ? 'prev' : 'next' ) . '">'
		. ( $thumb ? '<span class="bn-pn-thumb">' . $thumb . '</span>' : '' )
		. '<span class="bn-pn-text"><span class="bn-pn-label">' . ( 'prev' === $dir ? $arrow . ' ' : '' ) . $label . ( 'next' === $dir ? ' ' . $arrow : '' ) . '</span>'
		. '<span class="bn-pn-title">' . esc_html( get_the_title( $post ) ) . '</span></span></a>';
}

add_shortcode( 'bn_post_nav', function () {
	if ( ! is_singular( 'post' ) ) {
		return '';
	}
	$prev = get_previous_post();
	$next = get_next_post();
	if ( ! $prev && ! $next ) {
		return '';
	}
	return '<nav class="bn-pn" aria-label="Altri articoli">' . betnero_post_nav_card( $prev, 'prev' ) . betnero_post_nav_card( $next, 'next' ) . '</nav>';
} );
