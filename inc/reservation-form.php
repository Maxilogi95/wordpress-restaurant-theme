<?php
declare(strict_types=1);
/**
 * Tisch by Kohler — inc/reservation-form.php
 * Handles the reservation form submission via wp_mail().
 * No DB storage. Redirect-after-POST pattern.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'init', 'tisch_handle_reservation' );

function tisch_handle_reservation(): void {

    // Only process when form submitted
    if ( ! isset( $_POST['tisch_reservation_submit'] ) ) {
        return;
    }

    // Verify nonce
    if (
        ! isset( $_POST['tisch_reservation_nonce'] ) ||
        ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tisch_reservation_nonce'] ) ), 'tisch_reservation' )
    ) {
        tisch_redirect_reservation( 'error' );
        return;
    }

    // Honeypot check — if filled, silently redirect to success (don't reveal)
    if ( ! empty( $_POST['tisch_website'] ) ) {
        tisch_redirect_reservation( 'success' );
        return;
    }

    // ── Collect & sanitize ───────────────────────────────────────────────
    $name    = sanitize_text_field( wp_unslash( $_POST['tisch_name'] ?? '' ) );
    $email   = sanitize_email( wp_unslash( $_POST['tisch_email'] ?? '' ) );
    $phone   = sanitize_text_field( wp_unslash( $_POST['tisch_phone'] ?? '' ) );
    $date    = sanitize_text_field( wp_unslash( $_POST['tisch_date'] ?? '' ) );
    $time    = sanitize_text_field( wp_unslash( $_POST['tisch_time'] ?? '' ) );
    $guests  = absint( $_POST['tisch_guests'] ?? 0 );
    $message = sanitize_textarea_field( wp_unslash( $_POST['tisch_message'] ?? '' ) );
    $consent = isset( $_POST['tisch_dsgvo'] ) ? true : false;

    // ── Validate required fields ─────────────────────────────────────────
    if (
        empty( $name )   ||
        ! is_email( $email ) ||
        empty( $date )   ||
        empty( $time )   ||
        $guests < 1      ||
        ! $consent
    ) {
        tisch_redirect_reservation( 'error' );
        return;
    }

    // ── Validate date format (YYYY-MM-DD) ────────────────────────────────
    $date_obj = \DateTimeImmutable::createFromFormat( 'Y-m-d', $date );
    if ( ! $date_obj || $date_obj->format( 'Y-m-d' ) !== $date ) {
        tisch_redirect_reservation( 'error' );
        return;
    }

    // ── Build email ──────────────────────────────────────────────────────
    $to      = get_option( 'tisch_email', get_bloginfo( 'admin_email' ) );
    $subject = sprintf(
        /* translators: %s: guest name */
        __( 'Neue Reservierungsanfrage von %s', 'tisch-kohler' ),
        $name
    );

    $body  = __( 'Neue Reservierungsanfrage:', 'tisch-kohler' ) . "\n\n";
    $body .= __( 'Name:', 'tisch-kohler' )     . ' ' . $name    . "\n";
    $body .= __( 'E-Mail:', 'tisch-kohler' )   . ' ' . $email   . "\n";
    $body .= __( 'Telefon:', 'tisch-kohler' )  . ' ' . $phone   . "\n";
    $body .= __( 'Datum:', 'tisch-kohler' )    . ' ' . $date    . "\n";
    $body .= __( 'Uhrzeit:', 'tisch-kohler' )  . ' ' . $time    . "\n";
    $body .= __( 'Personen:', 'tisch-kohler' ) . ' ' . $guests  . "\n";
    if ( $message ) {
        $body .= __( 'Nachricht:', 'tisch-kohler' ) . "\n" . $message . "\n";
    }
    $body .= "\n---\n";
    $body .= __( 'DSGVO-Zustimmung erteilt.', 'tisch-kohler' ) . "\n";

    $headers = [
        'Content-Type: text/plain; charset=UTF-8',
        'Reply-To: ' . $name . ' <' . $email . '>',
    ];

    $sent = wp_mail( $to, $subject, $body, $headers );

    tisch_redirect_reservation( $sent ? 'success' : 'mail-error' );
}

/**
 * Redirect back to reservierung page with status query arg.
 */
function tisch_redirect_reservation( string $status ): void {
    $page = get_page_by_path( 'reservierung' );
    $url  = $page
        ? add_query_arg( 'reservierung', $status, get_permalink( $page ) )
        : add_query_arg( 'reservierung', $status, home_url( '/' ) );

    wp_safe_redirect( $url );
    exit;
}
