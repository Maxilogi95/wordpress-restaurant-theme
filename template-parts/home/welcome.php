<?php
declare(strict_types=1);
/**
 * Tisch by Kohler — template-parts/home/welcome.php
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! get_option( 'tisch_show_welcome', '1' ) ) {
    return;
}

$welcome_text = get_option( 'tisch_welcome_text', '' );

if ( empty( $welcome_text ) ) {
    return;
}
?>

<section class="section welcome-section" aria-label="<?php esc_attr_e( 'Willkommen', 'tisch-kohler' ); ?>">
    <div class="container container--narrow text-center">
        <div class="prose welcome-section__text">
            <?php echo wp_kses_post( $welcome_text ); ?>
        </div>
    </div>
</section>
