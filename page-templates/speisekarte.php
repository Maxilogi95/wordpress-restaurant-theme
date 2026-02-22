<?php
declare(strict_types=1);
/**
 * Template Name: Speisekarte
 * Template Post Type: page
 *
 * Tisch by Kohler — Speisekarte page template.
 * Shows optional intro text, an optional PDF download, and the structured menu.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

$speisekarte_pdf      = get_option( 'tisch_speisekarte_pdf', '' );
$speisekarte_sections = (array) get_option( 'tisch_speisekarte_sections', [] );
$has_pdf              = tisch_speisekarte_is_valid();
$valid_until          = get_option( 'tisch_speisekarte_valid_until', '' );
?>

<main id="main-content" class="site-main">

    <div class="page-hero page-hero--simple">
        <div class="container">
            <h1 class="page-hero__title"><?php the_title(); ?></h1>
        </div>
    </div>

    <div class="container section">
        <?php while ( have_posts() ) : the_post(); ?>
            <?php if ( get_the_content() ) : ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class( 'entry speisekarte prose' ); ?>>
                    <div class="entry__content">
                        <?php the_content(); ?>
                    </div>
                </article>
            <?php endif; ?>
        <?php endwhile; ?>

        <?php if ( $has_pdf ) : ?>
            <div class="speisekarte-pdf-bar">
                <span class="speisekarte-pdf-bar__label">
                    <?php esc_html_e( 'Speisekarte als PDF:', 'tisch-kohler' ); ?>
                    <?php if ( $valid_until ) : ?>
                        <span class="speisekarte-pdf-bar__date">
                            <?php
                            $d = \DateTimeImmutable::createFromFormat( 'Y-m-d', $valid_until );
                            if ( $d ) {
                                /* translators: %s: formatted date */
                                printf( esc_html__( 'gültig bis %s', 'tisch-kohler' ), esc_html( $d->format( 'd.m.Y' ) ) );
                            }
                            ?>
                        </span>
                    <?php endif; ?>
                </span>
                <a href="<?php echo esc_url( $speisekarte_pdf ); ?>"
                   class="btn btn--primary speisekarte-pdf-bar__btn"
                   target="_blank"
                   rel="noopener noreferrer"
                   download>
                    <?php esc_html_e( 'PDF herunterladen', 'tisch-kohler' ); ?>
                </a>
            </div>
        <?php endif; ?>

        <?php if ( ! empty( $speisekarte_sections ) ) : ?>
            <div class="speisekarte-menu">
                <?php foreach ( $speisekarte_sections as $section ) :
                    if ( empty( $section['title'] ) && empty( $section['items'] ) ) {
                        continue;
                    }
                    ?>
                    <section class="menu-section">
                        <?php if ( ! empty( $section['title'] ) ) : ?>
                            <h2 class="menu-section__title">
                                <?php echo esc_html( $section['title'] ); ?>
                            </h2>
                        <?php endif; ?>

                        <?php if ( ! empty( $section['items'] ) ) : ?>
                            <ul class="menu-items">
                                <?php foreach ( $section['items'] as $item ) :
                                    if ( empty( $item['name'] ) ) {
                                        continue;
                                    }
                                    ?>
                                    <?php
                                    $has_img = ! empty( $item['img_id'] );
                                    $li_class = $has_img ? 'menu-item has-img' : 'menu-item';
                                    ?>
                                    <li class="<?php echo esc_attr( $li_class ); ?>">
                                        <?php if ( $has_img ) : ?>
                                            <?php echo wp_get_attachment_image(
                                                (int) $item['img_id'],
                                                'tisch-thumb',
                                                false,
                                                [ 'class' => 'menu-item__img', 'loading' => 'lazy' ]
                                            ); ?>
                                        <?php endif; ?>
                                        <div class="menu-item__body">
                                            <div class="menu-item__row">
                                                <span class="menu-item__name">
                                                    <?php echo esc_html( $item['name'] ); ?>
                                                </span>
                                                <?php
                                                $badges = [];
                                                if ( ! empty( $item['veg'] ) )   $badges[] = '<span class="diet-badge diet-badge--veg" title="Vegetarisch">V</span>';
                                                if ( ! empty( $item['vegan'] ) ) $badges[] = '<span class="diet-badge diet-badge--vegan" title="Vegan">VG</span>';
                                                if ( ! empty( $item['spicy'] ) ) $badges[] = '<span class="diet-badge diet-badge--spicy" title="Scharf">&#x25CF;</span>';
                                                if ( $badges ) {
                                                    echo '<span class="diet-badges">' . implode( '', $badges ) . '</span>';
                                                }
                                                ?>
                                                <?php if ( ! empty( $item['price'] ) ) : ?>
                                                    <span class="menu-item__price">
                                                        <?php echo esc_html( $item['price'] ); ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <?php if ( ! empty( $item['desc'] ) ) : ?>
                                                <p class="menu-item__desc">
                                                    <?php echo esc_html( $item['desc'] ); ?>
                                                </p>
                                            <?php endif; ?>
                                            <?php if ( ! empty( $item['note'] ) ) : ?>
                                                <p class="menu-item__note">
                                                    <?php echo esc_html( $item['note'] ); ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </section>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</main>

<?php get_footer();
