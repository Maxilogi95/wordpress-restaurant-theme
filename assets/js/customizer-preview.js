/* Tisch by Kohler — Customizer live-preview */
( function () {
    'use strict';

    // ── Group A-1: Color tokens (existing) ────────────────────────────────────
    var colorStyleId = 'tisch-color-overrides';
    var colorVarMap  = {
        tisch_color_primary:      '--color-primary',
        tisch_color_primary_dark: '--color-primary-dark',
        tisch_color_accent:       '--color-accent',
        tisch_color_bg:           '--color-bg',
    };
    var currentColors = {};

    function getOrCreateStyle( id ) {
        var el = document.getElementById( id );
        if ( ! el ) {
            el = document.createElement( 'style' );
            el.id = id;
            document.head.appendChild( el );
        }
        return el;
    }

    function rebuildColors() {
        var css = '';
        Object.keys( colorVarMap ).forEach( function ( key ) {
            var val = currentColors[ key ];
            if ( val && /^#[0-9a-fA-F]{3,6}$/.test( val ) ) {
                css += colorVarMap[ key ] + ':' + val + ';';
            }
        } );
        getOrCreateStyle( colorStyleId ).textContent = css ? ':root{' + css + '}' : '';
    }

    Object.keys( colorVarMap ).forEach( function ( settingId ) {
        wp.customize( settingId, function ( value ) {
            value.bind( function ( newVal ) {
                currentColors[ settingId ] = newVal;
                rebuildColors();
            } );
        } );
    } );

    // ── Group A-2: Design tokens (typography, layout) ─────────────────────────
    var tokenStyleId   = 'tisch-design-tokens';
    var currentTokens  = {};

    var fontHeadingMap = {
        playfair: "'Playfair Display',Georgia,'Times New Roman',serif",
        georgia:  "Georgia,'Times New Roman',serif",
    };
    var fontBodyMap = {
        lato:   "'Lato',system-ui,-apple-system,sans-serif",
        system: "system-ui,-apple-system,sans-serif",
    };

    function rebuildTokens() {
        var width  = parseInt( currentTokens.tisch_container_width, 10 ) || 1200;
        var radius = parseInt( currentTokens.tisch_border_radius, 10 );
        if ( isNaN( radius ) ) { radius = 8; }
        var radiusSm = Math.round( radius / 2 );
        var radiusLg = Math.min( radius * 2, 32 );

        var headingKey  = currentTokens.tisch_font_heading || 'playfair';
        var bodyKey     = currentTokens.tisch_font_body    || 'lato';
        var headingStack = fontHeadingMap[ headingKey ] || fontHeadingMap.playfair;
        var bodyStack    = fontBodyMap[ bodyKey ]        || fontBodyMap.lato;

        var css = '--container-max:' + width + 'px;'
            + '--radius-sm:' + radiusSm + 'px;'
            + '--radius-md:' + radius   + 'px;'
            + '--radius-lg:' + radiusLg + 'px;'
            + '--font-heading:' + headingStack + ';'
            + '--font-body:'    + bodyStack    + ';';

        getOrCreateStyle( tokenStyleId ).textContent = ':root{' + css + '}';
    }

    [ 'tisch_container_width', 'tisch_border_radius',
      'tisch_font_heading', 'tisch_font_body' ].forEach( function ( id ) {
        wp.customize( id, function ( value ) {
            value.bind( function ( newVal ) {
                currentTokens[ id ] = newVal;
                rebuildTokens();
            } );
        } );
    } );

    // ── Group B: Text content updates ─────────────────────────────────────────
    function bindText( settingId, selector, toggleEmpty ) {
        wp.customize( settingId, function ( value ) {
            value.bind( function ( newVal ) {
                var els = document.querySelectorAll( selector );
                els.forEach( function ( el ) {
                    el.textContent = newVal;
                    if ( toggleEmpty ) {
                        el.style.display = newVal ? '' : 'none';
                    }
                } );
            } );
        } );
    }

    bindText( 'tisch_hero_tagline',    '.hero__tagline',              false );
    bindText( 'tisch_hero_subheadline','.hero__subheadline',          true  );
    bindText( 'tisch_hero_btn1_text',  '.hero__btn1',                 false );
    bindText( 'tisch_hero_btn2_text',  '.hero__btn2',                 false );
    bindText( 'tisch_footer_copyright','.site-footer__copyright-text',false );

    // Header CTA text (visibility controlled by both text + url below)
    wp.customize( 'tisch_header_cta_text', function ( value ) {
        value.bind( function () { updateHeaderCta(); } );
    } );

    // ── Group C: href updates ─────────────────────────────────────────────────
    function bindHref( settingId, selector ) {
        wp.customize( settingId, function ( value ) {
            value.bind( function ( newVal ) {
                document.querySelectorAll( selector ).forEach( function ( el ) {
                    el.href = newVal || '#';
                } );
            } );
        } );
    }

    bindHref( 'tisch_hero_btn1_url', '.hero__btn1' );
    bindHref( 'tisch_hero_btn2_url', '.hero__btn2' );

    wp.customize( 'tisch_header_cta_url', function ( value ) {
        value.bind( function () { updateHeaderCta(); } );
    } );

    function updateHeaderCta() {
        var ctaText = wp.customize( 'tisch_header_cta_text' )();
        var ctaUrl  = wp.customize( 'tisch_header_cta_url' )();
        document.querySelectorAll( '.site-header__cta' ).forEach( function ( el ) {
            el.textContent  = ctaText;
            el.href         = ctaUrl || '#';
            el.style.display = ( ctaText && ctaUrl ) ? '' : 'none';
        } );
    }

    // ── Group D: Class toggle / show-hide ─────────────────────────────────────
    function bindToggleClass( settingId, selector, className ) {
        wp.customize( settingId, function ( value ) {
            value.bind( function ( newVal ) {
                document.querySelectorAll( selector ).forEach( function ( el ) {
                    if ( newVal ) {
                        el.classList.add( className );
                    } else {
                        el.classList.remove( className );
                    }
                } );
            } );
        } );
    }

    function bindToggleVisibility( settingId, selector ) {
        wp.customize( settingId, function ( value ) {
            value.bind( function ( newVal ) {
                document.querySelectorAll( selector ).forEach( function ( el ) {
                    el.style.display = newVal ? '' : 'none';
                } );
            } );
        } );
    }

    bindToggleClass(      'tisch_sticky_header',         '.site-header',          'is-sticky' );
    bindToggleVisibility( 'tisch_header_show_phone',     '.site-header__phone'               );
    bindToggleVisibility( 'tisch_show_welcome',          '.welcome-section'                  );
    bindToggleVisibility( 'tisch_show_tagesessen_teaser','.teaser-section'                   );
    bindToggleVisibility( 'tisch_show_opening_hours',    '.hours-section'                    );

    // Header layout class swap
    wp.customize( 'tisch_header_layout', function ( value ) {
        var allowed = [ 'left', 'split', 'centered', 'split-right', 'right' ];
        value.bind( function ( newVal ) {
            var header = document.querySelector( '.site-header' );
            if ( ! header ) { return; }
            allowed.forEach( function ( v ) {
                header.classList.remove( 'layout-' + v );
            } );
            if ( allowed.indexOf( newVal ) !== -1 ) {
                header.classList.add( 'layout-' + newVal );
            }
        } );
    } );

}() );
