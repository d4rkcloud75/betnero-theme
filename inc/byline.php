<?php
/**
 * Autore e data dell'ultima modifica sotto l'H1 di tutte le pagine (0.10.4),
 * come gia' negli articoli. Home esclusa.
 *
 * "Di Francesco Esposito · Aggiornato il 19 novembre 2026"
 * L'autore e' quello della pagina (riquadro Autore nell'editor), con link alla sua pagina.
 *
 * @package betnero
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function betnero_page_byline_html( $post_id ) {
	$post = get_post( $post_id );
	if ( ! $post ) {
		return '';
	}
	$author = get_userdata( (int) $post->post_author );
	$parts  = array();
	if ( $author ) {
		$parts[] = 'Di <a class="bn-byline-author" href="' . esc_url( get_author_posts_url( $author->ID ) ) . '" rel="author">' . esc_html( $author->display_name ) . '</a>';
	}
	$parts[] = 'Aggiornato il <time datetime="' . esc_attr( get_post_modified_time( 'c', true, $post ) ) . '">' . esc_html( mb_strtolower( get_post_modified_time( 'j F Y', false, $post, true ) ) ) . '</time>';
	return '<p class="bn-byline">' . implode( ' <span aria-hidden="true">·</span> ', $parts ) . '</p>';
}

add_filter( 'render_block_core/post-content', function ( $html ) {
	if ( is_admin() || ! is_page() || is_front_page() ) {
		return $html;
	}
	static $done = false;
	if ( $done ) {
		return $html;
	}
	$byline = betnero_page_byline_html( get_queried_object_id() );
	if ( ! $byline ) {
		return $html;
	}
	$done = true;
	if ( false !== stripos( $html, '</h1>' ) ) {
		return preg_replace( '#</h1>#i', '</h1>' . $byline, $html, 1 );
	}
	// Pagina senza H1 nel contenuto: in cima.
	return preg_replace( '/^(\s*<div\b[^>]*>)/', '$1' . $byline, $html, 1 );
}, 20 );
