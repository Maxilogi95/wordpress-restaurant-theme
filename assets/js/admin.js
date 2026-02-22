/**
 * Tisch by Kohler — admin.js
 * Media uploader + Speisekarte repeater + schedule/special repeaters
 * for Tisch Einstellungen admin page.
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

	// ── Shared helper ─────────────────────────────────────────────────────────

	/**
	 * Clone a <template> element by id and return the first root element.
	 */
	function cloneTemplate( id ) {
		var tpl   = document.getElementById( id );
		var $wrap = $( '<div>' ).append( $( tpl.content.cloneNode( true ) ) );
		return $wrap.children().first();
	}

	// ── Per-day schedule repeater ─────────────────────────────────────────────

	/**
	 * Re-assign name attributes for all slots of a given weekday.
	 */
	function reindexSlots( day ) {
		$( '#tisch-slots-' + day + ' .tisch-time-slot' ).each( function ( i ) {
			$( this ).find( '[data-slot-field]' ).each( function () {
				$( this ).attr(
					'name',
					'tisch_hours_schedule[' + day + '][slots][' + i + '][' + $( this ).data( 'slot-field' ) + ']'
				);
			} );
		} );
	}

	/**
	 * Clone the time-slot template and append it to the day's slot container.
	 */
	function addSlot( day ) {
		var $slot = cloneTemplate( 'time-slot-tpl' );
		$( '#tisch-slots-' + day ).append( $slot );
		reindexSlots( day );
		return $slot;
	}

	/**
	 * Show or hide the slot container and add-button for a day.
	 */
	function closedToggle( day, isClosed ) {
		var $container = $( '#tisch-slots-' + day );
		var $addBtn    = $( '.tisch-add-slot[data-day="' + day + '"]' );
		if ( isClosed ) {
			$container.hide();
			$addBtn.hide();
		} else {
			$container.show();
			$addBtn.show();
		}
	}

	// Closed checkbox change
	$( document ).on( 'change', '.tisch-closed-cb', function () {
		closedToggle( $( this ).data( 'day' ), $( this ).prop( 'checked' ) );
	} );

	// Add slot button
	$( document ).on( 'click', '.tisch-add-slot', function () {
		addSlot( $( this ).data( 'day' ) );
	} );

	// Remove slot button
	$( document ).on( 'click', '.tisch-remove-slot', function () {
		var $slot = $( this ).closest( '.tisch-time-slot' );
		var day   = $slot.closest( '.tisch-slots-container' ).attr( 'id' ).replace( 'tisch-slots-', '' );
		$slot.remove();
		reindexSlots( day );
	} );

	/**
	 * Populate slot containers from saved scheduleData on page load.
	 */
	function initSchedule() {
		var scheduleData = ( typeof tischAdminData !== 'undefined' && tischAdminData.scheduleData )
			? tischAdminData.scheduleData
			: {};
		var days = [ 'mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun' ];
		days.forEach( function ( day ) {
			var dayData = scheduleData[ day ] || { closed: false, slots: [] };
			if ( Array.isArray( dayData.slots ) ) {
				dayData.slots.forEach( function ( slot ) {
					var $slot = addSlot( day );
					$slot.find( '[data-slot-field="open"]' ).val( slot.open || '' );
					$slot.find( '[data-slot-field="close"]' ).val( slot.close || '' );
				} );
			}
		} );
	}

	// ── Sonderöffnungszeiten repeater ─────────────────────────────────────────

	/**
	 * Re-assign name attributes for all special entries and their slots.
	 */
	function reindexSpecial() {
		$( '#tisch-special-entries .tisch-special-entry' ).each( function ( ei ) {
			var $entry = $( this );
			// Entry-level fields
			$entry.find( '[data-special-field]' ).each( function () {
				var field = $( this ).data( 'special-field' );
				$( this ).attr(
					'name',
					'tisch_hours_special[' + ei + '][' + field + ']'
				);
			} );
			// Slot-level fields
			$entry.find( '.tisch-special-slot' ).each( function ( si ) {
				$( this ).find( '[data-special-slot-field]' ).each( function () {
					$( this ).attr(
						'name',
						'tisch_hours_special[' + ei + '][slots][' + si + '][' + $( this ).data( 'special-slot-field' ) + ']'
					);
				} );
			} );
		} );
	}

	/**
	 * Add a time slot within a special entry.
	 */
	function addSpecialSlot( $entry ) {
		var $slot = cloneTemplate( 'special-slot-tpl' );
		$entry.find( '.tisch-special-slots-container' ).append( $slot );
		reindexSpecial();
		return $slot;
	}

	/**
	 * Clone the special-entry template and append it to #tisch-special-entries.
	 */
	function addSpecial() {
		var $entry = cloneTemplate( 'special-entry-tpl' );
		$( '#tisch-special-entries' ).append( $entry );
		reindexSpecial();
		return $entry;
	}

	// Add special entry button
	$( '#tisch-add-special' ).on( 'click', function () {
		addSpecial();
	} );

	// Remove special entry button (delegated)
	$( document ).on( 'click', '.tisch-remove-special', function () {
		$( this ).closest( '.tisch-special-entry' ).remove();
		reindexSpecial();
	} );

	// Add slot within special entry (delegated)
	$( document ).on( 'click', '.tisch-add-special-slot', function () {
		addSpecialSlot( $( this ).closest( '.tisch-special-entry' ) );
	} );

	// Remove slot within special entry (delegated)
	$( document ).on( 'click', '.tisch-remove-special-slot', function () {
		$( this ).closest( '.tisch-special-slot' ).remove();
		reindexSpecial();
	} );

	// Closed checkbox within special entry (delegated)
	$( document ).on( 'change', '.tisch-special-closed-cb', function () {
		var $entry   = $( this ).closest( '.tisch-special-entry' );
		var isClosed = $( this ).prop( 'checked' );
		$entry.find( '.tisch-special-slots-container' ).toggle( ! isClosed );
		$entry.find( '.tisch-add-special-slot' ).toggle( ! isClosed );
	} );

	/**
	 * Populate special entries from saved specialData on page load.
	 */
	function initSpecial() {
		var specialData = ( typeof tischAdminData !== 'undefined' && Array.isArray( tischAdminData.specialData ) )
			? tischAdminData.specialData
			: [];
		specialData.forEach( function ( entryData ) {
			var $entry = addSpecial();
			$entry.find( '[data-special-field="date"]' ).val( entryData.date || '' );
			$entry.find( '[data-special-field="label"]' ).val( entryData.label || '' );
			if ( entryData.closed ) {
				$entry.find( '[data-special-field="closed"]' ).prop( 'checked', true );
				$entry.find( '.tisch-special-slots-container' ).hide();
				$entry.find( '.tisch-add-special-slot' ).hide();
			}
			if ( Array.isArray( entryData.slots ) ) {
				entryData.slots.forEach( function ( slot ) {
					var $slot = addSpecialSlot( $entry );
					$slot.find( '[data-special-slot-field="open"]' ).val( slot.open || '' );
					$slot.find( '[data-special-slot-field="close"]' ).val( slot.close || '' );
				} );
			}
		} );
	}

	// ── Speisekarte nested repeater ───────────────────────────────────────────

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

	// ── Page load initialisation ──────────────────────────────────────────────

	// Schedule: populate slot containers from saved PHP data
	initSchedule();

	// Special dates: populate from saved PHP data
	initSpecial();

	// Speisekarte: populate from saved PHP data
	var savedSpeisekarte = ( typeof tischAdminData !== 'undefined' && tischAdminData.speisekarteData )
		? tischAdminData.speisekarteData
		: [];

	if ( Array.isArray( savedSpeisekarte ) ) {
		savedSpeisekarte.forEach( function ( section ) {
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
