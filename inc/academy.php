<?php
/**
 * Betnero: ultime pagine dell'Academy nei Query Loop con classe "bn-academy-query".
 *
 * @package betnero
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Query Loop con classe "bn-academy-query": ultime pagine dell'Academy
 * (tutte le sottopagine di /academy, dalla piu' recente).
 */
add_filter( 'render_block_data', function ( $parsed ) {
	if ( isset( $parsed['blockName'] ) && 'core/query' === $parsed['blockName'] ) {
		$cls = isset( $parsed['attrs']['className'] ) ? (string) $parsed['attrs']['className'] : '';
		if ( false !== strpos( $cls, 'bn-academy-query' ) && isset( $parsed['attrs']['queryId'] ) ) {
			$GLOBALS['betnero_academy_query_ids'][ (int) $parsed['attrs']['queryId'] ] = true;
		}
	}
	return $parsed;
} );

function betnero_academy_page_ids() {
	static $ids = null;
	if ( null !== $ids ) {
		return $ids;
	}
	$ids  = array();
	$root = get_page_by_path( 'academy' );
	if ( $root ) {
		$ids = wp_list_pluck( get_pages( array( 'child_of' => $root->ID, 'post_status' => 'publish' ) ), 'ID' );
	}
	return $ids;
}

add_filter( 'query_loop_block_query_vars', function ( $query, $block ) {
	$qid = isset( $block->context['queryId'] ) ? (int) $block->context['queryId'] : 0;
	if ( empty( $GLOBALS['betnero_academy_query_ids'][ $qid ] ) ) {
		return $query;
	}
	$ids                 = betnero_academy_page_ids();
	$query['post_type']  = 'page';
	$query['post__in']   = $ids ? $ids : array( 0 );
	$query['orderby']    = 'date';
	$query['order']      = 'DESC';
	unset( $query['tax_query'] );
	return $query;
}, 10, 2 );
