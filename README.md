# Tisch by Kohler — WordPress Theme

**Version:** 1.0.0 | **Requires WordPress:** 6.4+ | **Requires PHP:** 8.0+ | **License:** GPL-2.0-or-later

---

## Overview

DSGVO-compliant WordPress restaurant theme by Maximilian Kohler. Built with three core goals: full DSGVO compliance (zero external requests, self-hosted fonts, OSM map with consent gate), no required plugins, and no dependency on the block editor. All front-end output is controlled through page templates and `inc/` modules; the site admin fills in content through a dedicated settings page.

---

## Requirements

- PHP 8.0 or higher
- WordPress 6.4 or higher
- No plugins required

---

## Installation

### 1. Upload the theme

Copy the theme folder into your WordPress installation:

```
wp-content/themes/tisch-kohler/
```

The folder must be named exactly `tisch-kohler`.

### 2. Activate

Go to **Appearance > Themes** and activate "Tisch by Kohler".

### 3. Create required pages

Create the following pages in **Pages > Add New**. Each page must use the exact slug shown and have the corresponding page template selected under **Page Attributes > Template**.

| Slug | Template Name |
|---|---|
| `tagesessen` | Tagesessen |
| `speisekarte` | Speisekarte |
| `catering` | Catering |
| `reservierung` | Reservierung |
| `ueber-uns` | Über uns |
| `kontakt` | Kontakt |
| `impressum` | Impressum |
| `datenschutzerklaerung` | Datenschutzerklärung |

Create one additional page for the front page (e.g. titled "Startseite") — no template needed; it uses `front-page.php` automatically.

### 4. Set the static front page

Go to **Settings > Reading** and set:
- "Your homepage displays" → **A static page**
- "Homepage" → the Startseite page you just created

### 5. Create navigation menus

Go to **Appearance > Menus**:

1. Create a menu named "Hauptnavigation". Add the pages you want visible in the top navigation. Assign it to the **Hauptnavigation** location.
2. Create a menu named "Footer-Navigation". It **must** include the Impressum and Datenschutzerklärung pages to satisfy DSGVO requirements. Assign it to the **Footer-Navigation** location.

### 6. Fill in settings

Go to **Appearance > Tisch Einstellungen** and fill in all contact, hours, and content settings. See the Admin Settings Reference below.

---

## Admin Settings Reference

All settings are under **Appearance > Tisch Einstellungen**. Save with the button at the bottom of the page.

### Kontakt & Adresse

| Field | Description |
|---|---|
| E-Mail-Adresse | Contact email; also receives reservation requests |
| Telefon | Phone number; displayed in footer and contact page |
| Adresse (Footer) | Single-line address shown in the site footer |

### Öffnungszeiten

One row per day (Montag through Sonn- und Feiertag).

| Field | Description |
|---|---|
| Ruhetag / Geschlossen | Check to mark the day as closed; disables the hours textarea |
| Hours textarea | Free-text field for opening times (e.g. `11:30 – 14:00 Uhr`) |
| Hinweis | Optional note shown below the hours table (accepts basic HTML) |

### Betriebsferien / Schließzeiten

Three slots for planned closing periods. When today's date falls within a period, a notice banner is displayed on every page of the site.

| Field | Description |
|---|---|
| Von | Start date (date picker, stored as YYYY-MM-DD) |
| Bis | End date (date picker, stored as YYYY-MM-DD) |
| Bezeichnung | Label for the period, e.g. "Weihnachtsferien" |

Leave a slot's dates empty to disable it.

### Tagesessen – Wochenkarte (PDF)

| Field | Description |
|---|---|
| PDF-Datei | Upload a PDF via the media library. The URL and attachment ID are stored automatically. |
| Gültig bis | Expiry date. When today's date is past this date, the PDF viewer is replaced by a notice and the restaurant phone number. Leave empty for no expiry. |

### Speisekarte – PDF (optional)

| Field | Description |
|---|---|
| PDF-Datei | Optional PDF download (same media-library picker as Tagesessen). |
| Gültig bis | When exceeded the download button is hidden. Leave empty for no expiry. |

### Speisekarte – Menü-Karte

A dynamic repeater for the structured menu displayed on the Speisekarte page. Sections can be added, reordered with the arrow buttons, and removed. Each section has a title and any number of dishes.

Per dish: Name (required), Preis, Beschreibung (optional), Allergene (optional).

### Startseite – Hero

| Field | Description |
|---|---|
| Tagline | Short text displayed in italic below the site name in the hero section |
| Willkommenstext | Longer welcome paragraph; supports basic formatting via the mini editor |
| Hero-Bild | Background image for the hero; use a landscape image of at least 1920 × 800 px |

### OpenStreetMap – Koordinaten

| Field | Description |
|---|---|
| Breitengrad (Lat) | Decimal latitude, e.g. `48.087400` |
| Längengrad (Lng) | Decimal longitude, e.g. `9.218900` |

### Farben (Customizer)

Color overrides are set in **Appearance > Anpassen > Tisch by Kohler > Farben**. Live preview is supported.

| Control | CSS variable overridden | Default |
|---|---|---|
| Primärfarbe (Braun) | `--color-primary` | `#5C3D2E` |
| Primärfarbe dunkel | `--color-primary-dark` | `#3E2418` |
| Akzentfarbe (Gold) | `--color-accent` | `#C8922A` |
| Hintergrundfarbe | `--color-bg` | `#FAF6F0` |

---

## Page Templates Reference

| Template Name | Slug | Purpose |
|---|---|---|
| Tagesessen | `tagesessen` | Shows the weekly menu PDF with expiry logic |
| Speisekarte | `speisekarte` | PDF download bar + structured menu repeater |
| Catering | `catering` | Static catering information page |
| Reservierung | `reservierung` | Online reservation form |
| Über uns | `ueber-uns` | About page with free WordPress editor content |
| Kontakt | `kontakt` | Contact details + OSM map with consent gate |
| Impressum | `impressum` | Legal notice page |
| Datenschutzerklärung | `datenschutzerklaerung` | Privacy policy page |

---

## Theme Architecture

### File Tree

```
tisch-kohler/
├── style.css                     # Theme header; imports assets/css/main.css
├── functions.php                 # Autoloader only — requires inc/*.php in order
├── index.php                     # Fallback template (empty, required by WordPress)
├── front-page.php                # Homepage: hero, welcome, tagesessen teaser, hours
├── header.php                    # Skip link, site header, sticky nav, closing banner
├── footer.php                    # Site footer, nav menus, copyright
├── page.php                      # Default page template (WP editor content)
├── 404.php                       # 404 error page
│
├── inc/
│   ├── helpers.php               # Shared utility functions (see Helper Functions)
│   ├── theme-setup.php           # after_setup_theme: supports, menus, image sizes
│   ├── enqueue.php               # CSS/JS registration, conditional loading
│   ├── customizer.php            # Customizer color controls (Appearance > Anpassen)
│   ├── security.php              # Hardening hooks (see Security Hardening)
│   ├── options.php               # Admin settings page (Appearance > Tisch Einstellungen)
│   └── reservation-form.php      # Reservation POST handler
│
├── page-templates/
│   ├── tagesessen.php
│   ├── speisekarte.php
│   ├── catering.php
│   ├── reservierung.php
│   ├── ueber-uns.php
│   ├── kontakt.php
│   ├── impressum.php
│   └── datenschutz.php
│
├── template-parts/
│   ├── home/
│   │   ├── hero.php              # Full-bleed hero with background image/gradient
│   │   ├── welcome.php           # Welcome text section
│   │   ├── tagesessen-teaser.php # Teaser card linking to tagesessen page
│   │   └── opening-hours.php     # Hours table driven by options
│   ├── tagesessen/
│   │   └── pdf-viewer.php        # PDF embed with expiry logic
│   ├── reservierung/
│   │   └── reservation-form.php  # The reservation HTML form
│   └── kontakt/
│       └── osm-map.php           # OSM iframe with consent overlay
│
├── assets/
│   ├── css/
│   │   ├── main.css              # All front-end styles (design tokens + components)
│   │   └── print.css             # Print-only overrides
│   ├── fonts/
│   │   ├── fonts.css             # @font-face declarations
│   │   └── *.woff2               # Playfair Display 700, Lato 400 & 700
│   └── js/
│       ├── navigation.js         # Mobile menu toggle
│       ├── reservation.js        # Client-side form validation
│       ├── osm-consent.js        # OSM map consent gate (localStorage)
│       ├── customizer-preview.js # Live preview in Customizer
│       └── admin.js              # Media uploader + Speisekarte repeater (admin only)
│
└── languages/
    └── tisch-kohler.pot   # Translation template
```

### Autoloader

`functions.php` contains no logic; it requires the following `inc/` files in this order:

1. `helpers.php`
2. `theme-setup.php`
3. `enqueue.php`
4. `customizer.php`
5. `security.php`
6. `options.php`
7. `reservation-form.php`

### Design System / CSS Custom Properties

All design tokens are declared in `assets/css/main.css` under `:root`.

**Colors**

| Variable | Value | Usage |
|---|---|---|
| `--color-primary` | `#5C3D2E` | Walnut brown — headings, buttons, borders |
| `--color-primary-dark` | `#3E2418` | Hover states, footer background |
| `--color-primary-light` | `#7A5545` | Subtle highlights |
| `--color-accent` | `#C8922A` | Amber gold — CTAs, icons, underlines |
| `--color-accent-dark` | `#A07020` | Accent hover |
| `--color-accent-light` | `#E8B86D` | Accent on dark backgrounds |
| `--color-bg` | `#FAF6F0` | Warm cream — page background |
| `--color-surface` | `#FFF9F2` | Card/header background |
| `--color-surface-alt` | `#F2EAE0` | Alternate section background |
| `--color-text` | `#2C1810` | Dark brown — body text |
| `--color-text-muted` | `#6B5B4E` | Secondary text |
| `--color-text-inverse` | `#FAF6F0` | Text on dark backgrounds |
| `--color-border` | `#D4B896` | Default border |
| `--color-border-light` | `#E8DDD0` | Subtle dividers |
| `--color-success` | `#3A7D44` | Form success state |
| `--color-success-bg` | `#EBF5EC` | Success notice background |
| `--color-error` | `#9B2335` | Form error state |
| `--color-error-bg` | `#FBEAEA` | Error notice background |

**Typography**

| Variable | Value |
|---|---|
| `--font-heading` | `'Playfair Display', Georgia, 'Times New Roman', serif` |
| `--font-body` | `'Lato', system-ui, -apple-system, sans-serif` |
| `--text-xs` | `clamp(0.75rem, 1.5vw, 0.875rem)` |
| `--text-sm` | `clamp(0.875rem, 2vw, 1rem)` |
| `--text-base` | `clamp(1rem, 2.5vw, 1.125rem)` |
| `--text-lg` | `clamp(1.125rem, 2.5vw, 1.25rem)` |
| `--text-xl` | `clamp(1.25rem, 3vw, 1.5rem)` |
| `--text-2xl` | `clamp(1.5rem, 4vw, 2rem)` |
| `--text-3xl` | `clamp(1.875rem, 5vw, 2.5rem)` |
| `--text-4xl` | `clamp(2.25rem, 6vw, 3.5rem)` |

**Spacing**

`--space-1` (0.25rem) through `--space-24` (6rem) in common increments.

**Shadows**

| Variable | Value |
|---|---|
| `--shadow-sm` | `0 1px 3px rgba(44,24,16,0.08)` |
| `--shadow-md` | `0 4px 16px rgba(44,24,16,0.12)` |
| `--shadow-lg` | `0 8px 32px rgba(44,24,16,0.16)` |

**Layout**

| Variable | Value |
|---|---|
| `--container-max` | `1200px` |
| `--container-narrow` | `760px` |
| `--container-padding` | `clamp(1rem, 5vw, 2rem)` |
| `--header-height` | `72px` |

**Transitions**

| Variable | Value |
|---|---|
| `--transition-fast` | `150ms ease` |
| `--transition-normal` | `250ms ease` |

### JavaScript Modules

| File | Purpose | Dependencies | Loading |
|---|---|---|---|
| `navigation.js` | Mobile hamburger menu toggle; keyboard-accessible (Escape key, outside-click close) | None | `defer`, all pages |
| `reservation.js` | Client-side validation of the reservation form; highlights empty required fields | None | `defer`, Reservierung template only |
| `osm-consent.js` | Shows a consent overlay before loading the OSM iframe; stores consent in `localStorage` | None | `defer`, Kontakt template only |
| `customizer-preview.js` | Applies Customizer color changes as live CSS variable updates in the preview frame | `customize-preview` | Customizer only |
| `admin.js` | WordPress media uploader integration for PDF/image pickers; Speisekarte section/item repeater | jQuery | Admin settings page only |

### Navigation Menus

Two menu locations are registered:

| Location slug | Label | Fallback |
|---|---|---|
| `primary` | Hauptnavigation | `tisch_nav_fallback()` — renders a single "Startseite" link |
| `footer` | Footer-Navigation | `tisch_footer_nav_fallback()` — links to Impressum and Datenschutzerklärung pages if they exist |

### Custom Image Sizes

| Size name | Dimensions | Crop |
|---|---|---|
| `tisch-hero` | 1920 × 800 | Hard crop |
| `tisch-card` | 600 × 400 | Hard crop |
| `tisch-thumb` | 300 × 200 | Hard crop |

### Fonts

Self-hosted `.woff2` files in `assets/fonts/`. No requests to Google Fonts or any external CDN.

| Font | Weights |
|---|---|
| Playfair Display | 700 |
| Lato | 400, 700 |

`@font-face` declarations live in `assets/fonts/fonts.css`, enqueued as `tisch-fonts` before the main stylesheet.

---

## Option Keys Reference

All values are retrieved with `get_option( $key )`.

### Contact

| Key | Sanitizer | Default |
|---|---|---|
| `tisch_email` | `sanitize_email` | `''` |
| `tisch_phone` | `sanitize_text_field` | `''` |
| `tisch_address` | `sanitize_text_field` | `''` |

### Opening Hours

Repeat for each day key: `mon`, `tue`, `wed`, `thu`, `fri`, `sat`, `sun`.

| Key pattern | Sanitizer | Default |
|---|---|---|
| `tisch_hours_{day}` | `sanitize_textarea_field` | `''` |
| `tisch_hours_{day}_closed` | `sanitize_text_field` | `''` (empty = open, `'1'` = closed) |
| `tisch_hours_note` | `wp_kses_post` | `''` |

### Closing Periods

Repeat for `{n}` = 1, 2, 3.

| Key pattern | Sanitizer | Default |
|---|---|---|
| `tisch_closing_{n}_from` | `sanitize_text_field` | `''` (YYYY-MM-DD) |
| `tisch_closing_{n}_to` | `sanitize_text_field` | `''` (YYYY-MM-DD) |
| `tisch_closing_{n}_label` | `sanitize_text_field` | `''` |

### Tagesessen

| Key | Sanitizer | Default |
|---|---|---|
| `tisch_tagesessen_pdf` | `esc_url_raw` | `''` |
| `tisch_tagesessen_pdf_id` | `absint` | `0` |
| `tisch_tagesessen_valid_until` | `sanitize_text_field` | `''` (YYYY-MM-DD) |

### Speisekarte

| Key | Sanitizer | Default |
|---|---|---|
| `tisch_speisekarte_pdf` | `esc_url_raw` | `''` |
| `tisch_speisekarte_pdf_id` | `absint` | `0` |
| `tisch_speisekarte_valid_until` | `sanitize_text_field` | `''` (YYYY-MM-DD) |
| `tisch_speisekarte_sections` | `tisch_sanitize_speisekarte_sections` | `[]` |

`tisch_speisekarte_sections` is a nested PHP array: each element is `[ 'title' => string, 'items' => [ [ 'name', 'price', 'desc', 'note' ], ... ] ]`.

### Hero / Startseite

| Key | Sanitizer | Default |
|---|---|---|
| `tisch_hero_tagline` | `sanitize_text_field` | `'Herzlich willkommen'` |
| `tisch_hero_image_id` | `absint` | `0` |
| `tisch_welcome_text` | `wp_kses_post` | `''` |

### Map

| Key | Sanitizer | Default |
|---|---|---|
| `tisch_osm_lat` | `tisch_sanitize_coordinate` | `'48.087400'` |
| `tisch_osm_lng` | `tisch_sanitize_coordinate` | `'9.218900'` |

### Colors (Customizer)

Stored as WordPress options via `type => 'option'` in the Customizer setting.

| Key | Sanitizer | Default |
|---|---|---|
| `tisch_color_primary` | `sanitize_hex_color` | `'#5C3D2E'` |
| `tisch_color_primary_dark` | `sanitize_hex_color` | `'#3E2418'` |
| `tisch_color_accent` | `sanitize_hex_color` | `'#C8922A'` |
| `tisch_color_bg` | `sanitize_hex_color` | `'#FAF6F0'` |

---

## Helper Functions

All functions are defined in `inc/helpers.php`.

```php
tisch_tagesessen_is_valid(): bool
```
Returns `true` if a Tagesessen PDF is set and the valid-until date has not passed. Returns `false` if no PDF is set. If no expiry date is set, always returns `true`.

```php
tisch_speisekarte_is_valid(): bool
```
Same logic as above but for the Speisekarte PDF option keys.

```php
tisch_osm_embed_url(): string
```
Builds the OpenStreetMap embed URL from the stored lat/lng values, with a fixed zoom bounding box and a marker pin.

```php
tisch_get_opening_hours(): array
```
Returns a 7-element array, one per weekday. Each element: `[ 'label' => string, 'hours' => string, 'closed' => bool ]`.

```php
tisch_get_active_closing(): array
```
Checks today's date against all three closing-period slots. Returns `[ 'from' => string, 'to' => string, 'label' => string ]` for the matching period, or an empty array if none is active.

```php
tisch_output_color_overrides(): void
```
Hooked to `wp_head` at priority 99. Emits an inline `<style>` block overriding CSS custom properties with any custom hex colors saved via the Customizer.

```php
tisch_phone_link(): string
```
Returns the stored phone number stripped to digits, `+`, and `-`, suitable for use in a `tel:` URI.

```php
tisch_sanitize_speisekarte_sections( mixed $raw ): array
```
Sanitizes the nested Speisekarte sections array from POST data. Removes sections that have neither a title nor any items.

---

## Security Hardening

`inc/security.php` applies the following hardening measures on every request:

- **Version stripping** — removes the WordPress version from the `<head>`, RSS feeds, and all enqueued script/style query strings.
- **XML-RPC disabled** — `xmlrpc_enabled` filter returns false; RSD and WLW manifest links removed from `<head>`.
- **User enumeration blocked** — `/wp/v2/users` REST endpoints are removed for unauthenticated requests.
- **Head cleanup** — removes shortlink, adjacent post rel links, feed discovery links, and oEmbed host/discovery tags from `<head>`.
- **Embeds disabled** — oEmbed route, filter, and rewrite rules are all removed.
- **HTTP security headers** — sends `X-Content-Type-Options: nosniff`, `X-Frame-Options: SAMEORIGIN`, and `Referrer-Policy: strict-origin-when-cross-origin` on all front-end responses.

---

## Reservation Form

The reservation flow is handled entirely server-side with no database storage.

1. The visitor fills out the form on the Reservierung page (name, email, phone, date, time, number of guests, optional message, DSGVO consent checkbox).
2. On submit, `tisch_handle_reservation()` runs on the `init` hook.
3. **Nonce check** — `wp_verify_nonce()` against `tisch_reservation`. Invalid nonce redirects to `?reservierung=error`.
4. **Honeypot check** — a hidden `tisch_website` field that bots fill in. If non-empty, silently redirects to `?reservierung=success` (bot is not informed of detection).
5. **Validation** — name, valid email, date (YYYY-MM-DD format), time, guests ≥ 1, and DSGVO consent are all required. Missing fields redirect to `?reservierung=error`.
6. **Email** — `wp_mail()` sends a plain-text email to the address stored in `tisch_email`, with `Reply-To` set to the guest's name and email. No HTML, no database row.
7. **Redirect** — `wp_safe_redirect()` returns the visitor to the Reservierung page with a query param:

| Query param value | Displayed notice |
|---|---|
| `?reservierung=success` | Green success notice |
| `?reservierung=error` | Red error notice (validation or nonce failure) |
| `?reservierung=mail-error` | Red notice indicating the email could not be sent |

---

## DSGVO / Privacy Notes

- **Fonts** — Playfair Display and Lato are served from `assets/fonts/` as self-hosted `.woff2` files. No requests to Google Fonts or any other CDN.
- **OpenStreetMap** — the map iframe on the Kontakt page is blocked until the visitor clicks "Karte anzeigen". Consent is stored in `localStorage` (`osm_consent=1`) so subsequent visits load the map directly.
- **No analytics, no tracking pixels** — the theme loads no third-party scripts of any kind.
- **Reservation form** — submitted data (name, email, phone, date, time, guests, message) is transmitted to the restaurant via `wp_mail()` only. Nothing is written to the WordPress database.

---

## Extending the Theme

### Child theme

Create a standard WordPress child theme that declares `Template: tisch-kohler` in its `style.css`. The child theme's `functions.php` is loaded before the parent; use it to add hooks or override functions.

### Overriding template parts

To replace a template part without editing the parent theme, copy the file to the same relative path in your child theme. WordPress checks the child theme directory first.

For example, to replace the hero section:

```
child-theme/template-parts/home/hero.php
```

### Registering additional options

Add new keys to the `$options` array in `inc/options.php` and register them in `tisch_register_settings()` using `register_setting( 'tisch_options_group', $key, $args )`. Then render the field inside `tisch_render_settings_page()`.

---

## Changelog

### 1.0.0

Initial release.

- Classic WordPress theme, no block editor requirement
- DSGVO-compliant: self-hosted fonts, OpenStreetMap consent gate, zero external scripts
- Custom admin settings page (contact, hours, closing periods, PDF upload, hero content, OSM coordinates)
- Customizer color overrides for four primary CSS variables
- Reservation form with nonce, honeypot, server-side validation, `wp_mail()` delivery, no DB storage
- Page templates for Tagesessen, Speisekarte (PDF + structured repeater), Catering, Reservierung, Über uns, Kontakt, Impressum, Datenschutzerklärung
- Security hardening: version stripping, XML-RPC disabled, user enumeration blocked, HTTP security headers
