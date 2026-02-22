/**
 * Tisch by Kohler — admin.js
 * Media uploader + Speisekarte repeater for Tisch Einstellungen admin page.
 * Loaded only on appearance_page_tisch-einstellungen.
 */

( function ( $ ) {
	'use strict';

	// ── Generic PDF upload (targets fields via data attributes) ──────────────
	$( document ).on( 'click', '.tisch-pdf-upload', function ( e ) {
		e.preventDefault();

		var $btn      = $( this );
		var urlTarget = $btn.data( 'url-target' );
		var idTarget  = $btn.data( 'id-target' );

		var frame = wp.media( {
			title:    'PDF aus Mediathek wählen',
			button:   { text: 'Dieses PDF verwenden' },
			library:  { type: 'application/pdf' },
			multiple: false,
		} );

		frame.on( 'select', function () {
			var attachment = frame.state().get( 'selection' ).first().toJSON();
			$( urlTarget ).val( attachment.url );
			$( idTarget ).val( attachment.id );
		} );

		frame.open();
	} );

	$( document ).on( 'click', '.tisch-pdf-remove', function ( e ) {
		e.preventDefault();
		var $btn      = $( this );
		var urlTarget = $btn.data( 'url-target' );
		var idTarget  = $btn.data( 'id-target' );
		$( urlTarget ).val( '' );
		$( idTarget ).val( '' );
		$btn.hide();
	} );

	// ── Speisekarte nested repeater ───────────────────────────────────────────

	/**
	 * Clone a <template> element by id and return the first root element.
	 */
	function cloneTemplate( id ) {
		var tpl   = document.getElementById( id );
		var $wrap = $( '<div>' ).append( $( tpl.content.cloneNode( true ) ) );
		return $wrap.children().first();
	}

	/**
	 * Re-assign name attributes to all section/item inputs based on current DOM order.
	 */
	function reindexSpeisekarte() {
		$( '#speisekarte-sections .tisch-menu-section' ).each( function ( si ) {
			var $section = $( this );
			// Section-level fields (title)
			$section.find( '[data-field]' ).each( function () {
				$( this ).attr(
					'name',
					'tisch_speisekarte_sections[' + si + '][' + $( this ).data( 'field' ) + ']'
				);
			} );
			// Item-level fields
			$section.find( '.tisch-menu-item' ).each( function ( ii ) {
				$( this ).find( '[data-item-field]' ).each( function () {
					$( this ).attr(
						'name',
						'tisch_speisekarte_sections[' + si + '][items][' + ii + '][' + $( this ).data( 'item-field' ) + ']'
					);
				} );
			} );
		} );
	}

	/**
	 * Create a new section row and append it to #speisekarte-sections.
	 */
	function addSection() {
		var $section = cloneTemplate( 'speisekarte-section-tpl' );
		$( '#speisekarte-sections' ).append( $section );
		reindexSpeisekarte();
		return $section;
	}

	/**
	 * Create a new item row and append it to a section's items container.
	 */
	function addItem( $section ) {
		var $item = cloneTemplate( 'speisekarte-item-tpl' );
		$section.find( '.tisch-section-items' ).append( $item );
		reindexSpeisekarte();
		return $item;
	}

	// Button: add section
	$( '#speisekarte-add-section' ).on( 'click', function () {
		addSection();
	} );

	// Button: add item (delegated — sections are added dynamically)
	$( document ).on( 'click', '.tisch-add-item', function () {
		var $section = $( this ).closest( '.tisch-menu-section' );
		addItem( $section );
	} );

	// Button: move section up
	$( document ).on( 'click', '.tisch-move-section-up', function () {
		var $section = $( this ).closest( '.tisch-menu-section' );
		var $prev    = $section.prev( '.tisch-menu-section' );
		if ( $prev.length ) { $prev.before( $section ); }
		reindexSpeisekarte();
	} );

	// Button: move section down
	$( document ).on( 'click', '.tisch-move-section-down', function () {
		var $section = $( this ).closest( '.tisch-menu-section' );
		var $next    = $section.next( '.tisch-menu-section' );
		if ( $next.length ) { $next.after( $section ); }
		reindexSpeisekarte();
	} );

	// Button: remove section
	$( document ).on( 'click', '.tisch-remove-section', function () {
		$( this ).closest( '.tisch-menu-section' ).remove();
		reindexSpeisekarte();
	} );

	// Button: remove item
	$( document ).on( 'click', '.tisch-remove-item', function () {
		$( this ).closest( '.tisch-menu-item' ).remove();
		reindexSpeisekarte();
	} );

	// On page load: populate from saved PHP data
	var savedData = ( typeof tischAdminData !== 'undefined' && tischAdminData.speisekarteData )
		? tischAdminData.speisekarteData
		: [];

	if ( Array.isArray( savedData ) ) {
		savedData.forEach( function ( section ) {
			var $section = addSection();
			if ( section.title ) {
				$section.find( '[data-field="title"]' ).val( section.title );
			}
			if ( Array.isArray( section.items ) ) {
				section.items.forEach( function ( item ) {
					var $item = addItem( $section );
					$item.find( '[data-item-field="name"]' ).val( item.name  || '' );
					$item.find( '[data-item-field="price"]' ).val( item.price || '' );
					$item.find( '[data-item-field="desc"]' ).val( item.desc  || '' );
					$item.find( '[data-item-field="note"]' ).val( item.note  || '' );
				} );
			}
		} );
	}

} )( jQuery );
