<?php
/**
 * Betnero: blocchi dinamici per tabelle e toplist.
 *
 * I dati arrivano dalle query del Query Builder di JetEngine (le stesse
 * delle tabelle in home), quindi operatori, bonus e ordine si gestiscono
 * sempre da JetEngine.
 *
 *   betnero/tabella-casino     query 2, con filtri opzionali
 *   betnero/tabella-scommesse  query 12, con filtri opzionali
 *   betnero/toplist            versione compatta (casino o scommesse)
 *
 * @package betnero
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const BETNERO_QUERY_CASINO    = 2;
const BETNERO_QUERY_SCOMMESSE = 12;

/* ---------- Lettura dati ---------- */

/**
 * Elementi di una query del Query Builder di JetEngine.
 *
 * @param int $query_id ID della query.
 * @return array
 */
function betnero_query_items( $query_id ) {
	static $cache = array();
	if ( isset( $cache[ $query_id ] ) ) {
		return $cache[ $query_id ];
	}
	$items = array();
	if ( class_exists( '\Jet_Engine\Query_Builder\Manager' ) ) {
		$query = \Jet_Engine\Query_Builder\Manager::instance()->get_query_by_id( $query_id );
		if ( $query ) {
			$items = (array) $query->get_items();
		}
	}
	$cache[ $query_id ] = $items;
	return $items;
}

function betnero_field( $item, $key ) {
	if ( is_object( $item ) && isset( $item->$key ) ) {
		return $item->$key;
	}
	if ( is_array( $item ) && isset( $item[ $key ] ) ) {
		return $item[ $key ];
	}
	return '';
}

function betnero_text( $item, $key ) {
	$v = betnero_field( $item, $key );
	return is_scalar( $v ) ? (string) $v : '';
}

/**
 * Valori spuntati di un campo checkbox di JetEngine
 * (salvato come array chiave => true/false, serializzato o JSON).
 *
 * @return string[]
 */
function betnero_checked( $item, $key ) {
	$v = betnero_field( $item, $key );
	if ( is_string( $v ) ) {
		$u = maybe_unserialize( $v );
		if ( is_string( $u ) ) {
			$j = json_decode( $u, true );
			$u = is_array( $j ) ? $j : array( $u );
		}
		$v = $u;
	}
	if ( ! is_array( $v ) ) {
		return array();
	}
	$out = array();
	foreach ( $v as $k => $val ) {
		if ( is_int( $k ) && ! is_bool( $val ) && 'true' !== $val && 'false' !== $val ) {
			$out[] = (string) $val; // Lista semplice di valori.
		} elseif ( true === $val || 'true' === $val || 1 === $val || '1' === $val ) {
			$out[] = (string) $k;
		}
	}
	return $out;
}

function betnero_href( $html ) {
	return preg_match( '/<a\s[^>]*href=["\']([^"\']+)["\']/i', (string) $html, $m ) ? trim( html_entity_decode( $m[1] ) ) : '';
}

function betnero_href_new_tab( $html ) {
	return false !== stripos( (string) $html, 'target="_blank"' );
}

function betnero_img( $html, $alt ) {
	if ( ! preg_match( '/<img\s[^>]*src=["\']([^"\']+)["\']/i', (string) $html, $m ) ) {
		return '<span class="bnt-logo-text">' . esc_html( $alt ) . '</span>';
	}
	return '<img src="' . esc_url( $m[1] ) . '" alt="' . esc_attr( $alt ) . '" loading="lazy" decoding="async">';
}

/**
 * Testo bonus: righe <p> piccole, titoli <h1-h6> grandi.
 */
function betnero_bonus_html( $html ) {
	$out = '';
	if ( preg_match_all( '#<(p|h[1-6])[^>]*>(.*?)</\1>#is', (string) $html, $m, PREG_SET_ORDER ) ) {
		foreach ( $m as $b ) {
			$txt = trim( wp_strip_all_tags( $b[2] ) );
			if ( '' === $txt ) {
				continue;
			}
			$cls  = ( 'p' === strtolower( $b[1] ) ) ? 'bnt-small' : 'bnt-big';
			$out .= '<div class="' . $cls . '">' . esc_html( $txt ) . '</div>';
		}
	}
	if ( '' === $out ) {
		$txt = trim( wp_strip_all_tags( (string) $html ) );
		$out = $txt ? '<div class="bnt-big">' . esc_html( $txt ) . '</div>' : '';
	}
	return $out;
}

function betnero_stars( $val ) {
	$v   = max( 0, min( 5, (float) str_replace( ',', '.', (string) $val ) ) );
	$pct = round( $v / 5 * 100 );
	return '<span class="bnt-stars" role="img" aria-label="' . esc_attr( sprintf( 'Valutazione %s su 5', number_format_i18n( $v, 1 ) ) ) . '"><span class="bnt-stars-bg">★★★★★</span><span class="bnt-stars-fg" style="width:' . $pct . '%">★★★★★</span></span>';
}

function betnero_go_button( $html, $label = 'Vai al sito' ) {
	$href = betnero_href( $html );
	if ( ! $href ) {
		return '';
	}
	$target = betnero_href_new_tab( $html ) ? ' target="_blank"' : '';
	return '<a class="bnt-btn bnt-go" href="' . esc_url( $href ) . '"' . $target . ' rel="nofollow sponsored noopener">' . esc_html( $label ) . '</a>';
}

function betnero_review_button( $html ) {
	$href = betnero_href( $html );
	return $href ? '<a class="bnt-btn bnt-rev" href="' . esc_url( $href ) . '">Recensione</a>' : '';
}

/* ---------- Normalizzazione operatori ---------- */

function betnero_operators( $type ) {
	$is_casino = ( 'casino' === $type );
	$items     = betnero_query_items( $is_casino ? BETNERO_QUERY_CASINO : BETNERO_QUERY_SCOMMESSE );
	$ops       = array();
	foreach ( $items as $it ) {
		if ( $is_casino ) {
			$ops[] = array(
				'name'    => betnero_text( $it, 'name' ),
				'logo'    => betnero_text( $it, 'logo' ),
				'bonus'   => betnero_text( $it, 'bonusbonus' ),
				'nd'      => betnero_text( $it, 'bonus_senza_deposito' ),
				'spid'    => betnero_text( $it, 'registrazione_spid' ),
				'rating'  => betnero_text( $it, 'valutazione' ),
				'go'      => betnero_text( $it, 'visita_il_sito' ),
				'review'  => betnero_text( $it, 'vai_alla_scheda' ),
				'parere'  => betnero_text( $it, 'il_parere_dellesperto' ),
				'welcome' => betnero_checked( $it, 'welcome_bonus' ),
				'tipo'    => betnero_checked( $it, 'tipologia_bonus' ),
				'pay'     => betnero_checked( $it, 'pagamenti' ),
			);
		} else {
			$ops[] = array(
				'name'     => betnero_text( $it, 'nome2' ),
				'logo'     => betnero_text( $it, 'logo_898' ),
				'bonus'    => betnero_text( $it, 'bonus' ),
				'multipla' => betnero_text( $it, 'bonus_multipla' ),
				'rating'   => betnero_text( $it, 'valutazione_857' ),
				'go'       => betnero_text( $it, 'visita_il_sito2' ),
				'review'   => '',
				'parere'   => '',
			);
		}
	}
	return $ops;
}

/* ---------- Render ---------- */

function betnero_yn( $v ) {
	$v = trim( (string) $v );
	return '' === $v ? '-' : esc_html( $v );
}

/**
 * Filtri (lato browser, senza ricaricare la pagina).
 */
function betnero_filters_html( $type ) {
	if ( 'casino' === $type ) {
		$filters = array(
			'welcome' => array( 'Bonus di benvenuto', array( '0' => '0€', '100' => '100€', '200' => '200€' ) ),
			'nd'      => array( 'Bonus senza deposito', array( 'Sì' => 'Sì', 'No' => 'No' ) ),
			'tipo'    => array( 'Tipologia bonus', array( '50' => '50% sul deposito', '100' => '100% sul deposito', '125' => '125% sul deposito' ) ),
			'pay'     => array( 'Pagamenti', array( 'assegno_vidimato' => 'Assegno vidimato', 'bancoposta' => 'Bancoposta' ) ),
		);
	} else {
		$filters = array(
			'multipla' => array( 'Bonus multipla', array( 'Sì' => 'Sì', 'No' => 'No' ) ),
		);
	}
	$h = '<div class="bnt-filters">';
	foreach ( $filters as $key => $f ) {
		$h .= '<label class="bnt-filter"><span>' . esc_html( $f[0] ) . '</span><select data-bnt-filter="' . esc_attr( $key ) . '"><option value="">Tutti</option>';
		foreach ( $f[1] as $val => $label ) {
			$h .= '<option value="' . esc_attr( $val ) . '">' . esc_html( $label ) . '</option>';
		}
		$h .= '</select></label>';
	}
	$h .= '<button type="button" class="bnt-reset" data-bnt-reset hidden>Rimuovi filtri</button></div>';
	return $h;
}

function betnero_render_table( $type, $attributes ) {
	$ops = betnero_operators( $type );
	if ( empty( $ops ) ) {
		return '';
	}
	$limit = isset( $attributes['limit'] ) ? (int) $attributes['limit'] : 0;
	if ( $limit > 0 ) {
		$ops = array_slice( $ops, 0, $limit );
	}
	$show_filters = ! empty( $attributes['showFilters'] );
	$show_parere  = ! isset( $attributes['showParere'] ) || $attributes['showParere'];
	$is_casino    = ( 'casino' === $type );

	if ( $show_filters ) {
		wp_enqueue_script( 'betnero-tables' );
	}

	$h  = '<div class="bnt-table bnt-' . esc_attr( $type ) . '" data-bnt-table>';
	$h .= $show_filters ? betnero_filters_html( $type ) : '';
	if ( $is_casino ) {
		$h .= '<div class="bnt-cols" aria-hidden="true"><span>Casinò online</span><span>Welcome bonus</span><span>Senza deposito</span><span>Registrazione SPID</span><span>Valutazione</span><span></span></div>';
	} else {
		$h .= '<div class="bnt-cols" aria-hidden="true"><span>Bookmaker</span><span>Bonus sport</span><span>Bonus multipla</span><span>Valutazione</span><span></span></div>';
	}
	$h .= '<div class="bnt-rows">';
	$n  = 0;
	foreach ( $ops as $op ) {
		$n++;
		$data = '';
		if ( $is_casino ) {
			$data .= ' data-welcome="' . esc_attr( implode( ' ', $op['welcome'] ) ) . '"';
			$data .= ' data-nd="' . esc_attr( trim( $op['nd'] ) ) . '"';
			$data .= ' data-tipo="' . esc_attr( implode( ' ', $op['tipo'] ) ) . '"';
			$data .= ' data-pay="' . esc_attr( implode( ' ', $op['pay'] ) ) . '"';
		} else {
			$data .= ' data-multipla="' . esc_attr( trim( $op['multipla'] ) ) . '"';
		}
		$h .= '<div class="bnt-row"' . $data . '>';
		$h .= '<div class="bnt-logo"><b class="bnt-rank">' . $n . '</b>' . betnero_img( $op['logo'], $op['name'] ) . '</div>';
		$h .= '<div class="bnt-bonus">' . betnero_bonus_html( $op['bonus'] ) . '</div>';
		if ( $is_casino ) {
			$h .= '<div class="bnt-yn"><span class="bnt-lbl">Senza deposito: </span>' . betnero_yn( $op['nd'] ) . '</div>';
			$h .= '<div class="bnt-yn"><span class="bnt-lbl">SPID: </span>' . betnero_yn( $op['spid'] ) . '</div>';
		} else {
			$h .= '<div class="bnt-yn"><span class="bnt-lbl">Bonus multipla: </span>' . betnero_yn( $op['multipla'] ) . '</div>';
		}
		$h .= '<div class="bnt-rate">' . betnero_stars( $op['rating'] ) . '</div>';
		$h .= '<div class="bnt-btns">' . betnero_go_button( $op['go'] ) . betnero_review_button( $op['review'] ) . '</div>';
		if ( $show_parere && $op['parere'] ) {
			$h .= '<div class="bnt-parere">' . wp_kses_post( $op['parere'] ) . '</div>';
		}
		$h .= '</div>';
	}
	$h .= '</div>';
	$h .= '<p class="bnt-empty" hidden>Nessun operatore corrisponde ai filtri. Rimuovi uno o più filtri.</p>';
	$h .= '<div class="bnt-adm">18+ | Gioca responsabilmente | Solo operatori con concessione ADM</div>';
	$h .= '</div>';
	return $h;
}

function betnero_render_toplist( $attributes ) {
	$type      = ( isset( $attributes['type'] ) && 'scommesse' === $attributes['type'] ) ? 'scommesse' : 'casino';
	$limit     = isset( $attributes['limit'] ) ? max( 1, (int) $attributes['limit'] ) : 5;
	$is_casino = ( 'casino' === $type );
	$ops       = array_slice( betnero_operators( $type ), 0, $limit );
	if ( empty( $ops ) ) {
		return '';
	}
	$title   = ! empty( $attributes['title'] ) ? $attributes['title'] : ( $is_casino ? 'I migliori casino online' : 'I migliori bookmaker' );
	$all_url = $is_casino ? home_url( '/casino.html' ) : home_url( '/scommesse-sportive.html' );
	$all_txt = $is_casino ? 'Vedi tutti i casino' : 'Vedi tutti i bookmaker';

	$h  = '<div class="bnt-table bnt-toplist bnt-' . esc_attr( $type ) . '">';
	$h .= '<div class="bnt-head"><span class="bnt-title">' . esc_html( $title ) . '</span><a class="bnt-all" href="' . esc_url( $all_url ) . '">' . esc_html( $all_txt ) . ' &rsaquo;</a></div>';
	if ( $is_casino ) {
		$h .= '<div class="bnt-cols" aria-hidden="true"><span>Casinò</span><span>Welcome bonus</span><span>Senza deposito</span><span>SPID</span><span>Valutazione</span><span></span></div>';
	} else {
		$h .= '<div class="bnt-cols" aria-hidden="true"><span>Bookmaker</span><span>Bonus sport</span><span>Bonus multipla</span><span>Valutazione</span><span></span></div>';
	}
	$h .= '<div class="bnt-rows bnt-scroll">';
	$n  = 0;
	foreach ( $ops as $op ) {
		$n++;
		$h .= '<div class="bnt-row">';
		$h .= '<div class="bnt-logo"><b class="bnt-rank">' . $n . '</b>' . betnero_img( $op['logo'], $op['name'] ) . '</div>';
		$h .= '<div class="bnt-bonus">' . betnero_bonus_html( $op['bonus'] ) . '</div>';
		if ( $is_casino ) {
			$h .= '<div class="bnt-yn"><span class="bnt-lbl">Senza deposito: </span>' . betnero_yn( $op['nd'] ) . '</div>';
			$h .= '<div class="bnt-yn"><span class="bnt-lbl">SPID: </span>' . betnero_yn( $op['spid'] ) . '</div>';
		} else {
			$h .= '<div class="bnt-yn"><span class="bnt-lbl">Bonus multipla: </span>' . betnero_yn( $op['multipla'] ) . '</div>';
		}
		$h .= '<div class="bnt-rate">' . betnero_stars( $op['rating'] ) . '</div>';
		$h .= '<div class="bnt-btns">' . betnero_go_button( $op['go'] ) . betnero_review_button( $op['review'] ) . '</div>';
		$h .= '</div>';
	}
	$h .= '</div>';
	$h .= '<div class="bnt-hint">Scorri per vedere gli altri &rarr;</div>';
	$h .= '<div class="bnt-adm">18+ | Gioca responsabilmente | Solo operatori con concessione ADM</div>';
	$h .= '</div>';
	return $h;
}

/* ---------- Registrazione ---------- */

add_action( 'init', function () {
	wp_register_style( 'betnero-tables', get_theme_file_uri( 'assets/css/tables.css' ), array(), BETNERO_THEME_VERSION );
	wp_register_script( 'betnero-tables', get_theme_file_uri( 'assets/js/tables.js' ), array(), BETNERO_THEME_VERSION, array( 'in_footer' => true, 'strategy' => 'defer' ) );
	wp_register_script(
		'betnero-blocks-editor',
		get_theme_file_uri( 'assets/js/blocks-editor.js' ),
		array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-server-side-render' ),
		BETNERO_THEME_VERSION,
		true
	);

	$common = array(
		'editor_script' => 'betnero-blocks-editor',
		'style'         => 'betnero-tables',
		'category'      => 'betnero',
		'supports'      => array( 'html' => false, 'align' => array( 'wide', 'full' ) ),
	);

	register_block_type( 'betnero/tabella-casino', $common + array(
		'title'           => 'Tabella casino',
		'attributes'      => array(
			'showFilters' => array( 'type' => 'boolean', 'default' => true ),
			'showParere'  => array( 'type' => 'boolean', 'default' => true ),
			'limit'       => array( 'type' => 'number', 'default' => 0 ),
			'align'       => array( 'type' => 'string' ),
		),
		'render_callback' => function ( $attributes ) {
			return betnero_render_table( 'casino', $attributes );
		},
	) );

	register_block_type( 'betnero/tabella-scommesse', $common + array(
		'title'           => 'Tabella scommesse',
		'attributes'      => array(
			'showFilters' => array( 'type' => 'boolean', 'default' => false ),
			'limit'       => array( 'type' => 'number', 'default' => 0 ),
			'align'       => array( 'type' => 'string' ),
		),
		'render_callback' => function ( $attributes ) {
			return betnero_render_table( 'scommesse', $attributes );
		},
	) );

	register_block_type( 'betnero/toplist', $common + array(
		'title'           => 'Toplist compatta',
		'attributes'      => array(
			'type'  => array( 'type' => 'string', 'default' => 'casino' ),
			'limit' => array( 'type' => 'number', 'default' => 5 ),
			'title' => array( 'type' => 'string', 'default' => '' ),
			'align' => array( 'type' => 'string' ),
		),
		'render_callback' => 'betnero_render_toplist',
	) );
} );

/* Categoria "Betnero" nell'inseritore dei blocchi */
add_filter( 'block_categories_all', function ( $cats ) {
	array_unshift( $cats, array( 'slug' => 'betnero', 'title' => 'Betnero' ) );
	return $cats;
} );

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
