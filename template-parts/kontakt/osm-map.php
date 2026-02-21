<?php
declare(strict_types=1);
/**
 * Tisch by Kohler — template-parts/kontakt/osm-map.php
 * OpenStreetMap embed behind a one-click consent overlay.
 * Consent is stored in localStorage; map auto-loads on subsequent visits.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$embed_url = tisch_osm_embed_url();
?>

<div class="osm-map">
    <div class="osm-consent" id="osm-consent" aria-live="polite">
        <div class="osm-consent__inner">
            <p class="osm-consent__text">
                <?php esc_html_e( 'Die interaktive Karte wird von OpenStreetMap bereitgestellt. Beim Laden werden keine personenbezogenen Daten übertragen.', 'tisch-kohler' ); ?>
            </p>
            <button type="button" id="osm-accept-btn" class="btn btn--primary osm-consent__btn">
                <?php esc_html_e( 'Karte anzeigen', 'tisch-kohler' ); ?>
            </button>
        </div>
    </div>
    <iframe
        class="osm-map__iframe osm-map__iframe--hidden"
        id="osm-iframe"
        title="<?php esc_attr_e( 'Standort auf OpenStreetMap', 'tisch-kohler' ); ?>"
        data-src="<?php echo esc_url( $embed_url ); ?>"
        width="100%"
        height="450"
        style="border:0"
        allowfullscreen
    ></iframe>
    <p class="osm-map__attribution">
        <small>
            <?php esc_html_e( 'Kartendaten:', 'tisch-kohler' ); ?>
            <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener noreferrer">
                &copy; OpenStreetMap-Mitwirkende
            </a>
        </small>
    </p>
</div>
