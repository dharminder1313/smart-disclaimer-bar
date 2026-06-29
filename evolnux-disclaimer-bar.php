<?php
/**
 * Plugin Name: Evolnux Disclaimer Bar
 * Plugin URI:  https://github.com/evolnux/evolnux-disclaimer-bar/
 * Description: Display a customizable disclaimer bar anywhere on your site — position, style, animation, dismiss, and scope fully configurable from Settings → Disclaimer Manager.
 * Version:     1.0.0
 * Author:      Evolnux
 * Author URI:  https://github.com/evolnux/
 * Text Domain: evolnux-disclaimer-bar
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 5.6
 * Tested up to: 7.0
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'EVOLNUX_VERSION',    '1.0.0' );
define( 'EVOLNUX_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'EVOLNUX_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'EVOLNUX_OPTION_KEY', 'evolnux_settings' );

require_once EVOLNUX_PLUGIN_DIR . 'includes/class-loader.php';
require_once EVOLNUX_PLUGIN_DIR . 'includes/class-settings.php';
require_once EVOLNUX_PLUGIN_DIR . 'includes/helper-functions.php';
require_once EVOLNUX_PLUGIN_DIR . 'admin/class-admin.php';
require_once EVOLNUX_PLUGIN_DIR . 'public/class-frontend.php';

function evolnux_boot() {
	$loader = new EVOLNUX_Loader();

	if ( is_admin() ) {
		( new EVOLNUX_Admin() )->register( $loader );
	}

	( new EVOLNUX_Frontend() )->register( $loader );

	$loader->run();
}
add_action( 'plugins_loaded', 'evolnux_boot' );

register_activation_hook( __FILE__, function () {
	if ( false === get_option( EVOLNUX_OPTION_KEY ) ) {
		add_option( EVOLNUX_OPTION_KEY, EVOLNUX_Settings::defaults() );
	}
} );
