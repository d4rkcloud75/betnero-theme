<?php
/**
 * Blog: categorie principali nell'hub (/blog e sue sottocategorie).
 * Su mobile stanno su una sola riga che scorre in orizzontale.
 *
 * @package betnero
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function betnero_blog_cats_html() {
	if ( ! is_category() ) {
		return '';
	}
	$blog = get_category_by_slug( 'blog' );
	if ( ! $blog ) {
		return '';
	}
	$current = (int) get_queried_object_id();
	if ( $current !== (int) $blog->term_id && ! cat_is_ancestor_of( $blog->term_id, $current ) ) {
		return '';
	}
	$cats = get_categories( array(
		'parent'     => $blog->term_id,
		'hide_empty' => true,
		'orderby'    => 'count',
		'order'      => 'DESC',
	) );
	if ( ! $cats ) {
		return '';
	}
	$h  = '<nav class="bn-blog-cats" aria-label="Categorie del blog"><ul>';
	$on = $current === (int) $blog->term_id;
	$h .= '<li><a href="' . esc_url( get_category_link( $blog ) ) . '"' . ( $on ? ' aria-current="page"' : '' ) . '>Tutti</a></li>';
	foreach ( $cats as $c ) {
		$on = $current === (int) $c->term_id || cat_is_ancestor_of( $c->term_id, $current );
		$h .= '<li><a href="' . esc_url( get_category_link( $c ) ) . '"' . ( $on ? ' aria-current="page"' : '' ) . '>' . esc_html( $c->name ) . '</a></li>';
	}
	return $h . '</ul></nav>';
}

add_action( 'init', function () {
	add_shortcode( 'bn_blog_cats', 'betnero_blog_cats_html' );
} );

/**
 * Sidebar degli archivi del blog, come nel sito originale:
 * "Tutti i nostri contenuti" (categorie del blog) e "I Migliori Siti di Casinò Online".
 */
function betnero_blog_sidebar_html() {
	$h    = '';
	$blog = get_category_by_slug( 'blog' );
	if ( $blog ) {
		$cats = get_categories( array( 'parent' => $blog->term_id, 'hide_empty' => true, 'orderby' => 'name' ) );
		if ( $cats ) {
			$cur = is_category() ? (int) get_queried_object_id() : 0;
			$h  .= '<nav class="bn-bside-cats" aria-label="Tutti i nostri contenuti"><p class="bn-bside-title">Tutti i nostri contenuti</p><ul>';
			foreach ( $cats as $c ) {
				$h .= '<li><a href="' . esc_url( get_category_link( $c ) ) . '"' . ( $cur === (int) $c->term_id ? ' aria-current="page"' : '' ) . '>' . esc_html( $c->name ) . '</a></li>';
			}
			$h .= '</ul></nav>';
		}
	}
	if ( function_exists( 'bnc_operators' ) && function_exists( 'bnc_sidebar_row' ) ) {
		$ops = array_slice( (array) bnc_operators( 'casino' ), 0, 6 );
		if ( $ops ) {
			wp_enqueue_style( 'betnero-tables' );
			$h .= '<div class="bns2 bn-bside-top"><p class="bn-bside-title">I Migliori Siti di Casinò Online</p>';
			foreach ( $ops as $op ) {
				$h .= bnc_sidebar_row( $op );
			}
			$h .= '</div>';
		}
	}
	return $h;
}

add_action( 'init', function () {
	add_shortcode( 'bn_blog_sidebar', 'betnero_blog_sidebar_html' );
} );
