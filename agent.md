# Smart Disclaimer Bar — Agent Guide

## Purpose
A lightweight WordPress plugin (no database tables) that injects a configurable disclaimer bar into any position on the frontend. All settings live in a single serialized option: `sdb_settings`.

## File Map
```
smart-disclaimer-bar/
├── smart-disclaimer-bar.php     Main plugin file — constants, boot, activation
├── uninstall.php                Deletes sdb_settings option on removal
├── includes/
│   ├── class-loader.php         Queues actions/filters and fires them via run()
│   ├── class-settings.php       defaults(), get(), sanitize() — single source of truth
│   └── helper-functions.php     sdb_should_display(), sdb_build_inline_style()
├── admin/
│   ├── class-admin.php          Admin: menu, settings API, enqueue, preview AJAX
│   ├── settings-page.php        Admin UI template (4-tab form)
│   └── assets/
│       ├── css/admin.css        Tab nav, toggles, Select2 overrides, spacing grid
│       └── js/admin.js          Tabs, wpColorPicker, Select2, opacity live display, preview AJAX
└── public/
    ├── class-frontend.php       Frontend: sdb_should_display check, hook routing, render_bar()
    └── assets/
        ├── css/disclaimer-bar.css   Bar layout, fixed positions, animations, responsive
        └── js/disclaimer-bar.js     Dismiss logic (cookie + localStorage), DOM reposition
```

## Settings Schema (`sdb_settings` option)
| Key | Type | Default | Notes |
|-----|------|---------|-------|
| enabled | bool | false | Master on/off |
| content | html | see defaults | Stored via wp_kses_post |
| position | string | top_bar | top_bar \| below_header \| above_footer \| fixed_bottom \| fixed_top \| custom_hook |
| custom_hook | string | '' | Sanitized as key |
| display_scope | string | entire | entire \| homepage \| selected_pages \| selected_posts \| selected_post_types |
| selected_pages | int[] | [] | Page IDs |
| selected_posts | int[] | [] | Post IDs |
| selected_post_types | string[] | [] | Post type slugs |
| excluded_pages | int[] | [] | Always excluded, even under "entire" |
| responsive | string | both | both \| desktop \| mobile |
| font_size | int | 14 | px |
| font_weight | string | 400 | 300–900 |
| text_align | string | center | left \| center \| right \| justify |
| line_height | string | 1.6 | |
| text_color | hex | #5a4b00 | |
| bg_color | hex | #fff8e1 | |
| border_color | hex | #ffc107 | |
| border_position | string | bottom | none \| top \| bottom \| both |
| border_width | int | 3 | px |
| opacity | string | 1 | 0.1–1.0 |
| padding_top/bottom/left/right | int | 10/10/20/20 | px |
| margin_top/bottom | int | 0/0 | px |
| width_type | string | full | full \| boxed |
| dismissible | bool | false | |
| dismiss_text | string | × | Close button label |
| dismiss_expiry | int | 7 | Days; 0 = session only |
| animation | string | fade_in | none \| fade_in \| slide_down \| slide_up |
| z_index | int | 9999 | |

## Key Classes
- **SDB_Loader** — collects add_action/add_filter calls and fires them all in `run()`.
- **SDB_Settings** — never reads/writes the option directly from views; always go through `get()` and `sanitize()`.
- **SDB_Admin** — registers under Settings → Disclaimer Manager. Preview AJAX (`sdb_preview` action) returns rendered HTML.
- **SDB_Frontend** — hooks into `wp` to check display conditions, then conditionally hooks `render_bar()` into the right action. Frontend assets are only enqueued when the bar will display.

## Hook Routing (position → WordPress hook)
| Position | Primary hook | Fallback |
|----------|-------------|---------|
| top_bar | wp_body_open (priority 1) | wp_footer (priority 1) + JS prepend to body |
| below_header | wp_body_open (priority 5) | wp_footer (priority 1) + JS reposition after header |
| above_footer | wp_footer (priority 1) | — |
| fixed_top/bottom | wp_footer (priority 99) | — |
| custom_hook | dynamic add_action | — |

## Dismiss Persistence (JS)
- `expiry > 0`: cookie + `localStorage`, both set to the expiry period.
- `expiry = 0`: `sessionStorage` only (resets on browser close).
- Storage key: `sdb_dismissed`.

## Future Extension Points
- **Multiple bars**: convert `sdb_settings` to `sdb_settings[]` (array of configs). SDB_Settings::get() returns array, frontend loops and renders each.
- **Scheduled disclaimers**: add `start_date` / `end_date` fields; check in `sdb_should_display()`.
- **Geo-targeting**: call a geolocation API in `sdb_should_display()` or via JS class injection.
- **User-role targeting**: `current_user_can()` check in `sdb_should_display()`.
- **Shortcode**: `add_shortcode('disclaimer_bar', [SDB_Frontend::class, 'render_bar'])`.
- **Gutenberg block**: register `sdb/disclaimer-bar` block that calls `render_bar()` as `render_callback`.
- **AJAX preview nonce**: `sdb_preview` (created in SDB_Admin::enqueue_assets, verified in ajax_preview).
