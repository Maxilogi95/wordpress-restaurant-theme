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
$has_slots  = array_filter( array_map( function ( $h ) { return ! empty( $h['slots'] ); }, $hours ) );
$has_closed = array_filter( array_column( $hours, 'closed' ) );
if ( ! $has_slots && ! $has_closed && ! $note ) {
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
                if ( ! $entry['closed'] && empty( $entry['slots'] ) ) continue; ?>
                <div class="hours-table__row<?php echo $entry['closed'] ? ' hours-table__row--closed' : ''; ?>">
                    <dt class="hours-table__day<?php echo $entry['closed'] ? ' hours-table__day--closed' : ''; ?>">
                        <?php echo esc_html( $entry['label'] ); ?>
                    </dt>
                    <dd class="hours-table__time<?php echo $entry['closed'] ? ' hours-table__time--closed' : ''; ?>">
                        <?php if ( $entry['closed'] ) {
                            echo esc_html__( 'Ruhetag', 'tisch-kohler' );
                        } else {
                            foreach ( $entry['slots'] as $slot ) {
                                echo esc_html( $slot['open'] ) . ' – ' . esc_html( $slot['close'] ) . ' Uhr<br>';
                            }
                        } ?>
                    </dd>
                </div>
            <?php endforeach; ?>
        </dl>

        <?php $specials = tisch_get_upcoming_specials(); if ( $specials ) : ?>
            <div class="hours-specials">
                <h3 class="hours-specials__title">
                    <?php esc_html_e( 'Besondere Öffnungszeiten', 'tisch-kohler' ); ?>
                </h3>
                <dl class="hours-table">
                    <?php foreach ( $specials as $special ) :
                        $date_label = esc_html( wp_date( get_option( 'date_format' ), strtotime( $special['date'] ) ) );
                        $extra = $special['label'] ? ' — ' . esc_html( $special['label'] ) : '';
                    ?>
                    <div class="hours-table__row<?php echo $special['closed'] ? ' hours-table__row--closed' : ''; ?>">
                        <dt class="hours-table__day"><?php echo $date_label . $extra; ?></dt>
                        <dd class="hours-table__time">
                            <?php if ( $special['closed'] ) : ?>
                                <?php esc_html_e( 'Geschlossen', 'tisch-kohler' ); ?>
                            <?php else : ?>
                                <?php foreach ( $special['slots'] as $slot ) :
                                    echo esc_html( $slot['open'] ) . ' – ' . esc_html( $slot['close'] ) . ' Uhr<br>';
                                endforeach; ?>
                            <?php endif; ?>
                        </dd>
                    </div>
                    <?php endforeach; ?>
                </dl>
            </div>
        <?php endif; ?>

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
