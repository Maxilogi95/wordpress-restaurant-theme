<?php
declare(strict_types=1);
/**
 * Tisch by Kohler — template-parts/home/opening-hours.php
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! get_option( 'tisch_show_opening_hours', '1' ) ) {
    return;
}

$hours = tisch_get_opening_hours();
$note  = get_option( 'tisch_hours_note', '' );
$phone = get_option( 'tisch_phone', '' );

// Don't render if no hours configured
$has_hours = array_filter( array_column( $hours, 'hours' ) );
$has_closed = array_filter( array_column( $hours, 'closed' ) );
if ( ! $has_hours && ! $has_closed && ! $note ) {
    return;
}
?>

<section class="section hours-section" aria-label="<?php esc_attr_e( 'Öffnungszeiten', 'tisch-kohler' ); ?>">
    <div class="container container--narrow">

        <h2 class="section__title text-center">
            <?php esc_html_e( 'Öffnungszeiten', 'tisch-kohler' ); ?>
        </h2>

        <?php $closing = tisch_get_active_closing(); if ( $closing ) : ?>
        <div class="closing-notice closing-notice--inline" role="alert">
            <strong><?php esc_html_e( 'Betriebsferien:', 'tisch-kohler' ); ?></strong>
            <?php echo esc_html( $closing['label'] ); ?>
            (<?php echo esc_html( wp_date( get_option( 'date_format' ), strtotime( $closing['from'] ) ) ); ?>
            – <?php echo esc_html( wp_date( get_option( 'date_format' ), strtotime( $closing['to'] ) ) ); ?>)
        </div>
        <?php endif; ?>

        <dl class="hours-table">
            <?php foreach ( $hours as $entry ) :
                if ( ! $entry['closed'] && empty( $entry['hours'] ) ) continue; ?>
                <div class="hours-table__row<?php echo $entry['closed'] ? ' hours-table__row--closed' : ''; ?>">
                    <dt class="hours-table__day<?php echo $entry['closed'] ? ' hours-table__day--closed' : ''; ?>">
                        <?php echo esc_html( $entry['label'] ); ?>
                    </dt>
                    <dd class="hours-table__time<?php echo $entry['closed'] ? ' hours-table__time--closed' : ''; ?>">
                        <?php if ( $entry['closed'] ) {
                            echo esc_html__( 'Ruhetag', 'tisch-kohler' );
                        } else {
                            $slots = array_filter( array_map( 'trim', explode( "\n", $entry['hours'] ) ) );
                            echo implode( '<br>', array_map( 'esc_html', $slots ) );
                        } ?>
                    </dd>
                </div>
            <?php endforeach; ?>
        </dl>

        <?php if ( $note ) : ?>
            <p class="hours-note text-center"><?php echo wp_kses_post( $note ); ?></p>
        <?php endif; ?>

        <?php if ( $phone ) : ?>
            <p class="hours-cta text-center">
                <?php esc_html_e( 'Reservierungen:', 'tisch-kohler' ); ?>
                <a href="tel:<?php echo esc_attr( tisch_phone_link() ); ?>" class="hours-cta__link">
                    <?php echo esc_html( $phone ); ?>
                </a>
            </p>
        <?php endif; ?>

    </div>
</section>
