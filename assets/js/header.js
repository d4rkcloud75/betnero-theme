/*
 * Betnero: header.
 * - Ricerca: pulsante x per chiudere il campo aperto (usa la chiusura del blocco Cerca, tasto Esc).
 * - Menu mobile: i sottomenu sono chiusi e si aprono a tendina con la freccia.
 */
( function () {
	function initSearch() {
		document.querySelectorAll( '.bn-search' ).forEach( function ( form ) {
			var input = form.querySelector( '.wp-block-search__input' );
			if ( ! input || form.querySelector( '.bn-search-close' ) ) {
				return;
			}
			var x = document.createElement( 'button' );
			x.type = 'button';
			x.className = 'bn-search-close';
			x.setAttribute( 'aria-label', 'Chiudi la ricerca' );
			x.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>';
			input.insertAdjacentElement( 'afterend', x );
			// Il clic sulla x non deve togliere il fuoco al campo prima della chiusura.
			x.addEventListener( 'mousedown', function ( e ) {
				e.preventDefault();
			} );
			x.addEventListener( 'click', function () {
				input.value = '';
				input.dispatchEvent( new KeyboardEvent( 'keydown', { key: 'Escape', bubbles: true } ) );
				input.blur();
			} );
		} );
	}

	function initMobileSubmenus() {
		document.querySelectorAll( '.bn-nav .wp-block-navigation-item.has-child' ).forEach( function ( li, i ) {
			if ( li.querySelector( ':scope > .bn-sub-toggle' ) ) {
				return;
			}
			var sub = li.querySelector( ':scope > .wp-block-navigation__submenu-container' );
			var link = li.querySelector( ':scope > .wp-block-navigation-item__content' );
			if ( ! sub || ! link ) {
				return;
			}
			if ( ! sub.id ) {
				sub.id = 'bn-sub-' + i;
			}
			var b = document.createElement( 'button' );
			b.type = 'button';
			b.className = 'bn-sub-toggle';
			b.setAttribute( 'aria-expanded', 'false' );
			b.setAttribute( 'aria-controls', sub.id );
			b.setAttribute( 'aria-label', 'Apri il sottomenu ' + link.textContent.trim() );
			b.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>';
			link.insertAdjacentElement( 'afterend', b );
			b.addEventListener( 'click', function ( e ) {
				e.preventDefault();
				e.stopPropagation();
				var open = li.classList.toggle( 'bn-sub-open' );
				b.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
				if ( open && sub.animate && ! window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) {
					sub.animate( [ { opacity: 0, transform: 'translateY(-6px)' }, { opacity: 1, transform: 'none' } ], { duration: 220, easing: 'cubic-bezier(.2,.7,.2,1)' } );
				}
			} );
		} );
	}

	function boot() {
		initSearch();
		initMobileSubmenus();
	}
	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', boot );
	} else {
		boot();
	}
} )();

/* Pulsante "torna su": compare dopo un po' di scroll. */
( function () {
	function boot() {
		var b = document.querySelector( '.bn-totop' );
		if ( ! b ) {
			return;
		}
		var shown = false;
		function upd() {
			var on = window.scrollY > 600;
			if ( on !== shown ) {
				shown = on;
				b.hidden = false;
				b.classList.toggle( 'is-on', on );
			}
		}
		window.addEventListener( 'scroll', upd, { passive: true } );
		upd();
		b.addEventListener( 'click', function () {
			var reduce = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
			window.scrollTo( { top: 0, behavior: reduce ? 'auto' : 'smooth' } );
		} );
	}
	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', boot );
	} else {
		boot();
	}
} )();
