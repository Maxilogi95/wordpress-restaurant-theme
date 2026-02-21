<?php
declare(strict_types=1);
/**
 * Tisch by Kohler — inc/theme-setup.php
 * after_setup_theme, nav menus, image sizes, language support.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'after_setup_theme', 'tisch_theme_setup' );

function tisch_theme_setup(): void {

    // Translations
    load_theme_textdomain(
        'tisch-kohler',
        get_template_directory() . '/languages'
    );

    // HTML5 markup support
    add_theme_support( 'html5', [
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ] );

    // WordPress features
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'custom-logo', [
        'height'      => 80,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ] );
    add_theme_support( 'customize-selective-refresh-widgets' );
    add_theme_support( 'wp-block-styles' );

    // Register nav menus
    register_nav_menus( [
        'primary' => __( 'Hauptnavigation', 'tisch-kohler' ),
        'footer'  => __( 'Footer-Navigation', 'tisch-kohler' ),
    ] );

    // Image sizes
    add_image_size( 'tisch-hero',    1920, 800, true );
    add_image_size( 'tisch-card',     600, 400, true );
    add_image_size( 'tisch-thumb',    300, 200, true );

    // Editor styles
    add_editor_style( 'assets/css/main.css' );
}

/**
 * Fallback for primary nav when no menu is assigned.
 */
function tisch_nav_fallback(): void {
    echo '<ul class="site-nav__list">';
    echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Startseite', 'tisch-kohler' ) . '</a></li>';
    echo '</ul>';
}

/**
 * Fallback for footer nav when no menu is assigned.
 */
function tisch_footer_nav_fallback(): void {
    $impressum    = get_page_by_path( 'impressum' );
    $datenschutz  = get_page_by_path( 'datenschutzerklaerung' );
    echo '<ul class="site-footer__list">';
    if ( $impressum ) {
        echo '<li><a href="' . esc_url( get_permalink( $impressum ) ) . '">' . esc_html__( 'Impressum', 'tisch-kohler' ) . '</a></li>';
    }
    if ( $datenschutz ) {
        echo '<li><a href="' . esc_url( get_permalink( $datenschutz ) ) . '">' . esc_html__( 'Datenschutzerklärung', 'tisch-kohler' ) . '</a></li>';
    }
    echo '</ul>';
}

/**
 * Set content width.
 */
if ( ! isset( $content_width ) ) {
    $content_width = 1200;
}

// Customizer color controls moved to inc/customizer.php
