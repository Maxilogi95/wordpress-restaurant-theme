# Tisch by Kohler — Technical Reference

> This file is loaded automatically by Claude Code at session start.
> It is the **full technical reference**. MEMORY.md holds the brief overview.

---

## 1. Project Identity

| Key | Value |
|---|---|
| Theme Name | Tisch by Kohler |
| Folder / slug | `tisch-kohler` |
| Text domain | `tisch-kohler` |
| PHP function prefix | `tisch_` |
| HTML / JS class prefix | `tisch-` |
| WP option prefix | `tisch_` |
| Version | 1.0.0 |
| Author | Maximilian Kohler |
| Requires WP | 6.4+ |
| Requires PHP | 8.0+ (developed on 8.2) |
| License | GPL-2.0-or-later |

---

## 2. Local Dev Environment

- **Tool:** Local by Flywheel
- **Working directory:** `/Users/maximiliankohler/Wordpress/Hirsch Mägerkingen`
  (this is the theme root — placed directly in `wp-content/themes/tisch-kohler/`)
- **PHP binary:**
  `/Applications/Local.app/Contents/Resources/extraResources/lightning-services/php-8.2.27+1/bin/darwin-arm64/bin/php`

---

## 3. File Map

### Theme root
| File | Purpose |
|---|---|
| `style.css` | Theme header + `@import assets/css/main.css` |
| `functions.php` | Autoloader only — loads `inc/*.php` in order |
| `index.php` | Ultimate fallback template (empty loop with get_header/footer) |
| `front-page.php` | Static front page — hero, welcome, tagesessen teaser, opening hours |
| `page.php` | Generic page fallback (used when no page-template is selected) |
| `404.php` | 404 error page |
| `header.php` | `<head>`, skip-link, site-header, primary nav |
| `footer.php` | Site footer with footer nav, address, copyright |

### inc/
| File | Purpose |
|---|---|
| `inc/helpers.php` | Shared utility functions (see §6) |
| `inc/theme-setup.php` | `after_setup_theme`: nav menus, image sizes, HTML5, editor styles |
| `inc/enqueue.php` | Enqueues fonts, main CSS, print CSS, JS (conditional) |
| `inc/customizer.php` | WP Customizer panel for 4 color overrides |
| `inc/security.php` | Security hardening: removes WP version, disables xmlrpc, REST user endpoints |
| `inc/options.php` | Admin settings page `Appearance > Tisch Einstellungen` + `register_setting()` |
| `inc/reservation-form.php` | POST handler for reservation form (wp_mail, redirect-after-POST) |

### page-templates/
| File | Template Name |
|---|---|
| `page-templates/tagesessen.php` | Tagesessen |
| `page-templates/speisekarte.php` | Speisekarte |
| `page-templates/catering.php` | Catering |
| `page-templates/reservierung.php` | Reservierung |
| `page-templates/ueber-uns.php` | Über uns |
| `page-templates/kontakt.php` | Kontakt |
| `page-templates/impressum.php` | Impressum |
| `page-templates/datenschutz.php` | Datenschutzerklärung |

### template-parts/
| File | Purpose |
|---|---|
| `template-parts/home/hero.php` | Full-bleed hero with tagline + hero image |
| `template-parts/home/welcome.php` | Welcome text section |
| `template-parts/home/tagesessen-teaser.php` | Teaser linking to Tagesessen if PDF is valid |
| `template-parts/home/opening-hours.php` | Opening hours table + closing-period banner |
| `template-parts/tagesessen/pdf-viewer.php` | Inline PDF viewer or "not available" message |
| `template-parts/reservierung/reservation-form.php` | Reservation HTML form |
| `template-parts/kontakt/osm-map.php` | OSM iframe with consent overlay |

### assets/
| File | Purpose |
|---|---|
| `assets/css/main.css` | All front-end styles; CSS custom properties at top |
| `assets/css/print.css` | Print overrides; enqueued with `media="print"` |
| `assets/fonts/fonts.css` | `@font-face` declarations for self-hosted fonts |
| `assets/fonts/*.woff2` | Playfair Display 700, Lato 400, Lato 700 |
| `assets/js/navigation.js` | Mobile menu toggle (all pages, defer) |
| `assets/js/reservation.js` | Client-side form validation (Reservierung template only, defer) |
| `assets/js/osm-consent.js` | OSM consent overlay + localStorage (Kontakt template only, defer) |
| `assets/js/admin.js` | Media uploader + Speisekarte repeater (admin settings page only, jQuery) |
| `assets/js/customizer-preview.js` | Live color preview in WP Customizer (customize_preview_init) |

### languages/
| File | Purpose |
|---|---|
| `languages/tisch-kohler.pot` | Translation template |

---

## 4. Autoload Order

`functions.php` loads these files in this exact order:

1. `inc/helpers.php`
2. `inc/theme-setup.php`
3. `inc/enqueue.php`
4. `inc/customizer.php`
5. `inc/security.php`
6. `inc/options.php`
7. `inc/reservation-form.php`

---

## 5. Option Keys Reference

All options stored via `register_setting( 'tisch_options_group', ... )`.
Color options are additionally registered as Customizer settings (`type => 'option'`).

### Contact / Address
| Key | Type | Sanitizer | UI field |
|---|---|---|---|
| `tisch_email` | string | `sanitize_email` | Kontakt & Adresse › E-Mail-Adresse |
| `tisch_phone` | string | `sanitize_text_field` | Kontakt & Adresse › Telefon |
| `tisch_address` | string | `sanitize_text_field` | Kontakt & Adresse › Adresse (Footer) |

### Opening Hours (per day)
Pattern: `tisch_hours_{day}` and `tisch_hours_{day}_closed` where `{day}` ∈ {mon, tue, wed, thu, fri, sat, sun}

| Key | Type | Sanitizer |
|---|---|---|
| `tisch_hours_{day}` | string (multiline) | `sanitize_textarea_field` |
| `tisch_hours_{day}_closed` | string ('1' or '') | `sanitize_text_field` |
| `tisch_hours_note` | HTML string | `wp_kses_post` |

### Closing Periods (3 fixed slots)
| Key | Type | Sanitizer |
|---|---|---|
| `tisch_closing_{1\|2\|3}_from` | string (YYYY-MM-DD) | `sanitize_text_field` |
| `tisch_closing_{1\|2\|3}_to` | string (YYYY-MM-DD) | `sanitize_text_field` |
| `tisch_closing_{1\|2\|3}_label` | string | `sanitize_text_field` |

### Tagesessen PDF
| Key | Type | Sanitizer |
|---|---|---|
| `tisch_tagesessen_pdf` | URL string | `esc_url_raw` |
| `tisch_tagesessen_pdf_id` | int | `absint` |
| `tisch_tagesessen_valid_until` | string (YYYY-MM-DD) | `sanitize_text_field` |

### Speisekarte PDF + Menu Sections
| Key | Type | Sanitizer |
|---|---|---|
| `tisch_speisekarte_pdf` | URL string | `esc_url_raw` |
| `tisch_speisekarte_pdf_id` | int | `absint` |
| `tisch_speisekarte_valid_until` | string (YYYY-MM-DD) | `sanitize_text_field` |
| `tisch_speisekarte_sections` | array (nested) | `tisch_sanitize_speisekarte_sections()` |

`tisch_speisekarte_sections` structure:
```php
[
  [ 'title' => string, 'items' => [
      [ 'name' => string, 'price' => string, 'desc' => string, 'note' => string ],
      ...
  ]],
  ...
]
```

### OSM Coordinates
| Key | Type | Sanitizer | Default |
|---|---|---|---|
| `tisch_osm_lat` | string (decimal, 6dp) | `tisch_sanitize_coordinate()` | '48.087400' |
| `tisch_osm_lng` | string (decimal, 6dp) | `tisch_sanitize_coordinate()` | '9.218900' |

### Hero / Welcome
| Key | Type | Sanitizer |
|---|---|---|
| `tisch_hero_tagline` | string | `sanitize_text_field` |
| `tisch_hero_image_id` | int | `absint` |
| `tisch_welcome_text` | HTML | `wp_kses_post` |

### Color Overrides (Customizer, `type => 'option'`)
| Key | Default | WP Customizer label |
|---|---|---|
| `tisch_color_primary` | `#5C3D2E` | Primärfarbe (Braun) |
| `tisch_color_primary_dark` | `#3E2418` | Primärfarbe dunkel |
| `tisch_color_accent` | `#C8922A` | Akzentfarbe (Gold) |
| `tisch_color_bg` | `#FAF6F0` | Hintergrundfarbe |

---

## 6. Public PHP Helper Functions

All defined in `inc/helpers.php`.

| Signature | Returns | Description |
|---|---|---|
| `tisch_tagesessen_is_valid(): bool` | bool | True if Tagesessen PDF is set and `tisch_tagesessen_valid_until` has not passed |
| `tisch_speisekarte_is_valid(): bool` | bool | True if Speisekarte PDF is set and `tisch_speisekarte_valid_until` has not passed |
| `tisch_osm_embed_url(): string` | string | Builds OSM embed URL from `tisch_osm_lat` / `tisch_osm_lng` with 0.002° bbox |
| `tisch_get_opening_hours(): array` | `array<int, array{label:string, hours:string, closed:bool}>` | Returns 7-element array (Mon–Sun) of structured opening hours |
| `tisch_get_active_closing(): array` | `array{from:string, to:string, label:string}\|array{}` | Returns currently active closing period or empty array |
| `tisch_output_color_overrides(): void` | void | Echoes inline `<style>` for color CSS vars; hooked to `wp_head` at priority 99 |
| `tisch_sanitize_speisekarte_sections(mixed $raw): array` | array | Sanitizes nested Speisekarte sections array from admin form |
| `tisch_phone_link(): string` | string | Returns `tisch_phone` stripped to digits, `+`, `-` (safe for `tel:` URIs) |

Additional utility functions in other inc files:
- `tisch_sanitize_coordinate(string $value): string` — `inc/options.php` — casts to float, formats to 6 decimal places
- `tisch_redirect_reservation(string $status): void` — `inc/reservation-form.php` — wp_safe_redirect to reservierung page with `?reservierung={status}`
- `tisch_nav_fallback(): void` — `inc/theme-setup.php` — outputs a single Startseite `<li>` when no primary menu assigned
- `tisch_footer_nav_fallback(): void` — `inc/theme-setup.php` — outputs Impressum + Datenschutz links when no footer menu assigned

---

## 7. Template → Template-Parts Map

| Template file | get_template_part() calls |
|---|---|
| `front-page.php` | `home/hero`, `home/welcome`, `home/tagesessen-teaser`, `home/opening-hours` |
| `page-templates/tagesessen.php` | `tagesessen/pdf-viewer` |
| `page-templates/reservierung.php` | `reservierung/reservation-form` |
| `page-templates/kontakt.php` | `kontakt/osm-map` (plus inline hours/address output) |
| `page-templates/speisekarte.php` | (renders menu sections inline, no get_template_part) |
| `page-templates/catering.php` | (page content only) |
| `page-templates/ueber-uns.php` | (page content only) |
| `page-templates/impressum.php` | (page content only) |
| `page-templates/datenschutz.php` | (page content only) |

All templates call `get_header()` and `get_footer()`.

---

## 8. Page Template Slugs

| Page slug (WP) | Template Name | Template file |
|---|---|---|
| `tagesessen` | Tagesessen | `page-templates/tagesessen.php` |
| `speisekarte` | Speisekarte | `page-templates/speisekarte.php` |
| `catering` | Catering | `page-templates/catering.php` |
| `reservierung` | Reservierung | `page-templates/reservierung.php` |
| `ueber-uns` | Über uns | `page-templates/ueber-uns.php` |
| `kontakt` | Kontakt | `page-templates/kontakt.php` |
| `impressum` | Impressum | `page-templates/impressum.php` |
| `datenschutzerklaerung` | Datenschutzerklärung | `page-templates/datenschutz.php` |

Front page: Settings > Reading → set to static page (slug: `startseite` or similar).

---

## 9. JavaScript Files

### `assets/js/navigation.js`
- **Purpose:** Mobile menu toggle
- **Enqueued:** All pages, `strategy: defer`, in footer
- **Handle:** `tisch-navigation`
- **No dependencies**
- **DOM:** `#menu-toggle` (button), `#primary-menu` (nav list), `#site-navigation` (nav wrapper)
- **Events:** click (toggle), `keydown` Escape (close), `click` outside (close), `matchMedia` resize (reset at ≥768px)
- **ARIA:** `aria-expanded` on toggle, `is-open` class on menu

### `assets/js/reservation.js`
- **Purpose:** Client-side validation for reservation form (progressive enhancement — form works without JS)
- **Enqueued:** `page-templates/reservierung.php` only, `strategy: defer`, in footer
- **Handle:** `tisch-reservation`
- **No dependencies**
- **DOM:** `#reservation-form`
- **Validates:** `tisch_name`, `tisch_email`, `tisch_date` (must be future), `tisch_time`, `tisch_guests` (1–100), `tisch_dsgvo` (checkbox)
- **Events:** `blur` (validate), `input` (clear error if field had error), `submit` (full validate + focus first invalid)

### `assets/js/osm-consent.js`
- **Purpose:** OSM map consent overlay; auto-loads map if user previously consented
- **Enqueued:** `page-templates/kontakt.php` only, `strategy: defer`, in footer
- **Handle:** `tisch-osm-consent`
- **No dependencies**
- **DOM:** `#osm-consent` (overlay), `#osm-iframe` (iframe with `data-src`), `#osm-accept-btn`
- **localStorage key:** `tisch_osm_consent` ('1' = accepted)

### `assets/js/admin.js`
- **Purpose:** WP Media uploader for PDF fields + hero image; Speisekarte repeater UI
- **Enqueued:** `appearance_page_tisch-einstellungen` only, after `wp_enqueue_media()`
- **Handle:** `tisch-admin`
- **Dependencies:** `['jquery']`
- **Inline data (before):** `var tischAdminData = { speisekarteData: [...] };`
- **Globals used:** `wp.media`, `$` (jQuery)
- **Handles:** `.tisch-pdf-upload`, `.tisch-pdf-remove`, `.tisch-hero-image-upload`, `.tisch-hero-image-remove`, Speisekarte section/item add/remove/reorder, serialises sections to hidden inputs on form submit

### `assets/js/customizer-preview.js`
- **Purpose:** Live-previews color token changes in WP Customizer without page reload
- **Enqueued:** `customize_preview_init` hook only
- **Handle:** `tisch-customizer-preview`
- **Dependencies:** `['customize-preview']`
- **Transport:** `postMessage` for all 4 color settings
- **DOM:** Creates/updates `<style id="tisch-color-overrides">` in `<head>`

---

## 10. CSS Design Tokens

All in `assets/css/main.css` under `:root`.

### Colors
```css
--color-primary:        #5C3D2E   /* walnut brown — overridable via Customizer */
--color-primary-dark:   #3E2418   /* overridable */
--color-primary-light:  #7A5545
--color-accent:         #C8922A   /* amber gold — overridable */
--color-accent-dark:    #A07020
--color-accent-light:   #E8B86D
--color-bg:             #FAF6F0   /* cream — overridable */
--color-surface:        #FFF9F2
--color-surface-alt:    #F2EAE0
--color-text:           #2C1810
--color-text-muted:     #6B5B4E
--color-text-inverse:   #FAF6F0
--color-border:         #D4B896
--color-border-light:   #E8DDD0
--color-success:        #3A7D44
--color-success-bg:     #EBF5EC
--color-error:          #9B2335
--color-error-bg:       #FBEAEA
```

### Typography
```css
--font-heading: 'Playfair Display', Georgia, 'Times New Roman', serif
--font-body:    'Lato', system-ui, -apple-system, sans-serif

/* Fluid type scale (clamp) */
--text-xs:   clamp(0.75rem,  1.5vw, 0.875rem)
--text-sm:   clamp(0.875rem, 2vw,   1rem)
--text-base: clamp(1rem,     2.5vw, 1.125rem)
--text-lg:   clamp(1.125rem, 2.5vw, 1.25rem)
--text-xl:   clamp(1.25rem,  3vw,   1.5rem)
--text-2xl:  clamp(1.5rem,   4vw,   2rem)
--text-3xl:  clamp(1.875rem, 5vw,   2.5rem)
--text-4xl:  clamp(2.25rem,  6vw,   3.5rem)

--line-height-tight:  1.25
--line-height-normal: 1.6
--line-height-loose:  1.8
```

### Spacing
```css
--space-1: 0.25rem  --space-2: 0.5rem   --space-3: 0.75rem
--space-4: 1rem     --space-6: 1.5rem   --space-8: 2rem
--space-10: 2.5rem  --space-12: 3rem    --space-16: 4rem
--space-20: 5rem    --space-24: 6rem
```

### Layout / Misc
```css
--radius-sm: 0.25rem   --radius-md: 0.5rem
--radius-lg: 1rem      --radius-full: 9999px

--shadow-sm: 0 1px 3px rgba(44,24,16,0.08)
--shadow-md: 0 4px 16px rgba(44,24,16,0.12)
--shadow-lg: 0 8px 32px rgba(44,24,16,0.16)

--container-max:     1200px
--container-narrow:  760px
--container-padding: clamp(1rem, 5vw, 2rem)

--transition-fast:   150ms ease
--transition-normal: 250ms ease
```

### Custom image sizes (registered in `inc/theme-setup.php`)
| Handle | Width | Height | Crop |
|---|---|---|---|
| `tisch-hero` | 1920 | 800 | yes |
| `tisch-card` | 600 | 400 | yes |
| `tisch-thumb` | 300 | 200 | yes |

### Nav menus (registered in `inc/theme-setup.php`)
| Location | Label |
|---|---|
| `primary` | Hauptnavigation |
| `footer` | Footer-Navigation |

---

## 11. Conventions & Patterns

### PHP
- `declare(strict_types=1)` at top of every PHP file
- All functions prefixed `tisch_`
- ABSPATH guard in every file: `if ( ! defined( 'ABSPATH' ) ) { exit; }`
- All output escaped at point of echo: `esc_html()`, `esc_attr()`, `esc_url()`, `wp_kses_post()`
- Use `wp_unslash()` before `sanitize_*` on `$_POST` values
- `DateTimeImmutable` for all date comparisons; `wp_timezone()` for site-local dates
- Use `get_template_directory()` (not `get_stylesheet_directory()`) — this is not a child theme

### Hooks
- `after_setup_theme` → `tisch_theme_setup` (theme-setup.php)
- `wp_enqueue_scripts` → `tisch_enqueue_assets` (enqueue.php, priority 10)
- `wp_enqueue_scripts` → `tisch_dequeue_defaults` (enqueue.php, priority 20)
- `wp_head` → `tisch_output_color_overrides` (helpers.php, priority 99)
- `admin_init` → `tisch_register_settings` (options.php)
- `admin_menu` → `tisch_add_settings_page` (options.php)
- `admin_enqueue_scripts` → `tisch_admin_enqueue` (options.php — guard: hook = `appearance_page_tisch-einstellungen`)
- `customize_register` → `tisch_customize_register` (customizer.php)
- `customize_preview_init` → `tisch_customize_preview_scripts` (enqueue.php)
- `init` → `tisch_handle_reservation` (reservation-form.php)
- `init` → `tisch_disable_embeds` (security.php)
- `send_headers` → `tisch_security_headers` (security.php)

### Conditional JS enqueue
- `tisch-reservation` → only when `is_page_template('page-templates/reservierung.php')`
- `tisch-osm-consent` → only when `is_page_template('page-templates/kontakt.php')`
- `tisch-admin` → only when `$hook === 'appearance_page_tisch-einstellungen'`
- `tisch-customizer-preview` → only on `customize_preview_init`

### Dequeued WP defaults
`wp-block-library`, `wp-block-library-theme`, `global-styles` — removed in `tisch_dequeue_defaults`.

---

## 12. DSGVO / Security Rules

**Never add:**
- Google Analytics, Google Tag Manager, or any tracking pixel
- Google Fonts or any CDN-loaded font (`@import url(https://fonts.googleapis.com/...)`)
- Any external JavaScript loaded from a CDN at page load
- Form data stored to the database (reservation data is email-only via `wp_mail()`)
- Google Maps (use OpenStreetMap only)
- Cookies other than the WordPress session cookie (no analytics cookies, no tracking)

**Always maintain:**
- Self-hosted fonts only (woff2 files in `assets/fonts/`)
- OSM embed behind a consent overlay (`osm-consent.js` + `localStorage`)
- Reservation form: nonce + honeypot + no DB storage
- Security headers: `X-Content-Type-Options: nosniff`, `X-Frame-Options: SAMEORIGIN`, `Referrer-Policy: strict-origin-when-cross-origin`
- `the_generator` filter returns empty string (WP version hidden)
- XML-RPC disabled
- REST API user enumeration blocked for logged-out visitors

---

## 13. Verification Command

PHP syntax check — all 28 PHP files:

```bash
find "/Users/maximiliankohler/Wordpress/Hirsch Mägerkingen" \
  -name "*.php" \
  -not -path "*/node_modules/*" \
  | xargs /Applications/Local.app/Contents/Resources/extraResources/lightning-services/php-8.2.27+1/bin/darwin-arm64/bin/php \
  -l 2>&1 | grep -v "No syntax errors"
```

No output = all files pass. Alternatively check a single file:

```bash
/Applications/Local.app/Contents/Resources/extraResources/lightning-services/php-8.2.27+1/bin/darwin-arm64/bin/php \
  -l "/Users/maximiliankohler/Wordpress/Hirsch Mägerkingen/functions.php"
```

---

## 14. WordPress Setup Checklist

Required after activating the theme on a fresh WordPress install:

### Pages
Create pages with these exact slugs and assign the matching template:

| Page title | Slug | Page Template |
|---|---|---|
| Startseite | `startseite` | (default, set as front page) |
| Tagesessen | `tagesessen` | Tagesessen |
| Speisekarte | `speisekarte` | Speisekarte |
| Catering | `catering` | Catering |
| Reservierung | `reservierung` | Reservierung |
| Über uns | `ueber-uns` | Über uns |
| Kontakt | `kontakt` | Kontakt |
| Impressum | `impressum` | Impressum |
| Datenschutzerklärung | `datenschutzerklaerung` | Datenschutzerklärung |

### Settings
1. **Settings > Reading:** Front page displays → Static page → select Startseite
2. **Appearance > Menus:**
   - Create "Hauptnavigation" → assign to location `primary`
   - Create "Footer-Navigation" → assign to location `footer` (must include Impressum + Datenschutz)
3. **Appearance > Tisch Einstellungen:** Fill in email, phone, address, opening hours, OSM coordinates
4. **Appearance > Tisch Einstellungen:** Upload Tagesessen PDF + set valid-until date
5. **(Optional) Appearance > Customize > Tisch by Kohler > Farben:** Override brand colors
