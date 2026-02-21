<?php
declare(strict_types=1);
/**
 * Tisch by Kohler — inc/enqueue.php
 * CSS + JS, conditional per template.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'wp_enqueue_scripts', 'tisch_enqueue_assets' );

function tisch_enqueue_assets(): void {

    $ver = wp_get_theme()->get( 'Version' );
    $dir = get_template_directory_uri();

    // ── Fonts (self-hosted, no CDN) ─────────────────────────────────────
    wp_enqueue_style(
        'tisch-fonts',
        $dir . '/assets/fonts/fonts.css',
        [],
        $ver
    );

    // ── Main stylesheet ──────────────────────────────────────────────────
    wp_enqueue_style(
        'tisch-main',
        $dir . '/assets/css/main.css',
        [ 'tisch-fonts' ],
        $ver
    );

    // ── Print stylesheet ─────────────────────────────────────────────────
    wp_enqueue_style(
        'tisch-print',
        $dir . '/assets/css/print.css',
        [ 'tisch-main' ],
        $ver,
        'print'
    );

    // ── Navigation JS (all pages) ─────────────────────────────────────────
    wp_enqueue_script(
        'tisch-navigation',
        $dir . '/assets/js/navigation.js',
        [],
        $ver,
        [ 'strategy' => 'defer', 'in_footer' => true ]
    );

    // ── Reservation JS (only on Reservierung template) ────────────────────
    if ( is_page_template( 'page-templates/reservierung.php' ) ) {
        wp_enqueue_script(
            'tisch-reservation',
            $dir . '/assets/js/reservation.js',
            [],
            $ver,
            [ 'strategy' => 'defer', 'in_footer' => true ]
        );
    }

    // ── OSM consent JS (only on Kontakt template) ─────────────────────────
    if ( is_page_template( 'page-templates/kontakt.php' ) ) {
        wp_enqueue_script(
            'tisch-osm-consent',
            $dir . '/assets/js/osm-consent.js',
            [],
            $ver,
            [ 'strategy' => 'defer', 'in_footer' => true ]
        );
    }
}

// ── Customizer preview JS ─────────────────────────────────────────────────────
add_action( 'customize_preview_init', 'tisch_customize_preview_scripts' );

function tisch_customize_preview_scripts(): void {
    wp_enqueue_script(
        'tisch-customizer-preview',
        get_template_directory_uri() . '/assets/js/customizer-preview.js',
        [ 'customize-preview' ],
        wp_get_theme()->get( 'Version' ),
        true
    );
}

/**
 * Remove unnecessary default WordPress styles.
 */
add_action( 'wp_enqueue_scripts', 'tisch_dequeue_defaults', 20 );

function tisch_dequeue_defaults(): void {
    // Remove classic theme styles block library (we don't use Gutenberg blocks heavily)
    wp_dequeue_style( 'wp-block-library' );
    wp_dequeue_style( 'wp-block-library-theme' );
    wp_dequeue_style( 'global-styles' );
}
