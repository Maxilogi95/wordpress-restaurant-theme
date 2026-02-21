<?php
declare(strict_types=1);
/**
 * Template Name: Reservierung
 * Template Post Type: page
 *
 * Tisch by Kohler — Reservierung page template.
 * wp_mail() form, DSGVO checkbox, no DB storage.
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
            <?php
            $content = get_the_content();
            if ( $content ) : ?>
                <div class="entry prose section--narrow">
                    <?php the_content(); ?>
                </div>
            <?php endif; ?>
        <?php endwhile; ?>

        <?php get_template_part( 'template-parts/reservierung/reservation-form' ); ?>
    </div>

</main>

<?php get_footer();
