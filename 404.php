<?php
declare(strict_types=1);
/**
 * Tisch by Kohler — 404.php
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
?>

<main id="main-content" class="site-main">
    <div class="page-hero page-hero--simple">
        <div class="container">
            <h1 class="page-hero__title"><?php esc_html_e( 'Seite nicht gefunden', 'tisch-kohler' ); ?></h1>
        </div>
    </div>

    <div class="container section">
        <div class="error-404 prose">
            <p><?php esc_html_e( 'Die gesuchte Seite konnte leider nicht gefunden werden. Bitte nutzen Sie die Navigation oder kehren Sie zur Startseite zurück.', 'tisch-kohler' ); ?></p>
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn--primary">
                <?php esc_html_e( 'Zur Startseite', 'tisch-kohler' ); ?>
            </a>
        </div>
    </div>
</main>

<?php get_footer();
