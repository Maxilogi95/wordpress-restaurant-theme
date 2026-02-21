/**
 * Tisch by Kohler — osm-consent.js
 * OSM map consent overlay. Stores consent in localStorage so the map
 * auto-loads on subsequent page visits.
 */
( function () {
    'use strict';

    var STORAGE_KEY = 'tisch_osm_consent';
    var consent     = document.getElementById( 'osm-consent' );
    var iframe      = document.getElementById( 'osm-iframe' );
    var btn         = document.getElementById( 'osm-accept-btn' );

    if ( ! consent || ! iframe || ! btn ) {
        return;
    }

    function loadMap() {
        iframe.src = iframe.dataset.src;
        iframe.classList.remove( 'osm-map__iframe--hidden' );
        try {
            localStorage.setItem( STORAGE_KEY, '1' );
        } catch ( e ) {
            // Ignore storage errors (private browsing, quota exceeded, etc.)
        }
    }

    // Auto-load if user previously accepted — hide overlay immediately (no flash)
    try {
        if ( localStorage.getItem( STORAGE_KEY ) === '1' ) {
            consent.hidden = true;
            loadMap();
            return;
        }
    } catch ( e ) {
        // Ignore storage errors
    }

    // First visit: hide overlay only after the iframe has fully loaded
    btn.addEventListener( 'click', function () {
        loadMap();
        iframe.addEventListener( 'load', function onLoad() {
            consent.hidden = true;
            iframe.removeEventListener( 'load', onLoad );
        } );
    } );
}() );
