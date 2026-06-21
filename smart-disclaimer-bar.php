<?php
/**
 * Plugin Name: Smart Disclaimer Bar
 * Plugin URI:  https://github.com/dharminder1313/smart-disclaimer-bar/
 * Description: Display a customizable disclaimer bar anywhere on your site — position, style, animation, dismiss, and scope fully configurable from Settings → Disclaimer Manager.
 * Version:     1.0.0
 * Author:      Dharminder Singh
 * Author URI:  https://linkedin.com/in/dharminder-singh-dhaliwal
 * Text Domain: smart-disclaimer-bar
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 5.6
 * Tested up to: 7.0
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'SDB_VERSION',    '1.0.0' );
define( 'SDB_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SDB_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SDB_OPTION_KEY', 'sdb_settings' );

require_once SDB_PLUGIN_DIR . 'includes/class-loader.php';
require_once SDB_PLUGIN_DIR . 'includes/class-settings.php';
require_once SDB_PLUGIN_DIR . 'includes/helper-functions.php';
require_once SDB_PLUGIN_DIR . 'admin/class-admin.php';
require_once SDB_PLUGIN_DIR . 'public/class-frontend.php';

function sdb_boot() {
	$loader = new SDB_Loader();

	if ( is_admin() ) {
		( new SDB_Admin() )->register( $loader );
	}

	( new SDB_Frontend() )->register( $loader );

	$loader->run();
}
add_action( 'plugins_loaded', 'sdb_boot' );

register_activation_hook( __FILE__, function () {
	if ( false === get_option( SDB_OPTION_KEY ) ) {
		add_option( SDB_OPTION_KEY, SDB_Settings::defaults() );
	}
} );
