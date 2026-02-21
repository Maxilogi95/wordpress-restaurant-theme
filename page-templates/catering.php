<?php
declare(strict_types=1);
/**
 * Template Name: Catering
 * Template Post Type: page
 *
 * Tisch by Kohler — Catering page template.
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
        <?php while ( have_posts() ) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class( 'entry prose' ); ?>>
                <div class="entry__content">
                    <?php the_content(); ?>
                </div>
            </article>
        <?php endwhile; ?>

        <!-- CTA to Reservierung -->
        <div class="cta-box">
            <p class="cta-box__text">
                <?php esc_html_e( 'Interesse an Catering für Ihre Veranstaltung? Wir freuen uns auf Ihre Anfrage!', 'tisch-kohler' ); ?>
            </p>
            <?php
            $reservierung = get_page_by_path( 'reservierung' );
            if ( $reservierung ) : ?>
                <a href="<?php echo esc_url( get_permalink( $reservierung ) ); ?>" class="btn btn--primary">
                    <?php esc_html_e( 'Jetzt anfragen', 'tisch-kohler' ); ?>
                </a>
            <?php endif; ?>
        </div>

    </div>

</main>

<?php get_footer();
