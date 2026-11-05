<?php
/**
 * Sidebar dell'Academy: "Indice dell'Academy" a sinistra, come nel sito originale
 * (menu "Academy" di JetMenu). Sulle pagine /academy/... il contenuto viene
 * diviso: la fascia in alto resta a tutta larghezza, sotto ci sono indice e testo.
 *
 * @package betnero
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function betnero_academy_menu() {
	return apply_filters( 'betnero_academy_menu', array(
		array( 'Calcolatore di Rischio', '/academy/casino-risk-calculator.html', array( array( '– Infografica Studio sul Calcolatore', '/academy/casino-risk-calculator/infrgrafica-ricerca-calcolatore-rischio.html' ), array( '– Ricerca Quantitativa sul Calcolatore', '/academy/casino-risk-calculator/risultati-ricerca-calcolatore.html' ) ) ),
		array( 'Gioco Responsabile', '/academy/gioco-responsabile.html', array( array( '– Autoesclusione', '/academy/gioco-responsabile/autoesclusione.html' ), array( '– Chiusura Account', '/academy/gioco-responsabile/chiusura-account.html' ), array( '– Sicurezza Account e Privacy', '/academy/gioco-responsabile/sicurezza-account-privacy.html' ) ) ),
		array( 'Glossario', '/academy/glossario.html', array(  ) ),
		array( 'Guide Tecniche ai Giochi', '/academy/guida-tecnica-giochi.html', array( array( '– Casinò Live vs Casinò Online', '/academy/guida-tecnica-giochi/differenze-casino-live-casino-online.html' ), array( '– Linee di Pagamento', '/academy/guida-tecnica-giochi/linee-di-pagamento-slot.html' ), array( '– Matematica nel Gioco d’Azzardo', '/academy/guida-tecnica-giochi/matematica-gioco-azzardo.html' ), array( '– Errori Matematici nella Roulette', '/academy/guida-tecnica-giochi/errori-matematici-roulette.html' ), array( '– Meccaniche Blackjack Online', '/academy/guida-tecnica-giochi/meccaniche-blackjack-online.html' ), array( '– Meccaniche Roulette Online', '/academy/guida-tecnica-giochi/meccaniche-roulette-online.html' ), array( '– Provider di Casino Live', '/academy/guida-tecnica-giochi/provider-casino-live-differenze-evolution-pragmatic-play-playtech.html' ), array( '– RNG', '/academy/guida-tecnica-giochi/rng.html' ), array( '– RTP', '/academy/guida-tecnica-giochi/rtp.html' ), array( '– Simboli Slot', '/academy/guida-tecnica-giochi/simboli-slot.html' ), array( '– Tecnologia dei Giochi Live', '/academy/guida-tecnica-giochi/tecnologia-giochi-live.html' ), array( '– Tipologie di Slot Online', '/academy/guida-tecnica-giochi/tipologie-slot-modi-di-vincere-payout.html' ), array( '– Volatilità', '/academy/guida-tecnica-giochi/volatilita.html' ) ) ),
		array( 'Licenza ADM', '/academy/adm.html', array( array( '– 2025/26 Nuove Leggi', '/academy/adm/nuove-leggi-2025-2026-2.html' ), array( '– 2023/24 Nuove Leggi', '/academy/adm/nuove-leggi-2023-2024.html' ), array( '– Casinò senza licenza', '/academy/adm/casino-senza-licenza.html' ), array( '– Registrazione e Documenti', '/academy/adm/registrazione-casino-documenti.html' ), array( '– Sponsorizzazioni Gioco d’Azzardo', '/academy/adm/sponsorizzazioni-gioco-azzardo-online-norme.html' ) ) ),
		array( 'Metodologia', '/academy/metodologia.html', array( array( '– Funzionamento dei Bonus Casino', '/academy/metodologia/funzionamento-bonus.html' ), array( '– Funzionamento dei Bonus Scommesse', '/academy/metodologia/funzionamento-bonus-di-scommesse.html' ), array( '– Funzionamento dei Bonus Multipla', '/academy/metodologia/funzionamento-bonus-sulle-multiple.html' ), array( '– Guida al Wagering', '/academy/metodologia/funzionamento-wagering.html' ) ) ),
		array( 'Servizi', '/academy/servizi-del-casino.html', array( array( '– Programmi VIP e Fedeltà', '/academy/servizi-del-casino/differenze-programmi-vip-fedelta.html' ), array( '– Reclami Casinò', '/academy/servizi-del-casino/reclami-tutela-giocatore.html' ), array( '– Servizio Clienti', '/academy/servizi-del-casino/servizio-clienti.html' ) ) ),
		array( 'Sistemi di pagamento', '/academy/metodi-di-pagamento.html', array( array( '– Cryptovalute', '/academy/metodi-di-pagamento/crypto-casino-italia.html' ), array( '– Prelievi e Depositi', '/academy/metodi-di-pagamento/prelievi-e-depositi.html' ) ) ),
		array( 'Tassazione', '/academy/tassazione-gioco-azzardo.html', array(  ) ),
	) );
}

function betnero_is_academy_request() {
	if ( ! is_page() ) {
		return false;
	}
	$path = (string) wp_parse_url( get_permalink(), PHP_URL_PATH );
	return (bool) preg_match( '#^/academy(\.html|/)#', $path );
}

function betnero_academy_sidebar_html() {
	$path = (string) wp_parse_url( get_permalink(), PHP_URL_PATH );
	$h    = '<nav class="bn-acad-index" aria-label="Indice dell\'Academy"><p class="bn-acad-index-title">Indice dell\'Academy</p><ul>';
	foreach ( betnero_academy_menu() as $it ) {
		$cur  = $path === $it[1];
		$open = $cur;
		foreach ( $it[2] as $c ) {
			if ( $path === $c[1] ) {
				$open = true;
			}
		}
		$link = '<a href="' . esc_url( home_url( $it[1] ) ) . '"' . ( $cur ? ' aria-current="page"' : '' ) . '>' . esc_html( $it[0] ) . '</a>';
		if ( empty( $it[2] ) ) {
			$h .= '<li>' . $link . '</li>';
			continue;
		}
		$h .= '<li><details' . ( $open ? ' open' : '' ) . '><summary>' . $link . '<span class="bn-acad-arrow" aria-hidden="true"></span></summary><ul>';
		foreach ( $it[2] as $c ) {
			$h .= '<li><a href="' . esc_url( home_url( $c[1] ) ) . '"' . ( $path === $c[1] ? ' aria-current="page"' : '' ) . '>' . esc_html( $c[0] ) . '</a></li>';
		}
		$h .= '</ul></details></li>';
	}
	return $h . '</ul></nav>';
}

// Segna la fine della fascia in alto (gruppo .bn-ph), per dividere il contenuto.
add_filter( 'render_block_core/group', function ( $html, $block ) {
	static $done = false;
	if ( $done || ! betnero_is_academy_request() ) {
		return $html;
	}
	$cls = isset( $block['attrs']['className'] ) ? (string) $block['attrs']['className'] : '';
	if ( preg_match( '/(^|\s)bn-ph(\s|$)/', $cls ) ) {
		$done = true;
		return $html . '<!--bn-acad-split-->';
	}
	return $html;
}, 10, 2 );

add_filter( 'the_content', function ( $content ) {
	if ( ! in_the_loop() || ! is_main_query() || ! betnero_is_academy_request() ) {
		return $content;
	}
	$parts  = explode( '<!--bn-acad-split-->', $content, 2 );
	$before = 2 === count( $parts ) ? $parts[0] : '';
	$after  = 2 === count( $parts ) ? $parts[1] : $content;
	$extras = '';
	$banner = '';
	if ( function_exists( 'betnero_is_academy_guide' ) && betnero_is_academy_guide() ) {
		$id     = get_the_ID();
		$extras = betnero_academy_updated_html( $id ) . betnero_academy_related_html( $id ) . betnero_academy_author_html( $id );
		$banner = betnero_academy_banner_html();
	}
	return $before . '<div class="bn-acad-wrap alignwide"><aside class="bn-acad-side">' . betnero_academy_sidebar_html() . '</aside><div class="bn-acad-main">' . $after . $extras . '</div></div>' . $banner;
}, 99 );
