<?php
declare(strict_types=1);
/**
 * Tisch by Kohler — inc/security.php
 * Hardening: remove WP version, disable xmlrpc, hide REST user endpoints.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Remove WP version from head and feeds
add_filter( 'the_generator', '__return_empty_string' );
remove_action( 'wp_head', 'wp_generator' );

// Remove version from scripts and styles
add_filter( 'style_loader_src',  'tisch_remove_version_query', 9999 );
add_filter( 'script_loader_src', 'tisch_remove_version_query', 9999 );

function tisch_remove_version_query( string $src ): string {
    if ( strpos( $src, 'ver=' ) !== false ) {
        $src = remove_query_arg( 'ver', $src );
    }
    return $src;
}

// Disable XML-RPC
add_filter( 'xmlrpc_enabled', '__return_false' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );

// Hide user enumeration via REST API
add_filter( 'rest_endpoints', 'tisch_disable_rest_user_endpoints' );

function tisch_disable_rest_user_endpoints( array $endpoints ): array {
    if ( ! is_user_logged_in() ) {
        unset( $endpoints['/wp/v2/users'] );
        unset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
    }
    return $endpoints;
}

// Remove unnecessary wp_head items
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' );
remove_action( 'wp_head', 'feed_links_extra', 3 );
remove_action( 'wp_head', 'feed_links', 2 );
remove_action( 'wp_head', 'wp_oembed_add_host_js' );
remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
remove_action( 'template_redirect', 'rest_output_link_header', 11 );

// Disable embeds
add_action( 'init', 'tisch_disable_embeds' );

function tisch_disable_embeds(): void {
    remove_action( 'rest_api_init', 'wp_oembed_register_route' );
    add_filter( 'embed_oembed_discover', '__return_false' );
    remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );
    remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
    remove_action( 'wp_head', 'wp_oembed_add_host_js' );
    add_filter( 'rewrite_rules_array', 'tisch_disable_embeds_rewrite_rules' );
}

function tisch_disable_embeds_rewrite_rules( array $rules ): array {
    foreach ( $rules as $rule => $rewrite ) {
        if ( str_contains( $rewrite, 'embed=true' ) ) {
            unset( $rules[ $rule ] );
        }
    }
    return $rules;
}

// Set proper X-Content-Type-Options header
add_action( 'send_headers', 'tisch_security_headers' );

function tisch_security_headers(): void {
    if ( is_admin() ) {
        return;
    }
    header( 'X-Content-Type-Options: nosniff' );
    header( 'X-Frame-Options: SAMEORIGIN' );
    header( 'Referrer-Policy: strict-origin-when-cross-origin' );
}
