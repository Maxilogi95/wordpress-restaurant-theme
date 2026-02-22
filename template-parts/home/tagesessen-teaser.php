<?php
declare(strict_types=1);
/**
 * Tisch by Kohler — template-parts/home/tagesessen-teaser.php
 * Teaser card for the weekly menu on the homepage.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! get_option( 'tisch_show_tagesessen_teaser', '1' ) ) {
    return;
}

$tagesessen_page = get_page_by_path( 'tagesessen' );
if ( ! $tagesessen_page ) {
    return;
}

$is_valid = tisch_tagesessen_is_valid();
?>

<section class="section teaser-section" aria-label="<?php esc_attr_e( 'Aktuelle Wochenkarte', 'tisch-kohler' ); ?>">
    <div class="container">
        <div class="teaser-card">
            <div class="teaser-card__body">
                <h2 class="teaser-card__title">
                    <?php esc_html_e( 'Unsere Wochenkarte', 'tisch-kohler' ); ?>
                </h2>

                <?php if ( $is_valid ) : ?>
                    <p class="teaser-card__text">
                        <?php esc_html_e( 'Die aktuelle Wochenkarte steht zum Ansehen und Herunterladen bereit.', 'tisch-kohler' ); ?>
                    </p>
                <?php else : ?>
                    <p class="teaser-card__text">
                        <?php esc_html_e( 'Die neue Wochenkarte ist in Kürze verfügbar.', 'tisch-kohler' ); ?>
                    </p>
                <?php endif; ?>

                <a href="<?php echo esc_url( get_permalink( $tagesessen_page ) ); ?>" class="btn btn--primary">
                    <?php esc_html_e( 'Zur Wochenkarte', 'tisch-kohler' ); ?>
                </a>
            </div>

            <div class="teaser-card__icon" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="64" height="64">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                    <polyline points="10 9 9 9 8 9"/>
                </svg>
            </div>
        </div>
    </div>
</section>
