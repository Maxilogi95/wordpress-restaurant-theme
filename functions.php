<?php
declare(strict_types=1);

/**
 * Tisch by Kohler — functions.php
 * Autoloader only. All logic lives in /inc/*.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ── Autoload all /inc files ────────────────────────────────────────────────
$tisch_inc_files = [
    'helpers',
    'theme-setup',
    'enqueue',
    'customizer',
    'security',
    'options',
    'reservation-form',
];

foreach ( $tisch_inc_files as $file ) {
    $path = get_template_directory() . '/inc/' . $file . '.php';
    if ( file_exists( $path ) ) {
        require_once $path;
    }
}
