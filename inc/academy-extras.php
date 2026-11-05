<?php
/**
 * Academy: widget in fondo alle guide, come nel sito originale.
 * Ultimo aggiornamento, Guide correlate (per tag, poi stessa sezione),
 * box autore e banner che porta alla home dell'Academy.
 *
 * @package betnero
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Tag anche sulle pagine: servono alle Guide correlate dell'Academy.
 */
add_action( 'init', function () {
	register_taxonomy_for_object_type( 'post_tag', 'page' );
}, 20 );

/**
 * Pagina radice dell'Academy (/academy.html).
 */
function betnero_academy_root_id() {
	$root = get_page_by_path( 'academy' );
	return $root ? (int) $root->ID : 0;
}

/**
 * Le guide vere e proprie: tutte le pagine Academy tranne la home dell'Academy.
 */
function betnero_is_academy_guide() {
	return betnero_is_academy_request() && (int) get_queried_object_id() !== betnero_academy_root_id();
}

/**
 * Fino a 3 guide correlate: prima quelle con tag in comune, poi quelle della
 * stessa sezione, infine le altre guide Academy aggiornate di recente.
 */
function betnero_academy_related_ids( $post_id, $limit = 3 ) {
	$all = function_exists( 'betnero_academy_page_ids' ) ? betnero_academy_page_ids() : array();
	$all = array_values( array_diff( array_map( 'intval', $all ), array( (int) $post_id ) ) );
	if ( ! $all ) {
		return array();
	}
	$picked = array();
	$base   = array(
		'post_type'      => 'page',
		'post_status'    => 'publish',
		'posts_per_page' => $limit,
		'fields'         => 'ids',
		'orderby'        => 'modified',
		'order'          => 'DESC',
		'no_found_rows'  => true,
	);

	$tags = wp_get_post_terms( $post_id, 'post_tag', array( 'fields' => 'ids' ) );
	if ( $tags && ! is_wp_error( $tags ) ) {
		$picked = get_posts( $base + array(
			'post__in'  => $all,
			'tax_query' => array( array( 'taxonomy' => 'post_tag', 'terms' => $tags ) ),
		) );
	}

	if ( count( $picked ) < $limit ) {
		$parent = (int) wp_get_post_parent_id( $post_id );
		$root   = betnero_academy_root_id();
		// Sezione: le "sorelle" della guida; per una sezione principale, le sue sottopagine.
		$section = $parent && $parent !== $root ? $parent : (int) $post_id;
		$family  = get_posts( array_merge( $base, array(
			'post__in'       => $all,
			'post_parent'    => $section,
			'posts_per_page' => $limit,
			'post__not_in'   => $picked ? $picked : array( 0 ),
		) ) );
		if ( $parent && $parent !== $root && in_array( $parent, $all, true ) && ! in_array( $parent, $picked, true ) ) {
			array_unshift( $family, $parent );
		}
		$picked = array_unique( array_merge( $picked, $family ) );
	}

	if ( count( $picked ) < $limit ) {
		$picked = array_unique( array_merge( $picked, get_posts( $base + array(
			'post__in'     => $all,
			'post__not_in' => $picked ? $picked : array( 0 ),
		) ) ) );
	}

	return array_slice( array_map( 'intval', $picked ), 0, $limit );
}

/**
 * Estratto breve di una guida: riassunto, poi meta description Yoast, poi testo.
 */
function betnero_academy_excerpt( $id ) {
	$text = (string) get_post_field( 'post_excerpt', $id );
	if ( '' === trim( $text ) ) {
		$text = (string) get_post_meta( $id, '_yoast_wpseo_metadesc', true );
	}
	if ( '' === trim( $text ) || false !== strpos( $text, '%%' ) ) {
		$text = wp_strip_all_tags( strip_shortcodes( (string) get_post_field( 'post_content', $id ) ) );
	}
	return wp_trim_words( $text, 16, '&hellip;' );
}

function betnero_academy_updated_html( $post_id ) {
	return '<div class="bn-acad-updated"><p><strong>Ultimo aggiornamento:</strong> ' . esc_html( get_the_modified_date( 'd/m/Y', $post_id ) ) . '</p></div>';
}

function betnero_academy_related_html( $post_id ) {
	$ids = betnero_academy_related_ids( $post_id );
	if ( ! $ids ) {
		return '';
	}
	$fallback = content_url( 'uploads/2026/02/betnero-academy-150x150.jpg' );
	$h        = '<section class="bn-acad-related" aria-labelledby="bn-acad-related-title"><h2 id="bn-acad-related-title">Guide Correlate</h2><div class="bn-acad-related-grid">';
	foreach ( $ids as $id ) {
		$img = get_the_post_thumbnail( $id, 'thumbnail', array( 'class' => 'bn-acad-related-img', 'alt' => '', 'loading' => 'lazy' ) );
		if ( ! $img ) {
			$img = '<img class="bn-acad-related-img" src="' . esc_url( $fallback ) . '" width="150" height="150" alt="" loading="lazy">';
		}
		$h .= '<a class="bn-acad-related-item" href="' . esc_url( get_permalink( $id ) ) . '">'
			. '<span class="bn-acad-related-head">' . $img . '<span class="bn-acad-related-title">' . esc_html( get_the_title( $id ) ) . '</span></span>'
			. '<span class="bn-acad-related-text">' . esc_html( betnero_academy_excerpt( $id ) ) . '</span></a>';
	}
	return $h . '</div></section>';
}

function betnero_academy_author_html( $post_id ) {
	$uid = (int) get_post_field( 'post_author', $post_id );
	if ( ! $uid ) {
		return '';
	}
	$name = get_the_author_meta( 'display_name', $uid );
	$bio  = get_the_author_meta( 'description', $uid );
	$url  = get_author_posts_url( $uid );
	$h    = '<div class="bn-acad-author"><a class="bn-acad-author-avatar" href="' . esc_url( $url ) . '">' . get_avatar( $uid, 80, '', $name ) . '</a><div class="bn-acad-author-text">'
		. '<p class="bn-acad-author-name"><a href="' . esc_url( $url ) . '">' . esc_html( $name ) . '</a></p>';
	if ( $bio ) {
		$h .= '<p class="bn-acad-author-bio">' . esc_html( $bio ) . '</p>';
	}
	return $h . '</div></div>';
}

function betnero_academy_banner_html() {
	$ids     = apply_filters( 'betnero_academy_banner_ids', array( 'desktop' => 48097, 'mobile' => 48098 ) );
	$desktop = wp_get_attachment_image_url( $ids['desktop'], 'full' );
	$mobile  = wp_get_attachment_image_url( $ids['mobile'], 'full' );
	if ( ! $desktop ) {
		return '';
	}
	$root = betnero_academy_root_id();
	$href = $root ? get_permalink( $root ) : home_url( '/academy.html' );
	$pic  = '<picture>' . ( $mobile ? '<source media="(max-width: 767px)" srcset="' . esc_url( $mobile ) . '" width="600" height="171">' : '' )
		. '<img src="' . esc_url( $desktop ) . '" width="1046" height="174" alt="Betnero Academy: il Sapere dietro ogni Scommessa. Visita l\'Academy" loading="lazy"></picture>';
	return '<div class="bn-acad-banner alignfull"><a href="' . esc_url( $href ) . '">' . $pic . '</a></div>';
}
