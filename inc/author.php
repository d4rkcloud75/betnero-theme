<?php
/**
 * Pagina autore (0.9.1): foto, nome, biografia e profili social nella fascia blu,
 * come sul sito in produzione. I dati sono quelli del profilo utente:
 * - foto: l'avatar dell'utente (lo stesso usato nei box autore degli articoli);
 * - descrizione: "Informazioni biografiche";
 * - LinkedIn e altri profili: i campi social di Yoast nel profilo utente.
 * Vale per tutti gli autori, senza impostazioni da toccare.
 *
 * @package betnero
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function betnero_author_socials( $uid ) {
	$out  = array();
	$nets = array(
		'linkedin'  => 'LinkedIn',
		'twitter'   => 'X',
		'facebook'  => 'Facebook',
		'instagram' => 'Instagram',
	);
	foreach ( $nets as $key => $label ) {
		$v = trim( (string) get_the_author_meta( $key, $uid ) );
		if ( '' === $v ) {
			continue;
		}
		if ( 'twitter' === $key && ! preg_match( '#^https?://#', $v ) ) {
			$v = 'https://x.com/' . ltrim( $v, '@' );
		}
		$out[ $key ] = array( $label, $v );
	}
	return $out;
}

add_shortcode( 'bn_author_hero', function () {
	$a = get_queried_object();
	if ( ! ( $a instanceof WP_User ) ) {
		return '';
	}
	$uid  = (int) $a->ID;
	$name = $a->display_name;
	$bio  = get_the_author_meta( 'description', $uid );
	$img  = get_avatar( $uid, 160, '', 'Foto di ' . $name, array( 'class' => 'bn-author-photo', 'loading' => 'eager' ) );

	$links = '';
	foreach ( betnero_author_socials( $uid ) as $key => $s ) {
		$icon   = 'linkedin' === $key ? '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20.45 20.45h-3.56v-5.57c0-1.33-.02-3.04-1.85-3.04-1.85 0-2.14 1.45-2.14 2.94v5.67H9.34V9h3.42v1.56h.05c.48-.9 1.64-1.85 3.37-1.85 3.6 0 4.27 2.37 4.27 5.46v6.28zM5.34 7.43a2.06 2.06 0 1 1 0-4.13 2.06 2.06 0 0 1 0 4.13zM7.12 20.45H3.56V9h3.56v11.45zM22.22 0H1.77C.79 0 0 .77 0 1.73v20.54C0 23.23.79 24 1.77 24h20.45c.98 0 1.78-.77 1.78-1.73V1.73C24 .77 23.2 0 22.22 0z"/></svg>' : '';
		$links .= '<a class="bn-author-soc is-' . esc_attr( $key ) . '" href="' . esc_url( $s[1] ) . '" target="_blank" rel="noopener me">' . $icon . '<span>' . esc_html( $s[0] ) . '</span></a>';
	}
	$count = (int) count_user_posts( $uid, 'post', true );

	return '<div class="bn-author-hero">'
		. ( $img ? '<div class="bn-author-pic">' . $img . '</div>' : '' )
		. '<div class="bn-author-info"><p class="bn-author-kicker">Autore</p><h1 class="bn-author-name">' . esc_html( $name ) . '</h1>'
		. ( $bio ? '<div class="bn-author-bio">' . wpautop( wp_kses_post( $bio ) ) . '</div>' : '' )
		. '<div class="bn-author-meta">' . ( $links ? $links : '' ) . ( $count ? '<span class="bn-author-count">' . $count . ' articol' . ( 1 === $count ? 'o' : 'i' ) . '</span>' : '' ) . '</div>'
		. '</div></div>';
} );
