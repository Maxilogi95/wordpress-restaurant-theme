<?php
declare(strict_types=1);
/**
 * Template Name: Tagesessen
 * Template Post Type: page
 *
 * Tisch by Kohler — Tagesessen page template.
 * Displays the weekly menu PDF or an "in preparation" message.
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
            $intro = get_the_content();
            if ( $intro ) : ?>
                <div class="entry prose section--narrow">
                    <?php the_content(); ?>
                </div>
            <?php endif; ?>
        <?php endwhile; ?>

        <?php get_template_part( 'template-parts/tagesessen/pdf-viewer' ); ?>

    </div>

</main>

<?php get_footer();
