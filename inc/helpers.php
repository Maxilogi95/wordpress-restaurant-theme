<?php
declare(strict_types=1);
/**
 * Tisch by Kohler — inc/helpers.php
 * Shared utility functions.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Check whether the Tagesessen PDF is still valid.
 * Returns false if no PDF is set or the date has passed.
 */
function tisch_tagesessen_is_valid(): bool {
    $pdf = get_option( 'tisch_tagesessen_pdf', '' );
    if ( empty( $pdf ) ) {
        return false;
    }

    $valid_until = get_option( 'tisch_tagesessen_valid_until', '' );
    if ( empty( $valid_until ) ) {
        return true; // No expiry set — always valid.
    }

    // Reject malformed values instead of falling through to "always valid".
    if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $valid_until ) ) {
        return false;
    }

    // Compare date strings in the site's timezone (WordPress forces PHP to UTC).
    $today = ( new \DateTimeImmutable( 'today', wp_timezone() ) )->format( 'Y-m-d' );
    return $valid_until >= $today;
}

/**
 * Build an OpenStreetMap embed URL from the stored coordinates.
 */
function tisch_osm_embed_url(): string {
    $lat  = (float) get_option( 'tisch_osm_lat', '48.087400' );
    $lng  = (float) get_option( 'tisch_osm_lng', '9.218900' );
    $zoom = 0.002;

    $bbox = implode( ',', [
        number_format( $lng - $zoom, 6, '.', '' ),
        number_format( $lat - $zoom, 6, '.', '' ),
        number_format( $lng + $zoom, 6, '.', '' ),
        number_format( $lat + $zoom, 6, '.', '' ),
    ] );

    return 'https://www.openstreetmap.org/export/embed.html'
        . '?bbox=' . $bbox
        . '&layer=mapnik'
        . '&marker=' . number_format( $lat, 6, '.', '' ) . ',' . number_format( $lng, 6, '.', '' );
}

/**
 * Return the opening hours as a 7-element structured array.
 * Each element: [ 'label' => string, 'hours' => string, 'closed' => bool ]
 *
 * @return array<int, array{label: string, hours: string, closed: bool}>
 */
function tisch_get_opening_hours(): array {
    $days = [
        [ 'key' => 'mon', 'label' => 'Montag' ],
        [ 'key' => 'tue', 'label' => 'Dienstag' ],
        [ 'key' => 'wed', 'label' => 'Mittwoch' ],
        [ 'key' => 'thu', 'label' => 'Donnerstag' ],
        [ 'key' => 'fri', 'label' => 'Freitag' ],
        [ 'key' => 'sat', 'label' => 'Samstag' ],
        [ 'key' => 'sun', 'label' => 'Sonn- und Feiertag' ],
    ];

    $result = [];
    foreach ( $days as $day ) {
        $result[] = [
            'label'  => $day['label'],
            'hours'  => (string) get_option( 'tisch_hours_' . $day['key'], '' ),
            'closed' => (bool) get_option( 'tisch_hours_' . $day['key'] . '_closed', '' ),
        ];
    }
    return $result;
}

/**
 * Check whether today falls within any configured closing period.
 * Returns the matching closing entry array, or an empty array if none active.
 *
 * @return array{from: string, to: string, label: string}|array{}
 */
function tisch_get_active_closing(): array {
    $today = new \DateTimeImmutable( 'today' );
    for ( $i = 1; $i <= 3; $i++ ) {
        $from  = get_option( "tisch_closing_{$i}_from", '' );
        $to    = get_option( "tisch_closing_{$i}_to", '' );
        $label = get_option( "tisch_closing_{$i}_label", '' );
        if ( ! $from || ! $to ) {
            continue;
        }
        $from_dt = \DateTimeImmutable::createFromFormat( 'Y-m-d', $from );
        $to_dt   = \DateTimeImmutable::createFromFormat( 'Y-m-d', $to );
        if ( ! $from_dt || ! $to_dt ) {
            continue;
        }
        $from_dt = $from_dt->setTime( 0, 0, 0 );
        $to_dt   = $to_dt->setTime( 0, 0, 0 );
        if ( $today >= $from_dt && $today <= $to_dt ) {
            return [ 'from' => $from, 'to' => $to, 'label' => $label ];
        }
    }
    return [];
}

/**
 * Output inline <style> overriding CSS custom properties when admin has set custom colors.
 * Hooked to wp_head at priority 5.
 */
function tisch_output_color_overrides(): void {
    $map = [
        '--color-primary'      => get_option( 'tisch_color_primary', '' ),
        '--color-primary-dark' => get_option( 'tisch_color_primary_dark', '' ),
        '--color-accent'       => get_option( 'tisch_color_accent', '' ),
        '--color-bg'           => get_option( 'tisch_color_bg', '' ),
    ];
    $css = '';
    foreach ( $map as $var => $val ) {
        if ( $val && preg_match( '/^#[0-9a-fA-F]{3,6}$/', $val ) ) {
            $css .= $var . ':' . $val . ';';
        }
    }
    if ( $css ) {
        echo '<style id="tisch-color-overrides">:root{' . esc_html( $css ) . '}</style>' . "\n";
    }
}
add_action( 'wp_head', 'tisch_output_color_overrides', 99 );

/**
 * Output inline <style> for design-token CSS variables (typography, layout).
 * Values come from a hardcoded whitelist — map values (not raw user input) are echoed.
 * Hooked to wp_head at priority 99.
 */
function tisch_output_design_tokens(): void {
    $font_heading_map = [
        'playfair' => "'Playfair Display',Georgia,'Times New Roman',serif",
        'georgia'  => "Georgia,'Times New Roman',serif",
    ];
    $font_body_map = [
        'lato'   => "'Lato',system-ui,-apple-system,sans-serif",
        'system' => "system-ui,-apple-system,sans-serif",
    ];

    $heading_key    = (string) get_option( 'tisch_font_heading', 'playfair' );
    $body_key       = (string) get_option( 'tisch_font_body', 'lato' );
    $container_val  = (int) get_option( 'tisch_container_width', 1200 );
    $radius_val     = (int) get_option( 'tisch_border_radius', 8 );

    // Clamp to safe ranges
    $container_val = max( 900, min( 1400, $container_val ) );
    $radius_val    = max( 0, min( 32, $radius_val ) );

    $heading_stack = array_key_exists( $heading_key, $font_heading_map )
        ? $font_heading_map[ $heading_key ]
        : $font_heading_map['playfair'];
    $body_stack    = array_key_exists( $body_key, $font_body_map )
        ? $font_body_map[ $body_key ]
        : $font_body_map['lato'];

    $radius_sm = (int) round( $radius_val / 2 );
    $radius_lg = min( $radius_val * 2, 32 );

    $css = '--container-max:' . $container_val . 'px;'
        . '--radius-sm:' . $radius_sm . 'px;'
        . '--radius-md:' . $radius_val . 'px;'
        . '--radius-lg:' . $radius_lg . 'px;'
        . '--font-heading:' . $heading_stack . ';' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        . '--font-body:' . $body_stack . ';';      // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    echo '<style id="tisch-design-tokens">:root{' . $css . '}</style>' . "\n";
}
add_action( 'wp_head', 'tisch_output_design_tokens', 99 );

/**
 * Check whether the Speisekarte PDF is still valid.
 * Returns false if no PDF is set or the valid-until date has passed.
 */
function tisch_speisekarte_is_valid(): bool {
    $pdf = get_option( 'tisch_speisekarte_pdf', '' );
    if ( empty( $pdf ) ) {
        return false;
    }

    $valid_until = get_option( 'tisch_speisekarte_valid_until', '' );
    if ( empty( $valid_until ) ) {
        return true; // No expiry set — always valid.
    }

    // Reject malformed values instead of falling through to "always valid".
    if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $valid_until ) ) {
        return false;
    }

    // Compare date strings in the site's timezone (WordPress forces PHP to UTC).
    $today = ( new \DateTimeImmutable( 'today', wp_timezone() ) )->format( 'Y-m-d' );
    return $valid_until >= $today;
}

/**
 * Sanitize the Speisekarte sections array coming from the admin form.
 *
 * @param mixed $raw Raw POST value (expected: nested array).
 * @return array<int, array{title: string, items: array<int, array{name: string, price: string, desc: string, note: string}>}>
 */
function tisch_sanitize_speisekarte_sections( $raw ): array {
    if ( ! is_array( $raw ) ) {
        return [];
    }
    $clean = [];
    foreach ( array_values( $raw ) as $section ) {
        if ( ! is_array( $section ) ) {
            continue;
        }
        $s = [
            'title' => sanitize_text_field( $section['title'] ?? '' ),
            'items' => [],
        ];
        foreach ( array_values( $section['items'] ?? [] ) as $item ) {
            if ( ! is_array( $item ) ) {
                continue;
            }
            $s['items'][] = [
                'name'  => sanitize_text_field( $item['name']  ?? '' ),
                'price' => sanitize_text_field( $item['price'] ?? '' ),
                'desc'  => sanitize_text_field( $item['desc']  ?? '' ),
                'note'  => sanitize_text_field( $item['note']  ?? '' ),
            ];
        }
        // Only keep sections that have a title or at least one item
        if ( $s['title'] || $s['items'] ) {
            $clean[] = $s;
        }
    }
    return $clean;
}

/**
 * Get the site phone number (formatted for tel: links).
 */
function tisch_phone_link(): string {
    $phone = (string) get_option( 'tisch_phone', '' );
    // Strip everything but digits, +, and hyphens for tel: URI
    $clean = preg_replace( '/[^\d+\-]/', '', $phone ) ?? '';
    return $clean;
}
