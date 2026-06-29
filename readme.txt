=== Evolnux Disclaimer Bar ===
Contributors: evolnux
Tags: disclaimer, notice bar, announcement bar, notification bar, banner
Requires at least: 5.6
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.0.0
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

== Screenshots ==

1. Admin settings screen.
2. Frontend top bar display.

== Changelog ==

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 1.0.0 =
Initial release.
