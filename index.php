<?php
declare(strict_types=1);
/**
 * Tisch by Kohler — index.php
 * WordPress-required fallback template.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
?>

<main id="main-content" class="site-main container">
    <?php if ( have_posts() ) : ?>
        <?php while ( have_posts() ) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class( 'entry' ); ?>>
                <header class="entry__header">
                    <h1 class="entry__title"><?php the_title(); ?></h1>
                </header>
                <div class="entry__content">
                    <?php the_content(); ?>
                </div>
            </article>
        <?php endwhile; ?>
    <?php else : ?>
        <p class="no-results"><?php esc_html_e( 'Kein Inhalt gefunden.', 'tisch-kohler' ); ?></p>
    <?php endif; ?>
</main>

<?php get_footer();
