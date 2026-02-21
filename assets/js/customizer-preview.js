/* Tisch by Kohler — Customizer live-preview for color tokens */
( function () {
    'use strict';

    var styleId = 'tisch-color-overrides';
    var varMap  = {
        tisch_color_primary:      '--color-primary',
        tisch_color_primary_dark: '--color-primary-dark',
        tisch_color_accent:       '--color-accent',
        tisch_color_bg:           '--color-bg',
    };

    // Current values — start from what PHP already rendered
    var current = {};

    function getOrCreateStyle() {
        var el = document.getElementById( styleId );
        if ( ! el ) {
            el = document.createElement( 'style' );
            el.id = styleId;
            document.head.appendChild( el );
        }
        return el;
    }

    function rebuild() {
        var css = '';
        Object.keys( varMap ).forEach( function ( key ) {
            var val = current[ key ];
            if ( val && /^#[0-9a-fA-F]{3,6}$/.test( val ) ) {
                css += varMap[ key ] + ':' + val + ';';
            }
        } );
        getOrCreateStyle().textContent = css ? ':root{' + css + '}' : '';
    }

    Object.keys( varMap ).forEach( function ( settingId ) {
        wp.customize( settingId, function ( value ) {
            value.bind( function ( newVal ) {
                current[ settingId ] = newVal;
                rebuild();
            } );
        } );
    } );

}() );
