<?php
/**
 * Menu mobile a pannello (0.8.2): entra da sinistra e occupa 3/4 dello schermo,
 * il sito resta visibile a destra (scurito). Ricerca, scorciatoie Academy e
 * Calcolatore di Rischio, sezioni con icona e sottomenu a fisarmonica,
 * "I migliori del mese" (primi 3 della classifica casino).
 *
 * Le voci arrivano dal blocco Navigazione dell'header: se il menu cambia,
 * il pannello si aggiorna da solo. Il pulsante del menu del blocco Navigazione
 * apre questo pannello al posto del menu a tutto schermo di WordPress.
 *
 * @package betnero
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Voci del menu lette dal template part "header".
 * Ritorna: array( array( 'label', 'url', array( figli ) ), ... ).
 */
function betnero_menu_items() {
	static $items = null;
	if ( null !== $items ) {
		return $items;
	}
	$items = array();
	$tpl   = function_exists( 'get_block_template' ) ? get_block_template( get_stylesheet() . '//header', 'wp_template_part' ) : null;
	if ( ! $tpl || empty( $tpl->content ) ) {
		return $items;
	}
	$find = function ( $blocks ) use ( &$find ) {
		foreach ( $blocks as $b ) {
			if ( 'core/navigation' === $b['blockName'] ) {
				return $b;
			}
			if ( ! empty( $b['innerBlocks'] ) ) {
				$r = $find( $b['innerBlocks'] );
				if ( $r ) {
					return $r;
				}
			}
		}
		return null;
	};
	$nav = $find( parse_blocks( $tpl->content ) );
	if ( ! $nav ) {
		return $items;
	}
	foreach ( $nav['innerBlocks'] as $b ) {
		$a = $b['attrs'];
		if ( empty( $a['label'] ) ) {
			continue;
		}
		$kids  = array();
		$extra = array(); // sottomenu annidati (es. Casino Live dentro Casino): nel pannello diventano sezioni a parte
		foreach ( (array) $b['innerBlocks'] as $c ) {
			if ( empty( $c['attrs']['label'] ) ) {
				continue;
			}
			$c_url = isset( $c['attrs']['url'] ) ? $c['attrs']['url'] : '#';
			if ( ! empty( $c['innerBlocks'] ) ) {
				$sub = array();
				foreach ( $c['innerBlocks'] as $g ) {
					if ( ! empty( $g['attrs']['label'] ) ) {
						$sub[] = array( $g['attrs']['label'], isset( $g['attrs']['url'] ) ? $g['attrs']['url'] : '#' );
					}
				}
				$extra[] = array( $c['attrs']['label'], $c_url, $sub );
				continue;
			}
			$kids[] = array( $c['attrs']['label'], $c_url );
		}
		$items[] = array( $a['label'], isset( $a['url'] ) ? $a['url'] : '#', $kids );
		foreach ( $extra as $e ) {
			$items[] = $e;
		}
	}
	return $items;
}

function betnero_menu_icon_for( $label ) {
	$l = strtolower( remove_accents( $label ) );
	foreach ( array( 'live' => 'live', 'casino' => 'casino', 'slot' => 'slot', 'scommess' => 'scommesse', 'bonus' => 'bonus', 'promo' => 'bonus', 'blog' => 'blog', 'pronostic' => 'blog', 'academy' => 'academy', 'calcolatore' => 'calc' ) as $k => $icon ) {
		if ( false !== strpos( $l, $k ) ) {
			return $icon;
		}
	}
	return 'blog';
}

function betnero_mobile_menu_html() {
	$items = betnero_menu_items();
	if ( ! $items ) {
		return '';
	}
	$u      = function ( $url ) {
		return esc_url( 0 === strpos( $url, '/' ) ? home_url( $url ) : $url );
	};
	$logo   = get_custom_logo();
	$chips  = '';
	$secs   = '';
	$n      = 0;
	foreach ( $items as $it ) {
		list( $label, $url, $kids ) = $it;
		if ( ! $kids ) {
			$icon   = betnero_menu_icon_for( $label );
			$cls    = 'calc' === $icon ? 'bn-dr-chip is-calc' : 'bn-dr-chip';
			$chips .= '<a class="' . $cls . '" href="' . $u( $url ) . '">' . betnero_icon( $icon, 18 ) . '<span>' . esc_html( $label ) . '</span></a>';
			continue;
		}
		$n++;
		$id    = 'bn-dr-sub-' . $n;
		$secs .= '<div class="bn-dr-sec"><button type="button" class="bn-dr-it" aria-expanded="false" aria-controls="' . $id . '"><span class="bn-dr-ic">' . betnero_icon( betnero_menu_icon_for( $label ), 18 ) . '</span><span class="bn-dr-lb">' . esc_html( $label ) . '</span><span class="bn-dr-chev">' . betnero_icon( 'chev', 18 ) . '</span></button>';
		$secs .= '<div class="bn-dr-sub" id="' . $id . '" hidden><a class="is-all" href="' . $u( $url ) . '">Tutto su ' . esc_html( $label ) . '</a>';
		foreach ( $kids as $i => $k ) {
			$secs .= '<a href="' . $u( $k[1] ) . '"' . ( $i >= 5 ? ' class="is-more" hidden' : '' ) . '>' . esc_html( $k[0] ) . '</a>';
		}
		if ( count( $kids ) > 5 ) {
			$secs .= '<button type="button" class="bn-dr-more">Altre ' . ( count( $kids ) - 5 ) . ' voci ' . betnero_icon( 'chev', 14 ) . '</button>';
		}
		$secs .= '</div></div>';
	}

	// I migliori del mese: primi 3 casino della classifica (Betnero Core).
	$top = '';
	if ( function_exists( 'bnc_operators' ) && function_exists( 'bnc_logo' ) ) {
		$ops = array_slice( (array) bnc_operators( 'casino' ), 0, 3 );
		if ( $ops ) {
			$top = '<div class="bn-dr-top"><p class="bn-dr-top-t">I migliori di ' . esc_html( wp_date( 'F' ) ) . '</p>';
			foreach ( $ops as $i => $op ) {
				$raw   = isset( $op['bonus'] ) ? (string) $op['bonus'] : '';
				$bonus = preg_match( '#<h[1-6][^>]*>(.*?)</h[1-6]>#is', $raw, $m ) ? $m[1] : $raw; // la riga grande del bonus
				$bonus = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( $bonus ) ) );
				$go    = function_exists( 'bnc_go_button' ) ? bnc_go_button( $op, 'Vai', 'bn-dr-go' ) : '';
				$top  .= '<div class="bn-dr-row"><span class="bn-dr-n">' . ( $i + 1 ) . '</span><span class="bn-dr-logo">' . bnc_logo( $op ) . '</span><span class="bn-dr-b">' . esc_html( $bonus ) . '</span>' . $go . '</div>';
			}
			$top .= '</div>';
		}
	}

	$search = '<form class="bn-dr-search" role="search" method="get" action="' . esc_url( home_url( '/' ) ) . '">' . betnero_icon( 'search', 17 ) . '<input type="search" name="s" placeholder="Cerca casinò, slot, guide…" aria-label="Cerca"></form>';

	return '<div class="bn-drawer" id="bn-drawer" hidden><div class="bn-dr-dim" data-bn-close></div>'
		. '<aside class="bn-dr-panel" role="dialog" aria-modal="true" aria-label="Menu">'
		. '<div class="bn-dr-head">' . $logo . '<button type="button" class="bn-dr-x" data-bn-close aria-label="Chiudi il menu">' . betnero_icon( 'close', 18 ) . '</button></div>'
		. '<div class="bn-dr-body">' . $search
		. ( $chips ? '<div class="bn-dr-chips">' . $chips . '</div>' : '' )
		. '<p class="bn-dr-label">Esplora</p><nav class="bn-dr-list" aria-label="Sezioni">' . $secs . '</nav>' . $top . '</div>'
		. '<div class="bn-dr-foot"><b>18+</b><span>Gioca responsabilmente · Solo siti con licenza ADM</span></div>'
		. '</aside></div>';
}

add_action( 'wp_footer', function () {
	if ( is_admin() ) {
		return;
	}
	echo betnero_mobile_menu_html(); // phpcs:ignore WordPress.Security.EscapeOutput
}, 15 );
