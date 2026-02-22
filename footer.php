<?php
declare(strict_types=1);
/**
 * Tisch by Kohler — footer.php
 * Footer with legal links and wp_footer().
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<footer class="site-footer" role="contentinfo">
    <div class="container site-footer__inner">

        <div class="site-footer__brand">
            <p class="site-footer__name"><?php bloginfo( 'name' ); ?></p>
            <?php
            $address = get_option( 'tisch_address', 'Hauptstraße 1 · 72488 Sigmaringen' );
            if ( $address ) : ?>
                <p class="site-footer__address"><?php echo wp_kses_post( $address ); ?></p>
            <?php endif; ?>
        </div>

        <nav class="site-footer__nav" aria-label="<?php esc_attr_e( 'Footer-Navigation', 'tisch-kohler' ); ?>">
            <?php
            wp_nav_menu( [
                'theme_location' => 'footer',
                'menu_class'     => 'site-footer__list',
                'container'      => false,
                'depth'          => 1,
                'fallback_cb'    => 'tisch_footer_nav_fallback',
            ] );
            ?>
        </nav>

        <p class="site-footer__copy">
            &copy; <?php echo esc_html( gmdate( 'Y' ) ); ?>
            <span class="site-footer__copyright-text">
                <?php
                $copyright = (string) get_option( 'tisch_footer_copyright', '' );
                echo esc_html( $copyright ?: get_bloginfo( 'name' ) );
                ?>
            </span>
        </p>

    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
