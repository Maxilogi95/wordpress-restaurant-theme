<?php
declare(strict_types=1);
/**
 * Tisch by Kohler — inc/customizer.php
 * WordPress Customizer: full visual-design panel under "Tisch by Kohler".
 * All settings use type=>'option' so get_option() keeps working unchanged.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'customize_register', 'tisch_customize_register' );

function tisch_customize_register( WP_Customize_Manager $wp_customize ): void {

    // ── Panel ─────────────────────────────────────────────────────────────────
    $wp_customize->add_panel( 'tisch_panel', [
        'title'    => __( 'Tisch by Kohler', 'tisch-kohler' ),
        'priority' => 160,
    ] );

    // ── 1. Farben (Colors) — priority 10 ─────────────────────────────────────
    $wp_customize->add_section( 'tisch_colors', [
        'title'    => __( 'Farben', 'tisch-kohler' ),
        'panel'    => 'tisch_panel',
        'priority' => 10,
    ] );

    $color_controls = [
        'tisch_color_primary'      => [ 'label' => __( 'Primärfarbe (Braun)',  'tisch-kohler' ), 'default' => '#5C3D2E' ],
        'tisch_color_primary_dark' => [ 'label' => __( 'Primärfarbe dunkel',   'tisch-kohler' ), 'default' => '#3E2418' ],
        'tisch_color_accent'       => [ 'label' => __( 'Akzentfarbe (Gold)',   'tisch-kohler' ), 'default' => '#C8922A' ],
        'tisch_color_bg'           => [ 'label' => __( 'Hintergrundfarbe',     'tisch-kohler' ), 'default' => '#FAF6F0' ],
    ];

    foreach ( $color_controls as $id => $args ) {
        $wp_customize->add_setting( $id, [
            'type'              => 'option',
            'default'           => $args['default'],
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        ] );
        $wp_customize->add_control(
            new WP_Customize_Color_Control( $wp_customize, $id, [
                'label'   => $args['label'],
                'section' => 'tisch_colors',
            ] )
        );
    }

    // ── 2. Typografie — priority 20 ───────────────────────────────────────────
    $wp_customize->add_section( 'tisch_typography', [
        'title'    => __( 'Typografie', 'tisch-kohler' ),
        'panel'    => 'tisch_panel',
        'priority' => 20,
    ] );

    $wp_customize->add_setting( 'tisch_font_heading', [
        'type'              => 'option',
        'default'           => 'playfair',
        'sanitize_callback' => static function ( string $val ): string {
            return in_array( $val, [ 'playfair', 'georgia' ], true ) ? $val : 'playfair';
        },
        'transport'         => 'postMessage',
    ] );
    $wp_customize->add_control( 'tisch_font_heading', [
        'label'   => __( 'Überschriften-Schrift', 'tisch-kohler' ),
        'section' => 'tisch_typography',
        'type'    => 'select',
        'choices' => [
            'playfair' => __( 'Playfair Display (selbst gehostet)', 'tisch-kohler' ),
            'georgia'  => __( 'Georgia (systemseitig)', 'tisch-kohler' ),
        ],
    ] );

    $wp_customize->add_setting( 'tisch_font_body', [
        'type'              => 'option',
        'default'           => 'lato',
        'sanitize_callback' => static function ( string $val ): string {
            return in_array( $val, [ 'lato', 'system' ], true ) ? $val : 'lato';
        },
        'transport'         => 'postMessage',
    ] );
    $wp_customize->add_control( 'tisch_font_body', [
        'label'   => __( 'Fließtext-Schrift', 'tisch-kohler' ),
        'section' => 'tisch_typography',
        'type'    => 'select',
        'choices' => [
            'lato'   => __( 'Lato (selbst gehostet)', 'tisch-kohler' ),
            'system' => __( 'System-Schrift', 'tisch-kohler' ),
        ],
    ] );

    // ── 3. Layout — priority 30 ───────────────────────────────────────────────
    $wp_customize->add_section( 'tisch_layout', [
        'title'    => __( 'Layout', 'tisch-kohler' ),
        'panel'    => 'tisch_panel',
        'priority' => 30,
    ] );

    $wp_customize->add_setting( 'tisch_container_width', [
        'type'              => 'option',
        'default'           => 1200,
        'sanitize_callback' => static function ( $val ): int {
            $v = (int) $val;
            return max( 900, min( 1400, $v ) );
        },
        'transport'         => 'postMessage',
    ] );
    $wp_customize->add_control( 'tisch_container_width', [
        'label'       => __( 'Max. Breite des Inhaltsbereichs (px)', 'tisch-kohler' ),
        'section'     => 'tisch_layout',
        'type'        => 'range',
        'input_attrs' => [
            'min'  => 900,
            'max'  => 1400,
            'step' => 10,
        ],
    ] );

    $wp_customize->add_setting( 'tisch_border_radius', [
        'type'              => 'option',
        'default'           => 8,
        'sanitize_callback' => static function ( $val ): int {
            $v = (int) $val;
            return max( 0, min( 32, $v ) );
        },
        'transport'         => 'postMessage',
    ] );
    $wp_customize->add_control( 'tisch_border_radius', [
        'label'       => __( 'Abrundung (Border-Radius, px)', 'tisch-kohler' ),
        'section'     => 'tisch_layout',
        'type'        => 'range',
        'input_attrs' => [
            'min'  => 0,
            'max'  => 32,
            'step' => 1,
        ],
    ] );

    // ── 4. Hero — priority 40 ─────────────────────────────────────────────────
    $wp_customize->add_section( 'tisch_hero_section', [
        'title'    => __( 'Startseite: Hero', 'tisch-kohler' ),
        'panel'    => 'tisch_panel',
        'priority' => 40,
    ] );

    // Hero image (moved from options page — same key, no data loss)
    $wp_customize->add_setting( 'tisch_hero_image_id', [
        'type'              => 'option',
        'default'           => 0,
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control(
        new WP_Customize_Media_Control( $wp_customize, 'tisch_hero_image_id', [
            'label'     => __( 'Hero-Bild', 'tisch-kohler' ),
            'section'   => 'tisch_hero_section',
            'mime_type' => 'image',
        ] )
    );

    // Hero tagline (moved from options page — same key, no data loss)
    $wp_customize->add_setting( 'tisch_hero_tagline', [
        'type'              => 'option',
        'default'           => 'Herzlich willkommen',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ] );
    $wp_customize->add_control( 'tisch_hero_tagline', [
        'label'   => __( 'Tagline', 'tisch-kohler' ),
        'section' => 'tisch_hero_section',
        'type'    => 'text',
    ] );

    // Hero subheadline (new)
    $wp_customize->add_setting( 'tisch_hero_subheadline', [
        'type'              => 'option',
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ] );
    $wp_customize->add_control( 'tisch_hero_subheadline', [
        'label'       => __( 'Untertitel (optional)', 'tisch-kohler' ),
        'section'     => 'tisch_hero_section',
        'type'        => 'text',
        'description' => __( 'Kurzer Text unterhalb der Tagline.', 'tisch-kohler' ),
    ] );

    // Button 1
    $wp_customize->add_setting( 'tisch_hero_btn1_text', [
        'type'              => 'option',
        'default'           => 'Tisch reservieren',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ] );
    $wp_customize->add_control( 'tisch_hero_btn1_text', [
        'label'   => __( 'Button 1 – Text', 'tisch-kohler' ),
        'section' => 'tisch_hero_section',
        'type'    => 'text',
    ] );

    $wp_customize->add_setting( 'tisch_hero_btn1_url', [
        'type'              => 'option',
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'postMessage',
    ] );
    $wp_customize->add_control( 'tisch_hero_btn1_url', [
        'label'       => __( 'Button 1 – URL (leer = Reservierung-Seite)', 'tisch-kohler' ),
        'section'     => 'tisch_hero_section',
        'type'        => 'url',
    ] );

    // Button 2
    $wp_customize->add_setting( 'tisch_hero_btn2_text', [
        'type'              => 'option',
        'default'           => 'Zur Speisekarte',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ] );
    $wp_customize->add_control( 'tisch_hero_btn2_text', [
        'label'   => __( 'Button 2 – Text', 'tisch-kohler' ),
        'section' => 'tisch_hero_section',
        'type'    => 'text',
    ] );

    $wp_customize->add_setting( 'tisch_hero_btn2_url', [
        'type'              => 'option',
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'postMessage',
    ] );
    $wp_customize->add_control( 'tisch_hero_btn2_url', [
        'label'       => __( 'Button 2 – URL (leer = Speisekarte-Seite)', 'tisch-kohler' ),
        'section'     => 'tisch_hero_section',
        'type'        => 'url',
    ] );

    // ── 5. Sektionen — priority 50 ────────────────────────────────────────────
    $wp_customize->add_section( 'tisch_sections', [
        'title'    => __( 'Startseite: Sektionen', 'tisch-kohler' ),
        'panel'    => 'tisch_panel',
        'priority' => 50,
    ] );

    $section_toggles = [
        'tisch_show_welcome'           => __( 'Willkommenstext anzeigen', 'tisch-kohler' ),
        'tisch_show_tagesessen_teaser' => __( 'Tagesessen-Teaser anzeigen', 'tisch-kohler' ),
        'tisch_show_opening_hours'     => __( 'Öffnungszeiten anzeigen', 'tisch-kohler' ),
    ];
    foreach ( $section_toggles as $key => $label ) {
        $wp_customize->add_setting( $key, [
            'type'              => 'option',
            'default'           => '1',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        ] );
        $wp_customize->add_control( $key, [
            'label'   => $label,
            'section' => 'tisch_sections',
            'type'    => 'checkbox',
        ] );
    }

    // ── 6. Header — priority 60 ───────────────────────────────────────────────
    $wp_customize->add_section( 'tisch_header_section', [
        'title'    => __( 'Header', 'tisch-kohler' ),
        'panel'    => 'tisch_panel',
        'priority' => 60,
    ] );

    $valid_layouts = [ 'left', 'split', 'centered', 'split-right', 'right' ];

    $wp_customize->add_setting( 'tisch_header_layout', [
        'type'              => 'option',
        'default'           => 'split',
        'sanitize_callback' => static function ( string $val ) use ( $valid_layouts ): string {
            return in_array( $val, $valid_layouts, true ) ? $val : 'split';
        },
        'transport'         => 'postMessage',
    ] );
    $wp_customize->add_control( 'tisch_header_layout', [
        'label'   => __( 'Header-Layout', 'tisch-kohler' ),
        'section' => 'tisch_header_section',
        'type'    => 'select',
        'choices' => [
            'left'        => __( 'Links', 'tisch-kohler' ),
            'split'       => __( 'Geteilt links (Standard)', 'tisch-kohler' ),
            'centered'    => __( 'Zentriert', 'tisch-kohler' ),
            'split-right' => __( 'Geteilt rechts', 'tisch-kohler' ),
            'right'       => __( 'Rechts', 'tisch-kohler' ),
        ],
    ] );

    $wp_customize->add_setting( 'tisch_sticky_header', [
        'type'              => 'option',
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ] );
    $wp_customize->add_control( 'tisch_sticky_header', [
        'label'   => __( 'Fixierter Header (sticky)', 'tisch-kohler' ),
        'section' => 'tisch_header_section',
        'type'    => 'checkbox',
    ] );

    $wp_customize->add_setting( 'tisch_header_show_phone', [
        'type'              => 'option',
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ] );
    $wp_customize->add_control( 'tisch_header_show_phone', [
        'label'       => __( 'Telefonnummer im Header anzeigen', 'tisch-kohler' ),
        'section'     => 'tisch_header_section',
        'type'        => 'checkbox',
        'description' => __( 'Die Telefonnummer wird aus den Tisch-Einstellungen bezogen.', 'tisch-kohler' ),
    ] );

    $wp_customize->add_setting( 'tisch_header_cta_text', [
        'type'              => 'option',
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ] );
    $wp_customize->add_control( 'tisch_header_cta_text', [
        'label'       => __( 'Header-Button Text', 'tisch-kohler' ),
        'section'     => 'tisch_header_section',
        'type'        => 'text',
        'description' => __( 'Leer lassen = kein Button.', 'tisch-kohler' ),
    ] );

    $wp_customize->add_setting( 'tisch_header_cta_url', [
        'type'              => 'option',
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'postMessage',
    ] );
    $wp_customize->add_control( 'tisch_header_cta_url', [
        'label'   => __( 'Header-Button URL', 'tisch-kohler' ),
        'section' => 'tisch_header_section',
        'type'    => 'url',
    ] );

    // ── 7. Footer — priority 70 ───────────────────────────────────────────────
    $wp_customize->add_section( 'tisch_footer_section', [
        'title'    => __( 'Footer', 'tisch-kohler' ),
        'panel'    => 'tisch_panel',
        'priority' => 70,
    ] );

    $wp_customize->add_setting( 'tisch_footer_copyright', [
        'type'              => 'option',
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ] );
    $wp_customize->add_control( 'tisch_footer_copyright', [
        'label'       => __( 'Copyright-Text', 'tisch-kohler' ),
        'section'     => 'tisch_footer_section',
        'type'        => 'text',
        'description' => __( 'Leer = Websitename wird verwendet.', 'tisch-kohler' ),
    ] );
}
