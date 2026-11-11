<?php
/**
 * Menu veloce fisso in basso su mobile (pagine interne) e pulsante "torna su".
 *
 * Dalla 0.8.2: icone a linea piu' piccole nei colori del logo (nero e blu),
 * niente immagini. La barra non c'e' in home e sulle pagine che hanno gia'
 * la barra in basso di Betnero Core (recensioni), per non sovrapporle.
 *
 * @package betnero
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Icone a linea (24x24, tratto in currentColor).
 */
function betnero_icon( $name, $size = 22 ) {
	$paths = array(
		'casino'    => '<rect x="3" y="3" width="18" height="18" rx="4"/><circle cx="8.5" cy="8.5" r="1.2"/><circle cx="15.5" cy="8.5" r="1.2"/><circle cx="8.5" cy="15.5" r="1.2"/><circle cx="15.5" cy="15.5" r="1.2"/>',
		'live'      => '<rect x="2" y="6" width="14" height="12" rx="2"/><path d="m16 10 6-3v10l-6-3"/><circle cx="7" cy="12" r="1.6"/>',
		'slot'      => '<rect x="2" y="5" width="16" height="14" rx="2"/><path d="M6.5 9v6M10 9v6M13.5 9v6M18 9h2a1 1 0 0 1 1 1v3"/>',
		'scommesse' => '<circle cx="12" cy="12" r="9"/><path d="m12 7 4 3-1.5 4.5h-5L8 10z"/><path d="M12 3v4M21 10h-5M3 10h5M7 20l2-5.5M17 20l-2-5.5"/>',
		'bonus'     => '<rect x="3" y="8" width="18" height="13" rx="2"/><path d="M12 8v13M3 12h18M12 8c-2-4-6-4-6-1s6 1 6 1 6 2 6-1-4-3-6 1"/>',
		'blog'      => '<path d="M4 4h12l4 4v12H4z"/><path d="M8 10h8M8 14h8M8 18h5"/>',
		'academy'   => '<path d="M22 10 12 5 2 10l10 5 10-5z"/><path d="M6 12v5c3 2 9 2 12 0v-5"/>',
		'calc'      => '<rect x="5" y="3" width="14" height="18" rx="2"/><path d="M8 7h8M8 11h1M12 11h1M16 11h0M8 15h1M12 15h1M16 15v2"/>',
		'search'    => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
		'close'     => '<path d="M6 6l12 12M18 6 6 18"/>',
		'chev'      => '<path d="m6 9 6 6 6-6"/>',
		'arrow'     => '<path d="M5 12h14M13 6l6 6-6 6"/>',
	);
	$p = isset( $paths[ $name ] ) ? $paths[ $name ] : $paths['blog'];
	return '<svg width="' . (int) $size . '" height="' . (int) $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $p . '</svg>';
}

function betnero_mobile_bar_items() {
	return apply_filters( 'betnero_mobile_bar_items', array(
		array( 'Casino', '/casino.html', 'casino' ),
		array( 'Live', '/casino-live.html', 'live' ),
		array( 'Slot', '/casino/slot-machine.html', 'slot' ),
		array( 'Scommesse', '/scommesse-sportive.html', 'scommesse' ),
		array( 'Bonus', '/promozioni.html', 'bonus' ),
		array( 'Blog', '/blog', 'blog' ),
	) );
}

/**
 * Pagine interne si', home no; e niente barra dove c'e' gia' quella di Betnero Core.
 */
function betnero_mobile_bar_enabled( $path ) {
	$enabled = true; // dalla 0.9.1 anche in home (richiesta di Francesco)
	if ( $enabled && is_singular() && function_exists( 'bnc_bar_operator' ) && bnc_bar_operator( get_queried_object_id() ) ) {
		$enabled = false;
	}
	return (bool) apply_filters( 'betnero_mobile_bar_enabled', $enabled, $path );
}

add_action( 'wp_footer', function () {
	if ( is_admin() ) {
		return;
	}
	$req = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_parse_url( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH ) : '';
	if ( betnero_mobile_bar_enabled( $req ) ) {
		echo '<nav class="bn-mbar" aria-label="Menu rapido">';
		foreach ( betnero_mobile_bar_items() as $it ) {
			$base = preg_replace( '/\.html$/', '', $it[1] );
			$cur  = ( '' !== $req && 0 === strpos( $req, $base ) ) ? ' aria-current="page"' : '';
			echo '<a href="' . esc_url( home_url( $it[1] ) ) . '"' . $cur . '>' . betnero_icon( $it[2], 21 ) . '<span>' . esc_html( $it[0] ) . '</span></a>'; // phpcs:ignore WordPress.Security.EscapeOutput
		}
		echo '</nav>';
	}
	echo '<button type="button" class="bn-totop" aria-label="Torna su" hidden><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 19V5M5 12l7-7 7 7"/></svg></button>';
}, 20 );
