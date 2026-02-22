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
$hero_subhead  = (string) get_option( 'tisch_hero_subheadline', '' );
$btn1_text     = (string) get_option( 'tisch_hero_btn1_text', 'Tisch reservieren' );
$btn1_url      = (string) get_option( 'tisch_hero_btn1_url', '' );
$btn2_text     = (string) get_option( 'tisch_hero_btn2_text', 'Zur Speisekarte' );
$btn2_url      = (string) get_option( 'tisch_hero_btn2_url', '' );
$blog_name     = get_bloginfo( 'name' );

// Fallback to page permalink when URL option is empty
if ( ! $btn1_url ) {
    $p        = get_page_by_path( 'reservierung' );
    $btn1_url = $p ? (string) get_permalink( $p ) : '';
}
if ( ! $btn2_url ) {
    $p        = get_page_by_path( 'speisekarte' );
    $btn2_url = $p ? (string) get_permalink( $p ) : '';
}
?>

<section class="hero" aria-label="<?php esc_attr_e( 'Hero-Bereich', 'tisch-kohler' ); ?>">

    <?php if ( $hero_image_id ) : ?>
        <div class="hero__bg" aria-hidden="true">
            <?php echo wp_get_attachment_image( $hero_image_id, 'tisch-hero', false, [
                'class'         => 'hero__image',
                'loading'       => 'eager',
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

        <p class="hero__subheadline"<?php echo $hero_subhead ? '' : ' style="display:none"'; ?>>
            <?php echo esc_html( $hero_subhead ); ?>
        </p>

        <div class="hero__actions">
            <?php if ( $btn1_url ) : ?>
                <a href="<?php echo esc_url( $btn1_url ); ?>" class="btn btn--primary btn--lg hero__btn1">
                    <?php echo esc_html( $btn1_text ); ?>
                </a>
            <?php endif; ?>
            <?php if ( $btn2_url ) : ?>
                <a href="<?php echo esc_url( $btn2_url ); ?>" class="btn btn--outline btn--lg hero__btn2">
                    <?php echo esc_html( $btn2_text ); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>

</section>
