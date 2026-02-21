<?php
declare(strict_types=1);
/**
 * Tisch by Kohler — template-parts/tagesessen/pdf-viewer.php
 * <object> PDF embed with download fallback.
 * Shows expiry message if the PDF is outdated.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$pdf_url     = get_option( 'tisch_tagesessen_pdf', '' );
$valid_until = get_option( 'tisch_tagesessen_valid_until', '' );
$is_valid    = tisch_tagesessen_is_valid();
?>

<?php if ( $is_valid && $pdf_url ) : ?>

    <div class="pdf-viewer">

        <div class="pdf-viewer__header">
            <p class="pdf-viewer__meta">
                <?php if ( $valid_until ) : ?>
                    <?php
                    printf(
                        /* translators: %s: formatted date */
                        esc_html__( 'Gültig bis: %s', 'tisch-kohler' ),
                        esc_html(
                            wp_date(
                                get_option( 'date_format' ),
                                strtotime( $valid_until )
                            )
                        )
                    );
                    ?>
                <?php endif; ?>
            </p>
            <a href="<?php echo esc_url( $pdf_url ); ?>"
               class="btn btn--secondary"
               download
               target="_blank"
               rel="noopener noreferrer">
                <?php esc_html_e( 'PDF herunterladen', 'tisch-kohler' ); ?>
            </a>
        </div>

        <object
            class="pdf-viewer__embed"
            data="<?php echo esc_url( $pdf_url ); ?>#view=FitH"
            type="application/pdf"
            width="100%"
            aria-label="<?php esc_attr_e( 'Wochenkarte als PDF', 'tisch-kohler' ); ?>">
            <div class="pdf-viewer__fallback">
                <p><?php esc_html_e( 'Ihr Browser kann das PDF nicht direkt anzeigen.', 'tisch-kohler' ); ?></p>
                <a href="<?php echo esc_url( $pdf_url ); ?>"
                   class="btn btn--primary"
                   download
                   target="_blank"
                   rel="noopener noreferrer">
                    <?php esc_html_e( 'Wochenkarte herunterladen', 'tisch-kohler' ); ?>
                </a>
            </div>
        </object>

    </div>

<?php elseif ( ! empty( $pdf_url ) && ! $is_valid ) : ?>

    <!-- PDF exists but has expired -->
    <div class="pdf-expired">
        <p class="pdf-expired__message">
            <?php esc_html_e( 'Die aktuelle Wochenkarte ist in Kürze verfügbar. Schauen Sie gerne bald wieder vorbei!', 'tisch-kohler' ); ?>
        </p>
        <?php
        $phone = get_option( 'tisch_phone', '' );
        if ( $phone ) : ?>
            <p class="pdf-expired__phone">
                <?php esc_html_e( 'Für aktuelle Informationen rufen Sie uns an:', 'tisch-kohler' ); ?>
                <a href="tel:<?php echo esc_attr( tisch_phone_link() ); ?>">
                    <?php echo esc_html( $phone ); ?>
                </a>
            </p>
        <?php endif; ?>
    </div>

<?php else : ?>

    <!-- No PDF uploaded yet -->
    <div class="pdf-expired">
        <p class="pdf-expired__message">
            <?php esc_html_e( 'Die Wochenkarte wird in Kürze hochgeladen.', 'tisch-kohler' ); ?>
        </p>
    </div>

<?php endif; ?>
