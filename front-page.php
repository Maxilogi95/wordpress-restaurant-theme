<?php
declare(strict_types=1);
/**
 * Tisch by Kohler — front-page.php
 * Startseite: Hero, Willkommen, Tagesessen-Teaser, Öffnungszeiten.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
?>

<main id="main-content" class="site-main">

    <?php get_template_part( 'template-parts/home/hero' ); ?>

    <?php get_template_part( 'template-parts/home/welcome' ); ?>

    <?php get_template_part( 'template-parts/home/tagesessen-teaser' ); ?>

    <?php get_template_part( 'template-parts/home/opening-hours' ); ?>

</main>

<?php get_footer();
