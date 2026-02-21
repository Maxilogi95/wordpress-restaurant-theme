<?php
declare(strict_types=1);
/**
 * Template Name: Kontakt
 * Template Post Type: page
 *
 * Tisch by Kohler — Kontakt page template.
 * OSM map + address + opening hours.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
?>

<main id="main-content" class="site-main">

    <div class="page-hero page-hero--simple">
        <div class="container">
            <h1 class="page-hero__title"><?php the_title(); ?></h1>
        </div>
    </div>

    <div class="container section">

        <div class="kontakt-grid">

            <!-- Contact info -->
            <div class="kontakt-grid__info">
                <?php
                $address = get_option( 'tisch_address', '' );
                $phone   = get_option( 'tisch_phone', '' );
                $email   = get_option( 'tisch_email', '' );
                ?>

                <?php if ( $address ) : ?>
                    <div class="kontakt-item">
                        <h2 class="kontakt-item__label"><?php esc_html_e( 'Adresse', 'tisch-kohler' ); ?></h2>
                        <address class="kontakt-item__value">
                            <?php echo wp_kses_post( nl2br( $address ) ); ?>
                        </address>
                    </div>
                <?php endif; ?>

                <?php if ( $phone ) : ?>
                    <div class="kontakt-item">
                        <h2 class="kontakt-item__label"><?php esc_html_e( 'Telefon', 'tisch-kohler' ); ?></h2>
                        <p class="kontakt-item__value">
                            <a href="tel:<?php echo esc_attr( tisch_phone_link() ); ?>">
                                <?php echo esc_html( $phone ); ?>
                            </a>
                        </p>
                    </div>
                <?php endif; ?>

                <?php if ( $email ) : ?>
                    <div class="kontakt-item">
                        <h2 class="kontakt-item__label"><?php esc_html_e( 'E-Mail', 'tisch-kohler' ); ?></h2>
                        <p class="kontakt-item__value">
                            <a href="mailto:<?php echo esc_attr( $email ); ?>">
                                <?php echo esc_html( $email ); ?>
                            </a>
                        </p>
                    </div>
                <?php endif; ?>

                <!-- Öffnungszeiten -->
                <div class="kontakt-item">
                    <h2 class="kontakt-item__label"><?php esc_html_e( 'Öffnungszeiten', 'tisch-kohler' ); ?></h2>
                    <?php $closing = tisch_get_active_closing(); if ( $closing ) : ?>
                    <div class="closing-notice closing-notice--inline" role="alert">
                        <strong><?php esc_html_e( 'Betriebsferien:', 'tisch-kohler' ); ?></strong>
                        <?php echo esc_html( $closing['label'] ); ?>
                        (<?php echo esc_html( wp_date( get_option( 'date_format' ), strtotime( $closing['from'] ) ) ); ?>
                        – <?php echo esc_html( wp_date( get_option( 'date_format' ), strtotime( $closing['to'] ) ) ); ?>)
                    </div>
                    <?php endif; ?>
                    <dl class="hours-list">
                        <?php foreach ( tisch_get_opening_hours() as $entry ) :
                            if ( ! $entry['closed'] && empty( $entry['hours'] ) ) continue; ?>
                            <dt class="hours-list__day<?php echo $entry['closed'] ? ' hours-table__day--closed' : ''; ?>">
                                <?php echo esc_html( $entry['label'] ); ?>
                            </dt>
                            <dd class="hours-list__time<?php echo $entry['closed'] ? ' hours-table__time--closed' : ''; ?>">
                                <?php if ( $entry['closed'] ) {
                                    echo esc_html__( 'Ruhetag', 'tisch-kohler' );
                                } else {
                                    $slots = array_filter( array_map( 'trim', explode( "\n", $entry['hours'] ) ) );
                                    echo implode( '<br>', array_map( 'esc_html', $slots ) );
                                } ?>
                            </dd>
                        <?php endforeach; ?>
                    </dl>
                    <?php
                    $note = get_option( 'tisch_hours_note', '' );
                    if ( $note ) : ?>
                        <p class="hours-note"><?php echo wp_kses_post( $note ); ?></p>
                    <?php endif; ?>
                </div>

                <?php while ( have_posts() ) : the_post(); ?>
                    <?php
                    $content = get_the_content();
                    if ( $content ) : ?>
                        <div class="entry prose">
                            <?php the_content(); ?>
                        </div>
                    <?php endif; ?>
                <?php endwhile; ?>
            </div>

            <!-- OSM Map -->
            <div class="kontakt-grid__map">
                <?php get_template_part( 'template-parts/kontakt/osm-map' ); ?>
            </div>

        </div>

    </div>

</main>

<?php get_footer();
