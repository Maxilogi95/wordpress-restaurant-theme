<?php
declare(strict_types=1);
/**
 * Tisch by Kohler — inc/options.php
 * Admin settings page: Appearance > Tisch Einstellungen.
 * Manages PDF URL, address, opening hours, contact email, OSM coordinates.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ── Register settings ───────────────────────────────────────────────────────
add_action( 'admin_init', 'tisch_register_settings' );

function tisch_register_settings(): void {

    $options = [
        // Contact / address
        'tisch_email'   => [ 'sanitize_callback' => 'sanitize_email' ],
        'tisch_phone'   => [ 'sanitize_callback' => 'sanitize_text_field' ],
        'tisch_address' => [ 'sanitize_callback' => 'sanitize_text_field' ],

        // Opening hours — per individual day
        'tisch_hours_mon'        => [ 'sanitize_callback' => 'sanitize_textarea_field' ],
        'tisch_hours_mon_closed' => [ 'sanitize_callback' => 'sanitize_text_field' ],
        'tisch_hours_tue'        => [ 'sanitize_callback' => 'sanitize_textarea_field' ],
        'tisch_hours_tue_closed' => [ 'sanitize_callback' => 'sanitize_text_field' ],
        'tisch_hours_wed'        => [ 'sanitize_callback' => 'sanitize_textarea_field' ],
        'tisch_hours_wed_closed' => [ 'sanitize_callback' => 'sanitize_text_field' ],
        'tisch_hours_thu'        => [ 'sanitize_callback' => 'sanitize_textarea_field' ],
        'tisch_hours_thu_closed' => [ 'sanitize_callback' => 'sanitize_text_field' ],
        'tisch_hours_fri'        => [ 'sanitize_callback' => 'sanitize_textarea_field' ],
        'tisch_hours_fri_closed' => [ 'sanitize_callback' => 'sanitize_text_field' ],
        'tisch_hours_sat'        => [ 'sanitize_callback' => 'sanitize_textarea_field' ],
        'tisch_hours_sat_closed' => [ 'sanitize_callback' => 'sanitize_text_field' ],
        'tisch_hours_sun'        => [ 'sanitize_callback' => 'sanitize_textarea_field' ],
        'tisch_hours_sun_closed' => [ 'sanitize_callback' => 'sanitize_text_field' ],
        'tisch_hours_note'       => [ 'sanitize_callback' => 'wp_kses_post' ],

        // Special closing periods (3 fixed slots)
        'tisch_closing_1_from'  => [ 'sanitize_callback' => 'sanitize_text_field' ],
        'tisch_closing_1_to'    => [ 'sanitize_callback' => 'sanitize_text_field' ],
        'tisch_closing_1_label' => [ 'sanitize_callback' => 'sanitize_text_field' ],
        'tisch_closing_2_from'  => [ 'sanitize_callback' => 'sanitize_text_field' ],
        'tisch_closing_2_to'    => [ 'sanitize_callback' => 'sanitize_text_field' ],
        'tisch_closing_2_label' => [ 'sanitize_callback' => 'sanitize_text_field' ],
        'tisch_closing_3_from'  => [ 'sanitize_callback' => 'sanitize_text_field' ],
        'tisch_closing_3_to'    => [ 'sanitize_callback' => 'sanitize_text_field' ],
        'tisch_closing_3_label' => [ 'sanitize_callback' => 'sanitize_text_field' ],

        // Tagesessen PDF
        'tisch_tagesessen_pdf'         => [ 'sanitize_callback' => 'esc_url_raw' ],
        'tisch_tagesessen_pdf_id'      => [ 'sanitize_callback' => 'absint' ],
        'tisch_tagesessen_valid_until' => [ 'sanitize_callback' => 'sanitize_text_field' ],

        // Speisekarte PDF
        'tisch_speisekarte_pdf'          => [ 'sanitize_callback' => 'esc_url_raw' ],
        'tisch_speisekarte_pdf_id'       => [ 'sanitize_callback' => 'absint' ],
        'tisch_speisekarte_valid_until'  => [ 'sanitize_callback' => 'sanitize_text_field' ],
        'tisch_speisekarte_sections'     => [ 'sanitize_callback' => 'tisch_sanitize_speisekarte_sections' ],

        // OSM coordinates
        'tisch_osm_lat' => [ 'sanitize_callback' => 'tisch_sanitize_coordinate' ],
        'tisch_osm_lng' => [ 'sanitize_callback' => 'tisch_sanitize_coordinate' ],

        // Hero
        'tisch_hero_tagline'    => [ 'sanitize_callback' => 'sanitize_text_field' ],
        'tisch_hero_image_id'   => [ 'sanitize_callback' => 'absint' ],
        'tisch_welcome_text'    => [ 'sanitize_callback' => 'wp_kses_post' ],
    ];

    foreach ( $options as $key => $args ) {
        register_setting( 'tisch_options_group', $key, $args );
    }
}

/**
 * Sanitize a decimal coordinate value.
 */
function tisch_sanitize_coordinate( string $value ): string {
    $float = (float) $value;
    return number_format( $float, 6, '.', '' );
}

// ── Admin menu ──────────────────────────────────────────────────────────────
add_action( 'admin_menu', 'tisch_add_settings_page' );

function tisch_add_settings_page(): void {
    add_theme_page(
        __( 'Tisch Einstellungen', 'tisch-kohler' ),
        __( 'Tisch Einstellungen', 'tisch-kohler' ),
        'manage_options',
        'tisch-einstellungen',
        'tisch_render_settings_page'
    );
}

// ── Enqueue media uploader on our settings page ─────────────────────────────
add_action( 'admin_enqueue_scripts', 'tisch_admin_enqueue' );

function tisch_admin_enqueue( string $hook ): void {
    if ( 'appearance_page_tisch-einstellungen' !== $hook ) {
        return;
    }
    wp_enqueue_media();
    wp_enqueue_script(
        'tisch-admin',
        get_template_directory_uri() . '/assets/js/admin.js',
        [ 'jquery' ],
        wp_get_theme()->get( 'Version' ),
        true
    );
    wp_add_inline_script(
        'tisch-admin',
        'var tischAdminData = ' . wp_json_encode( [
            'speisekarteData' => array_values( (array) get_option( 'tisch_speisekarte_sections', [] ) ),
        ] ) . ';',
        'before'
    );
}

// ── Settings page HTML ───────────────────────────────────────────────────────
function tisch_render_settings_page(): void {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Tisch Einstellungen', 'tisch-kohler' ); ?></h1>

        <?php settings_errors( 'tisch_options_group' ); ?>

        <form method="post" action="options.php">
            <?php settings_fields( 'tisch_options_group' ); ?>

            <!-- ── Kontakt ────────────────────────────────── -->
            <h2><?php esc_html_e( 'Kontakt & Adresse', 'tisch-kohler' ); ?></h2>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="tisch_email"><?php esc_html_e( 'E-Mail-Adresse', 'tisch-kohler' ); ?></label></th>
                    <td><input type="email" id="tisch_email" name="tisch_email" value="<?php echo esc_attr( get_option( 'tisch_email' ) ); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="tisch_phone"><?php esc_html_e( 'Telefon', 'tisch-kohler' ); ?></label></th>
                    <td><input type="text" id="tisch_phone" name="tisch_phone" value="<?php echo esc_attr( get_option( 'tisch_phone' ) ); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="tisch_address"><?php esc_html_e( 'Adresse (Footer)', 'tisch-kohler' ); ?></label></th>
                    <td><input type="text" id="tisch_address" name="tisch_address" value="<?php echo esc_attr( get_option( 'tisch_address' ) ); ?>" class="regular-text"></td>
                </tr>
            </table>

            <!-- ── Öffnungszeiten ─────────────────────────── -->
            <h2><?php esc_html_e( 'Öffnungszeiten', 'tisch-kohler' ); ?></h2>
            <table class="form-table" role="presentation">
                <?php
                $hours_days = [
                    'mon' => __( 'Montag',     'tisch-kohler' ),
                    'tue' => __( 'Dienstag',   'tisch-kohler' ),
                    'wed' => __( 'Mittwoch',   'tisch-kohler' ),
                    'thu' => __( 'Donnerstag', 'tisch-kohler' ),
                    'fri' => __( 'Freitag',    'tisch-kohler' ),
                    'sat' => __( 'Samstag',    'tisch-kohler' ),
                    'sun' => __( 'Sonn- und Feiertag', 'tisch-kohler' ),
                ];
                foreach ( $hours_days as $day_key => $day_label ) :
                    $opt_hours  = 'tisch_hours_' . $day_key;
                    $opt_closed = 'tisch_hours_' . $day_key . '_closed';
                    $is_closed  = (bool) get_option( $opt_closed, '' );
                    ?>
                    <tr>
                        <th scope="row"><?php echo esc_html( $day_label ); ?></th>
                        <td>
                            <label style="display:inline-flex;align-items:center;gap:6px;margin-bottom:6px">
                                <input type="checkbox"
                                       id="<?php echo esc_attr( $opt_closed ); ?>"
                                       name="<?php echo esc_attr( $opt_closed ); ?>"
                                       value="1"
                                       class="tisch-closed-cb"
                                       data-target="<?php echo esc_attr( $opt_hours ); ?>"
                                       <?php checked( $is_closed ); ?>>
                                <?php esc_html_e( 'Ruhetag / Geschlossen', 'tisch-kohler' ); ?>
                            </label><br>
                            <textarea
                                id="<?php echo esc_attr( $opt_hours ); ?>"
                                name="<?php echo esc_attr( $opt_hours ); ?>"
                                class="regular-text"
                                rows="2"
                                placeholder="z. B.&#10;11:30 – 14:00 Uhr&#10;17:30 – 22:00 Uhr"
                                <?php echo $is_closed ? 'disabled' : ''; ?>
                            ><?php echo esc_textarea( get_option( $opt_hours, '' ) ); ?></textarea>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <th scope="row"><label for="tisch_hours_note"><?php esc_html_e( 'Hinweis', 'tisch-kohler' ); ?></label></th>
                    <td><textarea id="tisch_hours_note" name="tisch_hours_note" class="large-text" rows="3"><?php echo esc_textarea( get_option( 'tisch_hours_note' ) ); ?></textarea></td>
                </tr>
            </table>

            <!-- ── Betriebsferien / Schließzeiten ────────── -->
            <h2><?php esc_html_e( 'Betriebsferien / Schließzeiten', 'tisch-kohler' ); ?></h2>
            <p class="description" style="margin-bottom:1em"><?php esc_html_e( 'Liegt das heutige Datum innerhalb eines Zeitraums, erscheint auf jeder Seite ein Hinweisbanner.', 'tisch-kohler' ); ?></p>
            <table class="form-table" role="presentation">
                <?php for ( $i = 1; $i <= 3; $i++ ) : ?>
                    <tr>
                        <th scope="row"><?php printf( esc_html__( 'Schließzeit %d', 'tisch-kohler' ), $i ); ?></th>
                        <td>
                            <label><?php esc_html_e( 'Von:', 'tisch-kohler' ); ?>
                                <input type="date"
                                       name="<?php echo esc_attr( "tisch_closing_{$i}_from" ); ?>"
                                       value="<?php echo esc_attr( get_option( "tisch_closing_{$i}_from", '' ) ); ?>"
                                       style="margin-left:4px;margin-right:12px">
                            </label>
                            <label><?php esc_html_e( 'Bis:', 'tisch-kohler' ); ?>
                                <input type="date"
                                       name="<?php echo esc_attr( "tisch_closing_{$i}_to" ); ?>"
                                       value="<?php echo esc_attr( get_option( "tisch_closing_{$i}_to", '' ) ); ?>"
                                       style="margin-left:4px;margin-right:12px">
                            </label>
                            <label><?php esc_html_e( 'Bezeichnung:', 'tisch-kohler' ); ?>
                                <input type="text"
                                       name="<?php echo esc_attr( "tisch_closing_{$i}_label" ); ?>"
                                       value="<?php echo esc_attr( get_option( "tisch_closing_{$i}_label", '' ) ); ?>"
                                       class="regular-text"
                                       placeholder="<?php esc_attr_e( 'z. B. Weihnachtsferien', 'tisch-kohler' ); ?>"
                                       style="margin-left:4px">
                            </label>
                        </td>
                    </tr>
                <?php endfor; ?>
            </table>

            <!-- ── Tagesessen PDF ─────────────────────────── -->
            <h2><?php esc_html_e( 'Tagesessen – Wochenkarte (PDF)', 'tisch-kohler' ); ?></h2>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><?php esc_html_e( 'PDF-Datei', 'tisch-kohler' ); ?></th>
                    <td>
                        <input type="hidden" id="tisch_tagesessen_pdf_id" name="tisch_tagesessen_pdf_id"
                               value="<?php echo esc_attr( get_option( 'tisch_tagesessen_pdf_id' ) ); ?>">
                        <input type="url" id="tisch_tagesessen_pdf" name="tisch_tagesessen_pdf"
                               value="<?php echo esc_attr( get_option( 'tisch_tagesessen_pdf' ) ); ?>"
                               class="large-text" readonly>
                        <button type="button"
                                class="button button-secondary tisch-pdf-upload"
                                data-url-target="#tisch_tagesessen_pdf"
                                data-id-target="#tisch_tagesessen_pdf_id"
                                style="margin-top:4px">
                            <?php esc_html_e( 'PDF aus Mediathek wählen', 'tisch-kohler' ); ?>
                        </button>
                        <?php if ( get_option( 'tisch_tagesessen_pdf' ) ) : ?>
                            <button type="button"
                                    class="button tisch-pdf-remove"
                                    data-url-target="#tisch_tagesessen_pdf"
                                    data-id-target="#tisch_tagesessen_pdf_id"
                                    style="margin-top:4px;margin-left:4px">
                                <?php esc_html_e( 'Entfernen', 'tisch-kohler' ); ?>
                            </button>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="tisch_tagesessen_valid_until"><?php esc_html_e( 'Gültig bis (YYYY-MM-DD)', 'tisch-kohler' ); ?></label></th>
                    <td>
                        <input type="date" id="tisch_tagesessen_valid_until" name="tisch_tagesessen_valid_until"
                               value="<?php echo esc_attr( get_option( 'tisch_tagesessen_valid_until' ) ); ?>">
                        <p class="description"><?php esc_html_e( 'Ist das Datum überschritten, erscheint stattdessen ein Hinweistext.', 'tisch-kohler' ); ?></p>
                    </td>
                </tr>
            </table>

            <!-- ── Speisekarte PDF ────────────────────────── -->
            <h2><?php esc_html_e( 'Speisekarte – PDF (optional)', 'tisch-kohler' ); ?></h2>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><?php esc_html_e( 'PDF-Datei', 'tisch-kohler' ); ?></th>
                    <td>
                        <input type="hidden" id="tisch_speisekarte_pdf_id" name="tisch_speisekarte_pdf_id"
                               value="<?php echo esc_attr( get_option( 'tisch_speisekarte_pdf_id' ) ); ?>">
                        <input type="url" id="tisch_speisekarte_pdf" name="tisch_speisekarte_pdf"
                               value="<?php echo esc_attr( get_option( 'tisch_speisekarte_pdf' ) ); ?>"
                               class="large-text" readonly>
                        <button type="button"
                                class="button button-secondary tisch-pdf-upload"
                                data-url-target="#tisch_speisekarte_pdf"
                                data-id-target="#tisch_speisekarte_pdf_id"
                                style="margin-top:4px">
                            <?php esc_html_e( 'PDF aus Mediathek wählen', 'tisch-kohler' ); ?>
                        </button>
                        <?php if ( get_option( 'tisch_speisekarte_pdf' ) ) : ?>
                            <button type="button"
                                    class="button tisch-pdf-remove"
                                    data-url-target="#tisch_speisekarte_pdf"
                                    data-id-target="#tisch_speisekarte_pdf_id"
                                    style="margin-top:4px;margin-left:4px">
                                <?php esc_html_e( 'Entfernen', 'tisch-kohler' ); ?>
                            </button>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="tisch_speisekarte_valid_until">
                            <?php esc_html_e( 'Gültig bis (YYYY-MM-DD)', 'tisch-kohler' ); ?>
                        </label>
                    </th>
                    <td>
                        <input type="date" id="tisch_speisekarte_valid_until" name="tisch_speisekarte_valid_until"
                               value="<?php echo esc_attr( get_option( 'tisch_speisekarte_valid_until' ) ); ?>">
                        <p class="description">
                            <?php esc_html_e( 'Ist das Datum überschritten, wird der Download-Button ausgeblendet.', 'tisch-kohler' ); ?>
                        </p>
                    </td>
                </tr>
            </table>

            <!-- ── Speisekarte – Menü-Karte ──────────────── -->
            <h2><?php esc_html_e( 'Speisekarte – Menü-Karte', 'tisch-kohler' ); ?></h2>
            <p class="description" style="margin-bottom:1em">
                <?php esc_html_e( 'Abschnitte und Gerichte werden als strukturierte Speisekarte auf der Speisekarte-Seite angezeigt.', 'tisch-kohler' ); ?>
            </p>

            <!-- Hidden clone templates -->
            <template id="speisekarte-section-tpl">
                <div class="tisch-menu-section" style="border:1px solid #ddd;padding:12px;margin-bottom:12px;border-radius:4px;background:#fff">
                    <div style="display:flex;gap:8px;margin-bottom:8px;align-items:center">
                        <input type="text" data-field="title" placeholder="<?php esc_attr_e( 'Abschnittstitel, z.B. Vorspeisen', 'tisch-kohler' ); ?>" class="regular-text">
                        <button type="button" class="button tisch-move-section-up"   title="Nach oben">↑</button>
                        <button type="button" class="button tisch-move-section-down" title="Nach unten">↓</button>
                        <button type="button" class="button tisch-remove-section"><?php esc_html_e( 'Abschnitt entfernen', 'tisch-kohler' ); ?></button>
                    </div>
                    <div class="tisch-section-items"></div>
                    <button type="button" class="button tisch-add-item" style="margin-top:6px">
                        <?php esc_html_e( '+ Gericht hinzufügen', 'tisch-kohler' ); ?>
                    </button>
                </div>
            </template>

            <template id="speisekarte-item-tpl">
                <div class="tisch-menu-item" style="display:grid;grid-template-columns:2fr 1fr 2fr 1fr auto;gap:6px;margin-bottom:6px;align-items:center">
                    <input type="text" data-item-field="name"  placeholder="<?php esc_attr_e( 'Name des Gerichts *', 'tisch-kohler' ); ?>" class="regular-text">
                    <input type="text" data-item-field="price" placeholder="<?php esc_attr_e( 'Preis, z.B. 12,50 €', 'tisch-kohler' ); ?>" class="small-text">
                    <input type="text" data-item-field="desc"  placeholder="<?php esc_attr_e( 'Beschreibung (optional)', 'tisch-kohler' ); ?>" class="regular-text">
                    <input type="text" data-item-field="note"  placeholder="<?php esc_attr_e( 'Allergene (optional)', 'tisch-kohler' ); ?>" class="small-text">
                    <button type="button" class="button tisch-remove-item">&times;</button>
                </div>
            </template>

            <div id="speisekarte-sections" style="margin-bottom:12px"></div>
            <button type="button" class="button button-primary" id="speisekarte-add-section">
                <?php esc_html_e( '+ Abschnitt hinzufügen', 'tisch-kohler' ); ?>
            </button>

            <hr style="margin:2em 0">

            <!-- ── Startseite ─────────────────────────────── -->
            <h2><?php esc_html_e( 'Startseite – Hero', 'tisch-kohler' ); ?></h2>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="tisch_hero_tagline"><?php esc_html_e( 'Tagline (Hero)', 'tisch-kohler' ); ?></label></th>
                    <td><input type="text" id="tisch_hero_tagline" name="tisch_hero_tagline"
                               value="<?php echo esc_attr( get_option( 'tisch_hero_tagline', 'Herzlich willkommen' ) ); ?>"
                               class="large-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="tisch_welcome_text"><?php esc_html_e( 'Willkommenstext', 'tisch-kohler' ); ?></label></th>
                    <td>
                        <?php
                        wp_editor(
                            get_option( 'tisch_welcome_text', '' ),
                            'tisch_welcome_text',
                            [
                                'textarea_rows' => 6,
                                'teeny'         => true,
                            ]
                        );
                        ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Hero-Bild', 'tisch-kohler' ); ?></th>
                    <td>
                        <input type="hidden" id="tisch_hero_image_id" name="tisch_hero_image_id"
                               value="<?php echo esc_attr( get_option( 'tisch_hero_image_id' ) ); ?>">
                        <?php
                        $hero_id = (int) get_option( 'tisch_hero_image_id' );
                        if ( $hero_id ) :
                            echo wp_get_attachment_image( $hero_id, 'thumbnail' );
                        endif;
                        ?>
                        <button type="button" class="button button-secondary tisch-hero-image-upload" style="display:block;margin-top:6px">
                            <?php esc_html_e( 'Bild aus Mediathek wählen', 'tisch-kohler' ); ?>
                        </button>
                        <?php if ( get_option( 'tisch_hero_image_id' ) ) : ?>
                            <button type="button" class="button tisch-hero-image-remove"
                                    style="display:block;margin-top:6px">
                                <?php esc_html_e( 'Bild entfernen', 'tisch-kohler' ); ?>
                            </button>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>

            <!-- ── OSM-Koordinaten ────────────────────────── -->
            <h2><?php esc_html_e( 'OpenStreetMap – Koordinaten', 'tisch-kohler' ); ?></h2>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="tisch_osm_lat"><?php esc_html_e( 'Breitengrad (Lat)', 'tisch-kohler' ); ?></label></th>
                    <td><input type="text" id="tisch_osm_lat" name="tisch_osm_lat"
                               value="<?php echo esc_attr( get_option( 'tisch_osm_lat', '48.087400' ) ); ?>"
                               class="small-text" placeholder="48.087400"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="tisch_osm_lng"><?php esc_html_e( 'Längengrad (Lng)', 'tisch-kohler' ); ?></label></th>
                    <td><input type="text" id="tisch_osm_lng" name="tisch_osm_lng"
                               value="<?php echo esc_attr( get_option( 'tisch_osm_lng', '9.218900' ) ); ?>"
                               class="small-text" placeholder="9.218900"></td>
                </tr>
            </table>

            <?php submit_button( __( 'Einstellungen speichern', 'tisch-kohler' ) ); ?>
        </form>
    </div>
    <script>
    (function () {
        document.querySelectorAll('.tisch-closed-cb').forEach(function (cb) {
            var input = document.getElementById(cb.dataset.target);
            if (!input) return;
            cb.addEventListener('change', function () {
                input.disabled = cb.checked;
            });
        });
    }());
    </script>
    <?php
}
