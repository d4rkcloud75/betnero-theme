<?php
/**
 * Breadcrumb visibile, uguale su tutto il sito (0.8.7).
 *
 * Usa il breadcrumb di Yoast (lo stesso del BreadcrumbList nel JSON-LD), con
 * "Home" come prima voce. Va sempre in cima, sopra l'H1:
 * - dentro la prima fascia della pagina (hero blu delle pagine e degli archivi,
 *   testata chiara degli articoli), come prima riga;
 * - nelle pagine senza fascia (es. recensioni), sopra il contenuto.
 * In home non c'e'.
 *
 * @package betnero
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Il tema mostra il breadcrumb di Yoast (anche se l'opzione nelle impostazioni di Yoast e' spenta).
add_action( 'after_setup_theme', function () {
	add_theme_support( 'yoast-seo-breadcrumbs' );
} );

// Prima voce sempre "Home" (in passato compariva "Home nuova").
add_filter( 'wpseo_breadcrumb_links', function ( $links ) {
	if ( ! empty( $links ) && isset( $links[0]['text'] ) ) {
		$links[0]['text'] = 'Home';
	}
	// Ultima voce in italiano dove Yoast usa i suoi testi predefiniti in inglese.
	$last = count( $links ) - 1;
	if ( $last > 0 ) {
		if ( is_author() ) {
			$a = get_queried_object();
			$links[ $last ]['text'] = $a && isset( $a->display_name ) ? $a->display_name : $links[ $last ]['text'];
		} elseif ( is_search() ) {
			$links[ $last ]['text'] = 'Ricerca: ' . get_search_query();
		} elseif ( is_404() ) {
			$links[ $last ]['text'] = 'Pagina non trovata';
		}
	}
	return $links;
} );

function betnero_breadcrumb_html( $light ) {
	if ( is_front_page() || ! function_exists( 'yoast_breadcrumb' ) ) {
		return '';
	}
	$html = yoast_breadcrumb( '', '', false );
	if ( ! $html ) {
		return '';
	}
	return '<nav class="bn-bc' . ( $light ? ' bn-bc--light' : '' ) . '" aria-label="Percorso">' . $html . '</nav>';
}

function betnero_breadcrumb_done( $set = false ) {
	static $done = false;
	if ( $set ) {
		$done = true;
	}
	return $done;
}

// Nella prima fascia della pagina: come primo elemento del gruppo.
add_filter( 'render_block_core/group', function ( $html, $block ) {
	if ( betnero_breadcrumb_done() || is_admin() ) {
		return $html;
	}
	$cls = isset( $block['attrs']['className'] ) ? ' ' . $block['attrs']['className'] . ' ' : '';
	if ( ! preg_match( '/ (bn-hh|bn-ph|bn-hero) /', $cls, $m ) ) {
		return $html;
	}
	// Fascia chiara (testata degli articoli) o blu (tutte le altre).
	$light = ! ( 'bn-hero' === $m[1] && empty( $block['attrs']['gradient'] ) );
	$bc    = betnero_breadcrumb_html( $light );
	betnero_breadcrumb_done( true );
	if ( ! $bc ) {
		return $html;
	}
	return preg_replace( '/^(\s*<(?:div|section)\b[^>]*>)/', '$1' . $bc, $html, 1 );
}, 5, 2 );

// Pagine senza fascia: sopra il contenuto.
add_filter( 'render_block_core/post-content', function ( $html ) {
	if ( betnero_breadcrumb_done() || ! is_singular() || is_admin() ) {
		return $html;
	}
	betnero_breadcrumb_done( true );
	$bc = betnero_breadcrumb_html( false );
	return $bc ? preg_replace( '/^(\s*<div\b[^>]*>)/', '$1' . $bc, $html, 1 ) : $html;
}, 5 );
