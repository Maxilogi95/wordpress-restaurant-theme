<?php
declare(strict_types=1);
/**
 * Tisch by Kohler — template-parts/reservierung/reservation-form.php
 * DSGVO-safe reservation form. Works without JavaScript.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Status messages after redirect
$status = isset( $_GET['reservierung'] ) ? sanitize_key( $_GET['reservierung'] ) : '';
?>

<?php if ( 'success' === $status ) : ?>
    <div class="form-notice form-notice--success" role="alert">
        <p><?php esc_html_e( 'Vielen Dank für Ihre Anfrage! Wir werden uns so schnell wie möglich bei Ihnen melden.', 'tisch-kohler' ); ?></p>
    </div>

<?php elseif ( 'error' === $status ) : ?>
    <div class="form-notice form-notice--error" role="alert">
        <p><?php esc_html_e( 'Es ist ein Fehler aufgetreten. Bitte überprüfen Sie Ihre Eingaben und versuchen Sie es erneut.', 'tisch-kohler' ); ?></p>
    </div>

<?php elseif ( 'mail-error' === $status ) : ?>
    <div class="form-notice form-notice--error" role="alert">
        <p>
            <?php
            $phone = get_option( 'tisch_phone', '' );
            if ( $phone ) {
                printf(
                    /* translators: %s: phone number */
                    esc_html__( 'Die E-Mail konnte leider nicht gesendet werden. Bitte rufen Sie uns an: %s', 'tisch-kohler' ),
                    '<a href="tel:' . esc_attr( tisch_phone_link() ) . '">' . esc_html( $phone ) . '</a>'
                );
            } else {
                esc_html_e( 'Die E-Mail konnte leider nicht gesendet werden. Bitte kontaktieren Sie uns telefonisch.', 'tisch-kohler' );
            }
            ?>
        </p>
    </div>
<?php endif; ?>

<div class="reservation-form-wrap">
    <form
        class="reservation-form"
        id="reservation-form"
        method="post"
        action="<?php echo esc_url( get_permalink() ); ?>"
        novalidate
    >
        <?php wp_nonce_field( 'tisch_reservation', 'tisch_reservation_nonce' ); ?>

        <!-- Honeypot — hidden from humans via CSS, should remain empty -->
        <div class="reservation-form__honeypot" aria-hidden="true">
            <label for="tisch_website">Website (nicht ausfüllen)</label>
            <input type="text" id="tisch_website" name="tisch_website" tabindex="-1" autocomplete="off">
        </div>

        <div class="form-grid">

            <!-- Name -->
            <div class="form-field">
                <label class="form-field__label" for="tisch_name">
                    <?php esc_html_e( 'Name', 'tisch-kohler' ); ?>
                    <span class="form-field__required" aria-hidden="true">*</span>
                </label>
                <input
                    class="form-field__input"
                    type="text"
                    id="tisch_name"
                    name="tisch_name"
                    required
                    autocomplete="name"
                    maxlength="100"
                    value="<?php echo esc_attr( sanitize_text_field( wp_unslash( $_POST['tisch_name'] ?? '' ) ) ); ?>"
                >
            </div>

            <!-- E-Mail -->
            <div class="form-field">
                <label class="form-field__label" for="tisch_email_field">
                    <?php esc_html_e( 'E-Mail-Adresse', 'tisch-kohler' ); ?>
                    <span class="form-field__required" aria-hidden="true">*</span>
                </label>
                <input
                    class="form-field__input"
                    type="email"
                    id="tisch_email_field"
                    name="tisch_email"
                    required
                    autocomplete="email"
                    maxlength="200"
                    value="<?php echo esc_attr( sanitize_email( wp_unslash( $_POST['tisch_email'] ?? '' ) ) ); ?>"
                >
            </div>

            <!-- Telefon -->
            <div class="form-field">
                <label class="form-field__label" for="tisch_phone_field">
                    <?php esc_html_e( 'Telefon (optional)', 'tisch-kohler' ); ?>
                </label>
                <input
                    class="form-field__input"
                    type="tel"
                    id="tisch_phone_field"
                    name="tisch_phone"
                    autocomplete="tel"
                    maxlength="30"
                    value="<?php echo esc_attr( sanitize_text_field( wp_unslash( $_POST['tisch_phone'] ?? '' ) ) ); ?>"
                >
            </div>

            <!-- Datum -->
            <div class="form-field">
                <label class="form-field__label" for="tisch_date">
                    <?php esc_html_e( 'Wunschdatum', 'tisch-kohler' ); ?>
                    <span class="form-field__required" aria-hidden="true">*</span>
                </label>
                <input
                    class="form-field__input"
                    type="date"
                    id="tisch_date"
                    name="tisch_date"
                    required
                    min="<?php echo esc_attr( gmdate( 'Y-m-d', strtotime( '+1 day' ) ) ); ?>"
                    value="<?php echo esc_attr( sanitize_text_field( wp_unslash( $_POST['tisch_date'] ?? '' ) ) ); ?>"
                >
            </div>

            <!-- Uhrzeit -->
            <div class="form-field">
                <label class="form-field__label" for="tisch_time">
                    <?php esc_html_e( 'Uhrzeit', 'tisch-kohler' ); ?>
                    <span class="form-field__required" aria-hidden="true">*</span>
                </label>
                <select class="form-field__input form-field__select" id="tisch_time" name="tisch_time" required>
                    <option value=""><?php esc_html_e( 'Bitte wählen', 'tisch-kohler' ); ?></option>
                    <?php
                    $selected_time = sanitize_text_field( wp_unslash( $_POST['tisch_time'] ?? '' ) );
                    $times = [
                        '11:30', '12:00', '12:30', '13:00', '13:30',
                        '17:30', '18:00', '18:30', '19:00', '19:30', '20:00', '20:30',
                    ];
                    foreach ( $times as $t ) :
                        ?>
                        <option value="<?php echo esc_attr( $t ); ?>" <?php selected( $selected_time, $t ); ?>>
                            <?php echo esc_html( $t ); ?> Uhr
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Personenzahl -->
            <div class="form-field">
                <label class="form-field__label" for="tisch_guests">
                    <?php esc_html_e( 'Anzahl Personen', 'tisch-kohler' ); ?>
                    <span class="form-field__required" aria-hidden="true">*</span>
                </label>
                <input
                    class="form-field__input"
                    type="number"
                    id="tisch_guests"
                    name="tisch_guests"
                    required
                    min="1"
                    max="100"
                    value="<?php echo esc_attr( absint( $_POST['tisch_guests'] ?? 2 ) ); ?>"
                >
            </div>

            <!-- Nachricht -->
            <div class="form-field form-field--full">
                <label class="form-field__label" for="tisch_message">
                    <?php esc_html_e( 'Nachricht (optional)', 'tisch-kohler' ); ?>
                </label>
                <textarea
                    class="form-field__input form-field__textarea"
                    id="tisch_message"
                    name="tisch_message"
                    rows="4"
                    maxlength="2000"
                ><?php echo esc_textarea( sanitize_textarea_field( wp_unslash( $_POST['tisch_message'] ?? '' ) ) ); ?></textarea>
            </div>

            <!-- DSGVO Consent -->
            <div class="form-field form-field--full">
                <label class="form-field__checkbox-label">
                    <input
                        class="form-field__checkbox"
                        type="checkbox"
                        id="tisch_dsgvo"
                        name="tisch_dsgvo"
                        required
                        value="1"
                        <?php checked( isset( $_POST['tisch_dsgvo'] ) ); ?>
                    >
                    <span class="form-field__checkbox-text">
                        <?php
                        $datenschutz = get_page_by_path( 'datenschutzerklaerung' );
                        if ( $datenschutz ) {
                            printf(
                                /* translators: %1$s: opening link tag, %2$s: closing link tag */
                                esc_html__( 'Ich habe die %1$sDatenschutzerklärung%2$s gelesen und bin damit einverstanden, dass meine Daten zur Bearbeitung dieser Anfrage verwendet werden.', 'tisch-kohler' ),
                                '<a href="' . esc_url( get_permalink( $datenschutz ) ) . '" target="_blank" rel="noopener">',
                                '</a>'
                            );
                        } else {
                            esc_html_e( 'Ich bin damit einverstanden, dass meine Daten zur Bearbeitung dieser Anfrage verwendet werden.', 'tisch-kohler' );
                        }
                        ?>
                        <span class="form-field__required" aria-hidden="true">*</span>
                    </span>
                </label>
            </div>

            <!-- Submit -->
            <div class="form-field form-field--full">
                <button type="submit" name="tisch_reservation_submit" class="btn btn--primary btn--lg">
                    <?php esc_html_e( 'Anfrage senden', 'tisch-kohler' ); ?>
                </button>
                <p class="form-required-note">
                    <span aria-hidden="true">*</span> <?php esc_html_e( 'Pflichtfelder', 'tisch-kohler' ); ?>
                </p>
            </div>

        </div><!-- .form-grid -->
    </form>
</div>
