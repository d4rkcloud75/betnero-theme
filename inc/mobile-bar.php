<?php
/**
 * Barra icone fissa in basso su mobile e pulsante "torna su".
 * Sostituiscono il popup Elementor/JetPopup 42604 e il pulsante "home" del sito Elementor.
 *
 * @package betnero
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function betnero_mobile_bar_items() {
	$u = content_url( 'uploads/2025/05/' );
	return apply_filters( 'betnero_mobile_bar_items', array(
		array( 'Casino', '/casino.html', $u . '21x21_icona_casino_betnero.webp' ),
		array( 'Live Casino', '/casino-live.html', $u . '21x21_icona_casino_live_betnero.webp' ),
		array( 'Slot', '/casino/slot-machine.html', $u . '21x21_icona_slot_machine_betnero.webp' ),
		array( 'Scommesse', '/scommesse-sportive.html', $u . '21x21_icona_scommesse_betnero.webp' ),
		array( 'Promozioni', '/promozioni.html', $u . 'gift-1.png' ),
		array( 'Blog', '/blog', $u . '21x21_icona_blog_betnero.png' ),
	) );
}

/**
 * Come nel sito originale: la barra non c'e' sulle recensioni e sulle pagine dell'Academy.
 */
function betnero_mobile_bar_enabled( $path ) {
	$path    = '/' . ltrim( (string) $path, '/' );
	$enabled = ! preg_match( '#^/(recensioni|academy)(\.html|/|$)#', $path );
	return (bool) apply_filters( 'betnero_mobile_bar_enabled', $enabled, $path );
}

add_action( 'wp_footer', function () {
	if ( is_admin() ) {
		return;
	}
	$req = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_parse_url( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH ) : '';
	if ( ! betnero_mobile_bar_enabled( $req ) ) {
		return;
	}
	echo '<nav class="bn-mbar" aria-label="Menu rapido">';
	foreach ( betnero_mobile_bar_items() as $it ) {
		$cur = ( '' !== $req && 0 === strpos( $req, preg_replace( '/\.html$/', '', $it[1] ) ) ) ? ' aria-current="page"' : '';
		echo '<a href="' . esc_url( home_url( $it[1] ) ) . '"' . $cur . '><img src="' . esc_url( $it[2] ) . '" alt="" width="28" height="28" loading="lazy" decoding="async"><span>' . esc_html( $it[0] ) . '</span></a>';
	}
	echo '</nav>';
	echo '<button type="button" class="bn-totop" aria-label="Torna su" hidden><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 19V5M5 12l7-7 7 7"/></svg></button>';
}, 20 );
