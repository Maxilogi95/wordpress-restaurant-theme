<?php
declare(strict_types=1);
/**
 * Tisch by Kohler — header.php
 * <head>, site header, and primary navigation.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?php bloginfo( 'description' ); ?>">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#main-content">
    <?php esc_html_e( 'Zum Hauptinhalt springen', 'tisch-kohler' ); ?>
</a>

<?php
$layout        = (string) get_option( 'tisch_header_layout', 'split' );
$valid_layouts = [ 'left', 'split', 'centered', 'split-right', 'right' ];
$layout        = in_array( $layout, $valid_layouts, true ) ? $layout : 'split';
$sticky_cls    = get_option( 'tisch_sticky_header' ) ? ' is-sticky' : '';
?>
<header class="site-header layout-<?php echo esc_attr( $layout ); ?><?php echo $sticky_cls; ?>" role="banner">
    <div class="container site-header__inner">

        <div class="site-header__brand">
            <?php if ( has_custom_logo() ) : ?>
                <?php the_custom_logo(); ?>
            <?php endif; ?>
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-header__title-link" rel="home">
                <span class="site-header__title"><?php bloginfo( 'name' ); ?></span>
                <?php $description = get_bloginfo( 'description', 'display' );
                if ( $description ) : ?>
                    <span class="site-header__tagline"><?php echo esc_html( $description ); ?></span>
                <?php endif; ?>
            </a>
        </div>

        <nav class="site-nav" id="site-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Hauptnavigation', 'tisch-kohler' ); ?>">
            <button
                class="site-nav__toggle"
                id="menu-toggle"
                aria-controls="primary-menu"
                aria-expanded="false"
                aria-label="<?php esc_attr_e( 'Menü öffnen', 'tisch-kohler' ); ?>"
            >
                <span class="site-nav__toggle-bar"></span>
                <span class="site-nav__toggle-bar"></span>
                <span class="site-nav__toggle-bar"></span>
            </button>

            <?php
            wp_nav_menu( [
                'theme_location' => 'primary',
                'menu_id'        => 'primary-menu',
                'menu_class'     => 'site-nav__list',
                'container'      => false,
                'fallback_cb'    => 'tisch_nav_fallback',
            ] );
            ?>
        </nav>

        <div class="site-header__actions">
            <?php
            $phone          = (string) get_option( 'tisch_phone', '' );
            $show_phone     = (bool) get_option( 'tisch_header_show_phone', '' );
            $cta_text       = (string) get_option( 'tisch_header_cta_text', '' );
            $cta_url        = (string) get_option( 'tisch_header_cta_url', '' );

            if ( $phone ) : ?>
                <a href="tel:<?php echo esc_attr( tisch_phone_link() ); ?>"
                   class="site-header__phone"
                   <?php echo $show_phone ? '' : 'style="display:none"'; ?>>
                    <?php echo esc_html( $phone ); ?>
                </a>
            <?php endif; ?>

            <a href="<?php echo esc_url( $cta_url ?: '#' ); ?>"
               class="site-header__cta btn btn--primary btn--sm"
               <?php echo ( $cta_text && $cta_url ) ? '' : 'style="display:none"'; ?>>
                <?php echo esc_html( $cta_text ); ?>
            </a>
        </div>

    </div>
</header>

<?php $closing = tisch_get_active_closing(); if ( $closing ) : ?>
<div class="closing-notice" role="alert">
    <div class="container closing-notice__inner">
        <strong><?php esc_html_e( 'Betriebsferien:', 'tisch-kohler' ); ?></strong>
        <?php echo esc_html( $closing['label'] ); ?>
        (<?php echo esc_html( wp_date( get_option( 'date_format' ), strtotime( $closing['from'] ) ) ); ?>
        – <?php echo esc_html( wp_date( get_option( 'date_format' ), strtotime( $closing['to'] ) ) ); ?>)
    </div>
</div>
<?php endif; ?>
