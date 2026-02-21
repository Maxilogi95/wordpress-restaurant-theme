/**
 * Tisch by Kohler — reservation.js
 * Client-side validation for the reservation form.
 * German error messages. Enhancement only — form works without JS.
 */

( function () {
	'use strict';

	const form = document.getElementById( 'reservation-form' );
	if ( ! form ) {
		return;
	}

	// ── Validation rules ────────────────────────────────────────────────────

	/** @type {Record<string, (value: string, el: HTMLElement) => string>} */
	const validators = {
		tisch_name( value ) {
			if ( ! value.trim() ) return 'Bitte geben Sie Ihren Namen an.';
			if ( value.trim().length < 2 ) return 'Der Name muss mindestens 2 Zeichen haben.';
			return '';
		},

		tisch_email( value ) {
			if ( ! value.trim() ) return 'Bitte geben Sie Ihre E-Mail-Adresse an.';
			// Simple RFC-ish check
			if ( ! /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test( value.trim() ) ) {
				return 'Bitte geben Sie eine gültige E-Mail-Adresse an.';
			}
			return '';
		},

		tisch_date( value ) {
			if ( ! value ) return 'Bitte wählen Sie ein Datum aus.';
			const selected = new Date( value );
			const today    = new Date();
			today.setHours( 0, 0, 0, 0 );
			if ( selected <= today ) return 'Das Datum muss in der Zukunft liegen.';
			return '';
		},

		tisch_time( value ) {
			if ( ! value ) return 'Bitte wählen Sie eine Uhrzeit aus.';
			return '';
		},

		tisch_guests( value ) {
			const n = parseInt( value, 10 );
			if ( ! value || isNaN( n ) || n < 1 ) return 'Bitte geben Sie die Personenzahl an.';
			if ( n > 100 ) return 'Für Gruppen über 100 Personen wenden Sie sich bitte telefonisch an uns.';
			return '';
		},

		tisch_dsgvo( _value, el ) {
			if ( ! el.checked ) return 'Bitte stimmen Sie der Datenschutzerklärung zu.';
			return '';
		},
	};

	// ── Show / clear errors ─────────────────────────────────────────────────

	/**
	 * @param {HTMLElement} field
	 * @param {string}      message
	 */
	function showError( field, message ) {
		field.classList.add( 'has-error' );
		field.setAttribute( 'aria-invalid', 'true' );

		let errorEl = document.getElementById( field.id + '-error' );
		if ( ! errorEl ) {
			errorEl = document.createElement( 'span' );
			errorEl.id        = field.id + '-error';
			errorEl.className = 'form-field__error';
			errorEl.setAttribute( 'role', 'alert' );
			field.parentNode.insertBefore( errorEl, field.nextSibling );
		}
		errorEl.textContent = message;
		field.setAttribute( 'aria-describedby', errorEl.id );
	}

	/**
	 * @param {HTMLElement} field
	 */
	function clearError( field ) {
		field.classList.remove( 'has-error' );
		field.setAttribute( 'aria-invalid', 'false' );

		const errorEl = document.getElementById( field.id + '-error' );
		if ( errorEl ) {
			errorEl.remove();
		}
		field.removeAttribute( 'aria-describedby' );
	}

	// ── Validate a single field ──────────────────────────────────────────────

	/**
	 * @param {HTMLInputElement|HTMLSelectElement|HTMLTextAreaElement} el
	 * @returns {boolean}
	 */
	function validateField( el ) {
		const validator = validators[ el.name ];
		if ( ! validator ) {
			return true;
		}
		const error = validator( el.value, el );
		if ( error ) {
			showError( el, error );
			return false;
		}
		clearError( el );
		return true;
	}

	// ── Live validation on blur ──────────────────────────────────────────────

	Object.keys( validators ).forEach( function ( name ) {
		const el = form.elements[ name ];
		if ( ! el ) return;

		el.addEventListener( 'blur', function () {
			validateField( el );
		} );

		el.addEventListener( 'input', function () {
			// Only clear errors on input (don't trigger new ones)
			if ( el.classList.contains( 'has-error' ) ) {
				validateField( el );
			}
		} );
	} );

	// ── Full validation on submit ─────────────────────────────────────────────

	form.addEventListener( 'submit', function ( e ) {
		let isValid = true;
		let firstInvalid = null;

		Object.keys( validators ).forEach( function ( name ) {
			const el = form.elements[ name ];
			if ( ! el ) return;
			const fieldValid = validateField( el );
			if ( ! fieldValid ) {
				isValid = false;
				if ( ! firstInvalid ) {
					firstInvalid = el;
				}
			}
		} );

		if ( ! isValid ) {
			e.preventDefault();
			if ( firstInvalid ) {
				firstInvalid.focus();
			}
		}
	} );
}() );
