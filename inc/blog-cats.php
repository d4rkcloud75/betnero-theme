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
