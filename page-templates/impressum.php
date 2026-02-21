<?php
declare(strict_types=1);
/**
 * Template Name: Impressum
 * Template Post Type: page
 *
 * Tisch by Kohler — Impressum page template.
 * Full-width legal text page.
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
            <article id="post-<?php the_ID(); ?>" <?php post_class( 'entry prose legal-page' ); ?>>
                <div class="entry__content">
                    <?php the_content(); ?>
                </div>
            </article>
        <?php endwhile; ?>
    </div>

</main>

<?php get_footer();
