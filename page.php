<?php
declare(strict_types=1);
/**
 * Tisch by Kohler — page.php
 * Generic page fallback template.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
?>

<main id="main-content" class="site-main">
    <?php while ( have_posts() ) : the_post(); ?>

        <div class="page-hero page-hero--simple">
            <div class="container">
                <h1 class="page-hero__title"><?php the_title(); ?></h1>
            </div>
        </div>

        <div class="container section">
            <article id="post-<?php the_ID(); ?>" <?php post_class( 'entry prose' ); ?>>
                <div class="entry__content">
                    <?php the_content(); ?>
                </div>
            </article>
        </div>

    <?php endwhile; ?>
</main>

<?php get_footer();
