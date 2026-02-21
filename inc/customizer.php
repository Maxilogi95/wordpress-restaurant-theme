<?php
declare(strict_types=1);
/**
 * Tisch by Kohler — inc/customizer.php
 * WordPress Customizer: color overrides under "Tisch by Kohler" panel.
 * Uses type=>'option' so get_option() in helpers.php keeps working unchanged.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'customize_register', 'tisch_customize_register' );

function tisch_customize_register( WP_Customize_Manager $wp_customize ): void {

    $wp_customize->add_panel( 'tisch_panel', [
        'title'    => __( 'Tisch by Kohler', 'tisch-kohler' ),
        'priority' => 160,
    ] );

    $wp_customize->add_section( 'tisch_colors', [
        'title' => __( 'Farben', 'tisch-kohler' ),
        'panel' => 'tisch_panel',
    ] );

    $color_controls = [
        'tisch_color_primary'      => [ 'label' => __( 'Primärfarbe (Braun)',  'tisch-kohler' ), 'default' => '#5C3D2E' ],
        'tisch_color_primary_dark' => [ 'label' => __( 'Primärfarbe dunkel',   'tisch-kohler' ), 'default' => '#3E2418' ],
        'tisch_color_accent'       => [ 'label' => __( 'Akzentfarbe (Gold)',   'tisch-kohler' ), 'default' => '#C8922A' ],
        'tisch_color_bg'           => [ 'label' => __( 'Hintergrundfarbe',     'tisch-kohler' ), 'default' => '#FAF6F0' ],
    ];

    foreach ( $color_controls as $id => $args ) {
        $wp_customize->add_setting( $id, [
            'type'              => 'option',       // keeps get_option() working
            'default'           => $args['default'],
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',  // live update via JS
        ] );
        $wp_customize->add_control(
            new WP_Customize_Color_Control( $wp_customize, $id, [
                'label'   => $args['label'],
                'section' => 'tisch_colors',
            ] )
        );
    }
}
