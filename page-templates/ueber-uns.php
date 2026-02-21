<?php
declare(strict_types=1);
/**
 * Template Name: Über uns
 * Template Post Type: page
 *
 * Tisch by Kohler — Über uns page template.
 * Full-width WP editor page.
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
                <?php if ( has_post_thumbnail() ) : ?>
                    <div class="entry__featured-image">
                        <?php the_post_thumbnail( 'tisch-card', [ 'class' => 'entry__image' ] ); ?>
                    </div>
                <?php endif; ?>
                <div class="entry__content">
                    <?php the_content(); ?>
                </div>
            </article>
        <?php endwhile; ?>
    </div>

</main>

<?php get_footer();
