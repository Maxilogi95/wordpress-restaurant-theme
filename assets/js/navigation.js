/**
 * Tisch by Kohler — navigation.js
 * Mobile menu toggle. Vanilla JS, ~1 KB, no dependencies.
 * Keyboard-accessible: Escape closes menu.
 */

( function () {
	'use strict';

	const toggle = document.getElementById( 'menu-toggle' );
	const menu   = document.getElementById( 'primary-menu' );

	if ( ! toggle || ! menu ) {
		return;
	}

	/**
	 * Open or close the nav.
	 * @param {boolean} open
	 */
	function setMenuState( open ) {
		toggle.setAttribute( 'aria-expanded', String( open ) );
		toggle.setAttribute(
			'aria-label',
			open ? 'Menü schließen' : 'Menü öffnen'
		);
		menu.classList.toggle( 'is-open', open );
	}

	// Click / tap toggle
	toggle.addEventListener( 'click', function () {
		const isOpen = toggle.getAttribute( 'aria-expanded' ) === 'true';
		setMenuState( ! isOpen );
	} );

	// Close on Escape
	document.addEventListener( 'keydown', function ( e ) {
		if ( e.key === 'Escape' && toggle.getAttribute( 'aria-expanded' ) === 'true' ) {
			setMenuState( false );
			toggle.focus();
		}
	} );

	// Close when clicking outside
	document.addEventListener( 'click', function ( e ) {
		const nav = document.getElementById( 'site-navigation' );
		if ( nav && ! nav.contains( e.target ) ) {
			setMenuState( false );
		}
	} );

	// On resize to desktop: reset menu state
	const mediaQuery = window.matchMedia( '(min-width: 768px)' );
	mediaQuery.addEventListener( 'change', function ( e ) {
		if ( e.matches ) {
			setMenuState( false );
		}
	} );
}() );
