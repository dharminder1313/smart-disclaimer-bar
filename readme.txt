=== Evolnux Disclaimer Bar ===
Contributors: dharminder
Tags: disclaimer, notice bar, announcement bar, notification bar, banner
Requires at least: 5.6
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Display a customizable disclaimer bar with flexible position, style, animation, dismiss, and display-scope options.

== Description ==

Evolnux Disclaimer Bar adds a configurable disclaimer bar to your WordPress site. Use it for financial disclaimers, legal notices, risk notices, compliance messages, affiliate disclosures, announcements, or any short site-wide message that should be visible to visitors.

The plugin stores settings in a single WordPress option and does not create custom database tables.

= Features =

* Enable or disable the disclaimer bar from the dashboard.
* Add rich disclaimer content with the WordPress editor.
* Display the bar above the header, below the header, above the footer, fixed at the top, fixed at the bottom, or on a custom action hook.
* Show the bar across the full site, homepage only, selected pages, selected posts, or selected post types.
* Exclude selected pages from display.
* Control desktop and mobile visibility.
* Customize colors, typography, border, spacing, width, opacity, animation, and z-index.
* Allow visitors to dismiss the bar.
* Configure dismissal expiry in days or make dismissal session-only.
* Preview the design in the admin settings screen.
* Place the bar manually anywhere with the `[evolnux_disclaimer]` shortcode.
* Restrict the bar to a date range with a start and end date (optional).
* Remove plugin settings on uninstall.

== Installation ==

1. Upload the `evolnux-disclaimer-bar` folder to `/wp-content/plugins/`.
2. Activate **Evolnux Disclaimer Bar** from the **Plugins** screen.
3. Go to **Settings > Disclaimer Manager**.
4. Enable the disclaimer bar.
5. Add your disclaimer content and configure display, design, and advanced options.
6. Save changes.

== Frequently Asked Questions ==

= Where do I configure the disclaimer bar? =

Go to **Settings > Disclaimer Manager** in the WordPress admin.

= Can I show the bar only on selected pages? =

Yes. Set **Display Scope** to **Selected Pages**, then choose the pages where the bar should appear.

= Can I exclude pages from the site-wide display option? =

Yes. Use the **Exclude Pages** setting. Excluded pages are hidden even when the display scope is set to the entire website.

= Can visitors dismiss the bar? =

Yes. Enable the dismissible option in the advanced settings. Dismissal can be stored for a set number of days or for the current browser session only.

= Does the plugin create database tables? =

No. Settings are stored in the `evolnux_settings` WordPress option.

= How does the custom hook position work? =

Select **Custom Action Hook** as the position and enter the action hook name. The plugin will render the disclaimer bar when that hook runs.

= Can I place the bar manually instead of using Position? =

Yes. Use the `[evolnux_disclaimer]` shortcode in any post, page, or widget. It still respects the Enabled toggle and the Schedule window, but ignores the Position and Display Scope settings so you can place it exactly where you want.

= Can I limit the disclaimer to a date range? =

Yes. On the Display tab, set a Start and/or End date under Schedule. Leave either field empty for no limit on that side. This applies to both the automatic placement and the shortcode.

== Screenshots ==

1. Admin settings screen.
2. Frontend top bar display.

== Changelog ==

= 1.1.0 =
* Added the `[evolnux_disclaimer]` shortcode for manual placement in content, widgets, or templates.
* Added an optional Schedule (start/end date) to show the bar only within a date range.
* Added a "Settings" link on the Plugins screen.
* Fixed the admin live preview not loading the frontend stylesheet, so boxed width, the close button position, and animations now render correctly in Preview.
* Dismiss behaviour now supports multiple bar instances on the same page (e.g. an auto-placed bar plus a shortcode-placed one) and hides them together.

= 1.0.1 =
* Renamed plugin to Evolnux Disclaimer Bar; updated text domain, prefixes, and main plugin filename.

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 1.1.0 =
Adds the [evolnux_disclaimer] shortcode and an optional display schedule (start/end date). No settings are changed on upgrade.

= 1.0.1 =
Plugin renamed to Evolnux Disclaimer Bar.

= 1.0.0 =
Initial release.
