<?php
declare(strict_types=1);
/**
 * Tisch by Kohler — template-parts/home/hero.php
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$hero_image_id = (int) get_option( 'tisch_hero_image_id' );
$hero_tagline  = (string) get_option( 'tisch_hero_tagline', 'Herzlich willkommen' );
$blog_name     = get_bloginfo( 'name' );

$reservierung = get_page_by_path( 'reservierung' );
$speisekarte  = get_page_by_path( 'speisekarte' );
?>

<section class="hero" aria-label="<?php esc_attr_e( 'Hero-Bereich', 'tisch-kohler' ); ?>">

    <?php if ( $hero_image_id ) : ?>
        <div class="hero__bg" aria-hidden="true">
            <?php echo wp_get_attachment_image( $hero_image_id, 'tisch-hero', false, [
                'class'   => 'hero__image',
                'loading' => 'eager',
                'fetchpriority' => 'high',
            ] ); ?>
        </div>
    <?php else : ?>
        <div class="hero__bg hero__bg--placeholder" aria-hidden="true"></div>
    <?php endif; ?>

    <div class="hero__overlay" aria-hidden="true"></div>

    <div class="container hero__content">
        <h1 class="hero__title"><?php echo esc_html( $blog_name ); ?></h1>
        <p class="hero__tagline"><?php echo esc_html( $hero_tagline ); ?></p>

        <div class="hero__actions">
            <?php if ( $reservierung ) : ?>
                <a href="<?php echo esc_url( get_permalink( $reservierung ) ); ?>" class="btn btn--primary btn--lg">
                    <?php esc_html_e( 'Tisch reservieren', 'tisch-kohler' ); ?>
                </a>
            <?php endif; ?>
            <?php if ( $speisekarte ) : ?>
                <a href="<?php echo esc_url( get_permalink( $speisekarte ) ); ?>" class="btn btn--outline btn--lg">
                    <?php esc_html_e( 'Zur Speisekarte', 'tisch-kohler' ); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>

</section>
